
<div class="nk-header nk-header-fixed is-light">
  <div class="container-fluid">
    <div class="nk-header-wrap">
      <div class="nk-menu-trigger d-xl-none ms-n1">
        <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
          <em class="icon ni ni-menu"></em>
        </a>
      </div>
      <div class="nk-header-brand d-xl-none">
        <a href="https://app.migraz.com/" class="logo-link">
          <img class="logo-light logo-img" src="/images/logo.png" srcset="/images/logo.png 2x" alt="logo">
          <img class="logo-dark logo-img" src="/images/logo.png" srcset="/images/logo.png" alt="logo-dark">
        </a>
      </div>
      <div class="nk-header-search ms-3 ms-xl-0" style="width: 100%;">
        <?php if (isset($balance_percent)): ?>
          <div
            class="progress"
            role="progressbar"
            aria-label="Basic example"
            aria-valuenow="100"
            aria-valuemin="0"
            aria-valuemax="100"
          >
            <div class="progress-bar" style="width: <?= $balance_percent ?>%"></div>
          </div>
          <div class="tooltips">
            <div class="tooltipgeral" id="tooltip1">
              R$ <?= currencyk($balance_amount) ?>
            </div>
            <div class="tooltipgeral" id="tooltip2">
              R$ <?= currencyk($target_balance) ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <div class="nk-header-tools">
        <ul class="nk-quick-nav">
          <li class="dropdown notification-dropdown">
            <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="icon-status icon-status-info">
                <em class="icon ni ni-bell"></em>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end" style="">
              <div class="dropdown-head">
                <span class="sub-title nk-dropdown-title">Notificações</span>
              </div>
              <div class="dropdown-body">
                <div class="nk-notification"></div>
              </div>
              <div class="dropdown-foot center">
                <a href="#">Ver todas</a>
              </div>
            </div>
          </li>
          <li class="dropdown user-dropdown">
            <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-toggle">
                <div class="user-avatar sm">
                  <em class="icon ni ni-user-alt"></em>
                </div>
                <div class="user-info d-none d-xl-block">
                  <div class="user-name dropdown-indicator"><?php echo $user->name ?? $admin->name ?? ''; ?></div>
                </div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end" style="">
              <div class="dropdown-inner">
                <ul class="link-list">
                    <li><a href="<?php echo site_url(); ?>/profile"><em class="icon ni ni-user-alt"></em><span>Meu Perfil</span></a></li>
                    <li><a href="<?php echo site_url(); ?>/kyc"><em class="icon ni ni-user-check"></em><span>Kyc Verificação</span></a></li>
                    <?php if ($user->id ?? false): ?>
                        <li>
                          <a href="<?php echo site_url(); ?>/logout"><em class="icon ni ni-signout"></em><span><?= __('Sign out') ?></span>
                          </a>
                        </li>

                      /**
                       * Admin
                       */
                      <?php elseif ($admin->id ?? false): ?>
                      <li>
                          <a href="<?php echo site_url(); ?>/admin/logout"><em class="icon ni ni-signout"></em><span><?= __('Sign out') ?></span>
                          </a>
                      </li>
                      <?php endif; ?>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid">
    <div class="mb-2">
        <?php if ($user->account_under_analysis ?? false) : ?>
            <div class="alert alert-danger">
                Sua conta está em analise. Preencha 
                <a class="text-danger" 
                style="font-weight: 600; color: rgb(233 140 132) !important; text-decoration: underline;" 
                href="/kyc">este formulário</a> 
                para reativar sua conta. 
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- <div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                        class="icon ni ni-menu"></em></a>
            </div>
           
        </div>
    </div>
</div> -->

