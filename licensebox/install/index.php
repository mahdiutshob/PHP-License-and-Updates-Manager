<?php 
    ini_set('display_errors', 0);
    if (version_compare(PHP_VERSION, '5.3', '>='))
    {
      error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
    }
    else
    {
      error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Install - LicenseBox</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/bulma.css" />
  <link rel="stylesheet" href="../assets/vendor/FontAwesome-5.1.0/css/all.css"/>
  <link rel="icon" type="image/png" href="../assets/images/favicon-32x32.png" sizes="32x32"/>
  <link rel="icon" type="image/png" href="../assets/images/favicon-16x16.png" sizes="16x16"/>
</head>
  <body>
   
 <?php 
 require("install.php");
 ?>

 <div class="content has-text-centered p-b-sm">
  <p>
   Copyright <?php echo date('Y'); ?> <a href="https://www.techdynamics.org" style="color: inherit;">CodeMonks</a>, All rights reserved.
 </p>
</div>
</body>
</html>