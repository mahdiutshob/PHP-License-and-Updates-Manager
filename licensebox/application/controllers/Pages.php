<?php 

	class Pages extends CI_Controller{

		public function __construct() {
	        parent::__construct();
	        if(!$this->session->userdata('logged_in')){
	            redirect('users/login');
	        }
    	}
    	
		public function view($page = 'dashboard'){
			if(!file_exists(APPPATH.'views/pages/'.$page.'.php')){
				$data['is_404'] = True;
				$data['title'] = 'Page not found';
				$this->load->view('templates/header',$data);
				$this->load->view('templates/404');
				$this->load->view('templates/footer');
			}
			else{
						if(!$this->session->userdata('logged_in')){
				$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Please, login first."
					));
				redirect('login');
			}
				$data['is_404'] = False;
				$data['title'] = ucfirst($page);

				$data['license_count'] = $this->licenses_model->get_licenses_count();
				$data['license_count_active'] = $this->licenses_model->get_active_licenses_count();
				$data['product_count'] = $this->products_model->get_products_count();
				$data['product_count_active'] = $this->products_model->get_active_products_count();
				$data['installation_count'] = $this->installations_model->get_installations_count();
				$data['installation_count_active'] = $this->installations_model->get_active_installations_count();
				$data['download_count'] = $this->downloads_model->get_downloads_count();
				$data['activity_logs'] = $this->user_model->get_activity_log();
				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('pages/'.$page,$data);
				$this->load->view('templates/footer');
			}
		}

			public function view_404(){
				$data['title'] = 'Page not found';
				$data['is_404'] = True;
				$this->load->view('templates/header',$data);
				$this->load->view('templates/404');
				$this->load->view('templates/footer');
		}


		}
	 
?>