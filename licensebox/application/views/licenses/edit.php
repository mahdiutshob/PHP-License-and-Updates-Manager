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
 
<?php 
$hidden = array('license_code' => $license['license_code'], 'old_client' => $license['client']); 
echo form_open('licenses/edit','',$hidden); ?>
<div class="field">
  <label class="label">License for Product</label>
  <div class="select">
  <select name="product" aria-describedby="product_id_here" value="" tabindex="1" required>
<?php foreach($products as $product) : 
  if($product['pd_pid']==$license['pid']):?>
<option value="<?php echo $product['pd_pid']; ?>" selected><?php echo $product['pd_name']." (".($product['pd_status'] ? 'Active' : 'Inactive').")"; ?></option>
<?php else: ?>
<option value="<?php echo $product['pd_pid']; ?>"><?php echo $product['pd_name']." (".($product['pd_status'] ? 'Active' : 'Inactive').")"; ?></option>
<?php endif;
endforeach; ?>
  </select>
</div>
</div>


<div class="columns" style="margin-bottom:0px!important;">
  <div class="column">
    <div class="field">
  <label class="label">License Code</label>
  <div class="control">
    <input class="input" type="text" name="license" maxlength="255" minlength="7" aria-describedby="license_code_here" value="<?php echo $license['license_code']; ?>" placeholder="Enter License code" tabindex="2" required disabled>
  </div>
    <?php echo form_error('license', '<p class="help is-danger">', '</p>'); ?>
</div>

    <div class="field">
  <label class="label">Client (Leave empty for use by any client)</label>
  <div class="control">
    <input class="input" type="text" name="client" maxlength="255" aria-describedby="client_name_here" value="<?php if(!empty(set_value('client'))) {
      echo set_value('client');
    }
      else{ echo $license['client']; 
        }
     ?>" placeholder="Enter client's name or envato username" tabindex="4">
  </div>
</div>

<div class="field">
  <label class="label">Total License Use Limits (Leave empty for unlimited use) <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="License use limits define how many times a license can be used for activating the given product (e.g if use limits of a license is set to 10 then the given license can be used to activate a product 10 times before the license becomes invalid provided that other conditions like (domain, ip, parallel uses, expiration etc) are fulfilled)"><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="number" min="0" name="uses" aria-describedby="limits_here" value="<?php if(!empty(set_value('uses'))) {
      echo set_value('uses');
    }
      else{ echo $license['uses']; 
        }
     ?>" placeholder="Enter no of total available license use" tabindex="6">
  </div>
</div>

<div class="field">
  <label class="label">License Expiration Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="expiry" aria-describedby="license expiry date here" value="<?php if(!empty(set_value('expiry'))) {
      echo set_value('expiry');
    }
      else{ echo $license['expiry']; 
        }
     ?>" placeholder="Enter license expiry date" tabindex="8">
  </div>
</div>

<div class="field">
  <label class="label">Updates Expiration Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="updates_till" maxlength="255" aria-describedby="updates_untill" value="<?php if(!empty(set_value('updates_till'))) {
      echo set_value('updates_till');
    }
      else{ echo $license['updates_till']; 
        }
     ?>" placeholder="Enter updates expiration date" tabindex="10">
  </div>
</div>

 <div class="field">
  <label class="label">Licensed Domain (optional)</label>
  <div class="control">
    <input class="input" type="tags" name="domains" aria-describedby="licensed domains here" value="<?php if(!empty(set_value('domains'))) {
      echo set_value('domains');
    }
      else{ echo $license['domains']; 
        }
     ?>" placeholder="Enter licensed domains without http (eg. example.com)" tabindex="12">
  </div>
  <?php echo form_error('domains', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
  </div>
  <div class="column">

<div class="field">
  <label class="label">License Type (optional)</label>
  <div class="control">
    <input class="input" type="text" name="license_type" maxlength="255" aria-describedby="license type here" value="<?php if(!empty(set_value('license_type'))) {
      echo set_value('license_type');
    }
      else{ echo $license['license_type']; 
        }
     ?>" placeholder="Enter license type" tabindex="3">
  </div>
