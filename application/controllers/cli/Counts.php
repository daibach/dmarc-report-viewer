<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counts extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Domains_model','domains');
  }

  public function index() {}

  public function go() {
    $this->_generate_domain_counts();
  }

  private function _generate_domain_counts() {

    log_message('info', 'Starting domain counts');

    $domains = $this->domains->get_domains();

    $records = array();

    foreach($domains as $domain) {

      $total_dmarc_reports = $this->domains->get_domain_dmarc_reports_total($domain->domain_full);

      if(! $total_dmarc_reports) {
        $data = array(
          'domain_full'         => $domain->domain_full,
          'avg_weekly_sent'     => NULL,
          'this_week_sent'      => NULL,
          'this_week_pass_pct'  => NULL,
          'last_week_sent'      => NULL,
          'last_week_pass_pct'  => NULL,
          'received_dmarc_reports' => false
        );
        array_push($records,$data);

      } else {

        $average = $this->domains->get_domain_average_send($domain->domain_full);
        $this_week = $this->domains->get_domain_send_for_week($domain->domain_full, date('W'));
        $last_week = $this->domains->get_domain_send_for_week($domain->domain_full, date('W')-1);

        $data = array(
          'domain_full'         => $domain->domain_full,
          'avg_weekly_sent'     => $average,
          'this_week_sent'      => 0,
          'this_week_pass_pct'  => 0,
          'last_week_sent'      => 0,
          'last_week_pass_pct'  => 0,
          'received_dmarc_reports' => true
        );

        if($this_week) {
          $data['this_week_sent'] = $this_week->total;
          $data['this_week_pass_pct'] = $this_week->pct_pass;
        }

        if($last_week) {
          $data['last_week_sent'] = $last_week->total;
          $data['last_week_pass_pct'] = $last_week->pct_pass;
        }

        array_push($records,$data);

      }

    }

    $this->domains->update_domain_counts($records);

    log_message('info', 'Ending domain counts');

  }

  public function clear_cache() {
    $domains = $this->domains->get_domains();

    $this->output->delete_cache('/domains');

    foreach($domains as $domain) {
      $this->output->delete_cache('/domains/detail/'.$domain->domain_full);
      $this->output->delete_cache('/domains/chart/'.$domain->domain_full);
    }
  }

}
