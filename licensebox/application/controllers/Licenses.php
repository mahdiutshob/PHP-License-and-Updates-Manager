<?php 
	class Licenses extends CI_Controller{

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
				$data['title'] = 'Manage Licenses';
				$data['licenses'] = $this->licenses_model->get_license();
				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('licenses/index',$data);
				$this->load->view('templates/footer');
			}

			public function get_licenses()
			{		
                if(!$this->session->userdata('logged_in')){
                    redirect('users/login');
                }

				define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'); 
				if(!IS_AJAX) {header("HTTP/1.0 403 Forbidden");die('Direct access not allowed.');}

				$columns = array( 
                            0 => 'license_code', 
                            1 => 'pid',
                            2 => 'client',
                            3 => 'added_on',
                            4 => 'uses_left',
                            6 => 'validity'
                        );

				$limit = $this->input->post('length');
        		$start = $this->input->post('start');
        		$order = $columns[$this->input->post('order')[0]['column']];
        		$dir = $this->input->post('order')[0]['dir'];
  
        		$totalData = $this->licenses_model->get_licenses_count();
           
        		$totalFiltered = $totalData; 
            
        		if(empty($this->input->post('search')['value']))
        		{            
            		$posts = $this->licenses_model->get_licenses($limit,$start,$order,$dir);
        		}
        		else {
            		$search = $this->input->post('search')['value']; 
            		$posts =  $this->licenses_model->license_search($limit,$start,$search,$order,$dir);
            		$totalFiltered = $this->licenses_model->license_search_count($search);
        		}

        		$data = array();
        		if(!empty($posts))
        		{
            		foreach ($posts as $post)
            		{	$nestedData = null;
            			$product = $this->products_model->get_product($post->pid); 
            		 	if($post->client!=null){
          					$client=$post->client;
       					}
        				else
        				{		
          					$client="-";
        				}
        				$originalDate = $post->added_on;
        				$newDate = date($this->config->item('datetime_format_table'), strtotime($originalDate));
                        $current_activations=$this->installations_model->get_activation_by_license($post->license_code);
                        $current_activations_active=$this->installations_model->get_activation_by_license_active($post->license_code);
                        $licenses_left0 = $post->uses-$current_activations;
        				if($licenses_left0>0)
       		 			{ 
          					$licenses_left = str_return_s(" use",$licenses_left0,"s",1);
        				}
        				elseif($post->uses==NULL)
        				{   $licenses_left0 = null;
          					$licenses_left = '∞ uses';
        				}
        				else
        				{   $licenses_left0 = 0;
          					$licenses_left = 'no uses';
        				}
                        $parallel_licenses_left0 = $post->parallel_uses-$current_activations_active;
                        if($parallel_licenses_left0>0)
                        { 
                            $parallel_licenses_left = $parallel_licenses_left0.' parallel';
                        }
                        elseif($post->parallel_uses==NULL)
                        {   $parallel_licenses_left0=null;
                            $parallel_licenses_left = '∞ parallel';
                        }
                        else
                        {   $parallel_licenses_left0 = 0;
                            $parallel_licenses_left = 'no parallel';
                        }
        				if($post->validity==1){
          					if(($licenses_left0>0)||($post->uses==null)){
                                if(($parallel_licenses_left0>0)||($post->parallel_uses==null)){
                                    $is_valid = "Valid";
                                    $is_valid_typ = "success";
                                    $is_valid_tooltip = "License is valid and can be used for activations.";
                                    $today = date('Y-m-d H:i:s');
                                    if(!empty($post->expiry)){
                                      $expiry_date = $post->expiry;
                                      if ($today>=$expiry_date) {
                                        $is_valid = "Invalid";
                                        $is_valid_typ = "danger";
                                        $is_valid_tooltip = "License is invalid as this license has expired.";
                                      }
                                    }
                                }else{
                                    $is_valid = "Invalid";
                                    $is_valid_typ = "danger";
                                    $is_valid_tooltip = "License is invalid due to overuse of parallel uses limit.";
                                }
          					}
          					else{
            				$is_valid = "Invalid";
            				$is_valid_typ = "danger";
                            $is_valid_tooltip = "License is invalid due to overuse of license uses limit.";
          					}
        				}
        				else
        				{
          					$is_valid = "Blocked";
          					$is_valid_typ = "danger";
                            $is_valid_tooltip = "License is blocked and cannot be used for activations.";
        				}
                        $has_activations = $this->installations_model->get_activation($post->license_code, true);
                        if($has_activations){
                            $size_activations = sizeof($has_activations);
                            $is_activated = "Active";
                            $is_activated_typ = "link";
                            $is_activated_tooltip = "License is currently active on ".$size_activations." domain(s).";
                        }else{
                            $is_activated = "Inactive";
                            $is_activated_typ = "primary";
                            $is_activated_tooltip = "License is currently not active on any domain.";
                        }
                		$nestedData[] = "<b>".$post->license_code."</b>";
                		$nestedData[] = $product['pd_name'];
                		$nestedData[] = $client;
                		$nestedData[] = $newDate;
                		$nestedData[] = $licenses_left." (".$parallel_licenses_left.") ";
                        $nestedData[] = "<center><span class='tag is-".$is_activated_typ." is-small is-rounded tooltip' data-tooltip='".$is_activated_tooltip."'>".$is_activated."</span></center>";
                		$nestedData[] = "<center><span class='tag is-".$is_valid_typ." is-small is-rounded tooltip' data-tooltip='".$is_valid_tooltip."'>".$is_valid."</span></center>";

                		$form_buttons = "<div class='buttons is-centered'>";

                		if($post->validity!=0){
        				$hidden = array('license' => $post->license_code);
        				$form_buttons.= form_open('/licenses/block','',$hidden)."<button type='submit' class='button is-warning is-small tooltip' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Block License'><i class='fa fa-lock'></i></button></form>&nbsp;";
      					}else{
      					$hidden = array('license' => $post->license_code);
        				$form_buttons.= form_open('/licenses/unblock','',$hidden)."<button type='submit' class='button is-warning is-small tooltip' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Unblock License'><i class='fa fa-unlock'></i></button></form>&nbsp;";
      					}
                		$hidden = array('license_code' => $post->license_code);
                		$form_buttons.= form_open('/licenses/edit','',$hidden)."<button type='submit' class='button is-success is-small tooltip' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Edit License'><i class='fa fa-edit'></i></button></form>&nbsp;";
      					$hidden = array('license' => $post->license_code);
    					$js = 'onSubmit="return ConfirmDelete(\'license\',\'\n\nNote: All of its relevant records like (activation logs) will be also deleted.\');"';
    					$form_buttons.= form_open('/licenses/delete',$js,$hidden)."<button type='submit' class='button is-danger is-small tooltip is-tooltip-left' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Delete License'><i class='fa fa-trash'></i></button></form>";
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

			public function create(){

                if(!$this->session->userdata('logged_in')){
                    redirect('users/login');
                }

				if($this->licenses_model->check_license_exists($this->input->post('license'))){
				$this->session->set_flashdata('license_status', array(
        			'type'  => "danger",
        			'message' => "License code already exists, please recheck."
					));
				redirect('licenses/create');
				}

				$data['title'] = 'Create new license';
				$this->form_validation->set_rules('license', 'License code','required');
				$this->form_validation->set_rules('product', 'Product','required');
$this->form_validation->set_rules(
        'ips', 'Licensed IP addresses',
        array(
                array(
                        'ips_check',
                        function($str)
                        { if(empty($str)){
                        	return true;
                        }else{
                        	if(validate_ips($str)){
                              return true;
                        	}
                        	else{
                        		return false;
                        	}                        	
                        }

                        }
                )
        )
);
$this->form_validation->set_rules(
        'domains', 'Licensed domains',
        array(
                array(
                        'domains_check',
                        function($str)
                        { if(empty($str)){
                        	return true;
                        }else{
                        	if(validate_domains($str)){
                              return true;
                        	}
                        	else{
                        		return false;
                        	}
                        }
                        }
                )
        )
);
				$this->form_validation->set_message('ips_check', '{field} are incorrect, please check.');
				$this->form_validation->set_message('domains_check', '{field} are incorrect, please check.');
				$this->load->helper('license_helper');
				$data['created_license'] = gen_code($this->user_model->get_config_from_db('license_code_format'));
				$data['products'] = $this->products_model->get_product();
				if($this->form_validation->run()===FALSE){

				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('licenses/create',$data);
				$this->load->view('templates/footer');

				}
			else
			{
				if($this->licenses_model->create_license()){
					$product = $this->products_model->get_product(strip_tags(trim($this->input->post('product'))));
				$this->user_model->add_log('New '.$product['pd_name'].' license <b><i>'.$this->input->post('license').'</i></b> added by '.ucfirst($this->session->userdata('fullname')).'.');
				$this->session->set_flashdata('license_status', array(
        			'type'  => "primary",
        			'message' => "License was successfully added."
					));
				redirect('licenses');
				}else{
				$this->session->set_flashdata('license_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, Please recheck entered values. License was not added."
					));
				redirect('licenses/create');
				}
			}
			}

			public function edit(){

				if(empty($this->input->post('license_code'))){
					redirect('licenses');
				}

				$data['title'] = 'Edit License';
				$data['license'] = $this->licenses_model->get_license($this->input->post('license_code'));
				$data['products'] = $this->products_model->get_product();
				$this->form_validation->set_rules('product', 'Product','required');
				$this->form_validation->set_rules(
        'ips', 'Licensed IP addresses',
        array(
                array(
                        'ips_check',
                        function($str)
                        { if(empty($str)){
                        	return true;
                        }else{
                        	if(validate_ips($str)){
                              return true;
                        	}
                        	else{
                        		return false;
                        	}                        	
                        }

                        }
                )
        )
);
$this->form_validation->set_rules(
        'domains', 'Licensed domains',
        array(
                array(
                        'domains_check',
                        function($str)
                        { if(empty($str)){
                        	return true;
                        }else{
                        	if(validate_domains($str)){
                              return true;
                        	}
                        	else{
                        		return false;
                        	}
                        }
                        }
                )
        )
);
				$this->form_validation->set_message('ips_check', '{field} are incorrect, please check.');
				$this->form_validation->set_message('domains_check', '{field} are incorrect, please check.');
				if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('licenses/edit',$data);
					$this->load->view('templates/footer');
				}
				else
				{
					if($this->licenses_model->edit_license()){
                        if(strtolower($this->input->post('client'))!=strtolower($this->input->post('old_client'))){
                        $this->installations_model->change_activation_client($this->input->post('license_code'),$this->input->post('old_client'),$this->input->post('client'));
                        }
						$this->user_model->add_log('License <b>'.$this->input->post('license_code').'</b> edited by '.ucfirst($this->session->userdata('fullname')).'.');
						$this->session->set_flashdata('license_status', array(
							'type'  => "primary",
							'message' => "License was successfully updated."
						));
						redirect('licenses');
					}else{

						$this->session->set_flashdata('license_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values or you haven't made any changes, License was not updated."
						));
						redirect('licenses');
					}
				}

			}

			public function delete(){
                $this->installations_model->delete_installation_by_license($this->input->post('license'));
				if($this->licenses_model->delete_license()){
				$this->user_model->add_log('License <b><i>'.$this->input->post('license').'</i></b> deleted by '.ucfirst($this->session->userdata('fullname')).'.');
				$this->session->set_flashdata('license_status', array(
        			'type'  => "primary",
        			'message' => "License was successfully deleted."
					));
				redirect('licenses');
				}else{
				$this->session->set_flashdata('license_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, License was not deleted. Make sure the license does not have any activations."
					));
				redirect('licenses');
				}
			}

				public function block(){
				if($this->licenses_model->block_license()){
				$this->user_model->add_log('License <b>'.$this->input->post('license').'</b> blocked by '.ucfirst($this->session->userdata('fullname')).'.');
				$this->session->set_flashdata('license_status', array(
        			'type'  => "primary",
        			'message' => "License was successfully blocked."
					));
				redirect('licenses');
				}else{
				$this->session->set_flashdata('license_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, License was not blocked."
					));
				redirect('licenses');
				}
			}

				public function unblock(){
				if($this->licenses_model->unblock_license())
				{
				$this->user_model->add_log('License <b>'.$this->input->post('license').'</b> unblocked by '.ucfirst($this->session->userdata('fullname')).'.');
				$this->session->set_flashdata('license_status', array(
        			'type'  => "primary",
        			'message' => "License was successfully unblocked."
					));
				redirect('licenses');
				}else{
				$this->session->set_flashdata('license_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, License was not unblocked."
					));
				redirect('licenses');
				}
			}


		}

?>