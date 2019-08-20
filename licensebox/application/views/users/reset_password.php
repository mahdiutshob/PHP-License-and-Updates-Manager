 <div class="container">  <section class="hero is-fullheight">
    <div class="hero-body">
        <div class="container has-text-centered">
            <div class="column is-4 is-offset-4">
                <h3 class="title has-text-grey-dark">LicenseBox</h3>
                <p class="subtitle has-text-grey-dark">Let's reset your password</p>
                <?php if($this->session->flashdata('login_status')): ?>
                    <?php $flash = $this->session->flashdata('login_status');
                    echo ' <div class="notification is-'.$flash['type'].'"><button class="delete"></button>'.$flash['message'].'</div>'; ?>
                <?php endif; ?>
                <div class="box">
                  
                 <form method="post" action="<?php echo base_url();?>reset_password/<?php echo $this->uri->segment(2);?>/<?php echo $this->uri->segment(3); ?>"/>

                 <div class="field">
                    <div class="control has-icons-left">
                        <input class="input is-medium" type="password" minlength="8" name="new_password" placeholder="Enter your new password" required>
                        <span class="icon is-small is-left">
      <i class="fas fa-lock"></i>
    </span>
                    </div>
                    <?php echo form_error('new_password', '<p class"help is-danger">', '</p>'); ?>
                </div>
                <div class="field">
                    <div class="control has-icons-left">
                        <input class="input is-medium" type="password" minlength="8" name="password_confirm" placeholder="Confirm your password" required>
                        <span class="icon is-small is-left">
      <i class="fas fa-lock"></i>
    </span>
                    </div>
                    <?php echo form_error('password_confirm', '<p class"help is-danger">', '</p>'); ?>
                </div>

                <button class="button is-block is-link is-medium is-fullwidth">Update</button>
                <?php echo form_close(); ?>
                <a href="<?php echo base_url();?>login"><p class="has-text-dark p-t-sm m-t-xs">Go back to login</p></a>
            </div>
        </div>
    </div>
</div>
</section>
</div>