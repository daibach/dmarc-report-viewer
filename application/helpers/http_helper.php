<?php
function http_dns_lookup($domain, $type, $return_first_answer=true) {
  $response = http_get_url("https://dns.google.com/resolve?name=".$domain."&type=".$type);
  if($response['http_status'] == 200 && $response['curl_error']==0) {
    $json = json_decode($response['content']);
    if(property_exists($json,'Answer')) {
      if($return_first_answer) {
        return $json->Answer[0]->data;
      } else {
        return $json->Answer;
      }
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function http_get_url($url) {

  $useragent = "something v1.1";

  $ch = curl_init();
  curl_setopt_array($ch, array(CURLOPT_HEADER => FALSE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FAILONERROR => TRUE,
    CURLOPT_COOKIESESSION => TRUE,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_COOKIEJAR => "/dev/null",
    CURLOPT_CONNECTTIMEOUT => 14,
    CURLOPT_TIMEOUT => 21,
    CURLOPT_POST => FALSE,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_URL => $url));


  $content = curl_exec($ch);
  $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curl_error = curl_errno($ch);

  curl_close($ch);

  if($curl_error == 28) {
    $http_status = 598;
  }

  return array(
    'content'     => $content,
    'http_status' => $http_status,
    'curl_error'  => $curl_error
  );
}

function http_get_domain_info($domain) {

  $domain_parts = array_reverse(explode('.',$domain));
  $count = count($domain_parts);
  $domain_details = array(
    'tld'     => '',
    'domain'  => '',
    'sub'     => ''
  );

  if((substr_count($domain, '.service.gov.uk') > 0)
      || (substr_count($domain, '.direct.gov.uk') > 0)
      || (substr_count($domain, '.businesslink.gov.uk') > 0) ) {
    //this is a service.gov.uk domain
    $domain_details['tld'] = $domain_parts[2].'.gov.uk';
    $domain_details['domain'] = $domain_parts[3];

    if($count > 4) {
      $domain_details['sub'] = str_replace('.'.$domain_parts[3].'.'.
        $domain_parts[2].'.gov.uk', '', $domain);
    }
  } elseif(substr_count($domain, '.gov.uk') > 0) {
    //this is a gov.uk subdomain
    $domain_details['tld'] = 'gov.uk';
    $domain_details['domain'] = $domain_parts[2];

    if($count > 3) {
      $domain_details['sub'] = str_replace('.'.$domain_parts[2].'.gov.uk', '',
        $domain);
    }
  } elseif(substr_count($domain, '.uk') > 0) {
    //this is a .uk subdomain
    switch($domain_parts[1]) {
      case 'ac':
      case 'co':
      case 'gov':
      case 'judicary':
      case 'ltd':
      case 'me':
      case 'mod':
      case 'net':
      case 'nhs':
      case 'nic':
      case 'org':
      case 'parliament':
      case 'plc':
      case 'police':
      case 'sch':
        $domain_details['tld'] = $domain_parts[1].'.uk';
        $domain_details['domain'] = $domain_parts[2];
        if($count > 3) {
          $domain_details['sub'] = str_replace('.'.$domain_parts[2].'.'.
            $domain_parts[1].'.uk', '', $domain);
        }
        break;
      default:
        $domain_details['tld'] = 'uk';
        $domain_details['domain'] = $domain_parts[1];
        if($count > 2) {
          $domain_details['sub'] = str_replace('.'.$domain_parts[1].'.uk', '',
            $domain);
        }
    }

  } else {
    //this is some other domain
    $domain_details['tld'] = $domain_parts[0];
    $domain_details['domain'] = $domain_parts[1];
    if($count > 2) {
      $domain_details['sub'] = str_replace('.'.$domain_parts[1].'.'.
        $domain_parts[0], '', $domain);
    }
  }
  $domain_details['domain'] = $domain_details['domain'].'.'.
    $domain_details['tld'];
  return $domain_details;
}
?>
