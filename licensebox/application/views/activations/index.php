<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
   <?php echo $title; ?>
 </h1>
 <?php echo generateBreadcrumb(); ?>
 <?php if($this->session->flashdata('installations_status')): ?>
  <?php $flash = $this->session->flashdata('installations_status');
  echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
<?php endif; ?>
<div class="box">
  <table id="activations_table" class="table is-striped is-hoverable" style="width: 100%">
    <thead>
      <tr>
        <th>Product</th>
        <th>Client</th>
        <th>Using License</th>
        <th>Domain</th>
        <th>IP</th>
        <th>Activation Date</th>
        <th>Status</th>
        <th data-sortable="false">Action</th>
      </tr>
    </thead>
</table>
</div>
</div>
</div>