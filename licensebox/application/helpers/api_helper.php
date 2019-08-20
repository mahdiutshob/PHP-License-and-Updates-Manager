<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('verify_envato_purchase_code'))
{
function verify_envato_purchase_code($code_to_verify,$api_key) {
    $ch = curl_init();
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
	curl_setopt_array($ch, array(
    CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code=". $code_to_verify ."",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20, 
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . $api_key . "",
        "User-Agent: ". $agent .""
    )
));
	$output = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $output;
}
}