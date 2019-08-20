<?php if(count(get_included_files()) ==1) exit("No direct script access allowed");
ini_set('max_execution_time', 0);
ini_set('memory_limit', '268435456');

define("LB_API_DEBUG", false);

if(!LB_API_DEBUG){
ini_set('display_errors', 0);
}

class LicenseBoxAPI {

  private $product_id;
  private $api_url;
  private $current_version;
  private $current_path;
  private $root_path;
  private $verify_type;
  private $api_key;
  private $license_file;
  private $verification_period;

  public function __construct()
  { 
    $this->product_id = '{[PID]}';
    $this->api_url = '{[URL]}';
    $this->current_version = '{[CUR]}';
    $this->current_path = realpath(__DIR__);
    $this->root_path = realpath($this->current_path.'/..');
    $this->verify_type = '{[ENV]}';
    $this->api_key = '{[KEY]}';
    $this->license_file = $this->current_path.'/.lic';
    $this->verification_period = {[CHECK]};
  }

  private function callAPI($method, $url, $data)
  {
    $curl = curl_init();
    switch ($method){
      case "POST":
      curl_setopt($curl, CURLOPT_POST, 1);
      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                         
        break;
      default:
      if($data)
        $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
    $this_ip = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'LB-API-KEY: '.$this->api_key, 'LB-URL: '.$this_url, 'LB-IP: '.$this_ip));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if(!$result&&!LB_API_DEBUG){
      $rs = array('status' => FALSE, 'message' => 'Connection to server failed or the server returned an error, please contact support.');
      return json_encode($rs);
    }
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if(!LB_API_DEBUG){
      if($http_status!=200){
        $rs = array('status' => FALSE, 'message' => 'Server returned an invalid response, please contact support.');
        return json_encode($rs);
      }
    }
    curl_close($curl);
    return $result;
  }

  public function check_connection(){
    $data_array =  array();
    $get_data = $this->callAPI('POST',$this->api_url.'api/check_connection_ext', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function get_current_version(){
   return $this->current_version;
  }

  public function get_latest_version(){
    $data_array =  array(
     "product_id"  => $this->product_id
    );
    $get_data = $this->callAPI('POST',$this->api_url.'api/latest_version', json_encode($data_array));
    $response = json_decode($get_data, true);
    return $response;
  }

  public function activate_license($license,$client, $create_lic = true){
    $data_array =  array(
     "product_id"  => $this->product_id,
     "license_code" => $license,
     "client_name" => $client,
     "verify_type" => $this->verify_type
    );
   $get_data = $this->callAPI('POST',$this->api_url.'api/activate_license', json_encode($data_array));
   $response = json_decode($get_data, true);
   if(!empty($create_lic)){
   if($response['status']) {
    $licfile = trim($response['lic_response']);
    file_put_contents($this->license_file, $licfile, LOCK_EX);
   }else{
    @chmod($this->license_file,0777);
    if(is_writeable($this->license_file))
    {
    unlink($this->license_file);
    }
   }
   }
   return $response;
  }

  public function verify_license($time_based_check = false, $license = false, $client = false){
     if(!empty($license)&&!empty($client)){
     $data_array =  array(
     "product_id"  => $this->product_id,
     "license_file" => null,
     "license_code" => $license,
     "client_name" => $client
   );
   }
   else{
    if(file_exists($this->license_file)){
      $data_array =  array(
     "product_id"  => $this->product_id,
     "license_file" => file_get_contents($this->license_file),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $data_array =  array();
    }
   } 
    $res = array('status' => TRUE, 'message' => 'Verified! Thanks for purchasing.');
    ob_start();
    session_start();
    if($time_based_check&&$this->verification_period>0){
    $type=$this->verification_period;
    $today = date('d-m-Y');
    if(empty($_SESSION["{[RND1]}"])){
      $_SESSION["{[RND1]}"] = '0000-00-00';
    }
    if($type==1){
    if($today>=$_SESSION["{[RND1]}"]){
      $get_data = $this->callAPI('POST',$this->api_url.'api/verify_license', json_encode($data_array));
      $res = json_decode($get_data, true);
      if($res['status']!=True){
      }else{
      $tomo = date('d-m-Y', strtotime($today. ' + 1 days'));
      $_SESSION["{[RND1]}"] = $tomo;
      }
    }
    }elseif ($type==7) {
    if($today>=$_SESSION["{[RND1]}"]){
      $get_data = $this->callAPI('POST',$this->api_url.'api/verify_license', json_encode($data_array));
      $res = json_decode($get_data, true);
      if($res['status']!=True){
      }else{
      $tomo = date('d-m-Y', strtotime($today. ' + 1 week'));
      $_SESSION["{[RND1]}"] = $tomo;
      }
    }
    }
    elseif ($type==30) {
    if($today>=$_SESSION["{[RND1]}"]){
      $get_data = $this->callAPI('POST',$this->api_url.'api/verify_license', json_encode($data_array));
      $res = json_decode($get_data, true);
      if($res['status']!=True){
      }else{
      $tomo = date('d-m-Y', strtotime($today. ' + 1 months'));
      $_SESSION["{[RND1]}"] = $tomo;
      }
    }
    }
    elseif ($type==365) {
    if($today>=$_SESSION["{[RND1]}"]){
      $get_data = $this->callAPI('POST',$this->api_url.'api/verify_license', json_encode($data_array));
      $res = json_decode($get_data, true);
      if($res['status']!=True){
      }else{
      $tomo = date('d-m-Y', strtotime($today. ' + 1 year'));
      $_SESSION["{[RND1]}"] = $tomo;
      }
    }
    }
    ob_end_clean();
    }else{
     $get_data = $this->callAPI('POST',$this->api_url.'api/verify_license', json_encode($data_array));
     $res = json_decode($get_data, true);
    }
    return $res;
  }

  public function check_update(){
   $data_array =  array(
     "product_id"  => $this->product_id,
     "current_version" => $this->current_version
   );
   $get_data = $this->callAPI('POST',$this->api_url.'api/check_update', json_encode($data_array));
   $response = json_decode($get_data, true);
   return $response;
  }

  public function deactivate_license($license = false, $client = false){
    if(!empty($license)&&!empty($client)){
     $data_array =  array(
     "product_id"  => $this->product_id,
     "license_file" => null,
     "license_code" => $license,
     "client_name" => $client
   );
   }
   else{
    if(file_exists($this->license_file)){
      $data_array =  array(
     "product_id"  => $this->product_id,
     "license_file" => file_get_contents($this->license_file),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $data_array =  array();
    }
   }
   $get_data = $this->callAPI('POST',$this->api_url.'api/deactivate_license', json_encode($data_array));
   $response = json_decode($get_data, true);
   if($response['status']) {
    @chmod($this->license_file,0777);
    if(is_writeable($this->license_file))
    {
    unlink($this->license_file);
    }
   }
   return $response;
  }

  public function download_update($update_id,$type,$version,$license = false, $client = false)
  { 
    if(!empty($license)&&!empty($client)){
     $data_array =  array(
     "license_file" => null,
     "license_code" => $license,
     "client_name" => $client
   );
   }
   else{
    if(file_exists($this->license_file)){
      $data_array =  array(
     "license_file" => file_get_contents($this->license_file),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $data_array =  array();
    }
   }
    ob_end_flush(); 
    ob_implicit_flush(true);  
    $version=str_replace(".","_",$version);
    ob_start();
    $source_size = $this->api_url."api/get_update_size/main/".$update_id; 
    echo "Preparing to download main update... <br>";
    echo '<script>document.getElementById(\'prog\').value = 1;</script>';
    ob_flush();
    echo "Main Update size : ".$this->getRemoteFilesize($source_size).", please don't refresh. <br>";
    echo '<script>document.getElementById(\'prog\').value = 5;</script>';
    ob_flush();
    $temp_progress = '';
    $ch = curl_init();
    $source = $this->api_url."api/download_update/main/".$update_id; 
    curl_setopt($ch, CURLOPT_URL, $source);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
    $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
    $this_ip = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->api_key, 'LB-URL: '.$this_url, 'LB-IP: '.$this_ip));
    curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
    curl_setopt($ch, CURLOPT_NOPROGRESS, false); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    echo "Downloading main update... <br>";
    echo '<script>document.getElementById(\'prog\').value = 10;</script>';
    ob_flush();
    $data = curl_exec ($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($http_status!=200){
      if($http_status==401){
      curl_close($ch);
      exit("<br> Your update period has ended or your license is invalid, contact support.");
      }else{
      curl_close($ch);
      exit("<br> API call returned an server side error or Requested resource was not found, contact support.");
      }
    }
    curl_close ($ch);
    $destination = $this->root_path."/update_main_".$version.".zip"; 
    $file = fopen($destination, "w+");
    if(!$file){
    exit('<br> Folder does not have write permission or the update file path could not be resolved, contact support.');
    }
    fputs($file, $data);
    fclose($file);
    echo '<script>document.getElementById(\'prog\').value = 65;</script>';
    ob_flush();
    $zip = new ZipArchive;
    $res = $zip->open($destination);
    if ($res === TRUE) {
        $zip->extractTo($this->root_path."/"); 
        $zip->close();
        unlink($destination);
        echo "Main update files downloaded and extracted. <br><br>";
        echo '<script>document.getElementById(\'prog\').value = 75;</script>';
        ob_flush();
    } else {
        echo 'Update zip extraction failed. <br><br>';
        ob_flush();
    }
    if($type==true){
      $source_size = $this->api_url."api/get_update_size/sql/".$update_id; 
      echo "Preparing to download SQL update... <br>";
      ob_flush();
      echo "SQL Update size : ".$this->getRemoteFilesize($source_size).", please don't refresh. <br>";
      echo '<script>document.getElementById(\'prog\').value = 85;</script>';
      ob_flush();
      $temp_progress = '';
      $ch = curl_init();
      $source = $this->api_url."api/download_update/sql/".$update_id;
      curl_setopt($ch, CURLOPT_URL, $source);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
      $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
      $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
      $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
      $this_ip = getenv('SERVER_ADDR')?:
        $_SERVER['SERVER_ADDR']?:
        getenv('REMOTE_ADDR')?:
        $_SERVER['REMOTE_ADDR']?:
        $this->get_ip_from_third_party();
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->api_key, 'LB-URL: '.$this_url, 'LB-IP: '.$this_ip));
      curl_setopt($ch, CURLOPT_NOPROGRESS, false); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      echo "Downloading SQL update... <br>";
      echo '<script>document.getElementById(\'prog\').value = 90;</script>';
      ob_flush();
      $data = curl_exec ($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($http_status!=200){
        curl_close($ch);
        exit("<br> API call returned an server side error or Requested resource was not found, contact support.");
      }
      curl_close ($ch);
      $destination = $this->root_path."/update_sql_".$version.".sql"; 
      $file = fopen($destination, "w+");
      if(!$file){
      exit('<br> Folder does not have write permission or the update sql file path could not be resolved, contact support.');
      }
      fputs($file, $data);
      fclose($file);
      echo "SQL update files downloaded. <br><br>";
      echo '<script>document.getElementById(\'prog\').value = 100;</script>';
      echo "Update successful, Please import the downloaded sql file in your current database.";
      ob_flush();
    }else{
      echo '<script>document.getElementById(\'prog\').value = 100;</script>';
      echo "Update successful, There were no SQL updates. So you can run the updated application directly.";
      ob_flush();
    }
    ob_end_flush(); 
  }

  private function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
  {
    static $prev = 0;
    if($download_size == 0){
        $progress = 0;
    } else {
        $progress = round( $downloaded * 100 / $download_size );
    }
    if(($progress!=$prev) && ($progress == 25))
    {   $prev = $progress;
        echo '<script>document.getElementById(\'prog\').value = 22.5;</script>';
        ob_flush();
    }
    if(($progress!=$prev) && ($progress == 50))
    {$prev=$progress;
        echo '<script>document.getElementById(\'prog\').value = 35;</script>';
        ob_flush();
    }
     if(($progress!=$prev) && ($progress == 75))
    {$prev=$progress;
        echo '<script>document.getElementById(\'prog\').value = 47.5;</script>';
        ob_flush();
    }
     if(($progress!=$prev) && ($progress == 100))
    {   $prev=$progress;
        echo '<script>document.getElementById(\'prog\').value = 60;</script>';
        ob_flush();
    }
  }

  private function get_real($url) 
  {
    $headers = get_headers($url);
    foreach($headers as $header) {
        if (strpos(strtolower($header),'location:') !== false) {
            return preg_replace('~.*/(.*)~', '$1', $header);
        }
    }
  }

  private function get_ip_from_third_party(){
      $ch = curl_init ();
      curl_setopt ($ch, CURLOPT_URL, "http://ipecho.net/plain");
      curl_setopt ($ch, CURLOPT_HEADER, 0);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec ($ch);
      curl_close ($ch);
      return $response;
  }

  private function getRemoteFilesize($url)
  {
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_NOBODY, TRUE);
    $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
    $this_ip = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->api_key, 'LB-URL: '.$this_url, 'LB-IP: '.$this_ip));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    $filesize = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    if ($filesize){
      switch ($filesize){
          case $filesize < 1024:
              $size = $filesize .' B'; break;
          case $filesize < 1048576:
              $size = round($filesize / 1024, 2) .' KB'; break;
          case $filesize < 1073741824:
              $size = round($filesize / 1048576, 2) . ' MB'; break;
          case $filesize < 1099511627776:
              $size = round($filesize / 1073741824, 2) . ' GB'; break;
      }
      return $size; 
    }
  }
}
?>