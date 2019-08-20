<?php 
class Products extends CI_Controller{

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
		$data['title'] = 'Manage Products';
		$data['products'] = $this->products_model->get_product();
		$this->load->view('templates/header',$data);
		$this->load->view('templates/menu');
		$this->load->view('products/index',$data);
		$this->load->view('templates/footer');
	}

			public function add(){
				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}

				if($this->products_model->check_product_exists($this->input->post('product_id'))){
						$this->session->set_flashdata('product_status', array(
							'type'  => "danger",
							'message' => "Product ID already exists, please recheck."
						));
						redirect('products/add');
				}
				$data['title'] = 'Add Product';
				$data['product_id'] = strtoupper(substr(MD5(microtime()), 0, 8));
				$this->form_validation->set_rules('name', 'Product name','required');
				$this->form_validation->set_rules('product_id', 'Product ID','required');
				$this->form_validation->set_rules('product_status', 'Product Status','required');
				if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/add',$data);
					$this->load->view('templates/footer');

				}
				else
				{
					if($this->products_model->add_product()){
						$this->user_model->add_log('New product <b>'.$this->input->post('name').'</b> added by '.ucfirst($this->session->userdata('fullname')).'.');
						$this->session->set_flashdata('product_status', array(
							'type'  => "primary",
							'message' => "Product was successfully added."
						));
						redirect('products');
					}else{
						$this->session->set_flashdata('product_status', array(
							'type'  => "danger",
							'message' => "An error occured, Please recheck entered values. Product was not added."
						));
						redirect('products/add');
					}
				}

			}

			public function generate_external_helper(){

				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}

				$data['products'] = $this->products_model->get_product();
				$data['api_keys'] = $this->user_model->get_api_keys(true);
				$data['title'] = 'Generate External Helper File';
				$this->form_validation->set_rules('product', 'Product','required');
				$this->form_validation->set_rules('key', 'API Key','required');
				if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/generate_external_helper',$data);
					$this->load->view('templates/footer');

				}
				else
				{
				if(strip_tags(trim($this->input->post('envato')))=='on'){
				$is_envato = "envato";
				}
				else
				{
				$is_envato = "non_envato";
				}
				$latest_version = $this->products_model->get_latest_version(strip_tags(trim($this->input->post('product'))));	

 				if(!empty($latest_version)){
          		$ver = $latest_version[0]['version']; }
          		else{
            	$ver = 'v1.0.0';
          		}

				$trans1 = array("{[PID]}" => strip_tags(trim($this->input->post('product'))), "{[URL]}" => base_url(), "{[KEY]}" => strip_tags(trim($this->input->post('key'))), "{[ENV]}" => $is_envato, "{[CUR]}" => $ver , "{[CHECK]}" => strip_tags(trim($this->input->post('period'))), "{[RND1]}" => substr(MD5(microtime()), 0, 10));

				$data['generated_code'] = strtr(file_get_contents(FCPATH.'/application/libraries/lb_external_helper_sample.php'), $trans1);
				$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/generate_external_helper',$data);
					$this->load->view('templates/footer');

				}

			}

			public function generate_internal_helper(){

				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}

				$data['api_keys'] = $this->user_model->get_internal_api_keys();
				$data['title'] = 'Generate Internal Helper File';
				$this->form_validation->set_rules('key', 'API Key','required');
				if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/generate_internal_helper',$data);
					$this->load->view('templates/footer');

				}
				else
				{

				$trans1 = array("{[URL]}" => base_url(), "{[KEY]}" => strip_tags(trim($this->input->post('key'))));

				$data['generated_code'] = strtr(file_get_contents(FCPATH.'/application/libraries/lb_internal_helper_sample.php'), $trans1);
				
				$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/generate_internal_helper',$data);
					$this->load->view('templates/footer');

				}

			}

			public function delete(){
				$this->installations_model->delete_installation_by_pid($this->input->post('product'));
				$this->downloads_model->delete_downloads_by_pid($this->input->post('product'));
				$this->products_model->delete_versions_by_pid($this->input->post('product'));
				$this->licenses_model->delete_licenses_by_pid($this->input->post('product'));
				$product = $this->products_model->get_product($this->input->post('product'));
				if($this->products_model->delete_product()){
					$this->user_model->add_log('Product <b>'.$product['pd_name'].'</b> deleted by '.ucfirst($this->session->userdata('fullname')).'.');
					$this->session->set_flashdata('product_status', array(
						'type'  => "primary",
						'message' => "Product was successfully deleted."
					));
					redirect('products');
				}else{
					$this->session->set_flashdata('product_status', array(
						'type'  => "danger",
						'message' => "An error occured, Product was not deleted."
					));
					redirect('products');
				}
			}

			public function edit(){
				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}

				if(empty($this->input->post('product'))){
					redirect('products');
				}

				$data['title'] = 'Edit Product';
				$data['product'] = $this->products_model->get_product($this->input->post('product'));
				$this->form_validation->set_rules('name', 'Product name','required');
				if($this->form_validation->run()===FALSE){
					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/edit',$data);
					$this->load->view('templates/footer');
				}
				else
				{
					if($this->products_model->edit_product()>0){
						$this->user_model->add_log('Product <b>'.$data['product']['pd_name'].'</b> edited by '.ucfirst($this->session->userdata('fullname')).'.');
						$this->session->set_flashdata('product_status', array(
							'type'  => "primary",
							'message' => "Product was successfully updated."
						));
						redirect('products');
					}else{

						$this->session->set_flashdata('product_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values or you haven't made any changes, Product was not updated."
						));
						redirect('products');
					}
				}

			}

			public function add_version(){
				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}	

				if(empty($this->input->post('product'))){
					redirect('products');
				}

				$product = $this->products_model->get_product($this->input->post('product'));
				if($this->products_model->check_version_exists($this->input->post('product'),$this->input->post('version'))){
					$this->session->set_flashdata('product_status', array(
						'type'  => "danger",
						'message' => "Version already exists, please recheck."
					));
					$this->session->set_flashdata('product_id', $this->input->post('product'));
					redirect('products/versions');
				}
				$data['title'] = 'Add new '.$product['pd_name'].' version';
				$this->form_validation->set_rules('product', 'Product ID','required');
				$this->form_validation->set_rules('version', 'Product version','required');
				$this->form_validation->set_rules('released', 'Released date','required');
				$this->form_validation->set_rules('changelog', 'Changelog','required');
				if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('products/add_version',$data);
					$this->load->view('templates/footer');

				}
				else
				{
					$config['upload_path']          = './version-files/';
					$config['allowed_types']        = 'zip';
					$config['max_size']             = 102400;
					$config['encrypt_name'] = true;

					$config1['upload_path']          = './version-files/';
					$config1['allowed_types']        = 'sql';
					$config1['max_size']             = 51200;
					$config1['encrypt_name'] = true;

					$this->load->library('upload', $config);

					if (!$this->upload->do_upload('main_file'))
					{
						$error = array('error' => $this->upload->display_errors());
						$product = $this->products_model->get_product($this->input->post('product'));
						$data['title'] = 'Add new '.$product['pd_name'].' version';
						$data['product'] = $product['pd_pid'];
						$this->session->set_flashdata('upload_status_main', array(
							'type'  => "danger",
							'message' => "An error occured, Main files was not uploaded."
						));
						$this->load->view('templates/header',$data);
						$this->load->view('templates/menu');
						$this->load->view('products/add_version',$data);
						$this->load->view('templates/footer');

					}
					else
					{

						$main_filename = $this->upload->data('file_name');
					}

					unset($this->upload);

					if (!empty($_FILES['sql_file']['name'])) {

						$this->load->library('upload', $config1);

						if (!$this->upload->do_upload('sql_file'))
						{
							$error = array('error' => $this->upload->display_errors());
							$product = $this->products_model->get_product($this->input->post('product'));
							$data['title'] = 'Add new '.$product['pd_name'].' version';
							$data['product'] = $product['pd_pid'];
							$this->session->set_flashdata('upload_status_sql', array(
								'type'  => "danger",
								'message' => "An error occured, SQL file was not uploaded."
							));
							$this->load->view('templates/header',$data);
							$this->load->view('templates/menu');
							$this->load->view('products/add_version',$data);
							$this->load->view('templates/footer');

						}
						else
						{

							$has_sql = true;
							$sql_filename = $this->upload->data('file_name');
						}

					}
					else{
						$has_sql = false;
						$sql_filename = null;
					}



					if(isset($main_filename)&&isset($has_sql))
					{
						if($this->products_model->add_version($main_filename,$sql_filename)){
							$this->user_model->add_log('New version <b>'.$this->input->post('version').'</b> added for product <b>'.$product['pd_name'].'</b> by '.ucfirst($this->session->userdata('fullname')).'.');
							$this->session->set_flashdata('product_status', array(
								'type'  => "primary",
								'message' => "Version was successfully added."
							));
							$this->session->set_flashdata('product_id', $this->input->post('product'));
							redirect('products/versions');
						}else{
							$this->session->set_flashdata('product_status', array(
								'type'  => "danger",
								'message' => "An error occured, Please recheck entered values. Version was not added."
							));
							redirect('products/versions/add');
						}
					}

				}

			}

			public function view_versions(){
				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}	
				$has_product = $this->session->flashdata('product_id');
				if(!empty($has_product))
				{
					$product_id = $has_product;
				}
				else{
					$product_id = $this->input->post('product');
				}
				if(empty($product_id)){
					redirect('products');
				}
				$product = $this->products_model->get_product($product_id);
				$data['title'] = 'Manage '.$product['pd_name'].' versions';
				$data['productid'] = $product_id;
				$data['versions'] = $this->products_model->get_version($product_id);
				$this->load->view('templates/header',$data);
				$this->load->view('templates/menu');
				$this->load->view('products/view_versions',$data);
				$this->load->view('templates/footer');
			}

			public function delete_version(){
				$product = $this->products_model->get_product($this->input->post('product'));
				$this->downloads_model->delete_downloads_by_vid($this->input->post('vid'));
				if($this->products_model->delete_version()){
					$this->user_model->add_log('<b>'.$product['pd_name'].'</b> version <b>'.$this->input->post('version').'</b> deleted by '.ucfirst($this->session->userdata('fullname')).'.');
					$this->session->set_flashdata('product_status', array(
						'type'  => "primary",
						'message' => "Version was successfully deleted."
					));
					$this->session->set_flashdata('product_id', $this->input->post('product'));
					redirect('products/versions');
				}else{
					$this->session->set_flashdata('product_status', array(
						'type'  => "danger",
						'message' => "An error occured, Version was not deleted."
					));
					$this->session->set_flashdata('product_id', $this->input->post('product'));
					redirect('products/versions');
				}
			}
		}

		?>