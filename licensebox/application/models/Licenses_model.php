<?php
	class Licenses_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function get_licenses_count(){
			$query = $this->db->query('SELECT * FROM product_licenses');
			return $query->num_rows();
		}

		public function get_licenses_count_for_chart(){
			$query = $this->db->query('SELECT * FROM product_licenses');
			$res = $query->result_array();
			$valid_count = 0;
			$invalid_count = 0;
			$blocked_count = 0;

			foreach ($res as $post) {
				$current_activations=$this->installations_model->get_activation_by_license($post['license_code']);
				$licenses_left0 = $post['uses']-$current_activations;
				$parallel_licenses_left0 = $post['parallel_uses']-$current_activations;
				if($post['validity']==1){
				if(($licenses_left0>0)||($post['uses']==NULL)){
				if(($parallel_licenses_left0>0)||($post['parallel_uses']==NULL)){
					$today = date('Y-m-d H:i:s');
	                if(!empty($post['expiry'])){
	                  $expiry_date = $post['expiry'];
	                  if ($today>=$expiry_date) {
	                    $is_valid = "Invalid";
	                    $is_valid_typ = "danger";
	                    $is_valid_tooltip = "License is invalid as this license has expired.";
	                    $invalid_count++;
	                  }else{
	                  	$valid_count++;
	                  }
	                }else{
	                	$valid_count++;
	                }
				}else{
					$invalid_count++;
				}
				}
				else{
					$invalid_count++;
				}
				}
				else
				{
					$blocked_count++;
				}
			}
			return array('valid' => $valid_count,'invalid' => $invalid_count,'blocked' => $blocked_count );
		}

		public function get_licenses_based_on_date($start,$end){   
       		$this->db->where('added_on >=', $start);
			$this->db->where('added_on <=', $end);
			$query = $this->db->get('product_licenses');
			return $query->num_rows();
    	}
    
    	public function get_licenses($limit,$start,$col,$dir){   
       		$query = $this->db->limit($limit,$start)->order_by($col,$dir)->get('product_licenses');
        	if($query->num_rows()>0)
        	{
            	return $query->result(); 
        	}
        	else
       		{
            	return null;
        	}
        
    	}
   
    	public function license_search($limit,$start,$search,$col,$dir){
        	$query = $this->db->like('license_code',$search)->or_like('client',$search)->or_like('email',$search)->limit($limit,$start)->order_by($col,$dir)->get('product_licenses');
        
        	if($query->num_rows()>0)
        	{
            	return $query->result();  
        	}
        	else
        	{
            	return null;
        	}
    	}

    	public function license_search_count($search){
        	$query = $this->db->like('license_code',$search)->or_like('client',$search)->or_like('email',$search)->get('product_licenses');
        	return $query->num_rows();
    	} 

		public function get_active_licenses_count(){
			$query = $this->db->query('SELECT DISTINCT(pi_purchase_code) FROM product_installations WHERE pi_isvalid=1 AND pi_isactive=1');
			return $query->num_rows();
		}

		public function get_license($slug = FALSE){
			if($slug === FALSE){
				$this->db->order_by('id','DESC');
				$query = $this->db->get('product_licenses');
				return $query->result_array();
			}
			$query = $this->db->get_where('product_licenses', array('license_code' => $slug));
			return $query->row_array();
		}

		public function check_license_exists($license){	
			$query = $this->db->get_where('product_licenses', array('license_code' => $license));
			if(empty($query->row_array())){
				return false;
			} else {
				return true;
			}
		}

		public function edit_license(){
			if(strip_tags(trim($this->input->post('validity')))=='on'){
				$is_valid = 0;
			}
			else
			{
				$is_valid = 1;
			}
			if((strip_tags(trim($this->input->post('client')))!=null)&&(strip_tags(trim($this->input->post('client')))!='')){
				$client = strip_tags(trim($this->input->post('client')));
			}
			else
			{
				$client = null;
			}
			if((strip_tags(trim($this->input->post('uses')))!=null)&&(strip_tags(trim($this->input->post('uses')))!='')){

				$uses = strip_tags(trim($this->input->post('uses')));
				$uses_left = strip_tags(trim($this->input->post('uses')));

			}
			else
			{
				$uses = null;
				$uses_left = null;
			}
			if((strip_tags(trim($this->input->post('parallel_uses')))!=null)&&(strip_tags(trim($this->input->post('parallel_uses')))!='')){

				$parallel_uses = strip_tags(trim($this->input->post('parallel_uses')));
				$parallel_uses_left = strip_tags(trim($this->input->post('parallel_uses')));

			}
			else
			{
				$parallel_uses = null;
				$parallel_uses_left = null;
			}
			if(!empty(strip_tags(trim($this->input->post('expiry'))))){
				$expiry_date = strip_tags(trim($this->input->post('expiry')));
			}else{
				$expiry_date = null;
			}
			if(!empty(strip_tags(trim($this->input->post('supported_till'))))){
				$supported_till = strip_tags(trim($this->input->post('supported_till')));
			}else{
				$supported_till = null;
			}
			if(!empty(strip_tags(trim($this->input->post('updates_till'))))){
				$updates_till = strip_tags(trim($this->input->post('updates_till')));
			}else{
				$updates_till = null;
			}
			if(!empty(strip_tags(trim($this->input->post('license_type'))))){
				$license_type = strip_tags(trim($this->input->post('license_type')));
			}else{
				$license_type = null;
			}
			if(!empty(strip_tags(trim($this->input->post('comments'))))){
				$comments = strip_tags(trim($this->input->post('comments')));
			}else{
				$comments = null;
			}
			if(!empty(strip_tags(trim($this->input->post('invoice'))))){
				$invoice = strip_tags(trim($this->input->post('invoice')));
			}else{
				$invoice = null;
			}
			if(!empty(strip_tags(trim($this->input->post('ips'))))){
				$ips = strip_tags(trim($this->input->post('ips')));
			}else{
				$ips = null;
			}
			if(!empty(strip_tags(trim($this->input->post('domains'))))){
				$domains = strip_tags(trim($this->input->post('domains')));
			}else{
				$domains = null;
			}
			if(!empty(strip_tags(trim($this->input->post('email'))))){
				$email = strip_tags(trim($this->input->post('email')));
			}else{
				$email = null;
			}
			$data = array(
				'pid' => strip_tags(trim($this->input->post('product'))),
				'license_type' => $license_type,
				'invoice' => $invoice,
				'client' => $client,
				'email' => $email,
				'comments' => $comments,
				'ips' => $ips,
				'domains' => $domains,
				'expiry' => $expiry_date,
				'supported_till' => $supported_till,
				'updates_till' => $updates_till,
				'uses' => $uses,
				'uses_left' => $uses_left,
				'parallel_uses' => $parallel_uses,
				'parallel_uses_left' => $parallel_uses_left,
				'validity' => $is_valid

			);
 			$this->db->where('license_code', $this->input->post('license_code'));
			return $this->db->update('product_licenses', $data);
		}

		public function create_license(){
			if(strip_tags(trim($this->input->post('validity')))=='on'){
				$is_valid = 0;
			}
			else
			{
				$is_valid = 1;
			}
			if((strip_tags(trim($this->input->post('client')))!=null)&&(strip_tags(trim($this->input->post('client')))!='')){
				$client = strip_tags(trim($this->input->post('client')));
			}
			else
			{
				$client = null;
			}
			if((strip_tags(trim($this->input->post('uses')))!=null)&&(strip_tags(trim($this->input->post('uses')))!='')){
				$uses = strip_tags(trim($this->input->post('uses')));
				$uses_left = strip_tags(trim($this->input->post('uses')));
			}
			else
			{
				$uses = null;
				$uses_left = null;
			}
			if((strip_tags(trim($this->input->post('parallel_uses')))!=null)&&(strip_tags(trim($this->input->post('parallel_uses')))!='')){

				$parallel_uses = strip_tags(trim($this->input->post('parallel_uses')));
				$parallel_uses_left = strip_tags(trim($this->input->post('parallel_uses')));

			}
			else
			{
				$parallel_uses = null;
				$parallel_uses_left = null;
			}
			if(!empty(strip_tags(trim($this->input->post('expiry'))))){
				$expiry_date = strip_tags(trim($this->input->post('expiry')));
			}else{
				$expiry_date = null;
			}
			if(!empty(strip_tags(trim($this->input->post('supported_till'))))){
				$supported_till = strip_tags(trim($this->input->post('supported_till')));
			}else{
				$supported_till = null;
			}
			if(!empty(strip_tags(trim($this->input->post('updates_till'))))){
				$updates_till = strip_tags(trim($this->input->post('updates_till')));
			}else{
				$updates_till = null;
			}
			if(!empty(strip_tags(trim($this->input->post('license_type'))))){
				$license_type = strip_tags(trim($this->input->post('license_type')));
			}else{
				$license_type = null;
			}
			if(!empty(strip_tags(trim($this->input->post('invoice'))))){
				$invoice = strip_tags(trim($this->input->post('invoice')));
			}else{
				$invoice = null;
			}
			if(!empty(strip_tags(trim($this->input->post('comments'))))){
				$comments = strip_tags(trim($this->input->post('comments')));
			}else{
				$comments = null;
			}
			if(!empty(strip_tags(trim($this->input->post('ips'))))){
				$ips = strip_tags(trim($this->input->post('ips')));
			}else{
				$ips = null;
			}
			if(!empty(strip_tags(trim($this->input->post('domains'))))){
				$domains = strip_tags(trim($this->input->post('domains')));
			}else{
				$domains = null;
			}
			if(!empty(strip_tags(trim($this->input->post('email'))))){
				$email = strip_tags(trim($this->input->post('email')));
			}else{
				$email = null;
			}
			$license = strip_tags(trim($this->input->post('license')));
  			if (preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $license)){
  				$is_envato = 1;
  			}
  			else{
  				$is_envato = null;
  			}
			$data = array(
				'pid' => strip_tags(trim($this->input->post('product'))),
				'license_code' => $license,
				'license_type' => $license_type,
				'invoice' => $invoice,
				'is_envato' => $is_envato,
				'client' => $client,
				'email' => $email,
				'comments' => $comments,
				'ips' => $ips,
				'domains' => $domains,
				'supported_till' => $supported_till,
				'updates_till' => $updates_till,
				'expiry' => $expiry_date,
				'uses' => $uses,
				'uses_left' => $uses_left,
				'parallel_uses' => $parallel_uses,
				'parallel_uses_left' => $parallel_uses_left,
				'validity' => $is_valid

			);
			return $this->db->insert('product_licenses', $data);
		}

		public function delete_license(){
			$this->db->where('license_code', strip_tags(trim($this->input->post('license'))));
			$this->db->delete('product_licenses');
			return $this->db->affected_rows();
		}

		public function delete_licenses_by_pid($pid){
			$this->db->where('pid', strip_tags(trim($pid)));
			$this->db->delete('product_licenses');
			return $this->db->affected_rows();
		}

		public function block_license(){
			$this->db->where('license_code', strip_tags(trim($this->input->post('license'))));
			$this->db->update('product_licenses', array(
			'validity' => 0
			));
			return $this->db->affected_rows();
		}
		public function unblock_license(){
			$this->db->where('license_code', strip_tags(trim($this->input->post('license'))));
			$this->db->update('product_licenses', array(
			'validity' => 1
			));
			return $this->db->affected_rows();
		}
	}