</div>

<div class="field">
  <label class="label">Client's Email (optional)</label>
  <div class="control">
    <input class="input" type="email" name="email" maxlength="255" aria-describedby="client email here" value="<?php if(!empty(set_value('email'))) {
      echo set_value('email');
    }
      else{ echo $license['email']; 
        }
     ?>" placeholder="Enter client's email" tabindex="5">
  </div>
</div>

<div class="field">
    <label class="label">Total Parallel Use Limits (Leave empty for unlimited parallel uses) <small class="tooltip is-tooltip-multiline is-tooltip-left " data-tooltip="Parallel license use limits define how many active and valid activations can exist for a license at any given time (e.g if parallel uses of a license is set to 2 then the given license can be used to activate and run two instances of a product simultaneously, for activating a 3rd instance one of the current activation has to be marked as inactive)"><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="number" min="0" name="parallel_uses" aria-describedby="simultaneous limits here" value="<?php if(!empty(set_value('parallel_uses'))) {
      echo set_value('parallel_uses');
    }
      else{ echo $license['parallel_uses']; 
        }
     ?>" placeholder="Enter no of total simultaneous license uses allowed" tabindex="7">
  </div>
</div>

<div class="field">
  <label class="label">Support End Date (optional)</label>
  <div class="control">
    <input class="input date-time-picker" type="text" name="supported_till" maxlength="255" aria-describedby="supported_untill" value="<?php if(!empty(set_value('supported_till'))) {
      echo set_value('supported_till');
    }
      else{ echo $license['supported_till']; 
        }
     ?>" placeholder="Enter license supported till date (for Envato Purchase Codes it will be automatically added)" tabindex="9">
  </div>
</div>

<div class="field">
  <label class="label">Invoice Number (optional)</label>
  <div class="control">
    <input class="input" type="text" name="invoice" maxlength="255" aria-describedby="invoice email here" value="<?php if(!empty(set_value('invoice'))) {
      echo set_value('invoice');
    }
      else{ echo $license['invoice']; 
        }
     ?>" placeholder="Enter invoice/order Number" tabindex="11">
  </div>
</div>

<div class="field">
  <label class="label">Licensed IP (optional)</label>
  <div class="control">
    <input class="input" type="tags" name="ips" aria-describedby="licensed ips here" value="<?php if(!empty(set_value('ips'))) {
      echo set_value('ips');
    }
      else{ echo $license['ips']; 
        }
     ?>" placeholder="Enter licensed ip addresses (eg. 216.3.128.12)" tabindex="13">
  </div>
  <?php echo form_error('ips', '<p class="help is-danger m-t-md">', '</p>'); ?>
</div>
  </div>
</div>

<div class="field m-t-sm p-t-xs">
        <label class="label">Comments (optional)</label>
        <div class="control">
          <textarea class="textarea" name="comments" aria-describedby="enter comments here" tabindex="14" placeholder="Enter comments here"><?php if(!empty(set_value('comments'))) {
      echo set_value('comments');
    }
      else{ echo $license['comments']; 
        }
     ?></textarea>
        </div>
      </div>

<div class="field">
  <?php if($license['validity']==0):?>
  <input class="is-checkradio is-danger" type="checkbox" name="validity" id="validity" tabindex="15" checked>
  <?php else: ?>
  <input class="is-checkradio is-danger" type="checkbox" name="validity" id="validity" tabindex="15">
  <?php endif; ?>
  <label for="validity" style="margin-left: 0px !important;">Block license?</label>
</div>

<div class="field p-t-sm is-grouped">
  <div class="control">
    <button class="button is-link">Submit</button>
  </div>
</div>
<p class="help has-text-centered ">Note: If the license is already in-use, changes you make above will be applied on the next background license check from its activations.</p>
</form></div>
  </div>

  </div> </div>