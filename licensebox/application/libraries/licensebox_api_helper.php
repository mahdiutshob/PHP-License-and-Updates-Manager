<?php if(count(get_included_files()) ==1) exit("No direct script access allowed");
ini_set('max_execution_time', 0);
ini_set('memory_limit', '268435456');

define("LB_API_DEBUG", false);

if(!LB_API_DEBUG){
ini_set('display_errors', 0);
}

class LicenseBoxAPI {

  private $aqaotDdAci;
  private $tEweWJDSXS;
  private $SsvyypPipX;
  private $hLHkWIsbdA;
  private $rQDQkfieUM;
  private $AVfeuoTXOE;
  private $GUxhNvWuzT;
  private $oByCgZnQEC;
  private $YGmWZkWTEg;

  public function __construct()
  { 
    $this->aqaotDdAci = 'B2A17YLB';
    $this->tEweWJDSXS = 'https://techdynamics.org/license/';
    $this->SsvyypPipX = 'v1.2.1';
    $this->hLHkWIsbdA = realpath(__DIR__);
    $this->rQDQkfieUM = realpath($this->hLHkWIsbdA.'/../..');
    $this->AVfeuoTXOE = 'envato';
    $this->GUxhNvWuzT = '9ACFA2F7A705ED26672B';
    $this->oByCgZnQEC = $this->hLHkWIsbdA.'/.lic';
    $this->YGmWZkWTEg = 0;
  }

