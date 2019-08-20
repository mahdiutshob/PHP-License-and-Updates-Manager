<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
   <?php echo $title; ?> <a class="button is-warning is-rounded is-pulled-right is-hidden-smobile" href="<?php echo base_url(); ?>licenses/create"><i class="fas fa-plus-circle p-r-xs"></i> Add License</a>
 </h1>
 <?php echo generateBreadcrumb(); ?>
 <?php if($this->session->flashdata('license_status')): ?>
  <?php $flash = $this->session->flashdata('license_status');
  echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
<?php endif; ?>
<div class="box">
  <table id="licenses_table" class="table is-striped is-hoverable" style="width: 100%">
    <thead>
      <tr>
        <th>License code</th>
        <th>Product</th>
        <th>Client</th>
        <th>Date modified</th>
        <th>Uses left</th>
        <th data-sortable="false">Status</th>
        <th>Validity</th>
        <th data-sortable="false">Action</th>
      </tr>
    </thead>
</table>
</div>
</div>
</div>
