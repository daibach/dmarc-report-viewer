<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Records extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('records_model','records');
    $this->load->helper('http_helper');
  }

  public function index() {}

  public function go() {
    $this->_start_lookups();
  }

  private function _start_lookups() {

    log_message('info', 'Starting DNS record lookups');

    $domains = $this->records->get_domains();

    foreach($domains as $domain) {
      $this->_process_domain($domain);
    }
  }

  private function _process_domain($domain) {

    //SPF lookup
    $spf_record = $this->_do_txt_lookup($domain->domain_full, 'v=spf1');
    $dmarc_record = $this->_do_dmarc_lookup($domain->domain_full);

    $this->records->update_domain($domain->domain_full, $spf_record, $dmarc_record);

  }

  private function _do_dmarc_lookup($domain) {
    $result = $this->_do_txt_lookup($domain, 'v=dmarc1', true);
    if($result) {
      if(strpos($result,'p=reject') !== false) {
        return "reject";
      } elseif(strpos($result,'p=quarantine') !== false) {
        return "quarantine";
      } elseif(strpos($result,'p=none') !== false) {
        return "none";
      } else {
        return "other";
      }
    } else {
      return "empty";
    }
  }

  private function _do_txt_lookup($domain, $type, $return_result=false) {

    $dns_to_query = $domain;
    if($type === 'v=dmarc1') {
      $dns_to_query = '_dmarc.'.$domain;
    }

    $dns_result = http_dns_lookup($dns_to_query, 'TXT', false);

    if($dns_result) {
      $record_found = false;
      $record_contents = "";
      foreach($dns_result as $result) {
        if(strpos(strtolower($result->data),$type) !== false) {
          $this->records->create_record($domain, $result->data, $type);
          $record_found = true;
          $record_contents = $result->data;
        }
      }
      if($record_found) {
        if($return_result) {
          return $record_contents;
        } else {
          return true;
        }
      } else {
        $this->records->create_record($domain, NULL, $type);
        return false;
      }
    } else {
      $this->records->create_record($domain, NULL, $type);
      return false;
    }

  }


}