  private function callAPI($V0rcsnldbyat, $Vppu2gcpharw, $V3wp4hxs1h55)
  {
    $Vsdr0qa32uqc = curl_init();
    switch ($V0rcsnldbyat){
      case "POST":
      curl_setopt($Vsdr0qa32uqc, CURLOPT_POST, 1);
      if ($V3wp4hxs1h55)
        curl_setopt($Vsdr0qa32uqc, CURLOPT_POSTFIELDS, $V3wp4hxs1h55);
        break;
      case "PUT":
        curl_setopt($Vsdr0qa32uqc, CURLOPT_CUSTOMREQUEST, "PUT");
      if ($V3wp4hxs1h55)
        curl_setopt($Vsdr0qa32uqc, CURLOPT_POSTFIELDS, $V3wp4hxs1h55);                         
        break;
      default:
      if($V3wp4hxs1h55)
        $Vppu2gcpharw = sprintf("%s?%s", $Vppu2gcpharw, http_build_query($V3wp4hxs1h55));
    }
    $Vxjv54rtakzi = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $Vnf4nvfeu2ti = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $V55llgs3xucn = $Vnf4nvfeu2ti.$Vxjv54rtakzi.$_SERVER['REQUEST_URI'];
    $Vldgjer5q3fq = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($Vsdr0qa32uqc, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'LB-API-KEY: '.$this->GUxhNvWuzT, 'LB-URL: '.$V55llgs3xucn, 'LB-IP: '.$Vldgjer5q3fq));
    curl_setopt($Vsdr0qa32uqc, CURLOPT_URL, $Vppu2gcpharw);
    curl_setopt($Vsdr0qa32uqc, CURLOPT_RETURNTRANSFER, 1);
    $Vsqgpusbszc1 = curl_exec($Vsdr0qa32uqc);
    if(!$Vsqgpusbszc1&&!LB_API_DEBUG){
      $Vihadwqm3l5o = array('status' => FALSE, 'message' => 'Connection to server failed or the server returned an error, please contact support.');
      return json_encode($Vihadwqm3l5o);
    }
    $Vdi5bg3zye53 = curl_getinfo($Vsdr0qa32uqc, CURLINFO_HTTP_CODE);
    if(!LB_API_DEBUG){
      if($Vdi5bg3zye53!=200){
        $Vihadwqm3l5o = array('status' => FALSE, 'message' => 'Server returned an invalid response, please contact support.');
        return json_encode($Vihadwqm3l5o);
      }
    }
    curl_close($Vsdr0qa32uqc);
    return $Vsqgpusbszc1;
  }

  public function get_current_version(){
   return $this->SsvyypPipX;
  }

  public function get_latest_version(){
    $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci
    );
    $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/latest_version', json_encode($V3wp4hxs1h55_array));
    $Vvfdrruicq41 = json_decode($Vyovz30ncwu4, true);
    return $Vvfdrruicq41;
  }

  public function activate_license($Vd5fokn5bsqx,$V4ho5koorrgi,$V2k2qrms2qft,$Vvl3xiyfaxoa = true){
    $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "license_code" => $Vd5fokn5bsqx,
     "client_name" => $V4ho5koorrgi,
     "email" => $V2k2qrms2qft,
     "verify_type" => $this->AVfeuoTXOE
    );
   $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/activate_license', json_encode($V3wp4hxs1h55_array));
   $Vvfdrruicq41 = json_decode($Vyovz30ncwu4, true);
   if(!empty($Vvl3xiyfaxoa)){
   if($Vvfdrruicq41['status']) {
    $V01v0yfwh0cx = trim($Vvfdrruicq41['lic_response']);
    file_put_contents($this->oByCgZnQEC, $V01v0yfwh0cx, LOCK_EX);
   }else{
    @chmod($this->oByCgZnQEC,0777);
    if(is_writeable($this->oByCgZnQEC))
    {
    unlink($this->oByCgZnQEC);
    }
   }
   }
   return $Vvfdrruicq41;
  }

  public function verify_license($Vm4utrqike4j = false, $Vd5fokn5bsqx = false, $V4ho5koorrgi = false){
     if(!empty($Vd5fokn5bsqx)&&!empty($V4ho5koorrgi)){
     $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "license_file" => null,
     "license_code" => $Vd5fokn5bsqx,
     "client_name" => $V4ho5koorrgi
   );
   }
   else{
    if(file_exists($this->oByCgZnQEC)){
      $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "license_file" => file_get_contents($this->oByCgZnQEC),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $V3wp4hxs1h55_array =  array();
    }
   } 
    $Vn1wyddtzpcj = array('status' => TRUE, 'message' => 'Verified! Thanks for purchasing.');
    ob_start();
    session_start();
    if($Vm4utrqike4j&&$this->YGmWZkWTEg>0){
    $V3lkndiel5qx=$this->YGmWZkWTEg;
    $V3qnzk1xlhhs = date('d-m-Y');
    if(empty($_SESSION["gemk3e7qj5"])){
      $_SESSION["gemk3e7qj5"] = '0000-00-00';
    }
    if($V3lkndiel5qx==1){
    if($V3qnzk1xlhhs>=$_SESSION["gemk3e7qj5"]){
      $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/verify_license', json_encode($V3wp4hxs1h55_array));
      $Vn1wyddtzpcj = json_decode($Vyovz30ncwu4, true);
      if($Vn1wyddtzpcj['status']!=True){
      }else{
      $Vy2p3ekzpwo4 = date('d-m-Y', strtotime($V3qnzk1xlhhs. ' + 1 days'));
      $_SESSION["gemk3e7qj5"] = $Vy2p3ekzpwo4;
      }
    }
    }elseif ($V3lkndiel5qx==7) {
    if($V3qnzk1xlhhs>=$_SESSION["gemk3e7qj5"]){
      $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/verify_license', json_encode($V3wp4hxs1h55_array));
      $Vn1wyddtzpcj = json_decode($Vyovz30ncwu4, true);
      if($Vn1wyddtzpcj['status']!=True){
      }else{
      $Vy2p3ekzpwo4 = date('d-m-Y', strtotime($V3qnzk1xlhhs. ' + 1 week'));
      $_SESSION["gemk3e7qj5"] = $Vy2p3ekzpwo4;
      }
    }
    }
    elseif ($V3lkndiel5qx==30) {
    if($V3qnzk1xlhhs>=$_SESSION["gemk3e7qj5"]){
      $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/verify_license', json_encode($V3wp4hxs1h55_array));
      $Vn1wyddtzpcj = json_decode($Vyovz30ncwu4, true);
      if($Vn1wyddtzpcj['status']!=True){
      }else{
      $Vy2p3ekzpwo4 = date('d-m-Y', strtotime($V3qnzk1xlhhs. ' + 1 months'));
      $_SESSION["gemk3e7qj5"] = $Vy2p3ekzpwo4;
      }
    }
    }
    elseif ($V3lkndiel5qx==365) {
    if($V3qnzk1xlhhs>=$_SESSION["gemk3e7qj5"]){
      $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/verify_license', json_encode($V3wp4hxs1h55_array));
      $Vn1wyddtzpcj = json_decode($Vyovz30ncwu4, true);
      if($Vn1wyddtzpcj['status']!=True){
      }else{
      $Vy2p3ekzpwo4 = date('d-m-Y', strtotime($V3qnzk1xlhhs. ' + 1 year'));
      $_SESSION["gemk3e7qj5"] = $Vy2p3ekzpwo4;
      }
    }
    }
    ob_end_clean();
    }else{
     $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/verify_license', json_encode($V3wp4hxs1h55_array));
     $Vn1wyddtzpcj = json_decode($Vyovz30ncwu4, true);
    }
    return $Vn1wyddtzpcj;
  }

  public function check_update(){
   $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "current_version" => $this->SsvyypPipX
   );
   $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/check_update', json_encode($V3wp4hxs1h55_array));
   $Vvfdrruicq41 = json_decode($Vyovz30ncwu4, true);
   return $Vvfdrruicq41;
  }

  public function deactivate_license($Vd5fokn5bsqx = false, $V4ho5koorrgi = false){
    if(!empty($Vd5fokn5bsqx)&&!empty($V4ho5koorrgi)){
     $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "license_file" => null,
     "license_code" => $Vd5fokn5bsqx,
     "client_name" => $V4ho5koorrgi
   );
   }
   else{
    if(file_exists($this->oByCgZnQEC)){
      $V3wp4hxs1h55_array =  array(
     "product_id"  => $this->aqaotDdAci,
     "license_file" => file_get_contents($this->oByCgZnQEC),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $V3wp4hxs1h55_array =  array();
    }
   }
   $Vyovz30ncwu4 = $this->callAPI('POST',$this->tEweWJDSXS.'api/deactivate_license', json_encode($V3wp4hxs1h55_array));
   $Vvfdrruicq41 = json_decode($Vyovz30ncwu4, true);
   if($Vvfdrruicq41['status']) {
    @chmod($this->oByCgZnQEC,0777);
    if(is_writeable($this->oByCgZnQEC))
    {
    unlink($this->oByCgZnQEC);
    }
   }
   return $Vvfdrruicq41;
  }

  public function download_update($Vkszqc02zezo,$V3lkndiel5qx,$Vpcglnwif0u1,$Vd5fokn5bsqx = false, $V4ho5koorrgi = false)
  { 
    if(!empty($Vd5fokn5bsqx)&&!empty($V4ho5koorrgi)){
     $V3wp4hxs1h55_array =  array(
     "license_file" => null,
     "license_code" => $Vd5fokn5bsqx,
     "client_name" => $V4ho5koorrgi
   );
   }
   else{
    if(file_exists($this->oByCgZnQEC)){
      $V3wp4hxs1h55_array =  array(
     "license_file" => file_get_contents($this->oByCgZnQEC),
     "license_code" => null,
     "client_name" => null
   );
    }else{
      $V3wp4hxs1h55_array =  array();
    }
   }
    ob_end_flush(); 
    ob_implicit_flush(true);  
    $Vpcglnwif0u1=str_replace(".","_",$Vpcglnwif0u1);
    ob_start();
    $Vasip2lsz5vo = $this->tEweWJDSXS."api/get_update_size/main/".$Vkszqc02zezo; 
    echo "Preparing to download main update... <br>";
    echo '<script>document.getElementById(\'prog\').value = 1;</script>';
    ob_flush();
    echo "Main Update size : ".$this->getRemoteFilesize($Vasip2lsz5vo).", please don't refresh. <br>";
    echo '<script>document.getElementById(\'prog\').value = 5;</script>';
    ob_flush();
    $Vu1hxcmau3mj = '';
    $Vribsd35zwo5 = curl_init();
    $Vm03jspnndcu = $this->tEweWJDSXS."api/download_update/main/".$Vkszqc02zezo; 
    curl_setopt($Vribsd35zwo5, CURLOPT_URL, $Vm03jspnndcu);
    curl_setopt($Vribsd35zwo5, CURLOPT_POST, 1);
    curl_setopt($Vribsd35zwo5, CURLOPT_POSTFIELDS, $V3wp4hxs1h55_array);
    $Vxjv54rtakzi = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $Vnf4nvfeu2ti = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $V55llgs3xucn = $Vnf4nvfeu2ti.$Vxjv54rtakzi.$_SERVER['REQUEST_URI'];
    $Vldgjer5q3fq = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($Vribsd35zwo5, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->GUxhNvWuzT, 'LB-URL: '.$V55llgs3xucn, 'LB-IP: '.$Vldgjer5q3fq));
    curl_setopt($Vribsd35zwo5, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
    curl_setopt($Vribsd35zwo5, CURLOPT_NOPROGRESS, false); 
    curl_setopt($Vribsd35zwo5, CURLOPT_RETURNTRANSFER, 1);
    echo "Downloading main update... <br>";
    echo '<script>document.getElementById(\'prog\').value = 10;</script>';
    ob_flush();
    $V3wp4hxs1h55 = curl_exec ($Vribsd35zwo5);
    $Vdi5bg3zye53 = curl_getinfo($Vribsd35zwo5, CURLINFO_HTTP_CODE);
    if($Vdi5bg3zye53!=200){
      if($Vdi5bg3zye53==401){
      curl_close($Vribsd35zwo5);
      exit("<br> Your update period has ended or your license is invalid, contact support.");
      }else{
      curl_close($Vribsd35zwo5);
      exit("<br> API call returned an server side error or Requested resource was not found, contact support.");
      }
    }
    curl_close ($Vribsd35zwo5);
    $Vgnlxiilml3w = $this->rQDQkfieUM."/update_main_".$Vpcglnwif0u1.".zip"; 
    $Vta2nckjk4l4 = fopen($Vgnlxiilml3w, "w+");
    if(!$Vta2nckjk4l4){
    exit('<br> Folder does not have write permission or the update file path could not be resolved, contact support.');
    }
    fputs($Vta2nckjk4l4, $V3wp4hxs1h55);
    fclose($Vta2nckjk4l4);
    echo '<script>document.getElementById(\'prog\').value = 65;</script>';
    ob_flush();
    $Vwzmt5odfrz5 = new ZipArchive;
    $Vn1wyddtzpcj = $Vwzmt5odfrz5->open($Vgnlxiilml3w);
    if ($Vn1wyddtzpcj === TRUE) {
        $Vwzmt5odfrz5->extractTo($this->rQDQkfieUM."/"); 
        $Vwzmt5odfrz5->close();
        unlink($Vgnlxiilml3w);
        echo "Main update files downloaded and extracted. <br><br>";
        echo '<script>document.getElementById(\'prog\').value = 75;</script>';
        ob_flush();
    } else {
        echo 'Update zip extraction failed. <br><br>';
        ob_flush();
    }
    if($V3lkndiel5qx==true){
      $Vasip2lsz5vo = $this->tEweWJDSXS."api/get_update_size/sql/".$Vkszqc02zezo; 
      echo "Preparing to download SQL update... <br>";
      ob_flush();
      echo "SQL Update size : ".$this->getRemoteFilesize($Vasip2lsz5vo).", please don't refresh. <br>";
      echo '<script>document.getElementById(\'prog\').value = 85;</script>';
      ob_flush();
      $Vu1hxcmau3mj = '';
      $Vribsd35zwo5 = curl_init();
      $Vm03jspnndcu = $this->tEweWJDSXS."api/download_update/sql/".$Vkszqc02zezo;
      curl_setopt($Vribsd35zwo5, CURLOPT_URL, $Vm03jspnndcu);
      curl_setopt($Vribsd35zwo5, CURLOPT_POST, 1);
      curl_setopt($Vribsd35zwo5, CURLOPT_POSTFIELDS, $V3wp4hxs1h55_array);
      $Vxjv54rtakzi = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
      $Vnf4nvfeu2ti = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
      $V55llgs3xucn = $Vnf4nvfeu2ti.$Vxjv54rtakzi.$_SERVER['REQUEST_URI'];
      $Vldgjer5q3fq = getenv('SERVER_ADDR')?:
        $_SERVER['SERVER_ADDR']?:
        getenv('REMOTE_ADDR')?:
        $_SERVER['REMOTE_ADDR']?:
        $this->get_ip_from_third_party();
      curl_setopt($Vribsd35zwo5, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->GUxhNvWuzT, 'LB-URL: '.$V55llgs3xucn, 'LB-IP: '.$Vldgjer5q3fq));
      curl_setopt($Vribsd35zwo5, CURLOPT_NOPROGRESS, false); 
      curl_setopt($Vribsd35zwo5, CURLOPT_RETURNTRANSFER, 1);
      echo "Downloading SQL update... <br>";
      echo '<script>document.getElementById(\'prog\').value = 90;</script>';
      ob_flush();
      $V3wp4hxs1h55 = curl_exec ($Vribsd35zwo5);
      $Vdi5bg3zye53 = curl_getinfo($Vribsd35zwo5, CURLINFO_HTTP_CODE);
      if($Vdi5bg3zye53!=200){
        curl_close($Vribsd35zwo5);
        exit("<br> API call returned an server side error or Requested resource was not found, contact support.");
      }
      curl_close ($Vribsd35zwo5);
      $Vgnlxiilml3w = $this->rQDQkfieUM."/update_sql_".$Vpcglnwif0u1.".sql"; 
      $Vta2nckjk4l4 = fopen($Vgnlxiilml3w, "w+");
      if(!$Vta2nckjk4l4){
      exit('<br> Folder does not have write permission or the update sql file path could not be resolved, contact support.');
      }
      fputs($Vta2nckjk4l4, $V3wp4hxs1h55);
      fclose($Vta2nckjk4l4);
      define('BASEPATH', 'lets_update');
      define('ENVIRONMENT', 'production');

      $Vxflcu4fnynl = array('default' => array());

      require '../application/config/database.php';

      $Vjj5rxwxg4nb = "mysql:host=".$Vxflcu4fnynl['default']['hostname'].";dbname=".$Vxflcu4fnynl['default']['database'];
      $Vpyqmseuh53z = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
      $Vpelfjilx0ey = new PDO($Vjj5rxwxg4nb, $Vxflcu4fnynl['default']['username'], $Vxflcu4fnynl['default']['password'], $Vpyqmseuh53z);

      $Vgspoldgf511 = '';
      $Vwklhpnonix0 = file($Vgnlxiilml3w);
      foreach ($Vwklhpnonix0 as $Vbfp33a4wmjf) {
      if (substr($Vbfp33a4wmjf, 0, 2) == '--' || $Vbfp33a4wmjf == '')
      continue;
      $Vgspoldgf511 .= $Vbfp33a4wmjf;
      $Vcersr5x401j = false;
      if (substr(trim($Vbfp33a4wmjf), -1, 1) == ';') {
      $Vcersr5x401j = $Vpelfjilx0ey->query($Vgspoldgf511);
      $Vgspoldgf511 = '';
      }
      }

      $Vpelfjilx0ey->query("COMMIT;");

      @chmod($Vgnlxiilml3w,0777);
      if(is_writeable($Vgnlxiilml3w))
      {
      unlink($Vgnlxiilml3w);
      }
      echo "SQL update files downloaded. <br><br>";
      echo '<script>document.getElementById(\'prog\').value = 100;</script>';
      echo "Update successful, SQL updates were successfully imported.";
      ob_flush();
    }else{
      echo '<script>document.getElementById(\'prog\').value = 100;</script>';
      echo "Update successful, There were no SQL updates. So you can run the updated application directly.";
      ob_flush();
    }
    ob_end_flush(); 
  }

  public function download_sql($V1z1tfksvwmc){
    $Vribsd35zwo5 = curl_init();
    $Vm03jspnndcu = $this->tEweWJDSXS."get_sql/index.php?key=".$V1z1tfksvwmc; 
    curl_setopt($Vribsd35zwo5, CURLOPT_URL, $Vm03jspnndcu);
    curl_setopt($Vribsd35zwo5, CURLOPT_RETURNTRANSFER, 1);
    $V3wp4hxs1h55 = curl_exec ($Vribsd35zwo5);
    $Vdi5bg3zye53 = curl_getinfo($Vribsd35zwo5, CURLINFO_HTTP_CODE);
    if($Vdi5bg3zye53!=200){
    curl_close($Vribsd35zwo5);
    exit("API call returned an server side error or Requested resource was not found, contact support.");
    }
    curl_close ($Vribsd35zwo5);
    $Vgnlxiilml3w = $this->rQDQkfieUM."/install/database.sql"; 
    $Vta2nckjk4l4 = @fopen($Vgnlxiilml3w, "w+");
    if(!$Vta2nckjk4l4){
    exit('Install folder does not have write permission or the sql file path could not be resolved, contact support.');
    }
    fputs($Vta2nckjk4l4, $V3wp4hxs1h55);
    fclose($Vta2nckjk4l4);
  }

  private function progress($Vn1wyddtzpcjource,$Vjnrqoj2lisu, $Vtnmdkwsw5h1, $Vhypdbzepe3z, $Vdimahglbpuj)
  {
    static $Vamsgp0ugq5w = 0;
    if($Vjnrqoj2lisu == 0){
        $Vvubanlrp25v = 0;
    } else {
        $Vvubanlrp25v = round( $Vtnmdkwsw5h1 * 100 / $Vjnrqoj2lisu );
    }
    if(($Vvubanlrp25v!=$Vamsgp0ugq5w) && ($Vvubanlrp25v == 25))
    {   $Vamsgp0ugq5w = $Vvubanlrp25v;
        echo '<script>document.getElementById(\'prog\').value = 22.5;</script>';
        ob_flush();
    }
    if(($Vvubanlrp25v!=$Vamsgp0ugq5w) && ($Vvubanlrp25v == 50))
    {$Vamsgp0ugq5w=$Vvubanlrp25v;
        echo '<script>document.getElementById(\'prog\').value = 35;</script>';
        ob_flush();
    }
     if(($Vvubanlrp25v!=$Vamsgp0ugq5w) && ($Vvubanlrp25v == 75))
    {$Vamsgp0ugq5w=$Vvubanlrp25v;
        echo '<script>document.getElementById(\'prog\').value = 47.5;</script>';
        ob_flush();
    }
     if(($Vvubanlrp25v!=$Vamsgp0ugq5w) && ($Vvubanlrp25v == 100))
    {   $Vamsgp0ugq5w=$Vvubanlrp25v;
        echo '<script>document.getElementById(\'prog\').value = 60;</script>';
        ob_flush();
    }
  }

  private function get_real($Vppu2gcpharw) 
  {
    $Vmt3nwvbanvg = get_headers($Vppu2gcpharw);
    foreach($Vmt3nwvbanvg as $V03vpb3t0hfq) {
        if (strpos(strtolower($V03vpb3t0hfq),'location:') !== false) {
            return preg_replace('~.*/(.*)~', '$1', $V03vpb3t0hfq);
        }
    }
  }

  private function get_ip_from_third_party(){
      $Vribsd35zwo5 = curl_init ();
      curl_setopt ($Vribsd35zwo5, CURLOPT_URL, "http://ipecho.net/plain");
      curl_setopt ($Vribsd35zwo5, CURLOPT_HEADER, 0);
      curl_setopt ($Vribsd35zwo5, CURLOPT_RETURNTRANSFER, true);
      $Vvfdrruicq41 = curl_exec ($Vribsd35zwo5);
      curl_close ($Vribsd35zwo5);
      return $Vvfdrruicq41;
  }

  private function getRemoteFilesize($Vppu2gcpharw)
  {
    $Vsdr0qa32uqc = curl_init();
    curl_setopt ($Vsdr0qa32uqc, CURLOPT_HEADER, TRUE);
    curl_setopt($Vsdr0qa32uqc, CURLOPT_URL, $Vppu2gcpharw);
    curl_setopt ($Vsdr0qa32uqc, CURLOPT_NOBODY, TRUE);
    $Vxjv54rtakzi = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
    $Vnf4nvfeu2ti = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
    $V55llgs3xucn = $Vnf4nvfeu2ti.$Vxjv54rtakzi.$_SERVER['REQUEST_URI'];
    $Vldgjer5q3fq = getenv('SERVER_ADDR')?:
      $_SERVER['SERVER_ADDR']?:
      getenv('REMOTE_ADDR')?:
      $_SERVER['REMOTE_ADDR']?:
      $this->get_ip_from_third_party();
    curl_setopt($Vsdr0qa32uqc, CURLOPT_HTTPHEADER, array('LB-API-KEY: '.$this->GUxhNvWuzT, 'LB-URL: '.$V55llgs3xucn, 'LB-IP: '.$Vldgjer5q3fq));
    curl_setopt($Vsdr0qa32uqc, CURLOPT_RETURNTRANSFER, true);
    $Vsqgpusbszc1 = curl_exec($Vsdr0qa32uqc);
    $Vta2nckjk4l4size = curl_getinfo($Vsdr0qa32uqc, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    if ($Vta2nckjk4l4size){
      switch ($Vta2nckjk4l4size){
          case $Vta2nckjk4l4size < 1024:
              $Voh35lxczqyj = $Vta2nckjk4l4size .' B'; break;
          case $Vta2nckjk4l4size < 1048576:
              $Voh35lxczqyj = round($Vta2nckjk4l4size / 1024, 2) .' KB'; break;
          case $Vta2nckjk4l4size < 1073741824:
              $Voh35lxczqyj = round($Vta2nckjk4l4size / 1048576, 2) . ' MB'; break;
          case $Vta2nckjk4l4size < 1099511627776:
              $Voh35lxczqyj = round($Vta2nckjk4l4size / 1073741824, 2) . ' GB'; break;
      }
      return $Voh35lxczqyj; 
    }
  }
}
?>