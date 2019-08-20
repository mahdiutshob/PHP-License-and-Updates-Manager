<?php
/* 
Plugin Name: LicenseBox Test WP Plugin
Description: This is a very simple WP plugin to illustrate the use of licensebox API for checking licenses and updates. If the provided license is valid the plugin will work which is it will display a simple message in all the posts footer. Kindly add the license code in plugin options page for testing.
Version: 1.2.1
Author: CodeMonks
Author URI: https://techdynamics.org
*/
if(!defined('ABSPATH')){
	exit;
}

//Load the helper file, to increase protection you can include some core functions of your plugin like innit() functions etc in this file before you obfuscate it.
require 'includes/lb_helper.php';

$api = new LicenseBoxAPI();
$res = $api->verify_license();

function funct1_lb_test($content_data)
{ global $res;
	if ($res['status']) {
    $content_data .= '<footer class="fancy-content"><b>Sample plugin to test LicenseBox is active, Thank you for purchasing.</b></footer>';}
    else{
    $content_data .= '<footer class="fancy-content"><b>You have not activated the Sample plugin yet or your License is invalid, Please buy a valid license and enter it in the plugin options page.</b></footer>';
    }
    return $content_data;

}

function plugin_core_innit(){
add_filter( 'the_content', 'funct1_lb_test' );
}


plugin_core_innit();

include 'licensebox-test-options.php';


// Obfuscate this page as-well for better protection.
// Thats it, I hope this would give you some idea of how you can integrate licensebox with your wordpress plugin
