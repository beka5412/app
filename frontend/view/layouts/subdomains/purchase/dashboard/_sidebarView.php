<div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
  <div class="nk-sidebar-element nk-sidebar-head">
    <div class="nk-menu-trigger">
      <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
          class="icon ni ni-arrow-left"></em></a>
      <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex compact-active"
        data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
    </div>
    <div class="nk-sidebar-brand">
      <a class="logo-link nk-sidebar-logo" href="#">
      <img class="logo-light logo-img logo-img-sm" src="/images/logo.png" srcset="/images/logo.png" alt="logo">
      <img class="logo-dark logo-img logo-img-sm" src="/images/logo-dark.png" srcset="/images/logo-dark.png 2x" alt="logo-dark">
      </a>
    </div>
  </div>
  <div class="nk-sidebar-element nk-sidebar-body">
    <div class="nk-sidebar-content">
      <div class="nk-sidebar-menu" data-simplebar>
        <ul class="nk-menu ez-sidebar">
              <li class="nk-menu-item">
                <a href="<?php echo get_subdomain_serialized('purchase'); ?>/dashboard" 
                  class="<?php echo route_is(['/dashboard', '/ajax/pages/subdomains/purchase/dashboard/Index']) ? 'active' : ''; ?> nk-menu-link">
                  <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                  <span class="nk-menu-text"><?= lang($locale, 'My purchases') ?></span>
                </a>
              </li>
              <li class="nk-menu-item">
                <a href="<?php echo get_subdomain_serialized('purchase'); ?>/subscriptions" 
                  class="<?php echo route_is(['/subscriptions', '/ajax/pages/subdomains/purchase/subscriptions/Index']) ? 'active' : ''; ?> nk-menu-link">
                  <span class="nk-menu-icon"><em class="icon ni ni-hot"></em></span>
                  <span class="nk-menu-text"><?= lang($locale, 'Subscriptions') ?></span>
                </a>
              </li>
              <div class="nk-sidebar-profile nk-sidebar-profile-fixed dropdown" style="display: flex; align-items: center; justify-content: space-between;">
                <a href="#" data-bs-toggle="dropdown" data-offset="50,-50" style=" display: flex; align-items: center; ">
                  <div class="user-avatar">
                      <em class="icon ni ni-user-alt"></em>
                  </div>
                  <!-- <div class="user-info d-none d-md-block">
                      <div class="user-name">
                      <?php echo session_customer()?->name ?? ''; ?>
                      </div>
                  </div> -->
                </a>
                <a
                  href="<?php echo get_subdomain_serialized('purchase'); ?>/logout">
                  <em class="icon ni ni-signout"></em>
                  <span>Sign out</span>
                </a>
              </div>
          
         

        </ul>
      </div>
    </div>
  </div>
</div>

