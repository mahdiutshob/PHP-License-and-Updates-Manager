<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
    <?php echo $title; ?>
  </h1>
  <?php echo generateBreadcrumb('Generate Internal Helper File'); ?>
  <?php if($this->session->flashdata('product_status')): ?>
    <?php $flash = $this->session->flashdata('product_status');
    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
  <?php endif; ?>
  <div class="columns">
    <div class="column is-one-third">
     <div class="box">
      <div class="content">
        <?php echo form_open('generate_internal'); ?>
        <div class="field">
          <label class="label">API Key for Helper File</label>
          <div class="select">
            <select name="key" aria-describedby="api_key_here" value="<?php echo set_value('key'); ?>" required>
              <option disabled="" selected="">Select API Key</option>
              <?php foreach($api_keys as $key) : ?>
                <option value="<?php echo $key['key']; ?>"><?php echo $key['key']; ?> (Internal)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php echo form_error('key', '<p class="help is-danger">', '</p>'); ?>
        </div>
          <div class="field is-grouped">
            <div class="control">
              <button class="button is-link">Generate</button>
            </div>
          </div>
        </form></div>
      </div>
    </div>
    <div class="column">
     <div class="box">
      <div class="content">
        <div class="field">
          <label class="label">Generated Internal Helper File</label>
          <textarea class="textarea" name="generated_code" placeholder="Code will be generated here, once the code is generated copy it and just follow the instructions from the documentation." rows="18"><?php if(!empty($generated_code)){echo $generated_code; }?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
</div> 
</div>