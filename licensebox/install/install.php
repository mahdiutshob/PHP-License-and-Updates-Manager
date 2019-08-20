<?php 
require_once('../application/libraries/licensebox_api_helper.php');
$installFile = "install.licensebox";

if (is_file($installFile)) {
	$api = new LicenseBoxAPI();

	$errors=FALSE;
	$today=date('Y-m-d');
  $csrf_token_rand=substr(str_shuffle(MD5(microtime())), 0, 10);
  $csrf_cookie_rand=substr(str_shuffle(MD5(microtime())), 0, 10);
  $session_rand=substr(str_shuffle(MD5(microtime())), 0, 10);
  $encrypt_rand=substr(str_shuffle(MD5(microtime())), 0, 20);
  $db_file='../application/config/database.php';
  $db_file_sample='database-sample.php';
  $database_dump_file = 'database.sql';
  $config_file='../application/config/config.php';
  $config_file_sample='config-sample.php';

  @chmod($installFile,0777);
  @chmod($config_file,0777);
  @chmod($config_file_sample,0777);
	$step = isset($_GET['step']) ? $_GET['step'] : '';
	?>

	<div class="container main_body"> 
  <div class="section">
		<div class="column is-6 is-offset-3">
			<center><h1 class="title p-t-lg p-b-lg m-b-sm" style="margin-right: -5px;margin-left: -5px;">
				Welcome to LicenseBox installation!
			</h1></center>
      <?php 
      $update_data = $api->check_update();
      if(!empty($update_data['version']))
      { ?>
      <center><article class="message is-warning">
      <div class="message-body">
       New LicenseBox version <?php echo $update_data['version']; ?> available, download <a href="https://codecanyon.net/downloads" target="_blank">here</a>!
      </div>
    </article></center><br>
      <?php }
      ?>
			<div class="box">
                  <?php
 switch ($step) {
        default:
                        ?>  
  <div class="tabs is-fullwidth">
    <ul>
      <li class="is-active">
        <a>
          <span><b>Requirements</b></span>
        </a>
      </li>
      <li>
        <a>
          <span>Verify</span>
        </a>
      </li>
      <li>
        <a>
          <span>Database</span>
        </a>
      </li>
      <li>
        <a>
          <span>Finish!</span>
        </a>
      </li>
    </ul>
  </div>                                                      
<?php 
if(version_compare(PHP_VERSION, '5.6.0') < 0)
{$errors = TRUE;
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Current PHP version is ".phpversion()."! minimum PHP 5.6 or higher required.</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> You are running PHP version ".phpversion()."</div>";}
 if(!extension_loaded('pdo'))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> PDO PHP extension missing!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> PDO PHP extension available</div>";}
 if(!extension_loaded('curl'))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Curl PHP extension missing!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Curl PHP extension available</div>";}
if(!extension_loaded('openssl'))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Openssl PHP extension missing!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Openssl PHP extension available</div>";}
 if(!is_writeable($db_file))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Database file (".$db_file.") is not writable!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Database file (".$db_file.") is writable</div>";}
 if(!is_writeable($config_file))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Configuration file (".$config_file.") is not writable!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Configuration file (".$config_file.") is writable</div>";}
 if(!is_writeable($installFile))
{$errors = TRUE; 
echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Installation file (".$installFile.") is not writable!</div>";}
else{echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Installation file (".$installFile.") is writable</div>";}
?>

<div style='text-align: right;'>
 <?php if($errors==TRUE){ ?>
            <a href="#" class="button is-link" disabled>Next</a>
            <?php }else{ ?>
            <a href="index.php?step=0" class="button is-link">Next</a>
            <?php } ?>
</div>                 
        <?php
        break;
        case "0": 
        ?>
        <div class="tabs is-fullwidth">
  <ul>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Requirements</span>
      </a>
    </li>
    <li class="is-active">
      <a>
        <span><b>Verify</b></span>
      </a>
    </li>
    <li>
      <a>
        <span>Database</span>
      </a>
    </li>
    <li>
      <a>
        <span>Finish!</span>
      </a>
    </li>
  </ul>
</div>   
        <?php
            $license_code = 'none';
            $client_name = 'none';
        if(!empty($_POST['license'])&&!empty($_POST['client'])&&!empty($_POST['email'])){
            $license_code = $_POST["license"];
            $client_name = $_POST["client"];

            $verify_response = $api->activate_license($_POST['license'],$_POST['client'],$_POST['email'],false);

            if(empty($verify_response))
            {
             $msg='Server is unavailable at the moment, please try again.';
            }
            else
            {
              $msg=$verify_response['message'];
            }

            if ($verify_response['status'] != 'true') {
                ?>
   <form action="index.php?step=0" method="POST">
   <div class="notification is-danger"><?php echo ucfirst($msg); ?></div>
  
  <div class="field">
<label class="label">Purchase code <small class="has-text-weight-normal has-text-grey"> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where is my purchase code?</a>)</small></label>
  <div class="control">
    <input class="input" type="text" placeholder="Enter your purchase code" name="license" required>
  </div>
</div>

 <div class="field">
  <label class="label">Envato username</label>
  <div class="control">
    <input class="input" type="text" placeholder="Enter your envato username" name="client" required>
  </div>
</div>

 <div class="field">
  <label class="label">Email address <small class="has-text-weight-normal has-text-grey"> (It helps us provide you with faster support.)</small></label>
  <div class="control">
    <input class="input" type="email" placeholder="Enter your email address" name="email" required>
  </div>
</div>
 <div style='text-align: right;'>
 
            <button type="submit" class="button is-link">Verify</button>
       
            
          
</div>
  </form>
                <?php 


            } else { 
?>
   <form action="index.php?step=1" method="POST">
   <div class="notification is-success"><?php echo ucfirst($msg); ?></div>
  <input type="hidden" name="prc3" id="prc3" value="<?php echo ucfirst($verify_response['status']); ?>">
  <input type="hidden" name="sql_data" id="sql_data" value="<?php echo $verify_response['data']; ?>">
<center><p>Click next to proceed with the installation.</p></center>
 <div style='text-align: right;'>
 
            <button type="submit" class="button is-link">Next</button>
       
            
          
</div>
  </form>
     
<?php
}


?>
<?php
}else{  ?>
   <form action="index.php?step=0" method="POST">
  
  <div class="field">
<label class="label">Purchase code <small class="has-text-weight-normal has-text-grey"> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where is my purchase code?</a>)</small></label>
  <div class="control">
    <input class="input" type="text" placeholder="Enter your purchase code" name="license" required>
  </div>
</div>

 <div class="field">
  <label class="label">Envato username</label>
  <div class="control">
    <input class="input" type="text" placeholder="Enter your envato username" name="client" required>
  </div>
</div>
<div class="field">
  <label class="label">Email address <small class="has-text-weight-normal has-text-grey"> (It helps us provide you with faster support.)</small></label>
  <div class="control">
    <input class="input" type="email" placeholder="Enter your email address" name="email" required>
  </div>
</div>
 <div style='text-align: right;'>
 
            <button type="submit" class="button is-link">Verify</button>
       
            
          
</div>
  </form>
  <?php } ?>
<?php
        break;
        case "1":
        ?>

     <div class="tabs is-fullwidth">
  <ul>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Requirements</span>
      </a>
    </li>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Verify</span>
      </a>
    </li>
    <li class="is-active">
      <a>
     
        <span><b>Database</b></span>
      </a>
    </li>
    <li>
      <a>
      
        <span>Finish!</span>
      </a>
    </li>
  </ul>
</div> 
<?php
    if($_POST&&isset($_POST["prc3"])&&!empty($_POST["sql_data"])){
    $valid = $_POST["prc3"];
    $sql_id = $_POST["sql_data"];
    $db_host = strip_tags(trim($_POST["host"]));
    $db_user = strip_tags(trim($_POST["user"]));
    $db_pass = strip_tags(trim($_POST["pass"]));
    $db_name = strip_tags(trim($_POST["name"]));
    if(!empty($db_host))
    {


try {

$pdof = new PDO("mysql:host=$db_host", $db_user, $db_pass);
$pdof->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mysql_ver = $pdof->query('select version()')->fetchColumn();
if(version_compare($mysql_ver, '5.6.0') < 0){
  ?>
<div class='notification is-danger'>You are running MySQL <?php echo $mysql_ver; ?>, minimum requirement is MySQL 5.6 or higher. Please upgrade and re-run the installation or contact support.</div>
  <?php
die();
}

$api->download_sql($sql_id);
$dbname = "`".str_replace("`","``",$db_name)."`";
$pdof->query("CREATE DATABASE IF NOT EXISTS $dbname");
$pdof->query("use $dbname");
$pdof->query("DROP TABLE IF EXISTS activity_log");
$pdof->query("DROP TABLE IF EXISTS auth_users");
$pdof->query("DROP TABLE IF EXISTS product_details");
$pdof->query("DROP TABLE IF EXISTS product_installations");
$pdof->query("DROP TABLE IF EXISTS product_licenses");
$pdof->query("DROP TABLE IF EXISTS product_versions");

$templine = '';
$lines = file($database_dump_file);
foreach ($lines as $line) {
if (substr($line, 0, 2) == '--' || $line == '')
continue;
$templine .= $line;
$query = false;
if (substr(trim($line), -1, 1) == ';') {
$query = $pdof->query($templine);
$templine = '';
}
}

$pdof->query("COMMIT;");

@chmod($database_dump_file,0777);
if(is_writeable($database_dump_file))
{
    unlink($database_dump_file);
}

$trans = array("{[DB_NAME]}" => $db_name, "{[DB_USER]}" => $db_user, "{[DB_PASS]}" => $db_pass, "{[DB_HOST]}" => $db_host);


 if(is_writeable($db_file))
{
file_put_contents($db_file,strtr(file_get_contents($db_file_sample), $trans));
}
else{
?>
<div class='notification is-danger'>Database file (<strong><?php echo $db_file; ?></strong>) is not writable, you should change the file permission first then retry this step or you can change the db settings yourself.</div>

<?php
}

$http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
$redir = $http_or_https;
$redir .= $_SERVER['HTTP_HOST'];
$redir .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
$redir = str_replace('/install/','',$redir); 
$trans1 = array("{[BASE_URL]}" => $redir, "{[CSRFT]}" => $csrf_token_rand, "{[CSRFC]}" => $csrf_cookie_rand, "{[ENCRK]}" => $encrypt_rand, "{[SESSC]}" => $session_rand);

 if(is_writeable($config_file))
{
file_put_contents($config_file,strtr(file_get_contents($config_file_sample), $trans1));
}
else{
?>
<div class='notification is-danger'>Configuration file (<strong><?php echo $config_file; ?></strong>) is not writable, you should change the file permission first then retry this step or you can change the config settings yourself.</div>

<?php
}

?>
   <form action="index.php?step=2" method="POST">
   <div class='notification is-success'>Database was successfully created.</div>
  <input type="hidden" name="prc4" id="prc4" value="true">
<center><p>Click next to complete the installation.</p></center>
 <div style='text-align: right;'>
            <button type="submit" class="button is-link">Next</button>
         </div>  
  </form> 
<?php
}
catch (PDOException $err) {
?>
<div class='notification is-danger'>Error connecting to the database, make sure the credentials are correct and the user has all the permissions.</div>
       <form action="index.php?step=1" method="POST">
            <input type="hidden" name="prc3" id="prc3" value="<?php echo $valid; ?>">
            <input type="hidden" name="sql_data" id="sql_data" value="<?php echo $sql_id; ?>">
      <div class="field">
  <label class="label">Database Host</label>
  <div class="control">
    <input class="input" type="text" id="host" placeholder="Your database host" name="host" required>
  </div>
</div>

      <div class="field">
  <label class="label">Database Username</label>
  <div class="control">
    <input class="input" type="text" id="user" placeholder="Your database username" name="user" required>
  </div>
</div>

      <div class="field">
  <label class="label">Database Password</label>
  <div class="control">
    <input class="input" type="text" id="pass" placeholder="Your database password" name="pass">
  </div>
</div>

      <div class="field">
  <label class="label">Database Name</label>
  <div class="control">
    <input class="input" type="text" id="name" placeholder="Your database name" name="name" required>
  </div>
</div>


 <div style='text-align: right;'>
 
            <button type="submit" class="button is-link">Create</button>
              
</div>
 <p class='help has-text-grey has-text-centered'>Database creation may take some time, please don't refresh.</p>
  </form>
<?php
}
}
else
{
  ?>
       <form action="index.php?step=1" method="POST">
            <input type="hidden" name="prc3" id="prc3" value="<?php echo $valid; ?>">
    <input type="hidden" name="sql_data" id="sql_data" value="<?php echo $sql_id; ?>">
      <div class="field">
  <label class="label">Database Host</label>
  <div class="control">
    <input class="input" type="text" id="host" placeholder="Your database host" name="host" required>
  </div>
</div>

      <div class="field">
  <label class="label">Database Username</label>
  <div class="control">
    <input class="input" type="text" id="user" placeholder="Your database username" name="user" required>
  </div>
</div>

      <div class="field">
  <label class="label">Database Password</label>
  <div class="control">
    <input class="input" type="text" id="pass" placeholder="Your database password" name="pass">
  </div>
</div>

      <div class="field">
  <label class="label">Database Name</label>
  <div class="control">
    <input class="input" type="text" id="name" placeholder="Your database name" name="name" required>
  </div>
</div>

 <div style='text-align: right;'>
 
    <button type="submit" class="button is-link">Create</button>
                
</div>
 <p class='help has-text-grey has-text-centered'>Database creation may take some time, please don't refresh.</p>
  </form>
  <?php
}
            ?>
            <?php
}
else
{
?>
<div class='notification is-danger'>Sorry, something went wrong. Please rerun the installation</div>
<?php
}
?>
 <?php
        break;
        case "2":
        ?>    
     <div class="tabs is-fullwidth">
  <ul>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Requirements</span>
      </a>
    </li>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Verify</span>
      </a>
    </li>
    <li>
      <a>
        <span><i class="fas fa-check-circle"></i> Database</span>
      </a>
    </li>
    <li class="is-active">
      <a>    
        <span><b>Finish!</b></span>
      </a>
    </li>
  </ul>
</div>
<?php 
                      if($_POST&&isset($_POST["prc4"])){
    $valid = $_POST["prc4"];


 if(is_writeable($installFile))
{
    unlink($installFile);
}
else{
?>
<div class='notification is-danger'>Installation file was not removed, you should manually delete (<strong><?php echo $installation_file; ?></strong>) file to disable installer.</div>

<?php
}?>

<center><p><strong>Application is successfully installed.</strong></p><br>
<p>You can Login using Username : <strong>admin</strong> and Password : <strong>admin1234</strong></p><br><strong>
<p><a class='button is-link' href='../'>Login</a></p></strong>
<br>
<p class='help has-text-grey'>We recommend you to change default password.</p>
</center>

<?php
}
else
{?>

<div class='notification is-danger'>Sorry, something went wrong. Please rerun the installation</div>
<?php
}
?>
<?php
        break;}
        ?>    
			</div>
      <center>
      <p class="has-text-grey p-b-sm"><small>You are installing LicenseBox <?php echo $api->get_current_version();?></small></p>
      <a class="has-text-grey-darker has-text-weight-semibold" href="mailto:teamcodemonks@gmail.com?subject=LicenseBox Installation">Need Help?</a>
      </center>
		</div>
	</div> </div>
<?php } else{ ?>
	<div class="container main_body"> <div class="section" >
		<center><h1 class="title p-t-xxl m-t-xl">
			LicenseBox installer is locked, check documentation.
		</h1></center>
	</div></div>
<?php } ?>