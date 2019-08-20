<?php 
require 'includes/lb_helper_internal.php'; //loads helper file
$api = new LicenseBoxAPI(); //we create a new LicenseBoxAPI object as $api
?>
<!DOCTYPE html>
<html>
<head>
	<title>Internal API examples - LicenseBox</title>
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
				<h2>LicenseBox<sup><small> v1.2.1</small></sup> - Internal API & Helper File usage examples</h2>
				<p class="lead pt-2">These are some usage examples and explanations of functions from the Internal helper file along with their sample code, you can use them directly in your application or if you want you can create your own custom functions based on API endpoints, request headers, request body and parameters mentioned below.</p>
				<div class="alert alert-warning" role="alert">
				Note: All API request parameters are compulsory unless specified otherwise. You need an API key of type "internal" to access the internal API.
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
			<h4 class="mb-3">1. check_connection(), API endpoint: /api/check_connection_int [POST] <span style="font-size: 15px;vertical-align: center;" class="badge badge-pill badge-primary">New</span></h4>
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
			<h4 class="mb-3">2. add_product(), API endpoint: /api/add_product [POST] <span style="font-size: 15px;vertical-align: center;" class="badge badge-pill badge-success">Updated</span></h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It adds a new product in LicenseBox. If you don't want the system to use a random <code>product_id</code> and want to provide your own <code>product_id</code> you can pass it as a 2nd parameter in the function call.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "New product test with ID 3D1D6F03 was successfully added.",
  "product_id": "3D1D6F03"
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->add_product($product_name,$product_id);</b></pre>
					<p><em>Here, If the <b>$product_id</b> is empty or is not passed, the system will generate a random id and use it as the product ID.</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['product_name'])){
						$add_product_response = $api->add_product($_POST['product_name']);
						echo "<p><strong>".$add_product_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">Product Name :</label>
								<input type="text" class="form-control" name="product_name">
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
								<th>product_name</th>
								<td>Product Name, is the 1st argument passed in add_product() function during its call.</td>
							</tr>
							<tr>
								<th>product_id (optional)</th>
								<td>Product ID, is the 2nd argument passed in add_product() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">3. create_license(), API endpoint: /api/create_license [POST] <span style="font-size: 15px;vertical-align: center;" class="badge badge-pill badge-success">Updated</span></h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It creates a new license with the provided details in LicenseBox, If you don't want the system to use a random <code>license_code</code> and want to provide your own <code>license_code</code> you can pass it as a 3rd parameter in the function call. You can pass the rest of the license information (like client,email,uses,expiry etc) as a associative array in the 2nd parameter.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "New MyScript license 1234-1234-1234-1234 was successfully added.",
  "license_code": "1234-1234-1234-1234"
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre>
  <b>$data = array(
    'license_type' => 'Regular License',
    'invoice_number' => '#244583',
    'client_name' => 'John Snow',
    'client_email' => 'jon@example.com',
    'comments' => null,
    'licensed_ips' => null,
    'licensed_domains' => null,
    'support_end_date' => null,
    'updates_end_date' => null,
    'expiry_date' => '2019-11-20 10:30',
    'license_uses' => null,
    'license_parallel_uses' => 1
  );
  $api->create_license($product_id,$data,$license_code);</b></pre>
					<p><em>Here, If the <b>$license_code</b> is empty or is not passed, the system will generate a random key and use it as the license code.</em></p>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['product_id2'])&&!empty($_POST['license_code2'])){
						$create_license_response = $api->create_license($_POST['product_id2'],null,$_POST['license_code2']);
						echo "<p><strong>".$create_license_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">Product ID  :</label>
								<input type="text" class="form-control" name="product_id2">
							</div>
							<div class="form-group">
								<label for="pwd">License Code :</label>
								<input type="text" class="form-control" name="license_code2">
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
								<td>Product Name, is the 1st argument passed in create_license() function during its call.</td>
							</tr>
							<tr>
								<th>license_code</th>
								<td>Product ID, is the 3rd argument passed in create_license() function during its call.</td>
							</tr>
							<tr>
								<th>license_type (optional)</th>
								<td>The type of license (e.g Regular License).</td>
							</tr>
							<tr>
								<th>invoice_number (optional)</th>
								<td>Order ID or Invoice number, useful for relating licenses to an invoice.</td>
							</tr>
							<tr>
								<th>client_name (optional)</th>
								<td>License holder's name or username</td>
							</tr>
							<tr>
								<th>client_email (optional)</th>
								<td>License holder's email address, useful for sending license expiry etc notices.</td>
							</tr>
							<tr>
								<th>comments (optional)</th>
								<td>Extra space for putting any relevant comments for this license.</td>
							</tr>
							<tr>
								<th>licensed_ips (optional)</th>
								<td>Comma separeted licensed ips for limiting license to work only on specific ips only.</td>
							</tr>
							<tr>
								<th>licensed_domains (optional)</th>
								<td>Comma separeted licensed domains for limiting license to work only on specific domains only.</td>
							</tr>
							<tr>
								<th>support_end_date (optional)</th>
								<td>License support expiry datetime, format is (Y-m-d H:i).</td>
							</tr>
							<tr>
								<th>updates_end_date (optional)</th>
								<td>License updates expiry datetime, format is (Y-m-d H:i).</td>
							</tr>
							<tr>
								<th>expiry_date (optional)</th>
								<td>License expiration datetime, format is (Y-m-d H:i).</td>
							</tr>
							<tr>
								<th>license_uses (optional)</th>
								<td>Total no of license uses allowed.</td>
							</tr>
							<tr>
								<th>license_parallel_uses (optional)</th>
								<td>Total no of parallel license uses allowed.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
				<h4 class="mb-3">4. get_product(), API endpoint: /api/get_product [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns all the relevant information about the specified product based on the <code>product_id</code>.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "product_id": "B582AE33",
  "envato_item_id": null,
  "product_name": "MyScript",
  "product_details": "test",
  "latest_version": "v1.0.0",
  "latest_version_release_date": "2018-11-15",
  "is_product_active": true,
  "requires_license_for_downloading_updates": true
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->get_product($product_id);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['product_id3'])){
						$get_product_response = $api->get_product($_POST['product_id3']);
						echo "<p><strong>".var_dump($get_product_response)."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">Product ID :</label>
								<input type="text" class="form-control" name="product_id3">
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
								<td>Product ID, is the 1st argument passed in get_product() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">5. get_license(), API endpoint: /api/get_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It returns all the relevant information about the specified license code.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "license_code": "85B7-172F-9082-AFAA",
  "product_id": "B582AE33",
  "product_name": "MyScript",
  "license_type": null,
  "client_name": null,
  "client_email": null,
  "invoice_number": null,
  "license_comments": null,
  "licensed_ips": null,
  "licensed_domains": null,
  "uses": null,
  "uses_left": null,
  "parallel_uses": "1",
  "parallel_uses_left": 1,
  "license_expiry": null,
  "support_expiry": null,
  "updates_expiry": null,
  "date_modified": "2018-11-04 19:10:57",
  "is_blocked": false,
  "is_a_envato_purchase_code": false,
  "is_valid_for_future_activations": true
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->get_license($license_code);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['license_code4'])){
						$get_license_response = $api->get_license($_POST['license_code4']);
						echo "<p><strong>".var_dump($get_license_response)."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">License Code :</label>
								<input type="text" class="form-control" name="license_code4">
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
								<th>license_code</th>
								<td>License Code, is the 1st argument passed in get_license() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">6. mark_product_active(), API endpoint: /api/mark_product_active [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It marks the provided product as active in LicenseBox.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Product MyScript marked as active."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->mark_product_active($product_id);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['product_id5'])){
						$mark_product_active_response = $api->mark_product_active($_POST['product_id5']);
						echo "<p><strong>".$mark_product_active_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">Product ID :</label>
								<input type="text" class="form-control" name="product_id5">
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
								<td>Product ID, is the 1st argument passed in mark_product_active() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			<h4 class="mb-3">7. mark_product_inactive(), API endpoint: /api/mark_product_inactive [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It marks the provided product as inactive in LicenseBox.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "Product MyScript marked as inactive."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->mark_product_inactive($product_id);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['product_id6'])){
						$mark_product_inactive_response = $api->mark_product_inactive($_POST['product_id6']);
						echo "<p><strong>".$mark_product_inactive_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">Product ID :</label>
								<input type="text" class="form-control" name="product_id6">
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
								<td>Product ID, is the 1st argument passed in mark_product_inactive() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			 <h4 class="mb-3">8. block_license(), API endpoint: /api/block_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It blocks the provided license code in LicenseBox.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "License 85B7-172F-9082-AFAA was successfully blocked."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->block_license($license_code);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['license_code7'])){
						$block_license_response = $api->block_license($_POST['license_code7']);
						echo "<p><strong>".$block_license_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">License Code :</label>
								<input type="text" class="form-control" name="license_code7">
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
								<th>license_code</th>
								<td>License Code, is the 1st argument passed in block_license() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
			 <h4 class="mb-3">9. unblock_license(), API endpoint: /api/unblock_license [POST]</h4>
			<div class="card">
				<div class="card-body">
					<h5 class="mb-2">Description: </h5>
					<p>It unblocks the provided license code in LicenseBox.</p>
					<h5 class="mb-2">Sample API Response: </h5>
<pre>{
  "status": true,
  "message": "License 85B7-172F-9082-AFAA was successfully unblocked."
}</pre>
					<h5 class="mb-2">Usage and arguments: </h5>
					<pre><b>$api->unblock_license($license_code);</b></pre>
					<h5 class="mb-2">Live Example: </h5>
					<?php
					if(!empty($_POST['license_code8'])){
						$block_license_response = $api->unblock_license($_POST['license_code8']);
						echo "<p><strong>".$block_license_response['message']."</strong></p>"; ?>
						<br><br>
					<?php }
					else {
						?>
						<form action="internal_api_examples.php" method="POST">
							<div class="form-group">
								<label for="email">License Code :</label>
								<input type="text" class="form-control" name="license_code8">
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
								<th>license_code</th>
								<td>License Code, is the 1st argument passed in unblock_license() function during its call.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><br>
   <p><center>
     Copyright <?php echo date('Y'); ?> CodeMonks, All rights reserved.
   </p></center>
		</body>
		</html>
