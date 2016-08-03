<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('dmarc_model','dmarc');
  }

  public function index() {}

  public function go() {
    $this->_start_import();
  }

  public function regen_counts() {
    $this->_generate_daily_aggregate_counts();
    $this->_generate_weekly_aggregate_counts();
  }

  private function _start_import() {
    $this->load->helper('file');

    log_message('info', 'Starting DMARC report import');

    $asset_path = APP_DMARC_REPORT_PATH;
    $files = get_filenames($asset_path);
    $count = 0;

    if($files) {
      log_message('info', 'There are files to process');
      foreach($files as $file) {
        if(get_mime_by_extension($asset_path.$file)=='application/xml') {
          $count += $this->_process_file($asset_path, $file);
        }
      }
      log_message('info', 'File processing completed');
      if($count) {
        log_message('info', 'There were new files, starting additional processing.');
        $this->_generate_daily_aggregate_counts();
        $this->_generate_weekly_aggregate_counts();
        $this->dmarc->update_ip_table();
        $this->_do_domain_dns_lookups();
        $this->dmarc->update_domain_table();
      } else {
        log_message('info', 'There were no new files');
      }
    } else {
      log_message('info', 'There are no files to process');
    }

    log_message('info', 'Finishing DMARC report import');
  }

  /* FUNCTIONS FOR IMPORTING NEW DMARC REPORT FILES */

  private function _process_file($path, $filename) {
    log_message('info', "Processing file $filename");
    $xml = simplexml_load_file($path.$filename);
    $report_org = (string)$xml->report_metadata->org_name;
    $report_id = (string)$xml->report_metadata->report_id;
    $exists = $this->dmarc->get_report_from_reporter_id($report_id, $report_org);

    if($exists) {
      log_message('info', "Skipping (existing report: $exists->id)");
      return 0;
    } else {
      $pass = 0;
      $fail = 0;

      log_message('info', "New report from $report_org (#$report_id)");
      $database_id = $this->_create_report($xml);
      log_message('info', "Created as $database_id");
      log_message('info', "Processing records for $database_id");
      foreach($xml->record as $r) {
        if( (string)$r->row->policy_evaluated->dkim == 'pass' ||
            (string)$r->row->policy_evaluated->spf == 'pass') {
          $pass = $pass+(int)$r->row->count;
        } else {
          $fail = $fail+(int)$r->row->count;
        }
        $this->_create_record($r, $database_id);
      }
      $this->dmarc->update_report_totals($database_id,$pass,$fail);
      log_message('info', "Finished processing records for $database_id");
      return 1;
    }
  }

  private function _create_report($xml) {

    $report_start_date = (int)$xml->report_metadata->date_range->begin;
    $report_end_date   = (int)$xml->report_metadata->date_range->end;

    $report_date = $report_end_date;
    if ( date('H:i:s',$report_end_date) == '00:00:00' ) {
      $report_date = $report_end_date-1;
    }

    $report = array(
      'reporter'      => (string)$xml->report_metadata->org_name,
      'report_id'     => (string)$xml->report_metadata->report_id,
      'report_date'   => date('Y-m-d',$report_date),
      'report_start'  => date('Y-m-d H:i:s',$report_start_date),
      'report_end'    => date('Y-m-d H:i:s',$report_end_date),
      'domain'        => (string)$xml->policy_published->domain,
      'policy_adkim'  => (string)$xml->policy_published->adkim,
      'policy_aspf'   => (string)$xml->policy_published->aspf,
      'policy_p'      => (string)$xml->policy_published->p,
      'policy_sp'     => (string)$xml->policy_published->sp,
      'policy_pct'    => (int)$xml->policy_published->pct,
      'raw_metadata'  => json_encode($xml->report_metadata),
      'raw_policy'    => json_encode($xml->policy_published),
    );
    return $this->dmarc->create_report($report);

  }

  private function _create_record($xml,$report_id) {

    $notes = "";
    $overall_result = "";
    if( (string)$xml->row->policy_evaluated->dkim == 'pass' ||
        (string)$xml->row->policy_evaluated->spf == 'pass') {
      $overall_result = "pass";
    } else {
      $overall_result = "fail";
    }
    $reason_xml = $xml->row->policy_evaluated->reason;
    if($reason_xml) { $notes = (string)$reason_xml->type; }

    $domain_info = http_get_domain_info((string)$xml->identifiers->header_from);

    $source_ip = (string)$xml->row->source_ip;

    $record = array(
      'report_id'     => $report_id,
      'source_ip'     => $source_ip,
      'count'         => (int)$xml->row->count,
      'disposition'   => (string)$xml->row->policy_evaluated->disposition,
      'dkim_result'   => (string)$xml->row->policy_evaluated->dkim,
      'spf_result'    => (string)$xml->row->policy_evaluated->spf,
      'overall_result'=> $overall_result,
      'notes'         => $notes,
      'auth_results'  => json_encode($xml->auth_results),
      'full_record'   => json_encode($xml),
      'domain_full'   => (string)$xml->identifiers->header_from,
      'domain_tld'    => $domain_info['tld'],
      'domain_name'   => $domain_info['domain'],
      'domain_sub'    => $domain_info['sub']
    );
    return $this->dmarc->create_record($record, $report_id);

  }


  /* FUNCTION FOR GENERATING COUNTS */
  function _generate_daily_aggregate_counts() {

    log_message('info', 'Starting generating daily aggregate counts');

    $domains = $this->dmarc->get_distinct_domains();
    $this->dmarc->reset_daily_aggregate_counts();

    foreach($domains as $domain) {
      log_message('info', "Generating daily aggregate counts for $domain->domain_name");
      $data = array();
      $result = $this->dmarc->get_daily_aggregate_counts($domain->domain_name);

      foreach($result as $r) {
        if(! array_key_exists($r->report_date,$data)) {
          $data[$r->report_date] = array(
            'total'           => 0,
            'total_pass'      => 0,
            'total_fail'      => 0,
            'none_pass'       => 0,
            'none_fail'       => 0,
            'quarantine_fail' => 0,
            'reject_fail'     => 0,
            'other'           => 0
          );
        }
        $data[$r->report_date]['total'] += $r->c;
        $data[$r->report_date]['total_'.$r->overall_result] += $r->c;
        if(($r->disposition == 'QUARANTINE' || $r->disposition == 'REJECT') && $r->overall_result == 'pass') {
          $data[$r->report_date]['other'] += $r->c;
        } else {
          $data[$r->report_date][strtolower($r->disposition).'_'.$r->overall_result] += $r->c;
        }
      }

      foreach($data as $d=>$val) {
        $this->dmarc->create_daily_aggregate_count(
          $domain->domain_name, $d,
          $val['total'],
          $val['total_pass'],
          $val['total_fail'],
          $val['none_pass'],
          $val['none_fail'],
          $val['quarantine_fail'],
          $val['reject_fail'],
          $val['other']
        );
      }
    }

    log_message('info', 'Finished generating daily aggregate counts');
  }

  function _generate_weekly_aggregate_counts() {

    log_message('info', 'Starting generating weekly aggregate counts');

    $this->dmarc->reset_weekly_aggregate_counts();
    $this->dmarc->generate_weekly_aggregate_count();

    log_message('info', 'Finished generating weekly aggregate counts');
  }

  /* FUNCTIONS FOR DOMAIN DNS LOOKUPS */
  private function _do_domain_dns_lookups() {
    log_message('info', 'Starting DNS lookups');
    $ips = $this->dmarc->get_distinct_unresolved_ips();

    if($ips) {
      foreach($ips as $ip) {
        $result = $this->_lookup_ip($ip->source_ip);
        $this->dmarc->update_ptr($ip->source_ip,$result);
      }
    }
    log_message('info', 'Finished DNS lookups');
  }

  private function _lookup_ip($ip) {
    $this->load->helper('http_helper');

    $lookup_ip = "";
    if(strpos($ip, ":")) {
      //$lookup_ip = implode(".",str_split(str_replace(":","",strrev($ip))));
      $addr = inet_pton($ip);
      $unpack = unpack('H*hex', $addr);
      $hex = $unpack['hex'];
      $lookup_ip = implode('.', array_reverse(str_split($hex))) . '.ip6.arpa';
    } else {
      $lookup_ip = implode(".",array_reverse(explode(".", $ip))).".in-addr.arpa";
    }

    $dns = http_dns_lookup($lookup_ip, 'PTR');
    if($dns) {
      return $dns;
    } else {
      return $lookup_ip;
    }

  }

}
