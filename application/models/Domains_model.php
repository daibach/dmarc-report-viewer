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

  function get_domain_average_send($domain) {
    $this->db->select("ROUND(AVG(total),0) as avg_sent");
    $this->db->where('domain',$domain);
    $query = $this->db->get('dmarc_counts_weekly');
    if($query->num_rows() > 0) {
      return $query->row()->avg_sent;
    } else {
      return false;
    }
  }

  function get_domain_send_for_week($domain,$week) {
    $this->db->select('total, pct_pass');
    $this->db->where('domain',$domain);
    $this->db->where('week',$week);
    $this->db->where('year',date('Y'));
    $query = $this->db->get('dmarc_counts_weekly');
    if($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }

  function get_domain_dmarc_reports_total($domain) {
    $this->db->where('domain_name',$domain);
    $this->db->from('dmarc_records');
    return $this->db->count_all_results();
  }

  function update_domain_counts($records) {
    $this->db->update_batch('dmarc_domains', $records, 'domain_full');
  }

}
?>
