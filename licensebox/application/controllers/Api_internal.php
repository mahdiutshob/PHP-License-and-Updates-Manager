<?php defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/libraries/REST_Controller.php');
class Api_internal extends REST_Controller
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
        'error' => "Required headers are invalid or missing, please recheck."
      ], 403);
    }

    $this->client_ip = $has_ip;
    $this->client_url = $has_url;
    $this->client_agent = !empty($has_agent)?$has_agent:null;

    $has_domain = parse_url($has_url, PHP_URL_HOST);

    $api_rate_limit = $this->user_model->get_config_from_db('api_rate_limit');
    if($api_rate_limit){
      $this->methods['check_connection_int_post']['limit'] = $api_rate_limit;
      $this->methods['create_license_post']['limit'] = $api_rate_limit;
      $this->methods['add_product_post']['limit'] = $api_rate_limit;
      $this->methods['get_product_post']['limit'] = $api_rate_limit;
      $this->methods['mark_product_active_post']['limit'] = $api_rate_limit;
      $this->methods['mark_product_inactive_post']['limit'] = $api_rate_limit;
      $this->methods['get_license_post']['limit'] = $api_rate_limit;
      $this->methods['block_license_post']['limit'] = $api_rate_limit;
      $this->methods['unblock_license_post']['limit'] = $api_rate_limit;
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

  public function check_connection_int_post()
  {
      $this->response([
        'status' => true,
        'message' => 'Connection successful.'
      ], 200);
  }

  public function create_license_post()
  { 
    $product_id = strip_tags(trim($this->post('product_id')));
    $license_code = strip_tags(trim($this->post('license_code')));
    if(empty($license_code)){
      $this->load->helper('license_helper');
      $license_code = gen_code($this->user_model->get_config_from_db('license_code_format'));
      if($this->licenses_model->check_license_exists($license_code)){
        $license_code = gen_code($this->user_model->get_config_from_db('license_code_format'));
      }
    }
    if(!empty($product_id)){
      $row = $this->db->get_where('product_details', array('pd_pid' => $product_id))->row_array();
      if(!empty($row)){
        if($this->licenses_model->check_license_exists($license_code)){
          $this->response([
          'status' => false,
          'message' => "Provided license code already exists, please recheck."
          ],400);
        }
        $client_name = (!empty($this->post('client_name'))?strip_tags(trim($this->post('client_name'))):null);
        $license_type = (!empty($this->post('license_type'))?strip_tags(trim($this->post('license_type'))):null);
        $invoice_number = (!empty($this->post('invoice_number'))?strip_tags(trim($this->post('invoice_number'))):null);
        $client_email = (!empty($this->post('client_email'))?strip_tags(trim($this->post('client_email'))):null);
        if(!empty($client_email)){
          if(filter_var($client_email, FILTER_VALIDATE_EMAIL) == false){
            $this->response([
            'status' => false,
            'message' => "Provided client email address is incorrect, please check."
            ],400);
          }
        }
        $comments = (!empty($this->post('comments'))?strip_tags(trim($this->post('comments'))):null);
        $licensed_ips = (!empty($this->post('licensed_ips'))?strip_tags(trim($this->post('licensed_ips'))):null);
        if(!empty($blacklisted_ips)){
          if(!validate_ips($blacklisted_ips)){
              $this->response([
              'status' => false,
              'message' => "Provided licensed IPs are incorrect, please check."
              ],400);
          }   
        }
        $licensed_domains = (!empty($this->post('licensed_domains'))?strip_tags(trim($this->post('licensed_domains'))):null);
        if(!empty($licensed_domains)){
          if(!validate_domains($licensed_domains)){
              $this->response([
              'status' => false,
              'message' => "Provided licensed domains are incorrect, please check."
              ],400);
          }   
        }
        $support_end_date = (!empty($this->post('support_end_date'))?strip_tags(trim($this->post('support_end_date'))):null);
        if(!empty($support_end_date)){
          if(!validate_date($support_end_date)&&!validate_date($support_end_date,'Y-m-d H:i')){
            $this->response([
            'status' => false,
            'message' => "Provided license support end date is incorrect, please check."
            ],400);
          }
        }
        $updates_end_date = (!empty($this->post('updates_end_date'))?strip_tags(trim($this->post('updates_end_date'))):null);
        if(!empty($updates_end_date)){
          if(!validate_date($updates_end_date)&&!validate_date($updates_end_date,'Y-m-d H:i')){
            $this->response([
            'status' => false,
            'message' => "Provided license updates end date is incorrect, please check."
            ],400);
          }
        }
        $expiry_date = (!empty($this->post('expiry_date'))?strip_tags(trim($this->post('expiry_date'))):null);
        if(!empty($expiry_date)){
          if(!validate_date($expiry_date)&&!validate_date($expiry_date,'Y-m-d H:i')){
            $this->response([
            'status' => false,
            'message' => "Provided license expiration date is incorrect, please check."
            ],400);
          }
        }
        $license_uses = (!empty($this->post('license_uses'))?strip_tags(trim($this->post('license_uses'))):null);
        if(!empty($license_uses)){
          if(filter_var($license_uses, FILTER_VALIDATE_INT) === false){
            $this->response([
            'status' => false,
            'message' => "Provided license use limit is incorrect, please check."
            ],400);
          }
        }
        $license_parallel_uses = (!empty($this->post('license_parallel_uses'))?strip_tags(trim($this->post('license_parallel_uses'))):null);
        if(!empty($license_parallel_uses)){
          if(filter_var($license_parallel_uses, FILTER_VALIDATE_INT) === false){
            $this->response([
            'status' => false,
            'message' => "Provided license parallel use limit is incorrect, please check."
            ],400);
          }
        }
        $data = array(
        'pid' => $product_id,
        'license_code' => $license_code,
        'license_type' => $license_type,
        'invoice' => $invoice_number,
        'client' => $client_name,
        'email' => $client_email,
        'comments' => $comments,
        'ips' => $licensed_ips,
        'domains' => $licensed_domains,
        'supported_till' => $support_end_date,
        'updates_till' => $updates_end_date,
        'expiry' => $expiry_date,
        'uses' => $license_uses,
        'uses_left' => $license_uses,
        'parallel_uses' => $license_parallel_uses,
        'parallel_uses_left' => $license_parallel_uses,
        'validity' => 1
      );
        if($this->db->insert('product_licenses', $data)){
           $this->user_model->add_log('New '.$row['pd_name'].' license <b><i>'.$license_code.'</i></b> added.');
           $this->response([
           'status' => true,
           'message' => "New ".$row['pd_name']." license ".$license_code." was successfully added.",
           'license_code' => $license_code
           ],200);
        }else{
           $this->response([
           'status' => false,
           'message' => "An error occured, license was not added."
           ],400);
        }
      }
      else{
        $this->response([
          'status' => false,
          'message' => "Product ID specified is incorrect, please recheck."
        ],400);
      }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function add_product_post()
  { 
    $product_id = strip_tags(trim($this->post('product_id')));
    $product_name = strip_tags(trim($this->post('product_name')));
    if(empty($product_id)){
      $product_id = strtoupper(substr(MD5(microtime()), 0, 8));
      if($this->products_model->check_product_exists($product_id)){
        $product_id = strtoupper(substr(MD5(microtime()), 0, 8));
      }
    }
    if(!empty($product_name)){
        if($this->products_model->check_product_exists($product_id)){
            $this->response([
            'status' => false,
            'message' => "Provided product ID already exists, please recheck."
            ],400);
        }
        if(strlen($product_id)<6){
          $this->response([
          'status' => false,
          'message' => "Product ID should be at-least 6 characters long, please check."
          ],400);
        }
        $envato_item_id = (!empty($this->post('envato_item_id'))?strip_tags(trim($this->post('envato_item_id'))):null);
        $product_details = (!empty($this->post('product_details'))?strip_tags(trim($this->post('product_details'))):null);
        $data = array(
        'pd_pid' => $product_id,
        'envato_id' => $envato_item_id,
        'pd_name' => $product_name,
        'pd_details' => $product_details,
        'license_update' => 0,
        'pd_status' => 1
        );
        if($this->db->insert('product_details', $data)){
           $this->user_model->add_log('New product <b>'.$product_name.'</b> added.');
           $this->response([
           'status' => true,
           'message' => "New product ".$product_name." having ID ".$product_id." was successfully added.",
           'product_id' => $product_id
           ],200);
        }else{
           $this->response([
           'status' => false,
           'message' => "An error occured, product was not added."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function get_product_post()
  { 
    $product_id = strip_tags(trim($this->post('product_id')));
    if(!empty($product_id)){
        $product = $this->db->get_where('product_details', array('pd_pid' => $product_id))->row_array();
        if(!empty($product)){
          $latest_version_res = $this->products_model->get_latest_version($product['pd_pid']);
          if(!empty($latest_version_res)){
          $latest_version = $latest_version_res[0]['version']; 
          $latest_version_release_date = $latest_version_res[0]['release_date'];}
          else{
          $latest_version = null;
          $latest_version_release_date = null;
          }
          if($product['pd_status']==1){
            $product_status = true;
          }
          else{
            $product_status = false;
          }
          if($product['license_update']==1){
            $license_update = true;
          }
          else{
            $license_update = false;
          }
          $this->response([
           'status' => true,
           'product_id' => $product['pd_pid'],
           'envato_item_id' => $product['envato_id'],
           'product_name' => $product['pd_name'],
           'product_details' => $product['pd_details'],
           'latest_version' => $latest_version,
           'latest_version_release_date' => $latest_version_release_date,
           'is_product_active' => $product_status,
           'requires_license_for_downloading_updates' => $license_update,
           ],200);
        }else{
           $this->response([
           'status' => false,
           'message' => "Product ID specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function mark_product_active_post()
  { 
    $product_id = strip_tags(trim($this->post('product_id')));
    if(!empty($product_id)){
        $product = $this->db->get_where('product_details', array('pd_pid' => $product_id))->row_array();
        if(!empty($product)){
          $this->db->where('pd_pid', $product_id);
          $this->db->update('product_details', array(
          'pd_status' => 1
          ));
          if($this->db->affected_rows()>0){
             $this->user_model->add_log('Product <b>'.$product['pd_name'].'</b> status changed to active.');
             $this->response([
            'status' => true,
            'message' => "Product ".$product['pd_name']." marked as active."
            ],200);
          }else{
            $this->response([
            'status' => false,
            'message' => "Product ".$product['pd_name']." is already active."
            ],400);
          }
        }else{
           $this->response([
           'status' => false,
           'message' => "Product ID specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function mark_product_inactive_post()
  { 
    $product_id = strip_tags(trim($this->post('product_id')));
    if(!empty($product_id)){
        $product = $this->db->get_where('product_details', array('pd_pid' => $product_id))->row_array();
        if(!empty($product)){
          $this->db->where('pd_pid', $product_id);
          $this->db->update('product_details', array(
          'pd_status' => 0
          ));
          if($this->db->affected_rows()>0){
             $this->user_model->add_log('Product <b>'.$product['pd_name'].'</b> status changed to inactive.');
             $this->response([
            'status' => true,
            'message' => "Product ".$product['pd_name']." marked as inactive."
            ],200);
          }else{
            $this->response([
            'status' => false,
            'message' => "Product ".$product['pd_name']." is already inactive."
            ],400);
          }
        }else{
           $this->response([
           'status' => false,
           'message' => "Product ID specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function get_license_post()
  { 
    $license_code = strip_tags(trim($this->post('license_code')));
    if(!empty($license_code)){
        $license = $this->db->get_where('product_licenses', array('license_code' => $license_code))->row_array();
        if(!empty($license)){
          $product = $this->db->get_where('product_details', array('pd_pid' => $license['pid']))->row_array();
          if(!empty($product)){
          $product_name = $product['pd_name']; }
          else{
          $product_name = null;
          }
          if($license['is_envato']==1){
            $is_envato = true;
          }
          else{
            $is_envato = false;
          }
          if($license['validity']==1){
            $blocked = false;
          }
          else{
            $blocked = true;
          }
          $current_activations=$this->installations_model->get_activation_by_license($license['license_code']);
          $current_activations_active=$this->installations_model->get_activation_by_license_active($license['license_code']);
          $licenses_left0 = $license['uses']-$current_activations;
          if($licenses_left0>0)
          { 
              $licenses_left = $licenses_left0;
          }
          elseif($license['uses']==NULL)
          {   $licenses_left0 = null;
              $licenses_left = null;
          }
          else
          {   $licenses_left0 = 0;
              $licenses_left = 0;
          }
          $parallel_licenses_left0 = $license['parallel_uses']-$current_activations_active;
          if($parallel_licenses_left0>0)
          { 
              $parallel_licenses_left = $parallel_licenses_left0;
          }
          elseif($license['parallel_uses']==NULL)
          {   $parallel_licenses_left0=null;
              $parallel_licenses_left = null;
          }
          else
          {   $parallel_licenses_left0 = 0;
              $parallel_licenses_left = 0;
          }
          if($license['validity']==1){
                    if(($licenses_left0>0)||($license['uses']==null)){
                                if(($parallel_licenses_left0>0)||($license['parallel_uses']==null)){
                                    $is_valid = true;
                                }else{
                                  $is_valid = false;
                                }
                    }else{
                      $is_valid = false;
                    }
          }else{
            $is_valid = false;
          }
          $this->response([
           'status' => true,
           'license_code' => $license['license_code'],
           'product_id' => $license['pid'],
           'product_name' => $product_name,
           'license_type' => $license['license_type'],
           'client_name' => $license['client'],
           'client_email' => $license['email'],
           'invoice_number' => $license['invoice'],
           'license_comments' => $license['comments'],
           'licensed_ips' => $license['ips'],
           'licensed_domains' => $license['domains'],
           'uses' => $license['uses'],
           'uses_left' => $licenses_left,
           'parallel_uses' => $license['parallel_uses'],
           'parallel_uses_left' => $parallel_licenses_left0,
           'license_expiry' => $license['expiry'],
           'support_expiry' => $license['supported_till'],
           'updates_expiry' => $license['updates_till'],
           'date_modified' => $license['added_on'],
           'is_blocked' => $blocked,
           'is_a_envato_purchase_code' => $is_envato,
           'is_valid_for_future_activations' => $is_valid,
           ],200);
        }else{
           $this->response([
           'status' => false,
           'message' => "License code specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function block_license_post()
  { 
    $license_code = strip_tags(trim($this->post('license_code')));
    if(!empty($license_code)){
        $license = $this->db->get_where('product_licenses', array('license_code' => $license_code))->row_array();
        if(!empty($license)){
          $this->db->where('license_code', $license_code);
          $this->db->update('product_licenses', array(
          'validity' => 0
          ));
          if($this->db->affected_rows()>0){
             $this->user_model->add_log('License <b>'.$license['license_code'].'</b> blocked.');
             $this->response([
            'status' => true,
            'message' => "License ".$license['license_code']." was successfully blocked."
            ],200);
          }else{
            $this->response([
            'status' => false,
            'message' => "License ".$license['license_code']." is already blocked."
            ],400);
          }
        }else{
           $this->response([
           'status' => false,
           'message' => "License code specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }

  public function unblock_license_post()
  { 
    $license_code = strip_tags(trim($this->post('license_code')));
    if(!empty($license_code)){
        $license = $this->db->get_where('product_licenses', array('license_code' => $license_code))->row_array();
        if(!empty($license)){
          $this->db->where('license_code', $license_code);
          $this->db->update('product_licenses', array(
          'validity' => 1
          ));
          if($this->db->affected_rows()>0){
             $this->user_model->add_log('License <b>'.$license['license_code'].'</b> unblocked.');
             $this->response([
            'status' => true,
            'message' => "License ".$license['license_code']." was successfully unblocked."
            ],200);
          }else{
            $this->response([
            'status' => false,
            'message' => "License ".$license['license_code']." is already unblocked."
            ],400);
          }
        }else{
           $this->response([
           'status' => false,
           'message' => "License code specified is incorrect, please recheck."
           ],400);
        }
    }else{
        $this->response([
          'status' => false,
          'message' => "Incorrect method or missing values, please check."
        ],400);
    }
  }
}