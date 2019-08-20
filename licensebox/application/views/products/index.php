<div class="container is-fluid main_body"> <div class="section" >
  <h1 class="title">
    <?php echo $title; ?> <a class="button is-warning is-rounded is-pulled-right is-hidden-smobile" href="<?php echo base_url(); ?>products/add"><i class="fas fa-plus-circle p-r-xs"></i> Add Product</a>
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
        <th>ID</th>
        <th>Product</th>
        <th>Latest Version</th>
        <th>Latest Release Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
     <?php foreach($products as $product) : 
      $latest_version = $this->products_model->get_latest_version($product['pd_pid']);
      ?><tr>
        <td><?php echo $product['pd_pid']; ?></td>
        <th><?php echo $product['pd_name']; ?></th>
        <td><?php  if(!empty($latest_version)){
          echo $latest_version[0]['version']; }
          else{
            echo 'no versions';
          }
          ?></td>
          <td><?php 
          if(!empty($latest_version)){
            $originalDate = $latest_version[0]['release_date'];
            $newDate = date($this->config->item('date_format'), strtotime($originalDate));
            echo $newDate;
          }
          else{
            echo '-';
          }
          ?></td>
          <?php if($product['pd_status']==1){
            $is_valid = "Active";
            $is_valid_typ = "success";
        }
        else
        {
          $is_valid = "Inactive";
          $is_valid_typ = "danger";
        } ?>
           <td><center><span class="tag is-<?= $is_valid_typ; ?> is-small is-rounded"><?= $is_valid; ?></span></center></td>
          <td><div class="buttons is-centered">
            <?php 
            $hidden = array('product' => $product['pd_pid']);
            echo form_open('/products/versions/add','', $hidden); ?>
            <button type="submit" class="button is-link is-small" style="padding-top: 0px;padding-bottom: 0px;"><i class="fas fa-plus p-r-xs"></i> Add Version</button>
          </form>&nbsp;
          <?php 
          $hidden = array('product' => $product['pd_pid']);
          echo form_open('/products/versions/','', $hidden); ?>
          <button type="submit" class="button is-success is-small" style="padding-top: 0px;padding-bottom: 0px;"><i class="fas fa-wrench p-r-xs"></i> Manage Versions</button>
        </form>&nbsp;
        <?php $hidden = array('product' => $product['pd_pid']);
        echo form_open('/products/edit','', $hidden); ?>
        <button type="submit" class="button is-warning is-small" style="padding-top: 0px;padding-bottom: 0px;"><i class="fas fa-edit p-r-xs"></i> Edit</button>
      </form>&nbsp;
      <?php 
      $hidden = array('product' => $product['pd_pid']);
      $js = 'onSubmit="return ConfirmDelete(\'product\',\'\n\nNote: All of its relevant records like (licenses, versions etc) will be also deleted.\');"';
      echo form_open('/products/delete',$js, $hidden); ?>
      <button type="submit" class="button is-danger is-small" style="padding-top: 0px;padding-bottom: 0px;"><i class="fa fa-trash p-r-xs"></i>Delete</button>
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

