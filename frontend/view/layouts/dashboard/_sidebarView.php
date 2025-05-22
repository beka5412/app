<div class="nk-sidebar nk-sidebar-fixed is-compact" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="https://app.migraz.com" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="/images/logo.png" srcset="/images/logo.png 2x" alt="logo">
                <img class="logo-dark logo-img" src="/images/logo.png" srcset="/images/logo.png 2x" alt="logo-dark">
            </a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
              <ul class="nk-menu">
                
                <!--  menu admin -->
                <?php if ($admin->id ?? false): ?>
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/dashboard" class="<?php echo route_is(['/admin/dashboard', '/ajax/pages/admin/dashboard/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                      <span class="nk-menu-text">Dashboard</span>
                    </a>
                  </li>
                  <li class="nk-menu-hr"></li>
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/customers" class="<?php echo route_is(['/admin/customers', '/ajax/pages/admin/customer/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                      <span class="nk-menu-text">Clientes</span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/products" class="<?php echo route_is(['/admin/products', '/ajax/pages/admin/product/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                      <span class="nk-menu-text">Produtos</span>
                    </a>
                  </li>

                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/orders" class="<?php echo route_is(['/admin/orders', '/ajax/pages/admin/order/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                      <span class="nk-menu-text">Pedidos</span>
                    </a>
                  </li>
                  <li class="nk-menu-hr"></li>

                  
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/withdrawals" class="<?php echo route_is(['/admin/withdrawals', '/ajax/pages/admin/withdrawal/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                      <span class="nk-menu-text">Saques</span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/kyc" class="<?php echo route_is(['/admin/kyc', '/ajax/pages/admin/kyc/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                      <span class="nk-menu-text">Kyc</span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                    <a href="<?php echo site_url(); ?>/admin/awards" class="<?php echo route_is(['/admin/awards', '/ajax/pages/admin/arwads/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-gift"></em></span>
                      <span class="nk-menu-text">Premiações</span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                    <a  href="<?php echo site_url(); ?>/admin/settings" class="<?php echo route_is(['/admin/settings', '/ajax/pages/admin/settings/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                      <span class="nk-menu-text">Taxas</span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                    <a  href="<?php echo site_url(); ?>/admin/chargebacks" class="<?php echo route_is(['/admin/settings', '/ajax/pages/admin/settings/Index']) ? 'active' : ''; ?> nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-invest"></em></span>
                      <span class="nk-menu-text">Chargeback</span>
                    </a>
                  </li>
                  /**
                  * User
                  */
                <?php elseif ($user->id ?? false): ?>
                  <li class="nk-menu-item" class="<?php echo route_is(['/dashboard', '/ajax/pages/user/Dashboard']) ? 'active' : ''; ?>">
                    <a href="<?php echo site_url(); ?>/dashboard" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                      <span class="nk-menu-text">Dashboard</span>
                    </a>
                  </li>
                  <li class="nk-menu-item"  class="<?php echo route_is(['/sales', '/ajax/pages/user/Sale']) ? 'active' : ''; ?>">
                    <a href="<?php echo site_url(); ?>/sales" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                      <span class="nk-menu-text"><?= __('Sales') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item">
                      <a href="<?php echo site_url(); ?>/recurrence" 
                        class="<?php echo route_is(['/recurrence', '/ajax/pages/user/recurrence/Index']) ? 'active' : ''; ?> nk-menu-link">
                        <span class="nk-menu-icon"><em class="icon ni ni-invest"></em></span>
                        <span class="nk-menu-text">Assinaturas</span>
                      </a>
                    </li>
                  <li class="nk-menu-item"  class="<?php echo route_is(['/products', '/ajax/pages/user/Product']) ? 'active' : ''; ?>">
                    <a href="<?php echo site_url(); ?>/products" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-bag"></em></span>
                      <span class="nk-menu-text"><?= __('Products') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/coupons', '/ajax/pages/user/discount-coupon/Index']) ? 'active' : ''; ?>">
                    <a href="/coupons" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-offer"></em></span>
                      <span class="nk-menu-text"><?= __('Coupons') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/orderbumps', '/ajax/pages/user/orderbump/Index']) ? 'active' : ''; ?> ">
                    <a  href="<?php echo site_url(); ?>/orderbumps" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                      <span class="nk-menu-text"><?= __('Orderbumps') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/reports', '/ajax/pages/user/Reports']) ? 'active' : ''; ?>">
                    <a href="<?php echo site_url(); ?>/reports" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-chart-up"></em></span>
                      <span class="nk-menu-text"><?= __('Relatorios') ?></span></a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/balance', '/ajax/pages/user/Balance']) ? 'active' : ''; ?>" >
                    <a href="<?php echo site_url(); ?>/balance"  class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-coin-alt"></em></span>
                      <span class="nk-menu-text"><?= __('Balance') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/apps', '/ajax/pages/user/App']) ? 'active' : ''; ?>">
                    <a href="<?php echo site_url(); ?>/apps" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-puzzle"></em></span>
                      <span class="nk-menu-text"><?= __('Integrations') ?></span></a>
                  </li>

                  <li class="nk-menu-item" class="<?php echo route_is(['/refunds', '/ajax/pages/user/Refund']) ? 'active' : ''; ?> ">
                    <a href="<?php echo site_url(); ?>/refunds" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-curve-up-left"></em></span>
                      <span class="nk-menu-text"><?= __('Refunds') ?></span>
                    </a>
                  </li>
                  <li class="nk-menu-item" class="<?php echo route_is(['/refunds', '/ajax/pages/user/Refund']) ? 'active' : ''; ?>">
                    <a target="_blank" href="/members-area" class="nk-menu-link">
                      <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                      <span class="nk-menu-text"><?= __('Members area') ?></span>
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </div><!-- .nk-sidebar-menu -->
                
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>

