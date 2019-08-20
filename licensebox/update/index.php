<?php 
header('Cache-Control: no-cache'); 
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
  <title>Check for Update - LicenseBox</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/bulma.css" />
  <link rel="stylesheet" href="../assets/vendor/FontAwesome-5.1.0/css/all.css"/>
  <link rel="icon" type="image/png" href="../assets/images/favicon-32x32.png" sizes="32x32"/>
  <link rel="icon" type="image/png" href="../assets/images/favicon-16x16.png" sizes="16x16"/>
  <script type="text/javascript">
    function updateProgress(percentage) {
      document.getElementById('progress').value = percentage;
    }
  </script>
</head>
<body>

 <?php 
 require("update.php");
 ?>
 <div class="content has-text-centered p-b-md">
  <p>
   Copyright <?php echo date('Y'); ?> CodeMonks, All rights reserved.
 </p>
</div>
</body>
</html>