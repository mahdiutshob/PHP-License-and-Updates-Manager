<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
    <?php echo $title; ?>
  </h1>
  <?php echo generateBreadcrumb(); ?>
  <?php if($this->session->flashdata('product_status')): ?>
    <?php $flash = $this->session->flashdata('product_status');
    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
  <?php endif; ?>
  <div class="box">
    <div class="content">
      <?php echo form_open('products/add'); ?>
      <div class="field">
        <label class="label">Product Status <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="Product status is checked before responding to any api requests, marking a product inactive will return all api calls related to this product with a 'product not found or is inactive' response."><i class="fas fa-question-circle"></i></small></label>
        <div class="control">
          <div class="select">
            <select name="product_status" aria-describedby="product status here" value="<?php echo set_value('product_status'); ?>" required>
              <option value='1' selected>Active</option>
              <option value='0'>Inactive</option>
            </select>
          </div>
        </div>
        <?php echo form_error('product_status', '<p class="help is-danger">', '</p>'); ?>
      </div>
      <div class="columns" style="margin-bottom: 0px !important;">
        <div class="column">
          <div class="field">
          <label class="label">Product ID</label>
          <div class="control">
            <input class="input" type="text" name="product_id" maxlength="255" minlength="6" aria-describedby="product ID here" value="<?php 
            if(!empty(set_value('product_id'))) {
              echo set_value('product_id');
            }
            else{ echo $product_id; 
            } ?>" placeholder="Enter Product ID" required>
          </div>
          <?php echo form_error('product_id', '<p class="help is-danger">', '</p>'); ?>
        </div>
        </div>
        <div class="column">
          <div class="field">
          <label class="label">Envato Item ID (optional) <small class="tooltip is-tooltip-multiline " data-tooltip="When provided, Envato purchase codes will be checked if they are of the specified envato product or not, useful if you have more than one envato products."><i class="fas fa-question-circle"></i></small></label>
          <div class="control">
            <input class="input" type="text" name="envato_id" maxlength="255" aria-describedby="envato item id here" value="<?php echo set_value('envato_id'); ?>" placeholder="Enter Envato Product ID">
          </div>
        </div>
        </div>
      </div>
      <div class="field">
        <label class="label">Product Name</label>
        <div class="control">
          <input class="input" type="text" name="name" maxlength="255" aria-describedby="product name here" value="<?php echo set_value('name'); ?>" placeholder="Enter Product Name" required>
        </div>
        <?php echo form_error('name', '<p class="help is-danger">', '</p>'); ?>
      </div>
      <div class="field">
        <label class="label">Product Details (optional)</label>
        <div class="control">
          <textarea class="textarea" name="details" aria-describedby="product details here" placeholder="Enter product details here"><?php echo set_value('details'); ?></textarea>
        </div>
      </div>
      <div class="field">
        <input class="is-checkradio is-danger" type="checkbox" name="license_update" id="license_update">
        <label for="license_update" style="margin-left: 0px !important;">Make license check compulsory for downloading updates? <small class="tooltip is-tooltip-multiline " data-tooltip="When checked, updates for this product can only be downloaded by providing a valid license and if that license's update expiration date has not passed."><i class="fas fa-question-circle"></i></small></label>
      </div>
      <div class="field is-grouped">
        <div class="control">
          <button class="button is-link">Submit</button>
        </div>
      </div>
    </form></div>
  </div>

</div> </div>