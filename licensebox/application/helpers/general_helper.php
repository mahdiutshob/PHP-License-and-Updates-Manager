<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if(!function_exists('clr')){
    function clr($string) {
    $string = strtolower($string);
    $string = preg_replace("/[\s-]+/", " ", $string);
    $string = preg_replace("/[\s_]/", "-", $string);
    $string = str_replace(".","_",$string);
    return $string;
}
}

if(!function_exists('validate_ips')){
function validate_ips($input) {
    foreach(explode(',', $input) as $ip)
        if (!filter_var($ip, FILTER_VALIDATE_IP))
            return false;
    return true;
}
}

if(!function_exists('validate_domains')){
function validate_domains($input) {
    foreach(explode(',', $input) as $domain)
        if($domain!='localhost'){
            if (!filter_var('lb@'.$domain, FILTER_VALIDATE_EMAIL))
            return false;
        }
    return true;
}
}

if(!function_exists('return_s')){
function return_s($no) {
   if($no!=1)
   {
    return "s";
   }
}
}

if ( ! function_exists('str_replace_first'))
{
    function str_replace_first($from, $to, $content)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $content, 1);
}
}

if ( ! function_exists('str_return_s'))
{
    function str_return_s($str, $no, $s, $typ)
{
    if($typ==1)
    {
   if($no>1)
    {
        return number_format($no)." ".$str.$s;
    }elseif($no==1){
        return $no." ".$str;
    }else{
          return "no ".$str;
    }
    }
    else
    {
   if($no>1)
    {
        return $str.$s;
    }elseif($no==1){
        return $str;
    }else{
          return $str;
    }
    }
}
}

if ( ! function_exists('thousandsCurrencyFormat'))
{
function thousandsCurrencyFormat($num) {
if($num>1000) {
  $x = round($num);
  $x_number_format = number_format($x);
  $x_array = explode(',', $x_number_format);
  $x_parts = array('k', 'm', 'b', 't');
  $x_count_parts = count($x_array) - 1;
  $x_display = $x;
  $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
  $x_display .= $x_parts[$x_count_parts - 1];
  return $x_display;
} else return $num;
}
}


if(!function_exists('generatedBreadcrumb')){
        function generateBreadcrumb($ch=null){
            $ci=&get_instance();
            $i=1;
            $uri = $ci->uri->segment($i);
            $link='
            <nav class="breadcrumb has-succeeds-separator" aria-label="breadcrumbs">
            <ul><li><a href="'.base_url().'">Home</a></li>';

            while($uri != ''){
            $prep_link = '';
            for($j=1; $j<=$i; $j++){
            $prep_link.=$ci->uri->segment($j).'/';
            }

            if($ci->uri->segment($i+1)== ''){
                if($ch){
                $link.='<li class="is-active"><a href="'.site_url($prep_link).'">';
                $link.=ucfirst($ch).'</a></li>';
                }else{
                $link.='<li class="is-active"><a href="'.site_url($prep_link).'">';
                $link.=ucfirst($ci->uri->segment($i)).'</a></li>';
                }
            }else{
                $link.='<li><a href="'.site_url($prep_link).'">';
                $link.=ucfirst($ci->uri->segment($i)).'</a><span class="divider"></span></li>';
            }

            $i++;
            $uri = $ci->uri->segment($i);
            }
            $link .='</ul></nav>';
            return $link;
            }
    }
if(!function_exists('str_truncate_middle')){
function str_truncate_middle($text, $maxChars = 25, $filler = '...')
{
    $length = strlen($text);
    $fillerLength = strlen($filler);

    return ($length > $maxChars)
        ? substr_replace($text, $filler, ($maxChars - $fillerLength) / 2, $length - $maxChars + $fillerLength)
        : $text;
}
}

if(!function_exists('remove_http_www')){
function remove_http_www($input){
$url = $input;
$url = filter_var($url, FILTER_SANITIZE_URL);
if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
$input = trim($input, '/');
if (!preg_match('#^http(s)?://#', $input)) {
    $input = 'http://' . $input;
}
$urlParts = parse_url($input);
$domain = preg_replace('/^www\./', '', $urlParts['host']);
if(!empty($urlParts['path'])){
$domain .= $urlParts['path'];
}
return $domain;
} else {
return $input;
}

}
}

if(!function_exists('obfuscate_email')){
function obfuscate_email($email)
{
    $em   = explode("@",$email);
    $name = implode(array_slice($em, 0, count($em)-1), '@');
    $len  = floor(strlen($name)/2);

    return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);   
}
}

if(!function_exists('clean_html_codes')){
function clean_html_codes($html)
{
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $script = $dom->getElementsByTagName('script');
    $remove = [];
    foreach($script as $item)
    {
        $remove[] = $item;
    }
    foreach ($remove as $item)
    {
        $item->parentNode->removeChild($item); 
    }
    return preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());   
}
}

if(!function_exists('validate_date')){
function validate_date($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
}