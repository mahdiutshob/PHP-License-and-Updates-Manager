<?php 
	class Activations extends CI_Controller{

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
				$data['title'] = 'View Activations/Installations';
				$data['installations'] = $this->installations_model->get_installation();
				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('activations/index',$data);
				$this->load->view('templates/footer');
			}

		public function get_activations()
			{		
                if(!$this->session->userdata('logged_in')){
                    redirect('users/login');
                }

				define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'); 
				if(!IS_AJAX) {header("HTTP/1.0 403 Forbidden");die('Direct access not allowed.');}

				$columns = array( 
                            0 => 'pi_product', 
                            1 => 'pi_username',
                            2 => 'pi_purchase_code',
                            3 => 'pi_url',
                            4 => 'pi_ip',
                            5 => 'pi_date',
                            6 => 'pi_isvalid'
                        );

				$limit = $this->input->post('length');
        		$start = $this->input->post('start');
        		$order = $columns[$this->input->post('order')[0]['column']];
        		$dir = $this->input->post('order')[0]['dir'];
  
        		$totalData = $this->installations_model->get_installations_count();
           
        		$totalFiltered = $totalData; 
            
        		if(empty($this->input->post('search')['value']))
        		{            
            		$posts = $this->installations_model->get_activations($limit,$start,$order,$dir);
        		}
        		else {
            		$search = $this->input->post('search')['value']; 
            		$posts =  $this->installations_model->activation_search($limit,$start,$search,$order,$dir);
            		$totalFiltered = $this->installations_model->activation_search_count($search);
        		}

        		$data = array();
        		if(!empty($posts))
        		{
            		foreach ($posts as $post)
            		{	$nestedData = null;
            			$product = $this->products_model->get_product($post->pi_product);
                        if($post->pi_username!=null){
                            $client=$post->pi_username;
                        }
                        else
                        {       
                            $client="-";
                        } 
        				$originalDate = $post->pi_date;
        				$newDate = date($this->config->item('datetime_format_table'), strtotime($originalDate));
    				      if(($post->pi_isvalid==1)&&($post->pi_isactive==1)){
    			            $is_valid = "Active";
    			            $is_valid_typ = "success";
                            $is_valid_tooltip = "Activation is valid and currently active.";
    			          }
    			          elseif(($post->pi_isvalid==1)&&($post->pi_isactive==0)){
                            $is_valid = "Inactive";
                            $is_valid_typ = "primary";
                            $is_valid_tooltip = "Activation is valid but is no longer active.";
                          }else
    			          {
    			            $is_valid = "Invalid";
    			            $is_valid_typ = "danger";
                            $is_valid_tooltip = "Activation attempt was invalid.";
    			          }

                		$nestedData[] = "<b>".$product['pd_name']."</b>";
                		$nestedData[] = $client;
                		$nestedData[] = $post->pi_purchase_code;
                        $nestedData[] = "<a href='".$post->pi_url."' class='tooltip' data-tooltip='".$post->pi_url."'>".parse_url($post->pi_url, PHP_URL_HOST)."&nbsp;<small><i class='fas fa-external-link-alt'></i></small></a>";
                		$nestedData[] = $post->pi_ip;
                		$nestedData[] = $newDate;
                		$nestedData[] = "<center><span class='tag is-".$is_valid_typ." is-small is-rounded tooltip' data-tooltip='".$is_valid_tooltip."'>".$is_valid."</span></center>";

                		$form_buttons = "<div class='buttons is-centered'>";

                		if(($post->pi_isactive!=0)&&($post->pi_isvalid!=0)){
                        $hidden = array('iid' => $post->pi_iid);
                        $form_buttons.= form_open('/activations/deactivate','',$hidden)."<button type='submit' class='button is-warning is-small tooltip' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Mark as Inactive'><i class='fas fa-times'></i></button></form>&nbsp;";
                        }elseif($post->pi_isvalid!=0){
                        $hidden = array('iid' => $post->pi_iid);
                        $form_buttons.= form_open('/activations/activate','',$hidden)."<button type='submit' class='button is-warning is-small tooltip' style='padding-top: 0px;padding-bottom: 0px;' data-tooltip='Mark as Active'><i class='fas fa-check'></i></button></form>&nbsp;";
                        }else{
                        $form_buttons.= "<form><button type='button' class='button is-warning is-small' style='padding-top: 0px;padding-bottom: 0px;' disabled><i class='fas fa-ban'></i></button></form>&nbsp;";
                        }

                		$hidden = array('iid' => $post->pi_iid);
          				$js = 'onSubmit="return ConfirmDelete(\'activation\');"';
      
    					$form_buttons.= form_open('/activations/delete',$js,$hidden)."<button type='submit' class='button is-danger is-small' style='padding-top: 0px;padding-bottom: 0px;'><i class='fa fa-trash'></i>&nbsp;Delete</button></form>";
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
                $activation = $this->installations_model->get_installation($this->input->post('iid'));
                $product = $this->products_model->get_product($activation['pi_product']);
				if($this->installations_model->delete_installation()){
                $this->user_model->add_log('Client <b>'.$activation['pi_username'].'\'s</b> activation of product <b>'.$product['pd_name'].'</b> using license <b>'.$activation['pi_purchase_code'].'</b> on URL <a href="'.$activation['pi_url'].'">'.remove_http_www($activation['pi_url']).'</a> deleted by '.ucfirst($this->session->userdata('fullname')).'.');
				$this->session->set_flashdata('installations_status', array(
        			'type'  => "primary",
        			'message' => "Activation log was successfully deleted."
					));
				redirect('activations');
				}else{
				$this->session->set_flashdata('installations_status', array(
        			'type'  => "danger",
        			'message' => "An error occured, Activation log was not deleted."
					));
				redirect('activations');
				}
			}

            public function activate(){
                if($this->installations_model->activate_activation()){
                $activation = $this->installations_model->get_installation($this->input->post('iid'));
                $product = $this->products_model->get_product($activation['pi_product']);
                $this->user_model->add_log('Client <b>'.$activation['pi_username'].'\'s</b> activation of product <b>'.$product['pd_name'].'</b> using license <b>'.$activation['pi_purchase_code'].'</b> on URL <a href="'.$activation['pi_url'].'">'.remove_http_www($activation['pi_url']).'</a> marked as active by '.ucfirst($this->session->userdata('fullname')).'.');
                $this->session->set_flashdata('installations_status', array(
                    'type'  => "primary",
                    'message' => "Activation was successfully marked as active."
                    ));
                redirect('activations');
                }else{
                $this->session->set_flashdata('installations_status', array(
                    'type'  => "danger",
                    'message' => "An error occured, Activation was not marked as active."
                    ));
                redirect('activations');
                }
            }

            public function deactivate(){
                if($this->installations_model->deactivate_activation()){
                $activation = $this->installations_model->get_installation($this->input->post('iid'));
                $product = $this->products_model->get_product($activation['pi_product']);
                $this->user_model->add_log('Client <b>'.$activation['pi_username'].'\'s</b> activation of product <b>'.$product['pd_name'].'</b> using license <b>'.$activation['pi_purchase_code'].'</b> on URL <a href="'.$activation['pi_url'].'">'.remove_http_www($activation['pi_url']).'</a> marked as inactive by '.ucfirst($this->session->userdata('fullname')).'.');
                $this->session->set_flashdata('installations_status', array(
                    'type'  => "primary",
                    'message' => "Activation was successfully marked as inactive."
                    ));
                redirect('activations');
                }else{
                $this->session->set_flashdata('installations_status', array(
                    'type'  => "danger",
                    'message' => "An error occured, Activation was not marked as inactive."
                    ));
                redirect('activations');
                }
            }



		}

?>