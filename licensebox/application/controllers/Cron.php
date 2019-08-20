<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APPPATH.'third_party/PHPMailer/src/Exception.php';
require APPPATH.'third_party/PHPMailer/src/PHPMailer.php';
require APPPATH.'third_party/PHPMailer/src/SMTP.php';

class Cron extends CI_Controller {
 
    function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
    }
 
    public function run()
    {  $this->load->helper('api_helper');
       $licenses = $this->licenses_model->get_license();
       $licenses_touched = 0;
       $total_mails_sent = 0;
       $has_envato_licenses = false;
       $today = date('Y-m-d');

       foreach ($licenses as $license){

            if(!empty($this->user_model->get_config_from_db('envato_api_token'))){
                $product = $this->products_model->get_product($license['pid']);
                $has_envato_id = $product['envato_id'];
                $force_env_check = false;

                if(!$license['is_envato']&&(preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $license['license_code']))){
                    $check_env_response = verify_envato_purchase_code($license['license_code'],$this->user_model->get_config_from_db('envato_api_token'));
                     if(isset($check_env_response['buyer'])){
                        if(!empty($has_envato_id)&&($check_env_response['item']['id']!=$has_envato_id)){       
                        }else{
                            $this->cron_model->mark_envato($license['license_code']);
                            $force_env_check = true;
                        }
                     }
                }

                if($license['is_envato']||$force_env_check){
                    $has_envato_licenses = true;
                    $response = verify_envato_purchase_code($license['license_code'],$this->user_model->get_config_from_db('envato_api_token'));

                    if(isset($response['message'])){
                        $envato_api_not_set = true;
                    }else{

                     if(isset($response['buyer'])) {
                        $old_date = new DateTime($license['supported_till'], new DateTimeZone(date_default_timezone_get()));
                        $old_date->setTimezone(new DateTimeZone('Australia/Brisbane'));
                        $old_date_envato = $old_date->format('Y-m-d');
                        $envato_raw_date = $response['supported_until'];
                        $new_date = new DateTime("$envato_raw_date");
                        $new_date_envato=$new_date->format('Y-m-d');
                        $licenses_touched0 = false;
                        if($old_date_envato!=$new_date_envato){
                            $this->cron_model->update_support_date($license['license_code'],$response['supported_until']);
                            $this->user_model->add_log('Envato has updated client <b>'.$license['client'].'</b>\'s purchase code <b>'.$license['license_code'].'</b> support date, client may have renewed support, therefore the support date for this license was updated.');
                            $licenses_touched0 = true;
                        }
                        if($response['buyer']!=$license['client']){
                            $this->cron_model->update_username($license['license_code'],$response['buyer']);
                            $this->installations_model->change_activation_client($license['license_code'],$license['client'],$response['buyer']);
                            $this->user_model->add_log('Envato client <b>'.$license['client'].'</b> appears to have changed their envato username to <b>'.$response['buyer'].'</b>, therefore their license was modified to include new username.');
                            $licenses_touched0 = true;
                        }
                        if($licenses_touched0){
                          $licenses_touched++;  
                        }
                     }

                     if(!isset($response['buyer'])&&(isset($response['description']))){
                        $this->cron_model->mark_invalid($license['license_code']);
                        $this->cron_model->mark_nonenvato($license['license_code']);
                        $this->user_model->add_log('Envato has marked client <b>'.$license['client'].'</b>\'s purchase code <b>'.$license['license_code'].'</b> as invalid, there may have been a sale reversal or a refund, therefore this license is now blocked.');
                        $licenses_touched++;
                     }
                    }
                }

            }else{
                $envato_api_not_set = true;
            }

            if(!empty($license['email'])){
        
            $mail_type = null;
            $is_mail_sent = false;

            if($this->user_model->get_config_from_db('license_expiring_enable')==1){
                if(!empty($license['expiry'])){
                $last_sent_mail = $this->cron_model->check_mail_sent($license['license_code'],$license['email'], 'license_expiring');
                if(($today==date('Y-m-d', strtotime($license['expiry'])))&&(empty($last_sent_mail))){
                    $mail_type = "license_expiring";
                    $is_mail_sent = $this->send_mail_to_client($mail_type,$license);
                    if($is_mail_sent){
                    $total_mails_sent++;
                    $this->user_model->add_log('Automated license expiring email sent to client <b>'.$license['client'].'</b> having email address <b>'.$license['email'].'</b>.');
                        if($mail_type=='new_update'){
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type, $latest_version);
                        }else{
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type);
                        }
                    }
                }
            }
            }

            if($this->user_model->get_config_from_db('support_expiring_enable')==1){
                if(!empty($license['supported_till'])){
                $last_sent_mail = $this->cron_model->check_mail_sent($license['license_code'],$license['email'], 'support_expiring');
                if(($today==date('Y-m-d', strtotime($license['supported_till'])))&&(empty($last_sent_mail))){
                    $mail_type = "support_expiring";
                   $is_mail_sent = $this->send_mail_to_client($mail_type,$license);
                    if($is_mail_sent){
                    $total_mails_sent++;
                    $this->user_model->add_log('Automated support expiring email sent to client <b>'.$license['client'].'</b> having email address <b>'.$license['email'].'</b>.');
                        if($mail_type=='new_update'){
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type, $latest_version);
                        }else{
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type);
                        }
                    }
                }
            }
            }

            if($this->user_model->get_config_from_db('updates_expiring_enable')==1){
                if(!empty($license['updates_till'])){
                $last_sent_mail = $this->cron_model->check_mail_sent($license['license_code'],$license['email'], 'updates_expiring');
                if(($today==date('Y-m-d', strtotime($license['updates_till'])))&&(empty($last_sent_mail))){
                    $mail_type = "updates_expiring";
                   $is_mail_sent = $this->send_mail_to_client($mail_type,$license);
                    if($is_mail_sent){
                    $total_mails_sent++;
                    $this->user_model->add_log('Automated updates expiring email sent to client <b>'.$license['client'].'</b> having email address <b>'.$license['email'].'</b>.');
                        if($mail_type=='new_update'){
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type, $latest_version);
                        }else{
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type);
                        }
                    }
                }
            }
            }

            if($this->user_model->get_config_from_db('new_update_enable')==1){
                $res_latest_version = $this->products_model->get_latest_version($license['pid']);
                if(!empty($res_latest_version)){
                        $latest_version = $res_latest_version[0]['version'];
                    }else{
                        $latest_version = null;
                    }
                if(!empty($latest_version)){
                $last_sent_mail = $this->cron_model->check_mail_sent($license['license_code'],$license['email'], 'new_update', $latest_version);
                if(!empty($license['updates_till'])){
                if(($today>=date('Y-m-d', strtotime($license['updates_till'])))&&(empty($last_sent_mail))){
                    $update_valid = false;
                }elseif(empty($last_sent_mail)){
                    $update_valid = true;
                }else{
                    $update_valid = false;
                }
            }elseif(empty($last_sent_mail)){
                $update_valid = true;
            }else{
                $update_valid = false;
            }
                }else{
                $update_valid = false;
            }
                if($update_valid)
                {
                    $mail_type = "new_update";
                    $is_mail_sent = $this->send_mail_to_client($mail_type,$license);
                    if($is_mail_sent){
                    $total_mails_sent++;
                    $this->user_model->add_log('Automated new update released email sent to client <b>'.$license['client'].'</b> having email address <b>'.$license['email'].'</b>.');
                        if($mail_type=='new_update'){
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type, $latest_version);
                        }else{
                        $this->cron_model->mark_mail_sent($license['license_code'], $license['email'], $mail_type);
                        }
                    }
                }
            }
        }else{
            $is_mail_sent = false;
        }
        }

        $cr_response = "\r\nCron (".date('Y-m-d H:i:s').") execution completed, brief summary: \r\n ";
        if(isset($envato_api_not_set)){
            if($has_envato_licenses){
                $cr_response.="envato license checks were skipped as envato api key was invalid or not available. \r\n ";
            }else{
                $cr_response.="no envato licenses were found in the system.";
            }
        }else{
            if($licenses_touched<=0){
                $cr_response.="no envato licenses were found to be out-of-sync.";
            }elseif($licenses_touched==1){
                $cr_response.=$licenses_touched." envato license was out-of-sync and was updated.";
            }else{
                $cr_response.=$licenses_touched." envato licenses were out-of-sync and updated.";
            }
            $cr_response.=" \r\n ";
        }
        if($total_mails_sent<=0){
            $cr_response.="no automated emails were sent to clients.";
        }elseif($total_mails_sent==1){
            $cr_response.=$total_mails_sent." automated email was sent to client.";
        }else{
            $cr_response.=$total_mails_sent." automated emails were sent to clients.";
        }

        echo $cr_response;
    }


        public function send_mail_to_client($mail_type,$license){
        if(!empty($mail_type)){
            $mail = new PHPMailer();
            $today = date('Y-m-d');
            $year = date('Y');
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

            $product = $this->products_model->get_product($license['pid']);
            $res_latest_version = $this->products_model->get_latest_version($license['pid']);
            if(!empty($res_latest_version)){
                $latest_version = $res_latest_version[0]['version'];
                $changelog = $res_latest_version[0]['changelog'];
            }else{
                $latest_version = null;
                $changelog = null;
            }

            $mail->setFrom($this->user_model->get_config_from_db('server_email'));
            $mail->addAddress($license['email'], ucfirst($license['client']));    
            $mail->isHTML(true); 

            $mail_subject = '{[product]} - Information'; 
            $trans00 = array("{[product]}" => ucfirst($product['pd_name']));       
            $mail_subject_final = strtr($mail_subject, $trans00);            
            $trans0 = array('{[email_format]}' => $this->user_model->get_config_from_db($mail_type)); 
            $trans = array("{[subject]}" => $mail_subject_final, "{[product]}" => ucwords($product['pd_name']), "{[client]}" => ucwords($license['client']), "{[version]}" => $latest_version, "{[changelog]}" => $changelog,"{[year]}" => $year); 
            $mail_template_raw = <<<MAIL
<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
    <!--[if gte mso 9]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml><![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='format-detection' content='date=no'>
    <meta name='format-detection' content='telephone=no'>
    <title>{[product]}</title>
    <style type='text/css'>
        body {
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%
        }
        
        table {
            border-spacing: 0
        }
        
        table td {
            border-collapse: collapse
        }
        
        .ExternalClass {
            width: 100%
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%
        }
        
        .ReadMsgBody {
            width: 100%;
            background-color: #ebebeb
        }
        
        table {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt
        }
        
        img {
            -ms-interpolation-mode: bicubic
        }
        
        .yshortcuts a {
            border-bottom: none !important
        }
        
        @media screen and (max-width: 599px) {
            .force-row,
            .container {
                width: 100% !important;
                max-width: 100% !important
            }
        }
        
        @media screen and (max-width: 400px) {
            .container-padding {
                padding-left: 12px !important;
                padding-right: 12px !important
            }
        }
        
        .ios-footer a {
            color: #aaa !important;
            text-decoration: underline
        }
        
        a[href^='x-apple-data-detectors:'],
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important
        }
    </style>
</head>

<body style='margin:0; padding:0;' bgcolor='#F0F0F0' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
    <table border='0' width='100%' height='100%' cellpadding='0' cellspacing='0' bgcolor='#F0F0F0'>
        <tr>
            <td align='center' valign='top' bgcolor='#F0F0F0' style='background-color: #F0F0F0;'>
                <br>
                <br>
                <table border='0' width='600' cellpadding='0' cellspacing='0' class='container' style='width:600px;max-width:600px'>
                    <tr>
                        <td class='container-padding header' align='left' style='font-family:Helvetica, Arial, sans-serif;font-size:24px;font-weight:bold;padding-bottom:18px;color:#DF4726;padding-left:24px;padding-right:24px'>{[product]}</td>
                    </tr>
                    <tr>
                        <td class='container-padding content' align='left' style='padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px;background-color:#ffffff'>
                            <br>
                            <div class='body-text' style='font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333'>{[email_format]}
                                <br>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class='container-padding footer-text' align='left' style='font-family:Helvetica, Arial, sans-serif;font-size:12px;line-height:16px;color:#aaaaaa;padding-left:24px;padding-right:24px;padding-bottom:5px;'>
                            <br>
                            <center><small>This is a system generated email.</small></center>
                            <br>
                            <center>Copyright {[year]}, All Rights Reserved.</center>
                            <br>
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
MAIL;
            $mail_final0 = strtr($mail_template_raw, $trans0);

            $altmail_final0 = strtr("{[email_format]}", $trans0);

            $mail->Subject = $mail_subject_final;
            $mail->Body =  strtr($mail_final0, $trans);
            $mail->AltBody = strtr($altmail_final0, $trans);
            $is_mail_sent = true;
                if(!$mail->send()){
                    $is_mail_sent = false;
                }
            } catch (Exception $e) {
                    $is_mail_sent = false;
            }   
            }else{
                $is_mail_sent = false;
            }
            return $is_mail_sent;
    }
}