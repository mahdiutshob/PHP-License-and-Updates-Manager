<?php 
	class Downloads extends CI_Controller{

        public function __construct() {
	        parent::__construct();
	        if(!$this->session->userdata('logged_in')){
	            redirect('users/login');
	        }
    	}

		public function index($page = 'home'){
				if(!$this->session->userdata('logged_in')){
				redirect('users/login');
			}
				$data['title'] = 'View Update Downloads/Installations';
				$data['installations'] = $this->installations_model->get_installation();
				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('downloads/index',$data);
				$this->load->view('templates/footer');
			}

		public function get_update_downloads()
			{		
                if(!$this->session->userdata('logged_in')){
                    redirect('users/login');
                }

				define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'); 
				if(!IS_AJAX) {header("HTTP/1.0 403 Forbidden");die('Direct access not allowed.');}

				$columns = array( 
                            0 => 'product', 
                            1 => 'vid',
                            2 => 'url',
                            3 => 'ip',
                            4 => 'download_date',
                            5 => 'isvalid'
                        );

				$limit = $this->input->post('length');
        		$start = $this->input->post('start');
        		$order = $columns[$this->input->post('order')[0]['column']];
        		$dir = $this->input->post('order')[0]['dir'];
  
        		$totalData = $this->downloads_model->get_downloads_count();
           
        		$totalFiltered = $totalData; 
            
        		if(empty($this->input->post('search')['value']))
        		{            
            		$posts = $this->downloads_model->get_downloads($limit,$start,$order,$dir);
        		}
        		else {
            		$search = $this->input->post('search')['value']; 
            		$posts =  $this->downloads_model->download_search($limit,$start,$search,$order,$dir);
            		$totalFiltered = $this->downloads_model->downloads_search_count($search);
        		}

        		$data = array();
        		if(!empty($posts))
        		{
            		foreach ($posts as $post)
            		{	$nestedData = null;
            			$product = $this->products_model->get_product($post->product); 
                        $version = $this->products_model->get_version_by_vid($post->vid); 
        				$originalDate = $post->download_date;
        				$newDate = date($this->config->item('datetime_format'), strtotime($originalDate));

                         if($post->isvalid==1){
                            $is_valid = "Valid";
                            $is_valid_typ = "success";
                            $is_valid_tooltip = "Update files were successfully served.";
                          }else{
                            $is_valid = "Invalid";
                            $is_valid_typ = "danger";
                            $is_valid_tooltip = "Update download attempt was invalid.";
                          }

                		$nestedData[] = "<b>".$product['pd_name']."</b>";
                		$nestedData[] = $version['version'];
                		 $nestedData[] = "<a href='".$post->url."' class='tooltip' data-tooltip='".$post->url."'>".parse_url($post->url, PHP_URL_HOST)."&nbsp;<small><i class='fas fa-external-link-alt'></i></small></a>";
                		$nestedData[] = $post->ip;
                		$nestedData[] = $newDate;
                        $nestedData[] = "<center><span class='tag is-".$is_valid_typ." is-small is-rounded tooltip' data-tooltip='".$is_valid_tooltip."'>".$is_valid."</span></center>";

                		$form_buttons = "<div class='buttons is-centered'>";

                		
                		$hidden = array('did' => $post->did);
          				$js = 'onSubmit="return ConfirmDelete(\'update download log\');"';
      
    					$form_buttons.= form_open('/update_downloads/delete',$js,$hidden)."<button type='submit' class='button is-danger is-small' style='padding-top: 0px;padding-bottom: 0px;'><i class='fa fa-trash'></i>&nbsp;Delete</button></form>";
                		$form_buttons.= "</div>";

                		$nestedData[] = $form_buttons;
                
                		$data[] = $nestedData;

            		}
        		}
          
        		$json_data = array(
                    "draw"            => intval($this->input->post('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );

        		echo json_encode($json_data); 
			}

			public function delete(){
				if($this->downloads_model->delete_download()){
				$this->session->set_flashdata('update_downloads_status', array(
        			'type'  => "primary",
        			'message' => "Update download log was successfully deleted."
					));
				redirect('update_downloads');
				}else{
				$this->session->set_flashdata('update_downloads_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, Update download log was not deleted."
					));
				redirect('update_downloads');
				}
			}



		}

?>