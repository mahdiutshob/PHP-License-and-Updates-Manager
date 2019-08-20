<?php
	class Installations_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function get_installations_count(){
			$query = $this->db->get('product_installations');
			return $query->num_rows();
		}

		public function get_activations($limit,$start,$col,$dir){   
       		$query = $this->db->limit($limit,$start)->order_by($col,$dir)->get('product_installations');
        	if($query->num_rows()>0)
        	{
            	return $query->result(); 
        	}
        	else
       		{
            	return null;
        	}
        
    	}

    	public function get_activations_based_on_date($start,$end){   
       		$this->db->where('pi_date >=', $start);
			$this->db->where('pi_date <=', $end);
			$this->db->where('pi_isvalid', 1);
			$query = $this->db->get('product_installations');
			return $query->num_rows();
    	}
   
    	public function activation_search($limit,$start,$search,$col,$dir){
        	$query = $this->db->like('pi_purchase_code',$search)->or_like('pi_username',$search)->or_like('pi_date',$search)->or_like('pi_url',$search)->limit($limit,$start)->order_by($col,$dir)->get('product_installations');
        
        	if($query->num_rows()>0)
        	{
            	return $query->result();  
        	}
        	else
        	{
            	return null;
        	}
    	}

    	public function activation_search_count($search){
        	$query = $this->db->like('pi_purchase_code',$search)->or_like('pi_username',$search)->or_like('pi_date',$search)->or_like('pi_url',$search)->get('product_installations');
        	return $query->num_rows();
    	} 

		public function get_active_installations_count(){
			$query = $this->db->query('SELECT * FROM product_installations WHERE pi_isvalid=1 AND pi_isactive=1');
			return $query->num_rows();
		}

		public function get_installation($slug = FALSE){
			if($slug === FALSE){
				$this->db->order_by('pi_id','DESC');
				$query = $this->db->get('product_installations');
				return $query->result_array();
			}
			$query = $this->db->get_where('product_installations', array('pi_iid' => $slug));
			return $query->row_array();
		}

		public function get_activation($license, $active = false){
			if(!empty($active)){
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license, 'pi_isvalid' => 1, 'pi_isactive' => 1));
			}else{
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license, 'pi_isvalid' => 1));
			}
			return $query->result_array();
		}

		public function get_current_activation($license,$client,$domain){
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license,'pi_username' => $client, 'pi_isvalid' => 1, 'pi_isactive' => 1));
			$query_res = $query->result_array();
			$query_res_n = $query->num_rows();
			if(!empty($query_res)){
				foreach ($query_res as $query) {
				$domain_res = parse_url($query['pi_url'], PHP_URL_HOST);
				$username_res = strtolower($query['pi_username']);
				if((strtolower($client)==$username_res)&&($domain==$domain_res)){
					return $query_res_n;
				}
				}
				return false;
			}else{
				return false;
			}
		}

		public function deactivate_current_activation($license,$client,$domain){
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license,'pi_username' => $client, 'pi_isvalid' => 1, 'pi_isactive' => 1));
			$query_res = $query->result_array();
			$query_res_n = $query->num_rows();
			$deactivation_done = false;
			if(!empty($query_res)){
				foreach ($query_res as $activations) {
					$domain_res = parse_url($activations['pi_url'], PHP_URL_HOST);
					$username_res = strtolower($activations['pi_username']);
					if((strtolower($client)==$username_res)&&($domain==$domain_res)){
						$this->db->where('pi_iid', $activations['pi_iid']);
						$this->db->update('product_installations', array(
	        			'pi_isactive' => 0
	        			));
	        			$deactivation_done = true;
					}else{
						$deactivation_done = false;
					}
				}
				return $deactivation_done;
			}else{
				return false;
			}
		}

		public function get_activation_by_license($license){
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license, 'pi_isvalid' => 1));
			return $query->num_rows();
		}

		public function get_activation_by_license_active($license){
			$query = $this->db->get_where('product_installations', array('pi_purchase_code' => $license, 'pi_isvalid' => 1, 'pi_isactive' => 1));
			return $query->num_rows();
		}

		public function delete_installation(){
			$this->db->where('pi_iid', strip_tags(trim($this->input->post('iid'))));
			$this->db->delete('product_installations');
			return $this->db->affected_rows();
		}

		public function delete_installation_by_license($license){
			$this->db->where('pi_purchase_code', strip_tags(trim($license)));
			$this->db->delete('product_installations');
			return $this->db->affected_rows();
		}

		public function delete_installation_by_pid($pid){
			$this->db->where('pi_product', strip_tags(trim($pid)));
			$this->db->delete('product_installations');
			return $this->db->affected_rows();
		}

		public function activate_activation(){
			$this->db->where('pi_iid', strip_tags(trim($this->input->post('iid'))));
			$this->db->update('product_installations', array('pi_isactive' => 1));
			return $this->db->affected_rows();
		}
		public function deactivate_activation(){
			$this->db->where('pi_iid', strip_tags(trim($this->input->post('iid'))));
			$this->db->update('product_installations', array('pi_isactive' => 0));
			return $this->db->affected_rows();
		}

		public function change_activation_client($license,$client_old,$client_new){
			$array = array('pi_purchase_code' => strip_tags(trim($license)), 'pi_username' => strip_tags(trim($client_old)), 'pi_isvalid' => 1);
			$this->db->where($array); 
            $this->db->update('product_installations', array(
            'pi_username' => strip_tags(trim($client_new))
            ));
		}
	}