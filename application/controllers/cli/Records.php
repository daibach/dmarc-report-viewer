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
    $dmarc_record = $this->_do_txt_lookup($domain->domain_full, 'v=dmarc1');

    $this->records->update_domain($domain->domain_full, $spf_record, $dmarc_record);

  }

  private function _do_txt_lookup($domain, $type) {

    $dns_to_query = $domain;
    if($type === 'v=dmarc1') {
      $dns_to_query = '_dmarc.'.$domain;
    }

    $dns_result = http_dns_lookup($dns_to_query, 'TXT', false);

    if($dns_result) {
      $record_found = false;
      foreach($dns_result as $result) {
        if(strpos(strtolower($result->data),$type) !== false) {
          $this->records->create_record($domain, $result->data, $type);
          $record_found = true;
        }
      }
      if($record_found) {
        return true;
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
