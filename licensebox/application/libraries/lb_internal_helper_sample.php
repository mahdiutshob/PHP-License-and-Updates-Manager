<?php if(count(get_included_files()) ==1) exit("No direct script access allowed");
ini_set('max_execution_time', 0);
ini_set('memory_limit', '268435456');

define("LB_API_DEBUG", false);

if(!LB_API_DEBUG){
ini_set('display_errors', 0);
}

class LicenseBoxAPI {

  private $api_url;

  public function __construct()
  { 
    $this->api_url = '{[URL]}';
    $this->api_key = '{[KEY]}';
  }

  private function callAPI($method, $url, $data)
  {
    $curl = curl_init();
    switch ($method){
      case "POST":
      curl_setopt($curl, CURLOPT_POST, 1);
      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                         
        break;
      default:
      if($data)
        $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
    $this_ip = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'LB-API-KEY: '.$this->api_key, 'LB-URL: '.$this_url, 'LB-IP: '.$this_ip));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if(!$result&&!LB_API_DEBUG){
      $rs = array('status' => FALSE, 'message' => 'Connection to server failed or the server returned an error, please contact support.');
      return json_encode($rs);
    }
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if(!LB_API_DEBUG){
      if($http_status!=200){
        $rs = array('status' => FALSE, 'message' => 'Server returned an invalid response, please contact support.');
        return json_encode($rs);
      }
    }
    curl_close($curl);
    return $result;
  }

  public function check_connection(){
    $data_array =  array();
    $get_data = $this->callAPI('POST',$this->api_url.'api/check_connection_int', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function add_product($product_name, $product_id = null){
    $data_array =  array(
     "product_id"  => $product_id,
     "product_name"  => $product_name
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/add_product', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function create_license($product_id, $data = null, $license_code = null){
    $data_array =  array(
    "product_id"  => $product_id,
    "license_code"  => $license_code,
    "license_type" => (!empty($data['license_type']))?$data['license_type']:null,
    "invoice_number" => (!empty($data['invoice_number']))?$data['invoice_number']:null,
    "client_name" => (!empty($data['client_name']))?$data['client_name']:null,
    "client_email" => (!empty($data['client_email']))?$data['client_email']:null,
    "comments" => (!empty($data['comments']))?$data['comments']:null,
    "licensed_ips" => (!empty($data['licensed_ips']))?$data['licensed_ips']:null,
    "licensed_domains" => (!empty($data['licensed_domains']))?$data['licensed_domains']:null,
    "support_end_date" => (!empty($data['support_end_date']))?$data['support_end_date']:null,
    "updates_end_date" => (!empty($data['updates_end_date']))?$data['updates_end_date']:null,
    "expiry_date" => (!empty($data['expiry_date']))?$data['expiry_date']:null,
    "license_uses" => (!empty($data['license_uses']))?$data['license_uses']:null,
    "license_parallel_uses" => (!empty($data['license_parallel_uses']))?$data['license_parallel_uses']:null
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/create_license', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function get_product($product_id){
    $data_array =  array(
    "product_id"  => $product_id
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/get_product', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function get_license($license_code){
    $data_array =  array(
    "license_code"  => $license_code
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/get_license', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function mark_product_active($product_id){
    $data_array =  array(
    "product_id"  => $product_id
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/mark_product_active', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function mark_product_inactive($product_id){
    $data_array =  array(
    "product_id"  => $product_id
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/mark_product_inactive', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function block_license($license_code){
    $data_array =  array(
    "license_code"  => $license_code
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/block_license', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function unblock_license($license_code){
    $data_array =  array(
    "license_code"  => $license_code
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/unblock_license', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

}
?>