<div class="container is-fluid main_body">  <div class="section" >
<div class="tile is-ancestor">
  <div class="tile is-parent">
    <div class="p-t-sm m-t-sm"><h1 class="title is-size-3-mobile is-size-4-tablet is-size-3-desktop">
      Dashboard
    </h1>
    <p class="subtitle is-size-5-mobile is-size-6-5-tablet is-size-5-desktop">
     Welcome to dashboard
   </p></div>
 </div>
 <div class="tile is-parent">
    <article class="tile is-child has-background-info box has-shadow2 grow"><a href="<?php echo base_url(); ?>products">
         <div class="t-icon right"><i class="fas fa-database"></i></div>
        <div class="t-content">
        <p class="title has-text-white"><?php echo thousandsCurrencyFormat($product_count); ?></p>
        <p class="subtitle is-size-5-mobile is-size-6-5-tablet is-size-5-desktop has-text-white shrink-text">
        Product<?php echo return_s($product_count); ?> <small>(<?php echo thousandsCurrencyFormat($product_count_active); ?> active)</small></p>
        </div></a>
    </article>
</div>
<div class="tile is-parent">
    <article class="tile is-child has-background-success box has-shadow2 grow"><a href="<?php echo base_url(); ?>licenses">
         <div class="t-icon right"><i class="fas fa-key"></i></div>
        <div class="t-content">
        <p class="title has-text-white"><?php echo thousandsCurrencyFormat($license_count); ?></p>
        <p class="subtitle is-size-5-mobile is-size-6-5-tablet is-size-5-desktop has-text-white shrink-text">License<?php echo return_s($license_count); ?> <small>(<?php echo thousandsCurrencyFormat($license_count_active); ?> active)</small></p>
        </div></a>
    </article>
</div>
<div class="tile is-parent">
    <article class="tile is-child has-background-primary box has-shadow2 grow"><a href="<?php echo base_url(); ?>activations">
         <div class="t-icon right"><i class="fas fa-hdd"></i></div>
        <div class="t-content">
        <p class="title has-text-white"><?php echo thousandsCurrencyFormat($installation_count); ?></p>
        <p class="subtitle is-size-5-mobile is-size-6-5-tablet is-size-5-desktop has-text-white shrink-text">Activation<?php echo return_s($installation_count); ?> <small>(<?php echo thousandsCurrencyFormat($installation_count_active); ?> active)</small></p>
        </div></a>
    </article>
</div>
<div class="tile is-parent">
    <article class="tile is-child has-background-grey box has-shadow2 grow"><a href="<?php echo base_url(); ?>update_downloads">
         <div class="t-icon right"><i class="fas fa-download"></i></div>
        <div class="t-content">
        <p class="title has-text-white"><?php echo thousandsCurrencyFormat($download_count); ?></p>
        <p class="subtitle is-size-5-mobile is-size-6-5-tablet is-size-5-desktop has-text-white shrink-text">Update Download<?php echo return_s($download_count); ?></p>
        </div></a>
    </article>
</div>
</div>
<?php
$remove_top_margin = "m-t-md";
$lbapp = new LicenseBoxAPI();
if(!$this->session->tempdata('update_check_done')){
if($this->session->tempdata('lb_has_update')&&($this->session->tempdata('lb_current_ver')==$lbapp->get_current_version())){?>
  <article class="message is-warning">
  <div class="message-body">
     <?php echo $this->session->tempdata('lb_has_update'); ?>, download and install it directly using the included updater <a href="<?php echo base_url(); ?>update">here</a>.
  </div>
</article> <?php
$remove_top_margin = null;
}else{
$res = $lbapp->check_update();
if(!empty($res['version']))
{
  $updtemp = array('lb_has_update' => $res['message'], 'lb_current_ver' => $lbapp->get_current_version());
  $this->session->set_tempdata($updtemp, NULL, 600);
?>
<article class="message is-warning">
  <div class="message-body">
    <?php echo $res['message']; ?>, download and install it directly using the included updater <a href="<?php echo base_url(); ?>update">here</a>.
  </div>
</article>
<?php
$remove_top_margin = null;
}
else{
  $updtemp = array('update_check_done' => true);
  $this->session->set_tempdata($updtemp, NULL, 600);
}
}
}
?>
<div class="columns <?php echo $remove_top_margin; ?>">
  <div class="column is-one-third">
    <article class="tile is-child box has-shadow2">
     
        <p class="subtitle is-size-">License Share</p>
        <div id="chartContainer" style="height: 300px; width: 100%;"><div id="chart-loading"><center><i class="subtitle fa fa-spinner fa-spin"></i></center></div></div>

    </article>
  </div>
  <div class="column">
    <article class="tile is-child box has-shadow2">
      
        <p class="subtitle is-size-5">New License/Activations/Downloads (This Month)</p>
        <div id="chartContainer2" style="height: 300px; width: 100%;"><div id="chart-loading2"><center><i class="subtitle fa fa-spinner fa-spin"></i></center></div></div>
    </article>
  </div>
</div>
<article class="message is-primary has-shadow2">
  <div class="message-header">
    <p>Activity (Past 24 hours) <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="Activites done by you will be marked with a 'done by user' text, all other activities are done by the System, Cron Job or by the Internal API."><i class="fas fa-question-circle"></i></small></p><a href="<?php echo base_url(); ?>activities" class="button is-small is-rounded is-white is-pulled-right">View All</a>
  </div>
  <div id="inbox-messages" class="inbox-messages" style="max-height: 510px;overflow-y: auto;">
    <?php 
    if(!empty($activity_logs)):
      foreach($activity_logs as $log) : ?>
       <div class="card">
        <div class="card-content">
          <small><?php  $originalDate = $log['al_date'];
           $newDate = date($this->config->item('datetime_format'), strtotime($originalDate));
           echo $newDate; ?></small>
         <div><?php echo $log['al_log']; ?></div>
       </div>
     </div>
   <?php endforeach; 
   else: ?>
    <div class="card">
      <div class="card-content has-text-centered">
        <p class="has-text-weight-semibold">No new activity to show.</p>
      </div></div>
      <?php
    endif;
    ?>
  </div>
</article>
</div>
</div>