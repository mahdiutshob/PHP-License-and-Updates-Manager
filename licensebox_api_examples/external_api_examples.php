<?php 
require 'includes/lb_helper.php'; //loads helper file
$api = new LicenseBoxAPI(); //we create a new LicenseBoxAPI object as $api
?>
<!DOCTYPE html>
<html>
<head>
	<title>External/Client API examples - LicenseBox</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css" />
	<script type="text/javascript">
		function updateProgress(percentage) {
			document.getElementById('progress').value = percentage;
		}
	</script>
</head>
<body class="bg-light">
		<div class="container">
			<div class="mt-5 py-4 text-center">
				<h2>LicenseBox<sup><small> v1.2.1</small></sup> - External/Client API & Helper File usage examples</h2>
				<p class="lead pt-2">These are some usage examples and explanations of functions from the client/external helper file along with their sample code, you can use them directly in your application or if you want you can create your own custom functions based on API endpoints, request headers, request body and parameters mentioned below.</p>
				<div class="alert alert-warning" role="alert">
				Note: All API request parameters are compulsory unless specified otherwise. You need an API key of type "external" to access the external/client API.
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<h4 class="mb-3">Compulsory Request Headers</h4>
					<p>All Requests made to the LicenseBox server API must have the following request headers, if you are using the included Helper File these are already taken care of.</p>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Headers</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Content-Type</th>
								<td>Request content type, LicenseBox supports JSON (application/json) and XML (application/xml).</td>
							</tr>
							<tr>
								<th>LB-API-KEY</th>
								<td>LicenseBox API External/Client Key, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>LB-URL</th>
								<td>URL of the file from where the request is being made, it is already calculated by the LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>LB-IP</th>
								<td>IP of the server from where the request is being made, it is already calculated by the LicenseBoxAPI class.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">1. check_connection(), API endpoint: /api/check_connection_ext [POST] <span style="font-size: 15px;vertical-align: center;" class="badge badge-pill badge-primary">New</span></h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /check_connection endpoint. Response will contain a success message if the connection to the server was successful and request headers were correct otherwise it will contain the error message.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Connection successful."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->check_connection();</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					$check_connection_response = $api->check_connection();
					echo "<p><strong>".$check_connection_response['message']."</strong></p>"; ?>
				</div>
			</div><br>
			<h4 class="mb-3">2. get_latest_version(), API endpoint: /api/latest_version [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /latest_version endpoint. Response contains the latest version and release date of the provided product id.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Latest version of MyScript is v1.0.0",
  "product_name": "MyScript",
  "latest_version": "v1.0.0",
  "release_date": "2018-10-15"
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->get_latest_version();</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					$latest_version_response = $api->get_latest_version();
					echo "<p><strong>".$latest_version_response['message']."</strong></p>"; ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>product_id</th>
								<td>Product ID, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">3. activate_license(), API endpoint: /api/activate_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /activate_license endpoint, you can check if the provided license code is valid or not based on response <code>status</code> if it is TRUE or FALSE. This function activates the license and creates its activation log on the server. It also creates a local <code>.lic</code> license file based on the server response after successful activation and it deletes the existing local <code>.lic</code> file if the activation attempt fails. You can disable the creation/deletion of local <code>.lic</code> file by passing a 3rd parameter as FALSE during its function call.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Activated! Thanks for purchasing MyScript.",
  "data": null,
  "lic_response": "lic_file"
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->activate_license($license_code,$client_name);</b></pre>
					<p><em>You can also pass FALSE as a 3rd parameter if you don't want it to create a local <code>.lic</code> file on successful activation.</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['license'])&&!empty($_POST['client'])){
						$activate_response = $api->activate_license($_POST['license'],$_POST['client']);
						echo "<p><strong>".$activate_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="external_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">License code :</label>
								<input type="text" class="form-control" name="license">
							</div>
							<div class="form-group">
								<label for="pwd"> Client name :</label>
								<input type="text" class="form-control" name="client">
							</div>
							<button type="submit" class="btn btn-primary">Submit</button>
						</form> 
						<br>
						<?php
					}?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>product_id</th>
								<td>Product ID, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>verify_type</th>
								<td>It describes if LicenseBox will check the provided license with Envato or not, verify_type can be 'envato' or 'non_envato' (default), verify_type is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>license_code</th>
								<td>License code is the 1st argument passed in activate_license() function during its call.</td>
							</tr>
							<tr>
								<th>client_name</th>
								<td>Client name or Envato username (if envato purchase codes are allowed i.e verify_type is set to 'envato'), It is the 2nd argument passed in activate_license() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">4. verify_license(), API endpoint: /api/verify_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /verify_license endpoint, This function is meant to be used for background license checks. If you pass the 1st parameter as true the function will check license based on <code>verification period</code> (defined in the constructor function of LicenseBoxAPI class). It is meant to just check if the provided license is valid or not for the current activation without creating any logs on the server, you can check if the license is valid or not based on response <code>status</code> if it is TRUE or FALSE. If you call this function without passing the 2nd and 3rd parameters it will send the local <code>.lic</code> license file to the server which the server will decrypt and parse to check the license if it is valid or not.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Verified! Thanks for purchasing MyScript.",
  "data": "stuff here"
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->verify_license(false,$license_code,$client_name);</b></pre>
					<p><em>If you want it to check the license based on the <code>verification period</code> pass the 1st parameter as TRUE, if you want it to check the license from the local <code>.lic</code> license file, don't pass the 2nd and 3rd parameters.</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['license1'])&&!empty($_POST['client1'])){
						$verify_response = $api->verify_license(false,$_POST['license1'],$_POST['client1']);
						echo "<p><strong>".$verify_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="external_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">License code:</label>
								<input type="text" class="form-control" name="license1">
							</div>
							<div class="form-group">
								<label for="pwd"> Your name :</label>
								<input type="text" class="form-control" name="client1">
							</div>
							<button type="submit" class="btn btn-primary">Submit</button>
						</form> 
						<br>
						<?php
					}?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>product_id</th>
								<td>Product ID, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>license_code</th>
								<td>License code is the 2nd argument passed in verify_license() function during its call.</td>
							</tr>
							<tr>
								<th>client_name</th>
								<td>Client name or Envato username (if envato purchase codes are allowed i.e verify_type is set to 'envato'), It is the 3rd argument passed in verify_license() function during its call.</td>
							</tr>
							<tr>
								<th>license_file</th>
								<td>Contents of the local .lic file will be sent here if the 3rd and 4th parameters are empty in the verify_license() function call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">5. deactivate_license(), API endpoint: /api/deactivate_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /deactivate_license endpoint, This function deactivates the currently activated license from this installation and marks its activation as inactive on the server, It also deletes the local <code>.lic</code> license file. Useful for clients having a license with limited parallel uses so that they can deactivate the license and activate the same license somewhere else.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "License was deactivated successfully."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->deactivate_license($license_code,$client_name);</b></pre>
					<p><em>If you want are using the local <code>.lic</code> license file for storing the license information, don't pass any parameters.</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(isset($_POST['something'])){
						$deactivate_response = $api->deactivate_license();
						echo "<p><strong>".$deactivate_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="external_api_examples.php" method="POST">
								<input type="hidden" class="form-control" name="something">
							<button type="submit" class="btn btn-danger">Deactivate License</button>
						</form> 
						<br>
						<?php
					}?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>product_id</th>
								<td>Product ID, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>license_code</th>
								<td>License code is the 1st argument passed in deactivate_license() function during its call.</td>
							</tr>
							<tr>
								<th>client_name</th>
								<td>Client name or Envato username (if envato purchase codes are allowed i.e verify_type is set to 'envato'), It is the 2nd argument passed in deactivate_license() function during its call.</td>
							</tr>
							<tr>
								<th>license_file</th>
								<td>Contents of the local .lic file will be sent here if the 1st and 2nd parameters are empty in deactivate_license() function call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">6.1 check_update(), API endpoint : /api/check_update [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns the API response for /check_update endpoint, It will return the next update information even if there are more latest updates available. eg. if the current version is v1.0.0 and other released versions are v1.1.0 and v1.2.0, it will return v1.1.0 update and once we have updated to v1.1.0 we will get v1.2.0. This helps us to just push files and SQL for the current version and not worry about providing update files for each version to the latest version. This function only returns information about the next update most importantly it provides the <code>update_id</code> which is required for downloading the update using the next mentioned function.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "New version (v1.0.0) available for MyScript!",
  "version": "v1.0.0",
  "changelog": "This is a Major Version.",
  "update_id": "173e022ef1ab517ee210",
  "has_sql": false
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->check_update();</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(isset($_POST['versioncheck'])){
						$update_data = $api->check_update();
						echo "<p><strong>".$update_data['message']."</strong></p>";
						if($update_data['status']){
							echo "<p>".$update_data['changelog']."</p>"; ?>
							<?php
						}?>
						<br><br>
					<?php }
					else {
						?>
						<form action="external_api_examples.php" method="POST">
							<input type="hidden" class="form-control" name="versioncheck">
							<button type="submit" class="btn btn-primary">Check for Updates</button>
						</form><br>
					<?php } ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>product_id</th>
								<td>Product ID, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
							<tr>
								<th>current_version</th>
								<td>Current version, it is already defined in the constructor function of LicenseBoxAPI class.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">6.2 download_update(), API endpoint: /api/download_update/{type}/{update_id} [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It downloads and extracts/replaces main zip file contents and the SQL file in the application root folder (defined in the constructor function of LicenseBoxAPI class). download_update() is meant to be used in conjunction with check_update() function as <code>update_id</code> is available in the response of check_update() function, for the purpose of clean documentation we have separated both the functions. If you have checked the "Make license check compulsory for downloading updates?" option in the product page on LicenseBox server, the local <code>.lic</code> license file will be also sent and only if the license is valid the update download files will be served by the server. If you are not using the local license file for storing licenses you can also pass the <code>license_code</code> and the <code>client_name</code> manually as 4th and 5th parameter respectively in the function call.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>It returns update files</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->download_update($update_id,$has_sql,$version);</b></pre>
					<p><em>Here <b>$update_id</b> is the unique update id for this version, <b>$has_sql</b> is true if the update has a sql file so that it can be also downloaded and <b>$version</b> is the version name of this next update (all of these values can be taken and passed from the check_update function call's response).</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					$update_data1 = $api->check_update();
					if(!empty($_POST['update_id'])){
						echo "<progress id=\"prog\" value=\"0\" max=\"100.0\"></progress><br>";
						$api->download_update($_POST['update_id'],$_POST['has_sql'],$_POST['version']); ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="external_api_examples.php" method="POST">
							<input type="hidden" class="form-control" value="<?php echo $update_data1['update_id']; ?>" name="update_id">
							<input type="hidden" class="form-control" value="<?php echo $update_data1['has_sql']; ?>" name="has_sql">
							<input type="hidden" class="form-control" value="<?php echo $update_data1['version']; ?>" name="version">
							<button type="submit" class="btn btn-warning">Download Update</button>
						</form><br>
					<?php } ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">API Request Body</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>license_code</th>
								<td>License code is the 4th argument passed in download_update() function during its call.</td>
							</tr>
							<tr>
								<th>client_name</th>
								<td>Client name or Envato username (if envato purchase codes are allowed i.e verify_type is set to 'envato'), It is the 5th argument passed in download_update() function during its call.</td>
							</tr>
							<tr>
								<th>license_file</th>
								<td>Contents of the local .lic file will be sent here if the 4th and 5th parameters are empty in download_update() function call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			 <center><p>
     Copyright <?php echo date('Y'); ?> CodeMonks, All rights reserved.
   </p></center>
		</body>
		</html>
