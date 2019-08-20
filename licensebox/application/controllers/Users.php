<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APPPATH.'third_party/PHPMailer/src/Exception.php';
require APPPATH.'third_party/PHPMailer/src/PHPMailer.php';
require APPPATH.'third_party/PHPMailer/src/SMTP.php';

	class Users extends CI_Controller{

		public function login(){
			if($this->session->userdata('logged_in')){
				redirect('dashboard');
			}
			$data['title'] = 'Login';
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');
			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header', $data);
				$this->load->view('users/login', $data);
				$this->load->view('templates/footer');
			} else {
				
				$username = strip_tags(trim($this->input->post('username')));
				$password = strip_tags(trim($this->input->post('password')));
				$user_id = $this->user_model->login($username, $password);
				if($user_id){

					$user_data = $this->user_model->get_user($user_id);
					$user_data = array(
						'user_id' => $user_id,
						'username' => $username,
						'fullname' => $user_data['au_name'],
						'logged_in' => true
					);
					$this->session->set_userdata($user_data);
					$this->session->set_flashdata('login_status', array(
        			'type'  => "success",
        			'message' => "You have successfully logged in."
					));
					$this->user_model->add_log('Successful login from IP <b>'.$this->input->ip_address().'</b>.');
					redirect('dashboard');
				} else {
					$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Username or password is invalid."
					));
					$this->user_model->add_log('Failed login attempt from IP <b>'.$this->input->ip_address().'</b>.');
					redirect('login');
				}		
			}
		}

		public function forgot_password(){
			if($this->session->userdata('logged_in')){
				redirect('dashboard');
			}
			$data['title'] = 'Forgot Password?';
			$this->form_validation->set_rules('username', 'Username', 'required');
			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header', $data);
				$this->load->view('users/forgot_password', $data);
				$this->load->view('templates/footer');
			} else {

			$username = $this->input->post('username');
			$user_data = $this->user_model->get_user_from_username($username);
			if(!$user_data)
			{
				$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Username was not found in our records."
					));
				redirect('forgot_password');
			}

			$reset_time=date('Y-m-d H:i:s');
			$reset_exp=date('Y-m-d H:i:s',strtotime('+1 hour',strtotime($reset_time)));
			$reset_key=substr(str_shuffle(MD5(microtime())), 0, 35);
			$reset_keyf=password_hash($reset_key, PASSWORD_DEFAULT);

			$email = $user_data['au_email'];
			$cur_reset_exp=$user_data['au_reset_exp'];
			$user_name=$user_data['au_name'];
			$user_id=$user_data['au_uid'];

			if(!empty($cur_reset_exp)&&($reset_time<=$cur_reset_exp))
			{
				$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Password reset already requested."
					));
				redirect('forgot_password');
			}


			$mail = new PHPMailer();
			try {

            // If using SMTP, uncomment this code and add your mail host,username and password   
            /*$mail->isSMTP();                              
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;                              
            $mail->Username = '';           
            $mail->Password = '';                        
            $mail->SMTPSecure = 'tls';                 
            $mail->Port = 587;*/
            // SMTP End                                   

    		$mail->setFrom($this->user_model->get_config_from_db('server_email'));
    		$mail->addAddress($email, $user_name);    

    		$mail->isHTML(true);                                 
    		$mail->Subject = 'Reset Password - LicenseBox';
    		$clean_email=urlencode($email);
    		$trans = array("{[user]}" => $user_name, "{[site_url]}" => base_url(), "{[token]}" => $reset_key,"{[email]}" => $clean_email);
   			$mail->Body =  strtr("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><title>Reset Password - LicenseBox</title><style type='text/css'>body{padding-top:0 !important;padding-bottom:0 !important;padding-top:0 !important;padding-bottom:0 !important;margin:0 !important;width:100% !important;-webkit-text-size-adjust:100% !important;-ms-text-size-adjust:100% !important;-webkit-font-smoothing:antialiased !important}.footer-text {color:#382F2E;}.tableContent a{color:#382F2E}p,h1{color:#382F2E;margin:0}p{text-align:left;color:#7A7A7A;font-size:15px;font-weight:normal;line-height:19px}a.link1{color:#382F2E;text-decoration:none}a.link2{font-size:16px;text-decoration:none;color:#fff}h2{text-align:left;color:#222;font-size:19px;font-weight:normal}div,p,ul,h1{margin:0}.bgBody{background:#fff}.bgItem{background:#fff}@media only screen and (max-width:480px){table[class='MainContainer'],td[class='cell']{width:100% !important;height:auto !important}td[class='specbundle']{width:100% !important;float:left !important;font-size:13px !important;line-height:17px !important;display:block !important;padding-bottom:15px !important}td[class='spechide']{display:none !important}img[class='banner']{width:100% !important;height:auto !important}td[class='left_pad']{padding-left:15px !important;padding-right:15px !important}}@media only screen and (max-width:540px){table[class='MainContainer'],td[class='cell']{width:100% !important;height:auto !important}td[class='specbundle']{width:100% !important;float:left !important;font-size:13px !important;line-height:17px !important;display:block !important;padding-bottom:15px !important}td[class='spechide']{display:none !important}img[class='banner']{width:100% !important;height:auto !important}.font{font-size:18px !important;line-height:22px !important}.font1{font-size:18px !important;line-height:22px !important}}</style></head><body paddingwidth='0' paddingheight='0' style='padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;' offset='0' toppadding='0' leftpadding='0'><table bgcolor='#ffffff' width='100%' border='0' cellspacing='0' cellpadding='0' class='tableContent' align='center' style='font-family:Helvetica, Arial,serif;'><tbody><tr><td><table width='600' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#ffffff' class='MainContainer'><tbody><tr><td><table width='100%' border='0' cellspacing='0' cellpadding='0'><tbody><tr><td valign='top' width='40'>&nbsp;</td><td><table width='100%' border='0' cellspacing='0' cellpadding='0'><tbody><tr><td height='75' class='spechide'></td></tr><tr><td class='movableContentContainer ' valign='top'><div class='movableContent' style='border: 0px; padding-top: 0px; position: relative;'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tbody><tr><td height='35'></td></tr><tr><td><p style='text-align:center;margin:0;font-family:Georgia,Time,sans-serif;font-size:26px;color:#222222;'><span class='specbundle2'><span class='font1'>LicenseBox - Password Reset</span></span></p></td></tr></tbody></table></div><div class='movableContent' style='border: 0px; padding-top: 0px; position: relative;'><table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'><tr><td height='50'></td></tr><tr><td align='left'><div class='contentEditableContainer contentTextEditable'><div class='contentEditable' align='center'><p><span style='color:#222222;font-size: 15px'>Hello {[user]}</span></p></div></div></td></tr><tr><td height='15'></td></tr><tr><td align='left'><div class='contentEditableContainer contentTextEditable'><div class='contentEditable' align='center'><p> Let's reset your LicenseBox password! Click the link provided below for changing your account password.</p> <br><p>Password reset link will expire in an hour.</p></div></div></td></tr><tr><td height='35'></td></tr><tr><td align='center'><table><tr><td align='center' bgcolor='#1A54BA' style='background:#3273dc; padding:15px 18px;-webkit-border-radius: 4px; -moz-border-radius: 40px; border-radius: 40px;'><div class='contentEditableContainer contentTextEditable'><div class='contentEditable' align='center'> <a target='_blank' href='{[site_url]}reset_password/{[email]}/{[token]}' class='link2' style='color:#ffffff;'>Change Password</a></div></div></td></tr></table></td></tr><tr><td height='10'></td></tr></table></div><div class='movableContent' style='border: 0px; padding-top: 0px; position: relative;'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tbody><tr><td height='45'></tr><tr><td style='border-bottom:1px solid #DDDDDD;'></td></tr><tr><td height='25'></td></tr><tr><td style='font-size:12px;'><center><span class='footer-text'>Copyright 2018 <a class='link1' style='color:#222222;' href='https://www.techdynamics.org'>CodeMonks</a>, All Rights Reserved.</center></span></td></tr><tr><td height='88'></td></tr></tbody></table></div></td></tr></tbody></table></td><td valign='top' width='40'>&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>", $trans);
    			$mail->AltBody = strtr("To reset your LicenseBox password, please visit {[site_url]}reset_password/{[email]}/{[token]}", $trans);
    			if(!$mail->send()){
    			$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Password reset email was not sent, contact support."
					));
    			redirect('forgot_password');
    			}
    			$this->user_model->add_password_reset($user_id,$reset_keyf,$reset_exp);
    			$this->session->set_flashdata('login_status', array(
        			'type'  => "primary",
        			'message' => "Password reset instructions sent to ".obfuscate_email($email),
					));
				redirect('forgot_password');
				} catch (Exception $e) {
    			$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Password reset email was not sent, contact support."
					));
    			redirect('forgot_password');
				}	
			}
		}

		public function reset_password(){
			if(!empty($this->uri->segment(2))&&!empty($this->uri->segment(3))){
			$reset_key = strip_tags(trim($this->uri->segment(3)));
			$email = strip_tags(trim($this->uri->segment(2)));
			$user_data = $this->user_model->get_user_from_token($email, $reset_key);
			$reset_time=date('Y-m-d H:i:s');
			if(empty($user_data)&&empty($user_data['au_reset_exp'])&&($reset_time>$user_data['au_reset_exp']))
			{
				$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Reset token expired or is invalid."
					));
				redirect('forgot_password');
			}else{
			$data['title'] = 'Reset Password';
			$data['email'] = $email;
			$data['token'] = $reset_key;
			$this->form_validation->set_rules('new_password', 'New password', 'required');
			$this->form_validation->set_rules('password_confirm', 'Confirm password', 'required|matches[new_password]');
			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header', $data);
				$this->load->view('users/reset_password', $data);
				$this->load->view('templates/footer');
			}else{
					if($this->user_model->change_password($email)){
						$this->session->set_flashdata('login_status', array(
							'type'  => "primary",
							'message' => "Your password has changed, Please login."
						));
						$this->user_model->remove_password_reset($email);
						redirect('login');
					}else{
						$this->session->set_flashdata('login_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values, Password was not changed."
						));
						redirect('forgot_password');
					}
			}
			}
			}else{
				$this->session->set_flashdata('login_status', array(
        			'type'  => "danger",
        			'message' => "Reset token expired or is invalid."
					));
				redirect('forgot_password');
			}
		}

		public function logout(){
			if(!$this->session->userdata('logged_in')){
				redirect('login');
			}
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('username');
			$this->session->set_flashdata('login_status', array(
        			'type'  => "primary",
        			'message' => "You have successfully logged out."
					));

			redirect('login');
		}

		public function activities(){
			if(!$this->session->userdata('logged_in')){
				redirect('login');
			}
			$data['title'] = 'All Activity Logs';
			$data['activity_logs'] = $this->user_model->get_activity_logs();
			$this->load->view('templates/header',$data);
			$this->load->view('templates/menu');
			$this->load->view('users/activities',$data);
			$this->load->view('templates/footer');
		}

		public function get_activities()
			{		
                if(!$this->session->userdata('logged_in')){
                    redirect('users/login');
                }

				$columns = array( 
                            0 => 'al_log', 
                            1 => 'al_date'
                        );

				$limit = $this->input->post('length');
        		$start = $this->input->post('start');
        		$order = $columns[$this->input->post('order')[0]['column']];
        		$dir = $this->input->post('order')[0]['dir'];
  
        		$totalData = $this->user_model->get_activities_count();
           
        		$totalFiltered = $totalData; 
            
        		if(empty($this->input->post('search')['value']))
        		{            
            		$posts = $this->user_model->get_activities($limit,$start,$order,$dir);
        		}
        		else {
            		$search = $this->input->post('search')['value']; 
            		$posts =  $this->user_model->activity_search($limit,$start,$search,$order,$dir);
            		$totalFiltered = $this->user_model->activity_search_count($search);
        		}

        		$data = array();
        		if(!empty($posts))
        		{
            		foreach ($posts as $post)
            		{	$nestedData = null;
            			
            			$originalDate = $post->al_date;
            			$newDate = date($this->config->item('datetime_format'), strtotime($originalDate));
                		$nestedData[] = strip_tags($post->al_log);
                		$nestedData[] = $newDate;
                
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

		public function account(){

				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}

				$userid = $this->session->userdata('user_id');
				$data['title'] = 'Account Settings';
				$data['user'] = $this->user_model->get_user($userid);

				if($this->input->post('type') == 'general')
				{ 
					$this->form_validation->set_rules('full_name', 'Name','required');
					$this->form_validation->set_rules('username', 'Username','required');
					$this->form_validation->set_rules('email', 'Email','required');
					if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('users/account',$data);
					$this->load->view('templates/footer');
				}
				else
				{
					if($this->user_model->edit_user()){

						$this->session->set_userdata('username', $this->input->post('username'));
						$this->session->set_userdata('fullname', $this->input->post('full_name'));
						$this->session->set_flashdata('user_status', array(
							'type'  => "primary",
							'message' => "Account settings were successfully updated."
						));
						redirect('account');
					}else{

						$this->session->set_flashdata('user_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values or you haven't made any changes, Account settings were not updated."
						));
						redirect('account');
					}
				}

				}elseif($this->input->post('type') == 'password')
				{
					$this->form_validation->set_rules('current_password', 'Current Password','required');
					$this->form_validation->set_rules('new_password', 'New Password','required');
					if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('users/account',$data);
					$this->load->view('templates/footer');
				}
				else
				{

					$user_id = $this->user_model->login($this->session->userdata('username'), $this->input->post('current_password'));
					if($user_id){

						if($this->user_model->change_password()){
						$this->session->set_flashdata('user_status', array(
							'type'  => "primary",
							'message' => "Your password was successfully changed."
						));
						redirect('account');
					}else{

						$this->session->set_flashdata('user_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values or you haven't made any changes, Password was not changed."
						));
						redirect('account');
					}

					}
					else{

						$this->session->set_flashdata('user_status', array(
							'type'  => "danger",
							'message' => "Current password is incorrect, please recheck."
						));
						redirect('account');

					}
				}

				}else{

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('users/account',$data);
					$this->load->view('templates/footer');

				}

			}

		public function general(){

				if(!$this->session->userdata('logged_in')){
					redirect('users/login');
				}
				
				$data['title'] = 'General Settings';
				$data['license_format'] = $this->user_model->get_config_from_db('license_code_format');
				$data['envato_username'] = $this->user_model->get_config_from_db('envato_username');
				$data['envato_api_token'] = $this->user_model->get_config_from_db('envato_api_token');
				$data['server_email'] = $this->user_model->get_config_from_db('server_email');
				$data['api_rate_limit'] = $this->user_model->get_config_from_db('api_rate_limit');
				$data['api_blacklisted_ips'] = $this->user_model->get_config_from_db('blacklisted_ips');
				$data['api_blacklisted_domains'] = $this->user_model->get_config_from_db('blacklisted_domains');
				$data['license_expiring'] = $this->user_model->get_config_from_db('license_expiring');
				$data['license_expiring_enable'] = $this->user_model->get_config_from_db('license_expiring_enable');
				$data['updates_expiring'] = $this->user_model->get_config_from_db('updates_expiring');
				$data['updates_expiring_enable'] = $this->user_model->get_config_from_db('updates_expiring_enable');
				$data['support_expiring'] = $this->user_model->get_config_from_db('support_expiring');
				$data['support_expiring_enable'] = $this->user_model->get_config_from_db('support_expiring_enable');
				$data['new_update'] = $this->user_model->get_config_from_db('new_update');
				$data['new_update_enable'] = $this->user_model->get_config_from_db('new_update_enable');
				$data['failed_activation_logs'] = $this->user_model->get_config_from_db('failed_activation_logs');
				$data['failed_update_download_logs'] = $this->user_model->get_config_from_db('failed_update_download_logs');
				$data['api_keys'] = $this->user_model->get_api_keys();

				if($this->input->post('type') == 'general') 
				{ 
					$this->form_validation->set_rules('license_format', 'License code format','required');
					$this->form_validation->set_rules(
        			'api_blacklisted_ips', 'Blacklisted IP addresses',
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
			        'api_blacklisted_domains', 'Blacklisted domains',
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
					$this->load->view('users/general',$data);
					$this->load->view('templates/footer');
				}
				else
				{
					if($this->user_model->edit_config()){
						$this->session->set_flashdata('general_status', array(
							'type'  => "primary",
							'message' => "General settings were successfully updated."
						));
						redirect('general');
					}else{

						$this->session->set_flashdata('general_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values or you haven't made any changes, General settings were not updated."
						));
						redirect('general');
					}
				}

				}elseif($this->input->post('type') == 'api')
				{
					$this->form_validation->set_rules('api_key', 'API Key','required');
					if($this->form_validation->run()===FALSE){

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('users/general',$data);
					$this->load->view('templates/footer');
				}
				else
				{


						if($this->user_model->add_api_key()){
						$this->session->set_flashdata('general_status', array(
							'type'  => "primary",
							'message' => "API key was successfully added."
						));
						redirect('general');
					}else{

						$this->session->set_flashdata('general_status', array(
							'type'  => "danger",
							'message' => "Please recheck entered values, API Key was not added."
						));
						redirect('general');
					}
			
				}
			}else{

					$this->load->view('templates/header',$data);
					$this->load->view('templates/menu');
					$this->load->view('users/general',$data);
					$this->load->view('templates/footer');

				}
			}

			public function delete_api_key(){
				if($this->user_model->delete_api_key()){
					$this->session->set_flashdata('general_status', array(
						'type'  => "primary",
						'message' => "API key was successfully deleted."
					));
					redirect('general');
				}else{
					$this->session->set_flashdata('general_status', array(
						'type'  => "danger",
						'message' => "An error occured, API key was not deleted."
					));
					redirect('general');
				}
			}

		public function check_username_exists($username){
			$this->form_validation->set_message('check_username_exists', 'That username is taken. Please choose a different one');
			if($this->user_model->check_username_exists($username)){
				return true;
			} else {
				return false;
			}
		}

		public function check_email_exists($email){
			$this->form_validation->set_message('check_email_exists', 'That email is taken. Please choose a different one');
			if($this->user_model->check_email_exists($email)){
				return true;
			} else {
				return false;
			}
		}
	}