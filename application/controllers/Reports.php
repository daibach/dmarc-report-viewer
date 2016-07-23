<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('dmarc_model','dmarc');
  }

  public function index() {

    $page_data = array(
      'report_domains' => $this->dmarc->get_report_domains(),
      'tlds'           => $this->dmarc->get_distinct_tlds(),
    );

    $this->load->view('templates/header');
    $this->load->view('report-list', $page_data);
    $this->load->view('templates/footer');

  }

  public function dmarc_aggregate_reports($domain="") {
    if($domain === "") { show_404(); }

    $page_data = array(
      'domain'  => $domain,
      'reports' => $this->dmarc->get_reports_for_domains($domain)
    );

    $this->load->view('templates/header');
    $this->load->view('aggregate-reports', $page_data);
    $this->load->view('templates/footer');
  }

  public function dmarc_aggregate_report($report_id=0) {
    if($report_id === 0) { show_404(); }

    $page_data = array(
      'report' => $this->dmarc->get_report($report_id),
      'records' => $this->dmarc->get_records_for_report($report_id)
    );

    $this->load->view('templates/header');
    $this->load->view('aggregate-report', $page_data);
    $this->load->view('templates/footer');
  }

  public function view_record($record_id=0) {
    if($record_id === 0) { show_404(); }

    $page_data = array(
      'record'  => $this->dmarc->get_record($record_id)
    );

    $this->load->view('templates/header');
    $this->load->view('record-detail', $page_data);
    $this->load->view('templates/footer');
  }

  public function domains($tld="") {
    if($tld === "") { show_404(); }

    $page_data = array(
      'tld'     => $tld,
      'domains' => $this->dmarc->get_distinct_domains_for_tld($tld)
    );

    $this->load->view('templates/header');
    $this->load->view('domain-list', $page_data);
    $this->load->view('templates/footer');
  }

  public function domain_report($domain="") {
    if($domain === "") { show_404(); }

    $page_data = array(
      'domain'    => $domain,
      'counts'    => $this->dmarc->get_counts($domain),
      'reports'   => $this->dmarc->get_reports_for_domain_name($domain),
      'records'   => $this->dmarc->get_records_for_domain($domain)
    );

    $this->load->view('templates/header');
    $this->load->view('domain-report', $page_data);
    $this->load->view('templates/footer');
  }
}
