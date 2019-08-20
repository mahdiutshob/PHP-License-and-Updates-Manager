<?php 
header('Cache-Control: no-cache'); 
require_once('../includes/lb_helper.php'); //Include LicenseBox external/client api helper file
$api = new LicenseBoxAPI(); //Initialize a new LicenseBoxAPI object
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>MyScript - Updater</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.css" />
  <script type="text/javascript">
    function updateProgress(percentage) {
      document.getElementById('progress').value = percentage;
    }
  </script>
  <style type="text/css">
  body, html {
    background: #F7F7F7;
  }
</style>
</head>
<body>
  <?php
  $update_data = $api->check_update(); //First let's check for update and get available update data
  ?>
  <div class="container main_body"> <div class="section">
    <div class="column is-6 is-offset-3">
      <center><h1 class="title" style="padding-top: 20px">
        MyScript Updater
      </h1><br></center>
      <div class="box">
        <p class="subtitle is-5" style="margin-bottom: 0px"><?php echo $update_data['message']; ?></p>
        <div class='content'><p><?php
              if($update_data['status']){
              echo $update_data['changelog']; ?></p></div>
        <?php  if(!empty($_POST['update_id'])){
          echo "<progress id=\"prog\" value=\"0\" max=\"100.0\" style=\"width: 100%;\"></progress><br><br>";
          /*
          Once we have the update_id we can use LicenseBoxAPI's download_update() function for downloading and installing the update.
          */
          $api->download_update($_POST['update_id'],$_POST['has_sql'],$_POST['version']);
          ?>
          <br><br>
        <?php }
        else {
          ?>
          <form action="index.php" method="POST">
            <input type="hidden" class="form-control" value="<?php echo $update_data['update_id']; ?>" name="update_id">
            <input type="hidden" class="form-control" value="<?php echo $update_data['has_sql']; ?>" name="has_sql">
            <input type="hidden" class="form-control" value="<?php echo $update_data['version']; ?>" name="version">
            <center><button type="submit" class="button is-link">Download & Install Update</button></center>
          </form>
        <?php }} ?>
        </div>
      </div>
    </div> 
  </div>
  <div class="content has-text-centered">
    <p>
     Copyright <?php echo date('Y'); ?> MyScript, All rights reserved.
   </p>
   <br>
 </div>
</body>
</html>