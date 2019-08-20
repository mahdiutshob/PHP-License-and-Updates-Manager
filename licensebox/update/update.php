<?php
if(count(get_included_files()) ==1) exit("No direct script access allowed");
require_once('../application/libraries/licensebox_api_helper.php');
$installFile = "update.licensebox";
define('ENVIRONMENT', 'production');
$ds = DIRECTORY_SEPARATOR;
define('BASEPATH', dirname(dirname(__FILE__)));
define('APPPATH', BASEPATH . $ds . 'application' . $ds);
define('LIBBATH', BASEPATH . "{$ds}system{$ds}libraries{$ds}Session{$ds}");
require_once LIBBATH . 'Session_driver.php';
require_once LIBBATH . "drivers{$ds}Session_files_driver.php";
require_once BASEPATH . "{$ds}system{$ds}core{$ds}Common.php";
$config = get_config();
if (empty($config['sess_save_path'])) {
    $config['sess_save_path'] = rtrim(ini_get('session.save_path'), '/\\');
}
$config = array(
    'cookie_lifetime'   => $config['sess_expiration'],
    'cookie_name'       => $config['sess_cookie_name'],
    'cookie_path'       => $config['cookie_path'],
    'cookie_domain'     => $config['cookie_domain'],
    'cookie_secure'     => $config['cookie_secure'],
    'expiration'        => $config['sess_expiration'],
    'match_ip'          => $config['sess_match_ip'],
    'save_path'         => $config['sess_save_path'],
    '_sid_regexp'       => '[0-9a-v]{32}',
);
$class = new CI_Session_files_driver($config);
if (is_php('5.4')) {
    session_set_save_handler($class, TRUE);
} else {
    session_set_save_handler(
        array($class, 'open'),
        array($class, 'close'),
        array($class, 'read'),
        array($class, 'write'),
        array($class, 'destroy'),
        array($class, 'gc')
    );
    register_shutdown_function('session_write_close');
}
session_name($config['cookie_name']);
session_start();
if(!empty($_SESSION['logged_in'])){
if (is_file($installFile)) {
	$api = new LicenseBoxAPI();
	$update_data = $api->check_update();
	if(!empty($update_data['version']))
	{	
	?>
	<div class="container main_body"> <div class="section" >
		<div class="column is-6 is-offset-3">
			<center><h1 class="title p-t-lg p-b-lg">
				Check for Updates - LicenseBox
			</h1></center>
			<article class="message is-primary">
				<div class="message-body">
					Before updating, Please backup your database and files incase something goes wrong.
				</div>
			</article>
			<div class="box">
				        <?php
            $license_code = 'none';
            $client_name = 'none';
        if(!empty($_POST['license'])&&!empty($_POST['client'])){
            $license_code = $_POST["license"];
            $client_name = $_POST["client"];

			echo "<progress id=\"prog\" value=\"0\" max=\"100.0\" style=\"width: 100%;\"></progress><br><br>";	

            $api->download_update($_POST['update_id'],$_POST['has_sql'],$_POST['version'],$license_code,$client_name);
            ?>
 			<br><br>
		<?php 
            } else {  
		?>
   				<p class="subtitle is-5" style="margin-bottom: 0px"><?php echo $update_data['message']; ?></p>
				<div class='content'><p><?php
            	if($update_data['status']){
            	echo $update_data['changelog']; ?></p></div><hr>
					<form action="index.php" method="POST">
							<div class="field">
							<label class="label">Purchase code <small class="has-text-weight-normal has-text-grey"> (Purchase code is not saved for security reasons.)</small></label>
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
						<input type="hidden" class="form-control" value="<?php echo $update_data['update_id']; ?>" name="update_id">
						<input type="hidden" class="form-control" value="<?php echo $update_data['has_sql']; ?>" name="has_sql">
						<input type="hidden" class="form-control" value="<?php echo $update_data['version']; ?>" name="version">
						<center><button type="submit" class="button is-warning m-t-sm m-b-xs"><i class="fas fa-download p-r-xs"></i>Verify License & Download Update</button></center>
					</form>
				<?php } ?>
<?php
}
?>
			</div>
		</div>
	</div> </div>
<?php }else{
	?>
	<div class="container main_body"> <div class="section" >
		<center><h1 class="title p-t-xxl m-t-xl">
			Cheers! LicenseBox is up to date.
		</h1></center>
	</div></div>
	<?php
}} else{ ?>
	<div class="container main_body"> <div class="section" >
		<center><h1 class="title p-t-xxl m-t-xl">
			LicenseBox updater is locked, check documentation.
		</h1></center>
	</div></div>
	<?php }}else{ header("Location: ../");
die(); } ?>
