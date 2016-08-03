<?php

class Dmarc_model extends CI_Model {

  function __construct() {
      parent::__construct();
  }

  function get_report_from_reporter_id($id, $org) {
    $this->db->where('reporter',$org);
    $this->db->where('report_id',$id);
    $query = $this->db->get('dmarc_reports',1);
    if($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }

  function create_report($data) {
    $this->db->insert('dmarc_reports',$data);
    return $this->db->insert_id();
  }

  function create_record($data) {
    $this->db->insert('dmarc_records', $data);
    return $this->db->insert_id();
  }

  function update_report_totals($report,$pass,$fail) {
    $data = array(
      'total_pass' => $pass,
      'total_fail' => $fail
    );
    $this->db->where('id',$report);
    $this->db->update('dmarc_reports', $data);
  }

  function get_report($report_id) {
    $this->db->where('id', $report_id);
    $query = $this->db->get('dmarc_reports',1);
    if($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }

  function get_report_domains() {
    $this->db->select('domain');
    $this->db->distinct();
    $this->db->order_by('domain');
    $query = $this->db->get('dmarc_reports');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_reports_for_domains($domain) {
    $this->db->where('domain',$domain);
    $this->db->order_by('report_date desc, reporter asc');
    $query = $this->db->get('dmarc_reports');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_reports_for_domain_name($domain) {
    $this->db->select('dmarc_reports.id, dmarc_reports.reporter,
      dmarc_reports.report_date, dmarc_reports.domain,
      dmarc_reports.total_pass, dmarc_reports.total_fail,
      dmarc_reports.policy_p, dmarc_reports.policy_sp,
      dmarc_reports.policy_pct');
    $this->db->join('dmarc_reports','dmarc_reports.id=dmarc_records.report_id');
    $this->db->where('dmarc_records.domain_name',$domain);
    $this->db->group_by('dmarc_reports.id, dmarc_reports.reporter,
      dmarc_reports.report_date, dmarc_reports.domain');
    $this->db->order_by('dmarc_reports.report_date desc,
      dmarc_reports.reporter asc');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_record($record_id) {
    $this->db->select('dmarc_records.*, dmarc_reports.domain,
      dmarc_reports.reporter, dmarc_reports.report_date, dmarc_ip.ptr');
    $this->db->join('dmarc_reports','dmarc_reports.id=dmarc_records.report_id');
    $this->db->join('dmarc_ip','dmarc_records.source_ip=dmarc_ip.ip');
    $this->db->where('dmarc_records.id', $record_id);
    $query = $this->db->get('dmarc_records',1);
    if($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }

  function get_records_for_report($report_id) {
    $this->db->where('report_id',$report_id);
    $this->db->join('dmarc_ip','dmarc_records.source_ip=dmarc_ip.ip');
    $this->db->order_by('count desc, domain_tld asc, domain_name asc,
      domain_sub asc, source_ip');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_records_for_domain($domain) {
    $this->db->select('dmarc_records.*, dmarc_reports.report_date,
      dmarc_ip.ptr');
    $this->db->join('dmarc_reports','dmarc_reports.id=dmarc_records.report_id');
    $this->db->join('dmarc_ip','dmarc_records.source_ip=dmarc_ip.ip');
    $this->db->where('dmarc_records.domain_name',$domain);
    $this->db->order_by('report_date desc, count desc, domain_tld asc,
      domain_name asc, domain_sub asc, source_ip');
    $query = $this->db->get('dmarc_records',1000);
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  /*function get_record_domains() {
    $this->db->select('domain_full');
    $this->db->distinct();
    $this->db->order_by('domain_tld, domain_name, domain_sub');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }*/

  function get_distinct_unresolved_ips() {
    $this->db->select("ip as source_ip");
    $this->db->distinct();
    $this->db->where("ptr",null);
    $query = $this->db->get('dmarc_ip',1000);
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function update_ptr($ip,$ptr) {
    $data = array(
      'ptr' => $ptr
    );
    $this->db->where('ip',$ip);
    $this->db->update('dmarc_ip',$data);
  }


  function get_distinct_tlds() {
    $this->db->select('domain_tld');
    $this->db->distinct();
    $this->db->order_by('domain_tld');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_distinct_domains() {
    $this->db->select('domain_name');
    $this->db->distinct();
    $this->db->order_by('domain_name');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_distinct_domains_for_tld($tld) {
    $this->db->select('domain_name');
    $this->db->distinct();
    $this->db->where('domain_tld',$tld);
    $this->db->order_by('domain_name');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_counts($domain) {
    $this->db->where('domain', $domain);
    $this->db->order_by('date desc');
    $query = $this->db->get('dmarc_counts');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get_daily_aggregate_counts($domain='') {

    $this->db->select('dmarc_reports.report_date, dmarc_records.overall_result,
      dmarc_records.disposition, SUM(count) as c');
    $this->db->join('dmarc_reports','dmarc_records.report_id=dmarc_reports.id');
    $this->db->where('dmarc_records.domain_name',$domain);
    $this->db->group_by('dmarc_reports.report_date,
      dmarc_records.overall_result, dmarc_records.disposition');
    $this->db->order_by('dmarc_reports.report_date desc');
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }

  }

  function create_daily_aggregate_count($domain, $date, $total, $total_pass,
    $total_fail, $none_pass, $none_fail, $quarantine_fail, $reject_fail,
    $other) {

    $data = array(
      'date'            => $date,
      'domain'          => $domain,
      'total'           => $total,
      'total_pass'      => $total_pass,
      'total_fail'      => $total_fail,
      'none_pass'       => $none_pass,
      'none_fail'       => $none_fail,
      'quarantine_fail' => $quarantine_fail,
      'reject_fail'     => $reject_fail,
      'other'           => $other
    );
    $this->db->insert('dmarc_counts', $data);

  }

  function reset_daily_aggregate_counts() {
    $this->db->delete('dmarc_counts','id > 0');
  }

  public function update_ip_table() {
    $this->db->select('source_ip');
    $this->db->distinct();
    $this->db->where("NOT EXISTS (SELECT ip FROM daibach_dmarc_ip
      WHERE ip=source_ip)",false,false);
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      $result = $query->result();
      $ips = array();
      foreach($result as $row) {
        array_push($ips,array('ip'=>$row->source_ip));
      }
      $this->db->insert_batch('dmarc_ip',$ips);
    }
  }

  public function update_domain_table() {
    $this->db->select('domain_name, domain_tld');
    $this->db->distinct();
    $this->db->where("NOT EXISTS (SELECT domain_name FROM daibach_dmarc_domains
      WHERE daibach_dmarc_records.domain_name=daibach_dmarc_domains.domain_name)",false,false);
    $query = $this->db->get('dmarc_records');
    if($query->num_rows() > 0) {
      $result = $query->result();
      $domains = array();
      foreach($result as $row) {
        array_push(
          $domains,
          array(
            'domain_full'=>$row->domain_name,
            'domain_name'=>$row->domain_name,
            'domain_tld' =>$row->domain_tld
          )
        );
      }
      $this->db->insert_batch('dmarc_domains',$domains);
    }
  }

}
