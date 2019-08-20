<div class="container is-fluid main_body"> <div class="section" >
      <h1 class="title">
        <?php echo $title; ?>
      </h1>
<?php echo generateBreadcrumb(); ?>
  <?php if($this->session->flashdata('license_status')): ?>
                    <?php $flash = $this->session->flashdata('license_status');
                    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
                <?php endif; ?>
    <div class="box">
      <div class="content">
 
<?php echo form_open('licenses/create'); ?>
<div class="field">
  <label class="label">License for Product</label>
  <div class="select">
  <select name="product" aria-describedby="product_id_here" value="<?php echo set_value('product'); ?>" tabindex="1" required>
<option disabled="" selected="">Select Product</option>
<?php foreach($products as $product) : ?>
<option value="<?php echo $product['pd_pid']; ?>"><?php echo $product['pd_name']." (".($product['pd_status'] ? 'Active' : 'Inactive').")"; ?></option>
<?php endforeach; ?>
  </select>
</div>
<?php echo form_error('product', '<p class="help is-danger">', '</p>'); ?>
</div>

<div class="columns" style="margin-bottom:0px!important;">
  <div class="column">
    <div class="field">
  <label class="label">License Code</label>
  <div class="control">
    <input class="input" type="text" name="license" maxlength="255" minlength="7" aria-describedby="license_code_here" value="<?php 
    if(!empty(set_value('license'))) {
      echo set_value('license');
    }
      else{ echo $created_license; 
        } ?>" placeholder="Enter License code" tabindex="2" required>
  </div>
    <?php echo form_error('license', '<p class="help is-danger">', '</p>'); ?>
</div>

    <div class="field">
  <label class="label">Client (Leave empty for use by any client)</label>
  <div class="control">
    <input class="input" type="text" name="client" maxlength="255" aria-describedby="client_name_here" value="<?php echo set_value('client'); ?>" placeholder="Enter client's name or envato username" tabindex="4">
  </div>
</div>

<div class="field">
  <label class="label">Total License Use Limits (Leave empty for unlimited uses) <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="License use limits define how many times a license can be used for activating the given product (e.g if use limits of a license is set to 10 then the given license can be used to activate a product 10 times before the license becomes invalid provided that other conditions like (domain, ip, parallel uses, expiration etc) are fulfilled)"><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="number" min="0" name="uses" aria-describedby="limits_here" value="<?php echo set_value('uses'); ?>" placeholder="Enter no of total available license use" tabindex="6">
  </div>
</div>

<div class="field">
  <label class="label">License Expiration Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="expiry" aria-describedby="license expiry date here" value="<?php echo set_value('expiry'); ?>" placeholder="Enter license expiry date" tabindex="8">
  </div>
</div>

<div class="field">
  <label class="label">Updates Expiration Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="updates_till" maxlength="255" aria-describedby="updates_untill" value="<?php echo set_value('updates_till'); ?>" placeholder="Enter updates expiration date" tabindex="10">
  </div>
</div>

 <div class="field">
  <label class="label">Licensed Domain (optional)</label>
  <div class="control">
    <input class="input" type="tags" name="domains" aria-describedby="licensed domains here" value="<?php echo set_value('domains'); ?>" placeholder="Enter licensed domains without http (eg. example.com)" tabindex="12">
  </div>
  <?php echo form_error('domains', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
  </div>
  <div class="column">

<div class="field">
  <label class="label">License Type (optional)</label>
  <div class="control">
    <input class="input" type="text" name="license_type" maxlength="255" aria-describedby="license type here" value="<?php echo set_value('license_type'); ?>" placeholder="Enter license type" tabindex="3">
  </div>
</div>

<div class="field">
  <label class="label">Client's Email (optional)</label>
  <div class="control">
    <input class="input" type="email" name="email" maxlength="255" aria-describedby="client email here" value="<?php echo set_value('email'); ?>" placeholder="Enter client's email" tabindex="5">
  </div>
</div>

<div class="field">
  <label class="label">Total Parallel Use Limits (Leave empty for unlimited parallel uses) <small class="tooltip is-tooltip-multiline is-tooltip-left " data-tooltip="Parallel license use limits define how many active and valid activations can exist for a license at any given time (e.g if parallel uses of a license is set to 2 then the given license can be used to activate and run two instances of a product simultaneously, for activating a 3rd instance one of the current activation has to be marked as inactive)"><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="number" min="0" name="parallel_uses" aria-describedby="simultaneous limits here" value="<?php echo set_value('parallel_uses'); ?>" placeholder="Enter no of total simultaneous license uses allowed" tabindex="7">
  </div>
</div>

<div class="field">
  <label class="label">Support End Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="supported_till" maxlength="255" aria-describedby="supported_untill" value="<?php echo set_value('supported_till'); ?>" placeholder="Enter license supported till date (for Envato Purchase Codes it will be automatically added)" tabindex="9">
  </div>
</div>

<div class="field">
  <label class="label">Invoice Number (optional)</label>
  <div class="control">
    <input class="input" type="text" name="invoice" maxlength="255" aria-describedby="invoice email here" value="<?php echo set_value('invoice'); ?>" placeholder="Enter invoice/order Number" tabindex="11">
  </div>
</div>

<div class="field">
  <label class="label">Licensed IP (optional)</label>
  <div class="control">
    <input class="input" type="tags" name="ips" aria-describedby="licensed ips here" value="<?php echo set_value('ips'); ?>" placeholder="Enter licensed ip addresses (eg. 216.3.128.12)" tabindex="13">
  </div>
  <?php echo form_error('ips', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
  </div>
</div>


<div class="field m-t-sm p-t-xs">
        <label class="label">Comments (optional)</label>
        <div class="control">
          <textarea class="textarea" name="comments" aria-describedby="enter comments here" placeholder="Enter comments here" tabindex="14"><?php echo set_value('comments'); ?></textarea>
        </div>
      </div>

<div class="field">
  <input class="is-checkradio is-danger" type="checkbox" name="validity" id="validity" tabindex="15">
  <label for="validity" style="margin-left: 0px !important;">Block license?</label>
</div>

<div class="field p-t-sm is-grouped">
  <div class="control">
    <button class="button is-link">Submit</button>
  </div>
</div>
</form></div>
  </div>

  </div> </div>