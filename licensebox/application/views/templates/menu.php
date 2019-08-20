<?php require_once(APPPATH.'/libraries/licensebox_api_helper.php');
$lbapp = new LicenseBoxAPI();
?>
<nav id="navbar" class="navbar is-link has-border2 is-spaced">
  <div class="container is-fluid">
    <div class="navbar-brand">
      <a class="navbar-item" href="<?php echo base_url(); ?>">
        <h3 id="responsive-size" class="title is-5 has-text-white is-spaced" title="Dashboard">
          LicenseBox
        </h3>
      </a>
      <div id="navbarBurger" class="navbar-burger burger" data-target="navMenuDocumentation">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div id="navMenuDocumentation" class="navbar-menu">
      <div class="navbar-start">
       <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          <i class="fas fa-database p-r-xs"></i> Products
        </a>
        <div id="moreDropdown" class="navbar-dropdown">       
          <a class="navbar-item " href="<?php echo base_url(); ?>products/add" title="Add a new product">
            <span>
              <strong>Add product</strong>               
            </span>
          </a>          
          <a class="navbar-item " href="<?php echo base_url(); ?>products" title="Manage products and their versions">
            <span>
              <strong>Manage products</strong>
            </span>
          </a>                    
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          <i class="fas fa-key p-r-xs"></i> Licenses
        </a>
        <div id="moreDropdown" class="navbar-dropdown">       
          <a class="navbar-item " href="<?php echo base_url(); ?>licenses/create" title="Create a new license">
            <span>
              <strong>Create new license</strong>               
            </span>
          </a>          
          <a class="navbar-item " href="<?php echo base_url(); ?>licenses" title="Manage licenses">
            <span>
              <strong>Manage licenses</strong>
            </span>
          </a>                    
        </div>
      </div>
      <a class="navbar-item" href="<?php echo base_url(); ?>activations" title="View all products activations">
        <i class="fas fa-hdd p-r-xs"></i> Activations
      </a>
      <a class="navbar-item" href="<?php echo base_url(); ?>update_downloads" title="View all updates download logs">
        <i class="fas fa-download p-r-xs"></i> Downloads
      </a>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          <i class="fas fa-code p-r-xs"></i> Generate
        </a>
        <div id="moreDropdown" class="navbar-dropdown">  
        <a class="navbar-item " href="<?php echo base_url(); ?>generate_external" title="Generate client/external helper file for adding license checks, update checks etc in your own product.">
            <span>
              <strong>Client Helper File</strong>               
            </span>
          </a>         
          <a class="navbar-item " href="<?php echo base_url(); ?>generate_internal" title="Generate internal helper file for adding ability to add licenses, manage licenses etc to your order management system or some other system.">
            <span>
              <strong>Internal Helper File</strong>               
            </span>
          </a>          
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          <i class="fas fa-cogs p-r-xs"></i> Settings
        </a>
        <div id="moreDropdown" class="navbar-dropdown">  
        <a class="navbar-item " href="<?php echo base_url(); ?>general" title="Manage general and API settings">
            <span>
              <strong>General & API Settings</strong>               
            </span>
          </a>         
          <a class="navbar-item " href="<?php echo base_url(); ?>account" title="Manage your account settings">
            <span>
              <strong>Account Settings</strong>               
            </span>
          </a>          
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          <i class="fas fa-info-circle p-r-xs"></i> Help
        </a>
        <div id="moreDropdown" class="navbar-dropdown">
          <a class="navbar-item " href="<?php echo base_url(); ?>update" title="Check for new LicenseBox updates">
            <span>
              <strong>Check for updates!</strong>
            </span>
          </a>
          <hr class="navbar-divider">
          <p class="navbar-item">
            <span>
              <strong>About</strong>
              <br>
              LicenseBox is a full-fledged license and updates <br> manager for PHP applications. <br><br>
              LicenseBox <?php echo $lbapp->get_current_version(); ?>, <a href="mailto:teamcodemonks@gmail.com?subject=LicenseBox Support">teamcodemonks@gmail.com</a>
            </span>
          </p>
        </div>
      </div>
    </div>
          <div class="navbar-end">
       <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
           <span><?php echo ucfirst($this->session->userdata('fullname')); ?></span>
        </a>
        <div id="moreDropdown" class="navbar-dropdown">    
          <a class="navbar-item " href="<?php echo base_url(); ?>logout" title="Log out of LicenseBox">
            <span>
              <strong><i class="fas fa-sign-out-alt"></i> Logout</strong>
            </span>
          </a>        
        </div>
      </div>
      </div>
    </div>
  </div>
</nav>