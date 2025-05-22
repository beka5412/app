<div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em
                    class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a class="logo-link nk-sidebar-logo" href="#">
                <img class="logo-light logo-img-lg"
                    src="https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png"
                    srcset="https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png 2x" alt="logo">
                <img class="logo-dark logo-img-lg"
                    src="https://painel.rocketleads.com.br/images/plataformas/rocketpays.png"
                    srcset="https://painel.rocketleads.com.br/images/plataformas/rocketpays.png 2x" alt="logo-dark">
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a 
                            to="<?php echo site_url(); ?>/dashboard" 
                            href="<?php echo site_url(); ?>/dashboard"  
                            class="<?php echo route_is(['/dashboard', '/ajax/pages/user/Dashboard']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a
                            to="<?php echo site_url(); ?>/marketplace"
                            href="<?php echo site_url(); ?>/marketplace" 
                            class="<?php echo route_is(['/marketplace', '/ajax/pages/user/MarketPlace']) ? 'active' : ''; ?> nk-menu-link"
                            >
                            <span class="nk-menu-icon"><em class="icon ni ni-hot"></em></span>
                            <span class="nk-menu-text">Marketplace</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a 
                            to="<?php echo site_url(); ?>/products" 
                            href="<?php echo site_url(); ?>/products" 
                            class="<?php echo route_is(['/products', '/ajax/pages/user/Product']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                            <span class="nk-menu-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Gestão</h6>
                    </li>
                   
                    <li class="nk-menu-item">
                        <a href="#" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-check"></em></span>
                            <span class="nk-menu-text">Clientes</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a 
                            to="<?php echo site_url(); ?>/sales" 
                            href="<?php echo site_url(); ?>/sales" 
                            class="<?php echo route_is(['/sales', '/ajax/pages/user/Sale']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-trend-up"></em></span>
                            <span class="nk-menu-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a
                            to="<?php echo site_url(); ?>/abandoned-carts" 
                            href="<?php echo site_url(); ?>/abandoned-carts" 
                            class="<?php echo route_is(['/abandoned-carts', '/ajax/pages/user/AbandonedCart']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                            <span class="nk-menu-text">Carrinho Abandonado</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Ferramentas</h6>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a
                            to="<?php echo site_url(); ?>/affiliates" 
                            href="<?php echo site_url(); ?>/affiliates" 
                            class="<?php echo route_is(['/affiliates', '/ajax/pages/user/Affiliate']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">Meus Afiliados</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                    <a
                                    to="<?php echo site_url(); ?>/discount-coupon" 
                                    href="<?php echo site_url(); ?>/discount-coupon" 
                                    class="<?php echo route_is(['/discount-coupon', '/ajax/pages/user/DiscountCoupon']) ? 'active' : ''; ?> nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-offer"></em></span>
                                    <span class="nk-menu-text">Cupom de Desconto</span>
                                </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="#" 
                            class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-growth"></em></span>
                            <span class="nk-menu-text">Relatórios</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-puzzle"></em></span>
                            <span class="nk-menu-text">Integrações</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item">
                                <a href="#" class="nk-menu-link"><span class="nk-menu-text">Apps</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="#" class="nk-menu-link"><span class="nk-menu-text">RocketZap</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="#" class="nk-menu-link"><span class="nk-menu-text">RocketMember</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Bank Digital</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a
                            to="<?php echo site_url(); ?>/balance" 
                            href="<?php echo site_url(); ?>/balance" 
                            class="<?php echo route_is(['/balance', '/ajax/pages/user/Balance']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-invest"></em></span>
                            <span class="nk-menu-text">Financeiro</span><span class="nk-menu-badge">HOT</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Configurações</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a
                            to="<?php echo site_url(); ?>/upsell" 
                            href="<?php echo site_url(); ?>/upsell" 
                            class="<?php echo route_is(['/order_bumps', '/ajax/pages/user/order_bumps']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                            <span class="nk-menu-text">Configurações</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a
                            to="<?php echo site_url(); ?>/support" 
                            href="<?php echo site_url(); ?>/support" 
                            class="<?php echo route_is(['/support', '/ajax/pages/user/Support']) ? 'active' : ''; ?> nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-account-setting-fill"></em></span>
                            <span class="nk-menu-text">Suporte</span>
                        </a>
                    </li>
                    <li class="nk-menu-item mb-5">
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>