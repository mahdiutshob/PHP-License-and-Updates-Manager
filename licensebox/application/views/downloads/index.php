<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
   <?php echo $title; ?>
 </h1>
 <?php echo generateBreadcrumb('Update Downloads'); ?>
 <?php if($this->session->flashdata('update_downloads_status')): ?>
  <?php $flash = $this->session->flashdata('update_downloads_status');
  echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
<?php endif; ?>
<div class="box">
  <table id="downloads_table" class="table is-striped is-hoverable" style="width: 100%">
    <thead>
      <tr>
        <th>Product</th>
        <th>Version</th>
        <th>Downloaded from URL</th>
        <th>IP</th>
        <th>Download Date</th>
         <th>Status</th>
        <th data-sortable="false">Action</th>
      </tr>
    </thead>
</table>
</div>
</div>
</div>