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
                 <?php echo form_open('users/forgot_password'); ?>
                 <div class="field">
                    <div class="control has-icons-left">
                        <input class="input is-medium" type="text" name="username" placeholder="Enter your username" autofocus="" required>
                        <span class="icon is-small is-left">
    <i class="fas fa-user "></i>
  </span>
                    </div>
                </div>

                <button class="button is-block is-link is-medium is-fullwidth">Reset</button>
                <?php echo form_close(); ?>
                <a href="<?php echo base_url();?>login"><p class="has-text-dark p-t-sm m-t-xs">Go back to login</p></a>
            </div>
        </div>
    </div>
</div>
</section>
</div>