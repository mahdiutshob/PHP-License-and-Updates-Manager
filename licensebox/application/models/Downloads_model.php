<?php
	class Downloads_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function get_downloads_count(){
            $this->db->where('isvalid', 1);
			$query = $this->db->get('update_downloads');
			return $query->num_rows();
		}

		public function get_downloads($limit,$start,$col,$dir){   
       		$query = $this->db->limit($limit,$start)->order_by($col,$dir)->get('update_downloads');
        	if($query->num_rows()>0)
        	{
            	return $query->result(); 
        	}
        	else
       		{
            	return null;
        	}
        
    	}

		public function get_update_downloads_based_on_date($start,$end){  
            $this->db->where('isvalid', 1); 
       		$this->db->where('download_date >=', $start);
			$this->db->where('download_date <=', $end);
			$query = $this->db->get('update_downloads');
			return $query->num_rows();
    	}

    	public function get_update_downloads_based_on_product($product){   
       		$this->db->where('product', $product);
            $this->db->where('isvalid', 1);
			$query = $this->db->get('update_downloads');
			return $query->num_rows();
    	}

        public function get_update_downloads_based_on_version($version){   
            $this->db->where('vid', $version);
            $this->db->where('isvalid', 1);
            $query = $this->db->get('update_downloads');
            return $query->num_rows();
        }
   
    	public function download_search($limit,$start,$search,$col,$dir){
        	$query = $this->db->like('product',$search)->or_like('download_date',$search)->or_like('url',$search)->or_like('ip',$search)->limit($limit,$start)->order_by($col,$dir)->get('update_downloads');
        
        	if($query->num_rows()>0)
        	{
            	return $query->result();  
        	}
        	else
        	{
            	return null;
        	}
    	}

    	public function downloads_search_count($search){
        	$query = $this->db->like('download_date',$search)->or_like('url',$search)->or_like('ip',$search)->get('update_downloads');
        	return $query->num_rows();
    	} 

		public function delete_download(){
			$this->db->where('did', strip_tags(trim($this->input->post('did'))));
			$this->db->delete('update_downloads');
			return $this->db->affected_rows();
		}

        public function delete_downloads_by_vid($vid){
            $this->db->where('vid', strip_tags(trim($vid)));
            $this->db->delete('update_downloads');
            return $this->db->affected_rows();
        }

        public function delete_downloads_by_pid($pid){
            $this->db->where('product', strip_tags(trim($pid)));
            $this->db->delete('update_downloads');
            return $this->db->affected_rows();
        }
	}