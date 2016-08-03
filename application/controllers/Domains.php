<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domains extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('domains_model','domains');
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
      'domain_info'   => $this->domains->get($domain),
      'dmarc_records' => $this->domains->get_dns_record($domain,'v=dmarc1'),
      'spf_records'   => $this->domains->get_dns_record($domain,'v=spf1'),
    );

    $this->load->view('templates/header');
    $this->load->view('domains/detail', $page_data);
    $this->load->view('templates/footer');
  }

}
