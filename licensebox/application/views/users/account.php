<div class="container is-fluid main_body"> <div class="section" >
      <h1 class="title">
        <?php echo $title; ?>
      </h1>
<?php echo generateBreadcrumb(); ?>
  <?php if($this->session->flashdata('user_status')): ?>
                    <?php $flash = $this->session->flashdata('user_status');
                    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
                <?php endif; ?>
   <div class="columns">
  <div class="column">
    <div class="box">
      <div class="content">
 
<?php 
$hidden = array('type' => 'general'); 
echo form_open('users/account','',$hidden); ?>
<div class="field">
  <label class="label">Full Name</label>
  <div class="control">
    <input class="input" type="text" name="full_name" maxlength="255" aria-describedby="Full name here" value="<?php 
  echo $user['au_name']; ?>" placeholder="Enter your name" required>
  </div>
</div>

<div class="field">
  <label class="label">Username</label>
  <div class="control">
    <input class="input" type="text" name="username" maxlength="255" minlength="5" aria-describedby="Username here" value="<?php 
  echo $user['au_username']; ?>" placeholder="Enter your username" required>
  </div>
</div>


<div class="field">
  <label class="label">Email <small class="tooltip is-tooltip-right " data-tooltip="Password reset emails will be sent to this email."><i class="fas fa-question-circle"></i></small></label>
  <div class="control">
    <input class="input" type="email" name="email" maxlength="255" aria-describedby="Email here" value="<?php 
  echo $user['au_email']; ?>" placeholder="Enter your Email" required>
  </div>
</div>
<div class="field is-grouped">
  <div class="control">
    <button class="button is-link">Update</button>
  </div>
</div>

</form></div>
  </div>
  </div>
  <div class="column">
    <div class="box">
      <div class="content">
 
<?php 
$hidden = array('type' => 'password'); 
echo form_open('users/account','',$hidden); ?>

<div class="field">
  <label class="label">Current Password</label>
  <div class="control">
    <input class="input" type="password" name="current_password" minlength="8" aria-describedby="Current password here" value="" placeholder="Enter your current password" required>
  </div>
</div>

<div class="field">
  <label class="label">New Password</label>
  <div class="control">
    <input class="input" type="password" name="new_password" minlength="8" aria-describedby="New password here" value="" placeholder="Enter new password" required>
  </div>
</div>
<div class="field is-grouped">
  <div class="control">
    <button class="button is-link">Change Password</button>
  </div>
</div>
</form></div>
<!-- <div class="notification is-success">
  <button class="delete"></button>
You are successfully logged in.
</div> -->
  </div>
  </div>
</div> 

  </div> </div>