<?php
function http_dns_lookup($domain, $type) {
  $response = http_get_url("https://dns.google.com/resolve?name=".$domain."&type=".$type);
  if($response['http_status'] == 200 && $response['curl_error']==0) {
    $json = json_decode($response['content']);
    if(property_exists($json,'Answer')) {
      return $json->Answer[0]->data;
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
?>
