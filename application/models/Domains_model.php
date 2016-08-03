<?php

class Domains_model extends CI_Model {

  function __construct() {
      parent::__construct();
  }

  function get_domains() {
    $this->db->distinct();
    $this->db->group_by('domain_name');
    $this->db->order_by('domain_tld, domain_name, domain_full');
    $query = $this->db->get('dmarc_domains');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }

  function get($domain) {
    $this->db->where('domain_full',$domain);
    $query = $this->db->get('dmarc_domains');
    if($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }

  function get_dns_record($domain, $type) {
    $this->db->where('domain_name',$domain);
    $this->db->where('record_type',$type);
    $this->db->order_by('record_date','desc');
    $this->db->group_by('record_text');
    $query = $this->db->get('dmarc_domain_records');
    if($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
  }
}
?>
