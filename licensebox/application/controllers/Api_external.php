<?php defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/libraries/REST_Controller.php');
class Api_external extends REST_Controller
{
  private $client_ip;
  private $client_url;
  private $client_agent;

  public function __construct()
  {     
    parent::__construct();
    $_url = 'HTTP_' . strtoupper(str_replace('-', '_', 'LB-URL'));
    $has_url = $this->input->server($_url);
    $_ip = 'HTTP_' . strtoupper(str_replace('-', '_', 'LB-IP'));
    $has_ip = $this->input->server($_ip);
    $_agent = 'HTTP_' . strtoupper(str_replace('-', '_', 'User-Agent'));
    $has_agent = $this->input->server($_agent);

    if(!filter_var($has_url, FILTER_VALIDATE_URL)||!filter_var($has_ip, FILTER_VALIDATE_IP)){
      $this->response([
          'status' => false,
          'error' => "Required request headers are invalid or missing, please recheck."
        ], 403);
    }

    $this->client_ip = $has_ip;
    $this->client_url = $has_url;
    $this->client_agent = !empty($has_agent)?$has_agent:null;

    $has_domain = parse_url($has_url, PHP_URL_HOST);

    $api_rate_limit = $this->user_model->get_config_from_db('api_rate_limit');
    if($api_rate_limit){
      $this->methods['check_connection_ext_post']['limit'] = $api_rate_limit;
      $this->methods['latest_version_post']['limit'] = $api_rate_limit;
      $this->methods['check_update_post']['limit'] = $api_rate_limit;
      $this->methods['get_update_size_head']['limit'] = $api_rate_limit;
      $this->methods['download_update_post']['limit'] = $api_rate_limit;
      $this->methods['activate_license_post']['limit'] = $api_rate_limit;
      $this->methods['verify_license_post']['limit'] = $api_rate_limit;
      $this->methods['check_license_post']['limit'] = $api_rate_limit;
      $this->methods['deactivate_license_post']['limit'] = $api_rate_limit;
    }

    if(!empty($this->user_model->get_config_from_db('blacklisted_ips'))){
      $blacklisted_ips = explode(',', $this->user_model->get_config_from_db('blacklisted_ips'));
      if(in_array($has_ip, $blacklisted_ips)) {
        $this->response([
          'status' => false,
          'error' => "Your IP is blacklisted."
        ], 401);
      }
    }

    if(!empty($this->user_model->get_config_from_db('blacklisted_domains'))){
      $blacklisted_domains = explode(',', $this->user_model->get_config_from_db('blacklisted_domains'));
      if(in_array($has_domain, $blacklisted_domains)) {
        $this->response([
          'status' => false,
          'error' => "Your Domain is blacklisted."
        ], 401);
      }
    }
  }

  public function error_post()
  {
    $this->response([
      'status' => false,
      'error' => "Method not allowed or is invalid."
    ], 403);   
  }

  public function check_connection_ext_post()
  {
      $this->response([
        'status' => true,
        'message' => 'Connection successful.'
      ], 200);
  }

