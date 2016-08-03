<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domains extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('domains_model','domains');
    $this->load->model('dmarc_model','dmarc');
    $this->load->helper('ui_helper');
  }

  public function index() {

    $page_data = array(
      'domains' => $this->domains->get_domains()
    );

    $this->load->view('templates/header');
    $this->load->view('domains/list', $page_data);
    $this->load->view('templates/footer');

  }

  public function detail($domain="") {
    if($domain==="") { show_404(); }

    $page_data = array(
      'domain_info'       => $this->domains->get($domain),
      'dmarc_dns_records' => $this->domains->get_dns_record($domain,'v=dmarc1'),
      'spf_dns_records'   => $this->domains->get_dns_record($domain,'v=spf1'),
      'email_counts'      => $this->dmarc->get_counts($domain),
      'dmarc_reports'     => $this->dmarc->get_reports_for_domain_name($domain),
      'dmarc_records'     => $this->dmarc->get_records_for_domain($domain)
    );

    $this->load->view('templates/header');
    $this->load->view('domains/detail', $page_data);
    $this->load->view('templates/footer');
  }

  public function chart($domain="") {
    if($domain==="") { show_404(); }

    $page_data = array(
      'email_counts'      => $this->dmarc->get_counts($domain),
    );

    $this->load->view('domains/chart', $page_data);
    $this->output->set_content_type('text/javascript');
  }

}
