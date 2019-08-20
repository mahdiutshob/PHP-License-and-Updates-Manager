<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
add_action('admin_menu', function() {
    add_options_page( 'LicenseBox Test WP Plugin', 'LicenseBox Test WP Plugin', 'manage_options', 'licensebox-test', 'licensebox_test_plugin_page' );
});
add_action( 'admin_init', function() {
    register_setting( 'licensebox_test-settings', 'set1' );
    register_setting( 'licensebox_test-settings', 'set2' );
});
if(!empty($_POST['set1'])&&!empty($_POST['set2'])){
$api->activate_license($_POST['set2'],$_POST['set1']);
}
function licensebox_test_plugin_page() {
global $api;
global $res;
$update_data = $api->check_update();
?>
  <script>
    function updateProgress(percentage) {
      document.getElementById('progress').value = percentage;
    }
  </script>
    <div class="wrap">
      <h1>LicenseBox Test WP Plugin - Settings</h1><br>
      <?php if ($res['status']) {
        ?> <div class="notice notice-info"><p>Activated! Your license is valid.</p></div> <?php }
      else{
        ?> <div class="notice notice-error"><p>No license has been provided yet or the provided license is invalid.</p></div> <?php
      }?>
      <form action="options.php" method="post">
        <?php
          settings_fields( 'licensebox_test-settings' );
          do_settings_sections( 'licensebox_test-settings' );
        ?>
        <table>
          <tr>
                <th>Your name</th>
                <td><input type="text" placeholder="Enter your name/license holder's full name" name="set1" size="50" value="<?php echo get_option("set1",null); ?>" required/></td>
            </tr>       
            <tr>
                <th>License code</th>
                <td><input type="text" placeholder="Enter the license key" name="set2" size="50" value="<?php echo get_option("set2",null); ?>" required/></td>
            </tr>
            <tr>
                <td><?php submit_button(); ?></td>
            </tr>
        </table>
      </form>
      <?php if ($res['status']) { ?>
      <h2 class="title">Updates for this plugin</h2>
      <p><strong><?php echo $update_data['message']; ?></strong></p>
      <?php
        if($update_data['status']){
          ?><p>Changelog: <?php echo $update_data['changelog']; ?></p>
        <?php if(!empty($_POST['update_id'])){
          echo "<progress id=\"prog\" value=\"0\" max=\"100.0\" style=\"width: 20%;\"></progress><br>";
          $api->download_update($_POST['update_id'],$_POST['has_sql'],$_POST['version']);
          ?>
          <br><br>
        <?php }
        else {
          ?>
          <form action="" method="POST">
            <input type="hidden" value="<?php echo $update_data['update_id']; ?>" name="update_id">
            <input type="hidden" value="<?php echo $update_data['has_sql']; ?>" name="has_sql">
            <input type="hidden" value="<?php echo $update_data['version']; ?>" name="version">
            <span id="test-button">
              <input id="test-settings" type="submit" value="Download and install update" class="button">
            </span>
          </form>
        <?php }}} ?>
    </div>
<?php
}
?>