  public function latest_version_post()
  {
    $product_id = strip_tags(trim($this->post('product_id')));
    if(!empty($product_id)){
      $row = $this->db->get_where('product_details', array('pd_pid' => $product_id, 'pd_status' => 1))->row_array();
      if(!empty($row)){
        $latest_version = $this->products_model->get_latest_version($product_id);
        if(!empty($latest_version)){
          $this->response([
            'status' => true,
            'message' => 'Latest version of '.$row['pd_name'].' is '.$latest_version[0]['version'].'',
            'product_name' => $row['pd_name'],
            'latest_version' => $latest_version[0]['version'],
            'release_date' => $latest_version[0]['release_date']
          ],200);}
        else{
          $this->response([
            'status' => false,
            'message' => ''.$row['pd_name'].' has no versions yet.',
            'product_name' => $row['pd_name'],
            'latest_version' => null,
            'release_date' => null
          ],200);
        }
      }
      else{
        $this->response([
          'status' => false,
          'message' => 'Product ID specified is invalid or the product is inactive.'
        ],200);
      }
    }else{
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.'
      ],400);
    }
  }

  public function check_update_post()
  {
  if(!empty($this->post('product_id'))&&!empty($this->post('current_version'))){
    $product_id = strip_tags(trim($this->post('product_id')));
    $current_version = strip_tags(trim($this->post('current_version')));
    $product = $this->products_model->get_product($product_id);
    $product_active = $this->products_model->check_product_status($product_id);
    $versions = $this->products_model->get_version($product_id);
    if(!$product or !$product_active){
      $this->response([
        'status' => false,
        'message'=> 'Product not found or is inactive, please contact support!',
        'version' => null,
        'changelog'=> null,
        'update_id' => null,
        'has_sql' => null
      ],200);}
    if(empty($versions)){
      $this->response([
        'status' => false,
        'message'=> ''.$product['pd_name'].' has no versions yet.',
        'version' => null,
        'changelog'=> null,
        'update_id' => null,
        'has_sql' => null
      ],200);
    }
      if($versions){
        $latest_version = $this->products_model->get_oldest_version($product_id,$current_version);
        if($latest_version[0]['sql_file']!=null){
          $has_sql = true;
        }
        else{
          $has_sql = false;
        }
      }
      if(version_compare($latest_version[0]['version'],  $current_version)>0)
      {
        $this->response([
          'status' => true,
          'message'=> 'New version ('.$latest_version[0]['version'].') available for '.$product['pd_name'].'!',
          'version' => $latest_version[0]['version'],
          'changelog'=> $latest_version[0]['changelog'],
          'update_id' => $latest_version[0]['vid'],
          'has_sql' => $has_sql
        ],200);
      }
      else
      {
        $this->response([
          'status' => false,
          'message'=> 'You have the latest version of '.$product['pd_name'].'.',
          'version' => null,
          'changelog'=> null,
          'update_id' => null,
          'has_sql' => null
        ],200);
      }
    }else
    {
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.',
        'version' => null,
        'changelog' => null,
        'update_id' => null,
        'has_sql' => null
      ],400);
    }
  }

  public function get_update_size_head(){
    if(!empty($this->get('type'))&&!empty($this->get('vid')))
      { $type = strip_tags(trim($this->get('type')));
        $vid = strip_tags(trim($this->get('vid')));
        $version = $this->products_model->get_version_by_vid($vid);
        $product = $this->products_model->get_product($version['pid']);
        $product_active = $this->products_model->check_product_status($version['pid']);
        if(($type=='main')&&$version&&$product&&$product_active){
          $path_to_zip = './version-files/'.$version['main_file'];
          if (!file_exists($path_to_zip)) {
            exit;
          }
          $filename="main_".clr($product['pd_name'])."_".clr($version['version']).".zip";
          header("Content-Type: application/zip");
          header("Content-Transfer-Encoding: Binary");
          header("Content-Length:".filesize($path_to_zip));
          header("Content-Disposition: attachment; filename=".$filename);
          readfile($path_to_zip);
          exit;
        }elseif(($type=='sql')&&$version&&$product&&$product_active){
          $path_to_zip = './version-files/'.$version['sql_file'];
          if (!file_exists($path_to_zip)) {
            exit;
          }
          $filename="sql_".clr($product['pd_name'])."_".clr($version['version']).".sql";
          header("Content-Type: application/sql");
          header("Content-Transfer-Encoding: Binary");
          header("Content-Length:".filesize($path_to_zip));
          header("Content-Disposition: attachment; filename=".$filename);
          readfile($path_to_zip);
          exit;
        }else
        {
          exit;
        }
    }else{
      exit;
    }
  }

  public function download_update_post(){
    if(!empty($this->get('type'))&&!empty($this->get('vid'))){
      $type = strip_tags(trim($this->get('type')));
      $vid = strip_tags(trim($this->get('vid')));
      $url = $this->client_url;
      $ip = $this->client_ip;
      $did = substr(str_shuffle(MD5(microtime())), 0, 15);
      $version = $this->products_model->get_version_by_vid($vid);
      if (!$version) {
        $this->response(null,404);
      }
      $path_to_update_file = './version-files/'.$version['main_file'];
      if (!file_exists($path_to_update_file)) {
        $this->response(null,404);
      }
      $product = $this->products_model->get_product($version['pid']);
      if($product['license_update']==1){
        if(!empty($this->post('license_file'))){
          $res_lic = $this->license_check($version['pid'],null,null,$this->post('license_file'),true);
          if($res_lic){
            $passed_license_check=true;
          }else{
            $passed_license_check=false;
          }
        }elseif(!empty($this->post('license_code'))&&!empty($this->post('client_name'))){
          $res_lic = $this->license_check($version['pid'],$this->post('license_code'),$this->post('client_name'),null,true);
          if($res_lic){
            $passed_license_check=true;
          }else{
            $passed_license_check=false;
          }
        }else{
          $this->response(null,401);
        }
      }else{
        $passed_license_check=true;
      }
      if($passed_license_check){
        $product_active = $this->products_model->check_product_status($version['pid']);
         if(($type=='main')&&$version&&$product&&$product_active){
        $this->user_model->add_log('Product <b>'.$product['pd_name'].'</b> '.$version['version'].' update was downloaded from URL <a href="'.$url.'">'.remove_http_www($url).'</a>.');
        $data = array(
        'did' => $did,
        'product' => $version['pid'],
        'vid' => $vid,
        'url' => $url,
        'ip' => $ip,
        'isvalid' => 1
        );
        $this->db->insert('update_downloads', $data);
        $path_to_zip = './version-files/'.$version['main_file'];
        $filename="main_".clr($product['pd_name'])."_".clr($version['version']).".zip";
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($path_to_zip));
        header("Content-Disposition: attachment; filename=".$filename);
        readfile($path_to_zip);
        exit;
      }elseif(($type=='sql')&&$version&&$product&&$product_active){
        if(empty($version['sql_file'])){
          $this->response(null,404);
        }
        $path_to_zip = './version-files/'.$version['sql_file'];
        if (!file_exists($path_to_zip)) {
          $this->response(null,404);
        }
        $filename="sql_".clr($product['pd_name'])."_".clr($version['version']).".sql";
        header("Content-Type: application/sql");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($path_to_zip));
        header("Content-Disposition: attachment; filename=".$filename);
        readfile($path_to_zip);
        exit;
      }else
      {
        $this->response(null,404);
      }
      }else{
        $this->user_model->add_log('Product <b>'.$product['pd_name'].'</b> '.$version['version'].' update download attempt from URL <a href="'.$url.'">'.remove_http_www($url).'</a> was blocked as provided license was found to be invalid.');
        if($this->user_model->get_config_from_db('failed_update_download_logs')==1){
        $data = array(
        'did' => $did,
        'product' => $version['pid'],
        'vid' => $vid,
        'url' => $url,
        'ip' => $ip,
        'isvalid' => 0
        );
        $this->db->insert('update_downloads', $data);}
        $this->response(null, 401);
      }
    }else{
      $this->response(null, 404);
    }
  }

  public function activate_license_post()
  { $this->load->library('encryption');
    $this->load->helper('api_helper');
  if(!empty($this->post('product_id'))&&!empty($this->post('license_code'))&&!empty($this->post('verify_type'))){
    $iid = substr(str_shuffle(MD5(microtime())), 0, 15);
    $url = strip_tags(trim($this->client_url));
    $ip = strip_tags(trim($this->client_ip));
    $today = date('Y-m-d H:i:s');
    $domain = parse_url($url, PHP_URL_HOST);
    $agent = strip_tags(trim($this->client_agent));
    $username = strip_tags(trim($this->post('client_name')));
    $product_id = strip_tags(trim($this->post('product_id')));
    $license_code = strip_tags(trim($this->post('license_code')));
    $product_active = $this->products_model->check_product_status($product_id);
    $lic_config = array('license' => $license_code, 'client' => $username);
    $lic_response = $this->encryption->encrypt(json_encode($lic_config));
    if(!$product_active){
      $this->response([
        'status' => false,
        'message' => 'Product ID specified is invalid or the product is inactive.',
        'data' => null,
        'lic_response' => null
      ],200);
    }
    $type = strip_tags(trim($this->post('verify_type')));
    if(($type == 'envato')&&empty($this->licenses_model->get_license($license_code)))
    {
      $product = $this->products_model->get_product($product_id);
      $has_envato_id = $product['envato_id'];
      $response = verify_envato_purchase_code($license_code,$this->user_model->get_config_from_db('envato_api_token'));
      if(isset($response['buyer'])&&(strtolower($response['buyer'])==strtolower($username))) {
        if(!empty($has_envato_id)&&$response['item']['id']!=$has_envato_id){
          $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using Envato purchase code <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided purchase code is of a different envato product.');
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){  
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
        $this->response([
          'status' => false,
          'message' => 'Envato purchase code is invalid or is not of the specified product, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
        }
        if(!empty($response['supported_until'])){
          $supported_datetime = date('Y-m-d H:i:s', strtotime($response['supported_until']));
        }else{
          $supported_datetime = null;
        }
        $data1 = array(
          'pid' => $product_id,
          'license_code' => $license_code,
          'license_type' => $response['license'],
          'is_envato' => 1,
          'client' => $username,
          'supported_till' => $supported_datetime,
          'uses' => null,
          'uses_left' => null,
          'parallel_uses' => null,
          'parallel_uses_left' => null,
          'validity' => 1
        );
        $this->db->insert('product_licenses', $data1);
        $this->user_model->add_log('Envato purchase code <b><i>'.$license_code.'</i></b> added as new license for product '.$product['pd_name'].'.');  
        $this->user_model->add_log('Envato purchase code <b>'.$license_code.'</b> was used by client <b>'.$username.'</b> for activating product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>.');
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 1,
          'pi_isactive' => 1
        );
        $this->db->insert('product_installations', $data);
        $extra_trans = array("{[license_type]}" => $response['license'], "{[support_expiry]}" => $supported_datetime);       
        $extra_response_field = strtr($this->config->item('extra_field_response'), $extra_trans);      
        if(empty($extra_response_field)){
          $extra_response_field = null;
        }  
        $this->response([
          'status' => true,
          'message' => 'Activated! Thanks for purchasing '.$product['pd_name'].'.',
          'data' => $extra_response_field,
          'lic_response' => $lic_response
        ],200);
      }else{
        $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using Envato purchase code <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided purchase code is incorrect.');
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
        $this->response([
          'status' => false,
          'message' => 'Envato purchase code is invalid, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
    }
    $product = $this->products_model->get_product($product_id);
    $license = $this->licenses_model->get_license($license_code);
      if(!empty($license['domains'])){
      $valid_domains = explode(',',$license['domains']);
      if (!in_array($domain, $valid_domains)) {
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
      $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided license is not licensed for that domain.');
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your domain, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
      }

      if(!empty($license['ips'])){
      $valid_ips = explode(',',$license['ips']);
      if (!in_array($ip, $valid_ips)) {
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
       $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided license is not licensed for that IP address.');
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your ip, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
       }

      if(!empty($license['expiry'])){
      $expiry_date = $license['expiry'];
      if ($today>=$expiry_date) {
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
       $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided license has expired.');
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license has expired, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
       }

    if(!empty($license['id'])&&!empty($product['pd_id'])&&($license['pid']==$product['pd_pid'])){
      if($license['client']!=null&&(strtolower($license['client'])!=strtolower($username))){
        $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided license is not assigned to this client.');
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
        $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license is not assigned to you, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
      if($license['validity']==0){
        $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, as provided license is blocked.');
        if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
        $this->response([
          'status' => false,
          'message' => 'License is blocked, please contact support!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
      $current_activations = $this->installations_model->get_activation_by_license($license['license_code']);
      $licenses_left0 = $license['uses']-$current_activations;
      if(($licenses_left0>0)||($license['uses']==null)){
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, due to overuse of license limit.');
          if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
          $data = array(
            'pi_product' => $product_id,
            'pi_iid' => $iid,
            'pi_username' => $username,
            'pi_purchase_code' => $license_code,
            'pi_url' => $url,
            'pi_ip' => $ip,
            'pi_agent' => $agent,
            'pi_isvalid' => 0,
            'pi_isactive' => 0
          );
          $this->db->insert('product_installations', $data);}
          $this->response([
            'status' => false,
            'message' => 'License use limit reached, please contact support!',
            'data' => null,
            'lic_response' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }
        $current_activations_active = $this->installations_model->get_activation_by_license_active($license['license_code']);
        $licenses_left00 = $license['parallel_uses']-$current_activations_active;
      if(($licenses_left00>0)||($license['parallel_uses']==null)){
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, due to overuse of parallel license use limit.');
          if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
          $data = array(
            'pi_product' => $product_id,
            'pi_iid' => $iid,
            'pi_username' => $username,
            'pi_purchase_code' => $license_code,
            'pi_url' => $url,
            'pi_ip' => $ip,
            'pi_agent' => $agent,
            'pi_isvalid' => 0,
            'pi_isactive' => 0
          );
          $this->db->insert('product_installations', $data);}
          $this->response([
            'status' => false,
            'message' => 'License is already active on maximum allowed product instances, please deactivate this license from active domain(s) and try again!',
            'data' => null,
            'lic_response' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => $is_valid,
          'pi_isactive' => $is_valid
        );
        $this->db->insert('product_installations', $data);
        $extra_trans = array("{[license_type]}" => (!empty($license['license_type'])? $license['license_type'] : null), "{[support_expiry]}" => (!empty($license['supported_till'])? $license['supported_till'] : null), "{[client_email]}" => (!empty($license['email'])? $license['email'] : null), "{[license_expiry]}" => (!empty($license['expiry'])? $license['expiry'] : null), "{[updates_expiry]}" => (!empty($license['updates_till'])? $license['updates_till'] : null));       
        $extra_response_field = strtr($this->config->item('extra_field_response'), $extra_trans);
        if(empty($extra_response_field)){
          $extra_response_field = null;
        }
        $this->user_model->add_log('License <b>'.$license_code.'</b> was used by client <b>'.$username.'</b> for activating product <b>'.$product['pd_name'].'</b> on URL <a href="'.$url.'">'.remove_http_www($url).'</a>. ');
        $this->response([
          'status' => true,
          'message' => 'Activated! Thanks for purchasing '.$product['pd_name'].'.',
          'data' => $extra_response_field,
          'lic_response' => $lic_response
        ],200);
      }
      else
      { if($this->user_model->get_config_from_db('failed_activation_logs')==1){ 
        $data = array(
          'pi_product' => $product_id,
          'pi_iid' => $iid,
          'pi_username' => $username,
          'pi_purchase_code' => $license_code,
          'pi_url' => $url,
          'pi_ip' => $ip,
          'pi_agent' => $agent,
          'pi_isvalid' => 0,
          'pi_isactive' => 0
        );
        $this->db->insert('product_installations', $data);}
        $this->user_model->add_log('Activation attempt blocked for client <b>'.$username.'</b> using license <b>'.$license_code.'</b> for product '.$product['pd_name'].' on URL <a href="'.$url.'">'.remove_http_www($url).'</a>, due to incorrect license or product id specified.');
        $this->response([
          'status' => false,
          'message' => 'License code specified is incorrect or is of a different product, please recheck!',
          'data' => null,
          'lic_response' => null
        ],200);
      }
    }else{
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.',
        'data' => null,
        'lic_response' => null
      ],400);
    }
  }


  public function verify_license_post()
  { $this->load->helper('api_helper');
    $this->load->library('encryption');
  if(!empty($this->post('product_id'))){
    if(empty($this->post('license_code'))&&!empty($this->post('license_file'))){
      $license_dec = $this->encryption->decrypt($this->post('license_file'));
      $license_json = json_decode($license_dec, true);
      $license_code = $license_json['license'];
      $username = $license_json['client'];
    }
    else{
      $username = strip_tags(trim($this->post('client_name')));
      $license_code = strip_tags(trim($this->post('license_code')));
    }
    $product_id = strip_tags(trim($this->post('product_id')));
    $ip = strip_tags(trim($this->client_ip));
    $url = strip_tags(trim($this->client_url));
    $today = date('Y-m-d H:i:s');
    $domain = parse_url($url, PHP_URL_HOST);
    $product = $this->products_model->get_product($product_id);
    $license = $this->licenses_model->get_license($license_code);
    $product_active = $this->products_model->check_product_status($product_id);
    if(!empty($license['id'])&&!empty($product['pd_id'])&&$product_active&&($license['pid']==$product['pd_pid'])){
      if($license['client']!=null&&(strtolower($license['client'])!=strtolower($username))){
        $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license is not assigned to you, please contact support!',
          'data' => null
        ],200);
      }
      if($license['validity']==0){
        $this->response([
          'status' => false,
          'message' => 'License is blocked, please contact support!',
          'data' => null
        ],200);
      }
      if(!empty($license['domains'])){
      $valid_domains = explode(',',$license['domains']);
      if (!in_array($domain, $valid_domains)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your domain, please contact support!',
          'data' => null
        ],200);
      }
      }
         if(!empty($license['ips'])){
      $valid_ips = explode(',',$license['ips']);
      if (!in_array($ip, $valid_ips)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your ip, please contact support!',
          'data' => null
        ],200);
      }
       }

      if(!empty($license['expiry'])){
      $expiry_date = $license['expiry'];
      if ($today>=$expiry_date) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license has expired, please contact support!',
          'data' => null
        ],200);
      }
       }

      $this_activation = $this->installations_model->get_current_activation($license['license_code'],$username, $domain);
      if(empty($this_activation)){
        $this->response([
            'status' => false,
            'message' => 'Activation not recognized or is inactive, please contact support!',
            'data' => null
          ],200);
      }

      $current_activations = $this->installations_model->get_activation_by_license($license['license_code']);

      $licenses_left0 = $license['uses']-$current_activations;
      if(($licenses_left0>=0)||($license['uses']==null)){
        $uses_left = $license['uses'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->response([
            'status' => false,
            'message' => 'License use limit reached, please contact support!',
            'data' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }

      $current_activations_active = $this->installations_model->get_activation_by_license_active($license['license_code']);

      $licenses_left00 = $license['parallel_uses']-$current_activations_active;
      if(($licenses_left00>=0)||($license['parallel_uses']==null)){
        $uses_left = $license['parallel_uses'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->response([
            'status' => false,
            'message' => 'License is already active on maximum allowed product instances, please deactivate this license from active domain(s) and try again!',
            'data' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }
        $extra_trans = array("{[license_type]}" => (!empty($license['license_type'])? $license['license_type'] : null), "{[support_expiry]}" => (!empty($license['supported_till'])? $license['supported_till'] : null), "{[client_email]}" => (!empty($license['email'])? $license['email'] : null), "{[license_expiry]}" => (!empty($license['expiry'])? $license['expiry'] : null), "{[updates_expiry]}" => (!empty($license['updates_till'])? $license['updates_till'] : null));       
        $extra_response_field = strtr($this->config->item('extra_field_response'), $extra_trans);
        if(empty($extra_response_field)){
          $extra_response_field = null;
        }
        $this->response([
          'status' => true,
          'message' => 'Verified! Thanks for purchasing '.$product['pd_name'].'.',
          'data' => $extra_response_field
        ],200);
      }
      else
      {
        $this->response([
          'status' => false,
          'message' => 'License code specified is incorrect, please recheck!',
          'data' => null
        ],200);
      }
    }else{
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.',
        'data' => null
      ],400);
    }
  }

  public function check_license_post()
  { $this->load->helper('api_helper');
    $this->load->library('encryption');
  if(!empty($this->post('product_id'))){
    if(empty($this->post('license_code'))&&!empty($this->post('license_file'))){
      $license_dec = $this->encryption->decrypt($this->post('license_file'));
      $license_json = json_decode($license_dec, true);
      $license_code = $license_json['license'];
      $username = $license_json['client'];
    }
    else{
      $username = strip_tags(trim($this->post('client_name')));
      $license_code = strip_tags(trim($this->post('license_code')));
    }
    $product_id = strip_tags(trim($this->post('product_id')));
    $ip = strip_tags(trim($this->client_ip));
    $url = strip_tags(trim($this->client_url));
    $today = date('Y-m-d H:i:s');
    $domain = parse_url($url, PHP_URL_HOST);
    $product = $this->products_model->get_product($product_id);
    $license = $this->licenses_model->get_license($license_code);
    $product_active = $this->products_model->check_product_status($product_id);
    if(!empty($license['id'])&&!empty($product['pd_id'])&&$product_active&&($license['pid']==$product['pd_pid'])){
      if($license['client']!=null&&(strtolower($license['client'])!=strtolower($username))){
        $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license is not assigned to you, please contact support!',
          'data' => null
        ],200);
      }
      if($license['validity']==0){
        $this->response([
          'status' => false,
          'message' => 'License is blocked, please contact support!',
          'data' => null
        ],200);
      }
      if(!empty($license['domains'])){
      $valid_domains = explode(',',$license['domains']);
      if (!in_array($domain, $valid_domains)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your domain, please contact support!',
          'data' => null
        ],200);
      }
      }
         if(!empty($license['ips'])){
      $valid_ips = explode(',',$license['ips']);
      if (!in_array($ip, $valid_ips)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your ip, please contact support!',
          'data' => null
        ],200);
      }
       }

      if(!empty($license['expiry'])){
      $expiry_date = $license['expiry'];
      if ($today>=$expiry_date) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license has expired, please contact support!',
          'data' => null
        ],200);
      }
       }

      $current_activations = $this->installations_model->get_activation_by_license($license['license_code']);

      $licenses_left0 = $license['uses']-$current_activations;
      if(($licenses_left0>=0)||($license['uses']==null)){
        $uses_left = $license['uses'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->response([
            'status' => false,
            'message' => 'License use limit reached, please contact support!',
            'data' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }

      $current_activations_active = $this->installations_model->get_activation_by_license_active($license['license_code']);

      $licenses_left00 = $license['parallel_uses']-$current_activations_active;
      if(($licenses_left00>=0)||($license['parallel_uses']==null)){
        $uses_left = $license['parallel_uses'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          $this->response([
            'status' => false,
            'message' => 'License is already active on maximum allowed product instances, please deactivate this license from active domain(s) and try again!',
            'data' => null
          ],200);
          $uses_left = 0;
          $is_valid = 0;
        }
        $extra_trans = array("{[license_type]}" => (!empty($license['license_type'])? $license['license_type'] : null), "{[support_expiry]}" => (!empty($license['supported_till'])? $license['supported_till'] : null), "{[client_email]}" => (!empty($license['email'])? $license['email'] : null), "{[license_expiry]}" => (!empty($license['expiry'])? $license['expiry'] : null), "{[updates_expiry]}" => (!empty($license['updates_till'])? $license['updates_till'] : null));       
        $extra_response_field = strtr($this->config->item('extra_field_response'), $extra_trans);
        if(empty($extra_response_field)){
          $extra_response_field = null;
        }
        $this->response([
          'status' => true,
          'message' => 'Verified! Thanks for purchasing '.$product['pd_name'].'.',
          'data' => $extra_response_field
        ],200);
      }
      else
      {
        $this->response([
          'status' => false,
          'message' => 'License code specified is incorrect, please recheck!',
          'data' => null
        ],200);
      }
    }else{
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.',
        'data' => null
      ],400);
    }
  }

  public function deactivate_license_post()
  { $this->load->library('encryption');
  if(!empty($this->post('product_id'))){
    if(empty($this->post('license_code'))&&!empty($this->post('license_file'))){
      $license_dec = $this->encryption->decrypt($this->post('license_file'));
      $license_json = json_decode($license_dec, true);
      $license_code = $license_json['license'];
      $username = $license_json['client'];
    }
    else{
      $username = strip_tags(trim($this->post('client_name')));
      $license_code = strip_tags(trim($this->post('license_code')));
    }
    $product_id = strip_tags(trim($this->post('product_id')));
    $ip = strip_tags(trim($this->client_ip));
    $url = strip_tags(trim($this->client_url));
    $today = date('Y-m-d H:i:s');
    $domain = parse_url($url, PHP_URL_HOST);
    $product = $this->products_model->get_product($product_id);
    $license = $this->licenses_model->get_license($license_code);
    $product_active = $this->products_model->check_product_status($product_id);
    if(!empty($license['id'])&&!empty($product['pd_id'])&&$product_active&&($license['pid']==$product['pd_pid'])){
      if($license['client']!=null&&(strtolower($license['client'])!=strtolower($username))){
        $this->response([
          'status' => false,
          'message' => 'License is incorrect or this license is not assigned to you, please contact support!'
        ],200);
      }
      if($license['validity']==0){
        $this->response([
          'status' => false,
          'message' => 'License is blocked, please contact support!'
        ],200);
      }
      if(!empty($license['domains'])){
      $valid_domains = explode(',',$license['domains']);
      if (!in_array($domain, $valid_domains)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your domain, please contact support!'
        ],200);
      }
      }
      if(!empty($license['ips'])){
      $valid_ips = explode(',',$license['ips']);
      if (!in_array($ip, $valid_ips)) {
      $this->response([
          'status' => false,
          'message' => 'License is incorrect or it is not licensed for your ip, please contact support!'
        ],200);
      }
       }

      $this_activation = $this->installations_model->get_current_activation($license['license_code'],$username, $domain);
      if(empty($this_activation)){
        $this->response([
            'status' => false,
            'message' => 'Activation not recognized or is inactive, please contact support!'
          ],200);
      }

      $deactivate_activation = $this->installations_model->deactivate_current_activation($license['license_code'],$username, $domain);
      if(!$deactivate_activation){
        $this->response([
            'status' => false,
            'message' => 'License was not deactivated, please contact support!'
          ],200);
      }
        $this->user_model->add_log('Client <b>'.$username.'</b> deactivated license <b>'.$license['license_code'].'</b> from their activation on URL <a href="'.$url.'">'.remove_http_www($url).'</a>.');
        $this->response([
          'status' => true,
          'message' => 'License was deactivated successfully.'
        ],200);
      }
      else
      {
        $this->response([
          'status' => false,
          'message' => 'License code specified is incorrect, please recheck!'
        ],200);
      }
    }else{
      $this->response([
        'status' => false,
        'message' => 'Incorrect method or missing values, please check.'
      ],400);
    }
  }

  protected function license_check($_product_id,$_license,$_client,$_license_file,$is_update_check)
  { $this->load->helper('api_helper');
    $this->load->library('encryption');
  if(!empty($_product_id)){
    if(empty($_license)&&!empty($_license_file)){
      $license_dec = $this->encryption->decrypt($_license_file);
      $license_json = json_decode($license_dec, true);
      $license_code = $license_json['license'];
      $username = $license_json['client'];
    }
    else{
      $username = strip_tags(trim($_client));
      $license_code = strip_tags(trim($_license));
    }
    $product_id = strip_tags(trim($_product_id));
    $ip = strip_tags(trim($this->client_ip));
    $url = strip_tags(trim($this->client_url));
    $today = date('Y-m-d H:i:s');
    $domain = parse_url($url, PHP_URL_HOST);
    $product = $this->products_model->get_product($product_id);
    $license = $this->licenses_model->get_license($license_code);
    $product_active = $this->products_model->check_product_status($product_id);
    if(!empty($license['id'])&&!empty($product['pd_id'])&&$product_active&&($license['pid']==$product['pd_pid'])){
      if($license['client']!=null&&(strtolower($license['client'])!=strtolower($username))){
        return false;
      }
      if($license['validity']==0){
        return false;
      }
      if(!empty($license['domains'])){
      $valid_domains = explode(',',$license['domains']);
      if (!in_array($domain, $valid_domains)) {
        return false;
      }
      }
         if(!empty($license['ips'])){
      $valid_ips = explode(',',$license['ips']);
      if (!in_array($ip, $valid_ips)) {
        return false;
      }
       }

      if(!empty($license['expiry'])){
      $expiry_date = $license['expiry'];
      if ($today>=$expiry_date) {
        return false;
      }
       }
      if($is_update_check){
        if(!empty($license['updates_till'])){
          $support_end_date = $license['updates_till'];
          if ($today>=$support_end_date) {
          return false;
          }
        }
      }

      $current_activations = $this->installations_model->get_activation_by_license($license['license_code']);

      $licenses_left0 = $license['uses']-$current_activations;
      if(($licenses_left0>=0)||($license['uses']==null)){
        $uses_left = $license['uses_left'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          return false;
          $uses_left = 0;
          $is_valid = 0;
        }

      $current_activations_active = $this->installations_model->get_activation_by_license_active($license['license_code']);

      $licenses_left00 = $license['parallel_uses']-$current_activations_active;
      if(($licenses_left00>=0)||($license['parallel_uses']==null)){
        $uses_left = $license['parallel_uses'];
        $validity = $license['validity'];
        $is_valid = 1;}
        else
        {
          return false;
          $uses_left = 0;
          $is_valid = 0;
        }
          return true;
      }
      else
      {
        return false;
      }
    }else{
        return false;
    }
  }
}