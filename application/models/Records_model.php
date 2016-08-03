<?php

class Records_model extends CI_Model {

  function __construct() {
      parent::__construct();
  }

  function get_domains() {
    $this->db->order_by('domain_full');
    $query = $this->db->get('dmarc_domains');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function update_domain($domain, $spf_result, $dmarc_result) {
    $data = array(
      'last_checked'      => date('Y-m-d H:i:s', now()),
      'last_dmarc_result' => $dmarc_result,
      'last_spf_result'   => $spf_result,
    );
    $this->db->where('domain_full',$domain);
    $this->db->update('dmarc_domains', $data);
  }

  function create_record($domain, $text, $type) {
    $data = array(
      'domain_name'   => $domain,
      'record_text'   => $text,
      'record_type'   => $type,
      'record_date'   => date('Y-m-d H:i:s', now())
    );
    if($text === NULL) {
      $data['record_empty'] =  true;
    } else {
      $data['record_empty'] =  false;
    }
    $this->db->insert('dmarc_domain_records',$data);
  }
}
