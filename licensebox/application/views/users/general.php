<div class="container is-fluid main_body"> <div class="section" >
      <h1 class="title">
        <?php echo $title; ?>
      </h1>
<?php echo generateBreadcrumb(); ?>
  <?php if($this->session->flashdata('general_status')): ?>
                    <?php $flash = $this->session->flashdata('general_status');
                    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
                <?php endif; ?>
    <div class="box">
      <div class="content">
 
<?php 
$hidden = array('type' => 'general'); 
echo form_open('users/general','',$hidden); ?>
<div class="field">
  <label class="label">License Code Format</label>
  <div class="control">
    <input class="input" type="text" name="license_format" aria-describedby="License format here" value="<?php 
  echo $license_format; ?>" placeholder="Enter license code format" tabindex="1" required>
  </div>
  <p class="help">{[X]} = any number from 0-9, {[Y]} = any letter from a-z, {[Z]} = any number from 0-9 or letter from a-z, anything else will not be replaced.</p>
</div>

<div class="columns" style="margin-bottom: 0px;">
 <div class="column">
  <div class="field">
<label class="label">Entries for failed activation attempts</label>
   <?php if($failed_activation_logs==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="failed_activation_logs" id="failed_activation_logs" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="failed_activation_logs" id="failed_activation_logs">
  <?php endif; ?>
  <label for="failed_activation_logs" style="margin-left: 0px !important;">Add entries for failed activation attempts? <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="Checking it will allow recording/adding of failed activations attempts, if unchecked failed attempts will be only visible in activity logs and not on activations page."><i class="fas fa-question-circle"></i></small></label>
  </div>
  <div class="field">
  <label class="label">Envato Username</label>
  <div class="control">
    <input class="input" type="text" name="envato_username" aria-describedby="Envato username here" value="<?php
  echo $envato_username; ?>" placeholder="Enter your envato username" tabindex="2">
  </div>
</div>
  <div class="field">
  <label class="label">Server Reply Email <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="Email address to be used by LicenseBox for sending emails, some web hosts only allow sending of emails from a registered email address."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="text" name="server_email" aria-describedby="Server no-reply email here" value="<?php
  echo $server_email; ?>" placeholder="Enter your server reply email" tabindex="4" required>
  </div>
</div>
<div class="field">
  <label class="label">API Blacklisted IPs <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="If provided, these IP addresses will be prevented from accessing/calling the API."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="tags" name="api_blacklisted_ips" aria-describedby="api_blacklisted_ips" value="<?php
     if(!empty(set_value('api_blacklisted_ips'))) {
      echo set_value('api_blacklisted_ips');
    }
      else{ echo $api_blacklisted_ips; 
        }
?>" placeholder="Enter IPs to block them from accessing LicenseBox API" tabindex="6">
  </div>
  <?php echo form_error('api_blacklisted_ips', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
<div class="field m-t-md p-t-sm">
        <label class="label">License Expiring Today Email</label>
        <div class="control">
          <textarea class="textarea" id="license_expiring" name="license_expiring" aria-describedby="license expiring today email" placeholder="Enter license expiring today email format here" rows="6"><?php
     if(!empty(set_value('license_expiring'))) {
      echo set_value('license_expiring');
    }
      else{ echo $license_expiring; 
        }
?></textarea>
        </div>
        <p class="help">{[client]} = client's name/username, {[product]} = product name</p>
      </div>
      <div class="field">
        <?php if($license_expiring_enable==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="license_expiring_enable" id="license_expiring_enable" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="license_expiring_enable" id="license_expiring_enable">
  <?php endif; ?>
        <label for="license_expiring_enable" style="margin-left: 0px !important;">Enable license expired today notification?</label>
      </div>
      <div class="field p-t-xs">
        <label class="label">Updates Expiring Today Email</label>
        <div class="control">
          <textarea class="textarea" id="updates_expiring" name="updates_expiring" aria-describedby="updates expiring today email" placeholder="Enter updates expiring today email format here" rows="6"><?php
     if(!empty(set_value('updates_expiring'))) {
      echo set_value('updates_expiring');
    }
      else{ echo $updates_expiring; 
        }
?></textarea>
        </div>
        <p class="help">{[client]} = client's name/username, {[product]} = product name</p>
      </div>
      <div class="field">
        <?php if($updates_expiring_enable==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="updates_expiring_enable" id="updates_expiring_enable" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="updates_expiring_enable" id="updates_expiring_enable">
  <?php endif; ?>
       <label for="updates_expiring_enable" style="margin-left: 0px !important;">Enable updates expired today notification?</label>
      </div>
</div>
  <div class="column">
      <div class="field">
<label class="label">Entries for failed update download attempts</label>
   <?php if($failed_update_download_logs==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="failed_update_download_logs" id="failed_update_download_logs" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="failed_update_download_logs" id="failed_update_download_logs">
  <?php endif; ?>
  <label for="failed_update_download_logs" style="margin-left: 0px !important;">Add entries for failed update download attempts? <small class="tooltip is-tooltip-multiline is-tooltip-left" data-tooltip="Checking it will allow recording/adding of failed update download attempts, if unchecked failed attempts will be only visible in activity logs and not on update downloads page."><i class="fas fa-question-circle"></i></small></label>
  </div>
    <div class="field">
  <label class="label">Envato API Token <small class="has-text-weight-normal has-text-grey"> (<a href="https://build.envato.com/create-token/" target="_blank">Don't have a token? Create a new one!</a>)</small></label>
  <div class="control">
    <input class="input" type="text" name="envato_api_token" aria-describedby="Envato API token here" value="<?php echo $envato_api_token; ?>" placeholder="Enter your envato api token" tabindex="3">
  </div>
</div>
    <div class="field">
  <label class="label">API Requests Rate Limit (per API Key / hour) <small class="tooltip is-tooltip-multiline is-tooltip-left " data-tooltip="Rate limiting API, allows you to control the no of requests the API receives every hour from a specific API Key."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="number" name="api_rate_limit" aria-describedby="api_rate_limit" value="<?php
  echo $api_rate_limit; ?>" placeholder="Requests allowed per api key per hour (leave empty for unlimited use)" tabindex="5">
  </div>
</div>
    <div class="field">
  <label class="label">API Blacklisted Domains <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="If provided, these domains will be prevented from accessing/calling the API."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="tags" name="api_blacklisted_domains" aria-describedby="api_blacklisted_domains" value="<?php
    if(!empty(set_value('api_blacklisted_domains'))) {
      echo set_value('api_blacklisted_domains');
    }
      else{ echo $api_blacklisted_domains; 
        }
?>" placeholder="Enter Domains to block them from accessing LicenseBox API" tabindex="7">
  </div>
  <?php echo form_error('api_blacklisted_domains', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
<div class="field m-t-md p-t-sm">
        <label class="label">Support Period Ending Today Email</label>
        <div class="control">
          <textarea class="textarea" id="support_expiring" name="support_expiring" aria-describedby="support expiring today email" placeholder="Enter support duration ending today email format here" rows="6"><?php
     if(!empty(set_value('support_expiring'))) {
      echo set_value('support_expiring');
    }
      else{ echo $support_expiring; 
        }
?></textarea>
        </div>
        <p class="help">{[client]} = client's name/username, {[product]} = product name</p>
      </div>
     <div class="field">
                <?php if($support_expiring_enable==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="support_expiring_enable" id="support_expiring_enable" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="support_expiring_enable" id="support_expiring_enable">
  <?php endif; ?>
        <label for="support_expiring_enable" style="margin-left: 0px !important;">Enable support expired today notification?</label>
      </div>
<div class="field p-t-xs">
        <label class="label">New Update Released Email</label>
        <div class="control">
          <textarea class="textarea" id="new_update" name="new_update" aria-describedby="new update released email" placeholder="Enter new product update released email format here" rows="6"><?php
     if(!empty(set_value('new_update'))) {
      echo set_value('new_update');
    }
      else{ echo $new_update; 
        }
?></textarea>
        </div>
        <p class="help">{[client]} = client's name/username, {[product]} = product name, , {[version]} = version name, {[changelog]} = version changelog</p>
      </div>
      <div class="field">
        <?php if($new_update_enable==1):?>
<input class="is-checkradio is-danger" type="checkbox" name="new_update_enable" id="new_update_enable" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="new_update_enable" id="new_update_enable">
  <?php endif; ?>
         <label for="new_update_enable" style="margin-left: 0px !important;">Enable new version released notification?</label>
      </div>
</div>
</div>
<div class="field is-grouped">
  <div class="control">
    <button class="button is-link">Update</button>
  </div>
</div>
<p class="help has-text-centered is-hidden-smobile">Note: Automated emails will be sent only if cron job is setup correctly and client's email address is available. During a cron job Envato purchase codes (if available) will also be re-verified/synced.</p>
</form></div>
  </div>
  <div class="columns">
  <div class="column is-one-third">
    <div class="box">
      <div class="content">
 
<?php 
$hidden = array('type' => 'api'); 
echo form_open('users/general','',$hidden); ?>

<div class="field">
  <label class="label">API Key</label>
  <div class="control">
    <input class="input" type="text" name="api_key" minlength="10" aria-describedby="API key here" value="<?php echo strtoupper(substr(str_shuffle(MD5(microtime())), 0, 20)); ?>" placeholder="Enter api key to add" required>
  </div>
</div>
<div class="field">
  <label class="label">API Key Type <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="Make sure you use the right API Key for the right purpose, never give a internal API key to your clients."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <div class="select">
      <select name="api_type" required>
        <option disabled="" selected="">Select Type</option>
        <option value="external">External/Client (for your clients to verify licenses etc)</option>
        <option value="internal">Internal (for you to add licenses etc)</option>
      </select>
    </div>
  </div>
</div>
<div class="field is-grouped">
  <div class="control">
    <button class="button is-link">Add</button>
  </div>
</div>
</form></div>
  </div>
  </div>
    <div class="column">
    <div class="box">
      <div class="content">
  <table class="table nots" style="width: 100%;">
    <thead>
      <tr>
        <th>API Key</th>
        <th>Type</th>
        <th>Date Added</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
     <?php foreach($api_keys as $key) : 
      ?><tr>
        <td><?php echo $key['key']; ?></td>
        <td><?php 
            if($key['controller']=='/api_external'){
              $key_type = "<span class='tag is-success is-rounded'>External</span>";
            }else{
              $key_type = "<span class='tag is-warning is-rounded'>Internal</span>";
            }
        echo $key_type; ?></td>
        <td class=""><?php 
            $originalDate = $key['date_created'];
            $newDate = date($this->config->item('date_format'), strtotime($originalDate));
            echo $newDate;
          ?></td>
          <td><div class="buttons is-centered">
      <?php 
      $hidden = array('key' => $key['key']);
      $js = 'onSubmit="return ConfirmDelete(\'api key\');"';
      echo form_open('/users/delete_api_key',$js, $hidden); ?>
      <button type="submit" class="button is-danger is-small"><i class="fa fa-trash p-r-xs"></i>Delete</button>
    </form>
  </div>
</td>
</tr>
<?php endforeach; ?> 
</tbody>
</table></div>
  </div>
  </div>
</div> 
</div>
</div>
<script src="<?php echo base_url(); ?>assets/vendor/Ckeditor/ckeditor.js"></script>
  <script>
    ClassicEditor.create( document.querySelector( '#license_expiring' ), {
      removePlugins: [ 'ImageUpload' ]
    });
    ClassicEditor.create( document.querySelector( '#updates_expiring' ), {
      removePlugins: [ 'ImageUpload' ]
    });
    ClassicEditor.create( document.querySelector( '#support_expiring' ), {
      removePlugins: [ 'ImageUpload' ]
    });
    ClassicEditor.create( document.querySelector( '#new_update' ), {
      removePlugins: [ 'ImageUpload' ]
    });
  </script>