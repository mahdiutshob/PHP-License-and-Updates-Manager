<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
    <?php echo $title; ?>
  </h1>
  <?php echo generateBreadcrumb(); ?>
  <?php if($this->session->flashdata('product_status')): ?>
    <?php $flash = $this->session->flashdata('product_status');
    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
  <?php endif; ?>
  <?php if($this->session->flashdata('upload_status_main')): ?>
    <?php $flash = $this->session->flashdata('upload_status_main');
    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
  <?php endif; ?>
  <?php if($this->session->flashdata('upload_status_sql')): ?>
    <?php $flash = $this->session->flashdata('upload_status_sql');
    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
  <?php endif; ?>
  <div class="box">
    <div class="content">
      <?php 
      $hidden = array('product' => $this->input->post('product'));
      echo form_open_multipart('products/versions/add','',$hidden); ?>
      <div class="field">
        <label class="label">Version</label>
        <div class="control">
          <input class="input" type="text" name="version" maxlength="255" aria-describedby="product version here" value="<?php echo set_value('version'); ?>" placeholder="Product version eg. v1.0.0" required>
        </div>
      </div>
      <div class="field">
        <label class="label">Release Date</label>
        <div class="control">
          <input class="input date-time-picker" type="text" name="released" aria-describedby="version release date" value="<?php echo set_value('released'); ?>" placeholder="Version release date" required>
        </div>
      </div>
      <div class="field">
        <label class="label">Changelog</label>
        <div class="control">
          <textarea class="textarea" name="changelog" aria-describedby="version changelog" id="changelog" rows="10"><?php echo set_value('changelog'); ?></textarea>
        </div>
      </div>
      <div class="columns">
        <div class="column">
         <div class="field">
          <label class="label">Main files (.zip) <small class="tooltip is-tooltip-multiline is-tooltip-right " data-tooltip="These files will be replaced in the application root so structure it accordingly, also make sure you include the updated helper file in the zip file, which has the current_version value changed to the new version."><i class="fas fa-question-circle"></i></small></label>
          <input type="file" class="dropify" name="main_file" data-height="150" data-max-file-size="100M" data-allowed-file-extensions="zip" accept=".zip" required/>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label">Sql file (.sql) [Optional]</label>
          <input type="file" class="dropify" name="sql_file" data-height="150" data-max-file-size="50M" data-allowed-file-extensions="sql" accept=".sql" />
        </div>
      </div>
    </div>
    <div class="field is-grouped">
      <div class="control">
        <button class="button is-link">Submit</button>
      </div>
    </div>
  </form></div>
</div>
</div> </div>
<script src="<?php echo base_url(); ?>assets/vendor/Ckeditor/ckeditor.js"></script>
  <script>
    ClassicEditor.create( document.querySelector( '#changelog' ), {
      removePlugins: [ 'ImageUpload' ]
    });
  </script>