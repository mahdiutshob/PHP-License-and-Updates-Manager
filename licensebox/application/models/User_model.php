<?php
	class User_model extends CI_Model{
		public function login($username, $password){
			$query = $this->db->get_where('auth_users', array('au_username' => $username));
			$row = $query->row_array();
			if(($query->num_rows() == 1)&&password_verify($password, ($row['au_password']))){
				return $query->row(0)->au_uid;
			}
			else{
				return false;
			}
		}

		public function edit_user(){
				$full_name = strip_tags(trim($this->input->post('full_name')));
				$username = strip_tags(trim($this->input->post('username')));
				$email = strip_tags(trim($this->input->post('email')));

				$data = array(
				'au_name' => $full_name,
				'au_username' => $username,
				'au_email' => $email
				);
 				$this->db->where('au_uid', $this->session->userdata('user_id'));
				return $this->db->update('auth_users', $data);
		}

		public function get_config_from_db($config){
			$query = $this->db->query('SELECT * FROM app_settings WHERE 1=1');
			foreach ($query->result() as $row)
			{
			if($row->as_name == $config){
				return $row->as_value;
			}
			}
			return false;
		}

		public function edit_config(){
			$data = array('as_value' => strip_tags(trim($this->input->post('license_format'))));
			$this->db->where('as_name', 'license_code_format');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => strip_tags(trim($this->input->post('envato_username'))));
			$this->db->where('as_name', 'envato_username');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => strip_tags(trim($this->input->post('envato_api_token'))));
			$this->db->where('as_name', 'envato_api_token');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => strip_tags(trim($this->input->post('server_email'))));
			$this->db->where('as_name', 'server_email');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => !empty($this->input->post('api_blacklisted_ips'))?strip_tags(trim($this->input->post('api_blacklisted_ips'))):null);
			$this->db->where('as_name', 'blacklisted_ips');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => !empty($this->input->post('api_blacklisted_domains'))?strip_tags(trim($this->input->post('api_blacklisted_domains'))):null);
			$this->db->where('as_name', 'blacklisted_domains');
			$this->db->update('app_settings', $data);
			$data = array('as_value' => !empty($this->input->post('api_rate_limit'))?strip_tags(trim($this->input->post('api_rate_limit'))):null);
			$this->db->where('as_name', 'api_rate_limit');
			$this->db->update('app_settings', $data);

			$data = array('as_value' => clean_html_codes($this->input->post('license_expiring')));
			$this->db->where('as_name', 'license_expiring');
			$this->db->update('app_settings', $data);
			if(strip_tags(trim($this->input->post('license_expiring_enable')))=='on'){
				$license_expiring_enable = 1;
			}
			else
			{
				$license_expiring_enable = 0;
			}
			$data = array('as_value' => $license_expiring_enable);
			$this->db->where('as_name', 'license_expiring_enable');
			$this->db->update('app_settings', $data);

			$data = array('as_value' => clean_html_codes($this->input->post('support_expiring')));
			$this->db->where('as_name', 'support_expiring');
			$this->db->update('app_settings', $data);
			if(strip_tags(trim($this->input->post('support_expiring_enable')))=='on'){
				$support_expiring_enable = 1;
			}
			else
			{
				$support_expiring_enable = 0;
			}
			$data = array('as_value' => $support_expiring_enable);
			$this->db->where('as_name', 'support_expiring_enable');
			$this->db->update('app_settings', $data);

			$data = array('as_value' => clean_html_codes($this->input->post('updates_expiring')));
			$this->db->where('as_name', 'updates_expiring');
			$this->db->update('app_settings', $data);
			if(strip_tags(trim($this->input->post('updates_expiring_enable')))=='on'){
				$updates_expiring_enable = 1;
			}
			else
			{
				$updates_expiring_enable = 0;
			}
			$data = array('as_value' => $updates_expiring_enable);
			$this->db->where('as_name', 'updates_expiring_enable');
			$this->db->update('app_settings', $data);

			$data = array('as_value' => clean_html_codes($this->input->post('new_update')));
			$this->db->where('as_name', 'new_update');
			$this->db->update('app_settings', $data);
			if(strip_tags(trim($this->input->post('new_update_enable')))=='on'){
				$new_update_enable = 1;
			}
			else
			{
				$new_update_enable = 0;
			}
			$data = array('as_value' => $new_update_enable);
			$this->db->where('as_name', 'new_update_enable');
			$this->db->update('app_settings', $data);

			if(strip_tags(trim($this->input->post('failed_activation_logs')))=='on'){
				$failed_activation_logs = 1;
			}
			else
			{
				$failed_activation_logs = 0;
			}
			$data = array('as_value' => $failed_activation_logs);
			$this->db->where('as_name', 'failed_activation_logs');
			$this->db->update('app_settings', $data);
			if(strip_tags(trim($this->input->post('failed_update_download_logs')))=='on'){
				$failed_update_download_logs = 1;
			}
			else
			{
				$failed_update_download_logs = 0;
			}
			$data = array('as_value' => $failed_update_download_logs);
			$this->db->where('as_name', 'failed_update_download_logs');
			$this->db->update('app_settings', $data);
			return true;
		}

		public function get_api_keys($only_external = false){
			if($only_external){
				$this->db->where('controller', '/api_external');
			}
				$query = $this->db->get('api_keys');
				return $query->result_array();
		}

		public function get_internal_api_keys(){
				$this->db->where('controller', '/api_internal');
				$query = $this->db->get('api_keys');
				return $query->result_array();
		}

		public function add_api_key(){
			$data = array(
				'key' => strip_tags(trim($this->input->post('api_key'))),
				'controller' => "/api_".strtolower(strip_tags(trim($this->input->post('api_type'))))
			);
			return $this->db->insert('api_keys', $data);
		}

		public function delete_api_key(){
			$this->db->where('key', strip_tags(trim($this->input->post('key'))));
			$this->db->delete('api_keys');
			return $this->db->affected_rows();
		}

		public function change_password($email = null){
				$password = password_hash(strip_tags(trim($this->input->post('new_password'))), PASSWORD_DEFAULT);
				$data = array(
				'au_password' => $password
				);
				if(!empty($email)){
					$this->db->where('au_email', urldecode($email));
				}
				else{
					$this->db->where('au_uid', $this->session->userdata('user_id'));
				}
				return $this->db->update('auth_users', $data);
		}


		public function get_user($slug){
			$this->db->select('au_uid, au_name, au_username, au_email');
			$query = $this->db->get_where('auth_users', array('au_uid' => $slug));
			return $query->row_array();
		}

		public function get_user_from_username($slug){
			$this->db->select('au_uid, au_name, au_username, au_email, au_reset_key, au_reset_exp');
			$query = $this->db->get_where('auth_users', array('au_username' => $slug));
			return $query->row_array();
		}

		public function get_user_from_token($email, $token){
			$this->db->select('au_uid, au_name, au_username, au_email, au_reset_key, au_reset_exp');
			$query = $this->db->get_where('auth_users', array('au_email' => urldecode($email)));
			$response = $query->row_array();
			if(password_verify($token, ($response['au_reset_key']))){
				return $response;
			}else{
				return false;
			}
		}

		public function add_password_reset($uid,$reset_key,$reset_exp){
			$data = array(
			'au_reset_key' => $reset_key,
			'au_reset_exp' => $reset_exp
				);
 			$this->db->where('au_uid', $uid);
			return $this->db->update('auth_users', $data);
		}

		public function remove_password_reset($email){
			$data = array(
			'au_reset_key' => null,
			'au_reset_exp' => null
				);
 			$this->db->where('au_email', urldecode($email));
			return $this->db->update('auth_users', $data);
		}

		public function check_username_exists($username){
			$query = $this->db->get_where('users', array('username' => $username));
			if(empty($query->row_array())){
				return true;
			} else {
				return false;
			}
		}

		public function check_email_exists($email){
			$query = $this->db->get_where('users', array('email' => $email));
			if(empty($query->row_array())){
				return true;
			} else {
				return false;
			}
		}

		public function add_log($log){
			if(!empty(trim($log))){
				$data = array(
				'al_log' => trim($log)
			);
			$this->db->insert('activity_log', $data);
			return true;
			}
			else
			{
				return false;
			}
		}

		public function get_activities($limit,$start,$col,$dir){   
       		$query = $this->db->limit($limit,$start)->order_by($col,$dir)->get('activity_log');
        	if($query->num_rows()>0)
        	{
            	return $query->result(); 
        	}
        	else
       		{
            	return null;
        	}
        
    	}

    	public function get_activities_count(){
			$query = $this->db->query('SELECT * FROM activity_log');
			return $query->num_rows();
		}
   
    	public function activity_search($limit,$start,$search,$col,$dir){
        	$query = $this->db->like('al_log',$search)->or_like('al_date',$search)->limit($limit,$start)->order_by($col,$dir)->get('activity_log');
        
        	if($query->num_rows()>0)
        	{
            	return $query->result();  
        	}
        	else
        	{
            	return null;
        	}
    	}

    	public function activity_search_count($search){
        	$query = $this->db->like('al_log',$search)->or_like('al_date',$search)->get('activity_log');
        	return $query->num_rows();
    	} 

		public function get_activity_log(){
			$query = $this->db->query('SELECT * from activity_log WHERE al_date > (NOW() - INTERVAL 24 HOUR) ORDER BY al_date DESC');
			return $query->result_array();
		}

		public function get_activity_logs(){
			$query = $this->db->query('SELECT * from activity_log ORDER BY al_date DESC');
			return $query->result_array();
		}
	}