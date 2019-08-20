<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
   <?php echo $title;
   ?>
 </h1>
 <?php echo generateBreadcrumb(); ?>
 <?php if($this->session->flashdata('product_status')): ?>
  <?php $flash = $this->session->flashdata('product_status');
  echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
<?php endif; ?>
<div class="box">
  <table class="table ts is-striped is-hoverable" style="width: 100%">
    <thead>
      <tr>
        <th>Version</th>
        <th>Release date</th>
        <th>Changelog</th>
        <th>Downloads</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
     <?php foreach($versions as $version) : ?><tr>
      <th><?php echo $version['version']; ?></th>
      <td><?php 

      $originalDate = $version['release_date'];
      $newDate = date($this->config->item('date_format'), strtotime($originalDate));

      echo $newDate; ?></td>
      <td><?php echo $version['changelog'] ?></td>
      <td><center><span class="tag is-primary is-rounded"><?php echo $this->downloads_model->get_update_downloads_based_on_version($version['vid']); ?> downloads</span></center></td>
      <td><div class="buttons is-centered">
      <?php 
      $js = 'onSubmit="return ConfirmDelete(\'version\',\'\n\nNote: All of its relevant records like (update download logs) will be also deleted.\');"';
      $hidden = array('vid' => $version['vid'],'version' => $version['version'],'product' => $version['pid']);
      echo form_open('/products/versions/delete',$js, $hidden); ?>
      <button type="submit" class="button is-danger is-small" style="padding-top: 0px;padding-bottom: 0px;"><i class="fa fa-trash"></i>&nbsp;Delete</button></div></td>
    </form>
  </div>
</td>
</tr>
<?php endforeach; ?> 
</tbody>
</table>
</div>
</div>
</div>

