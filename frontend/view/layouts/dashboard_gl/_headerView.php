<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                        class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="html/index.html" class="logo-link">
                    <img class="logo-dark logo-img-lg"
                        src="https://painel.rocketleads.com.br/images/plataformas/rocketpays.png"
                        srcset="https://painel.rocketleads.com.br/images/plataformas/rocketpays.png 2x" alt="logo-dark">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <a class="nk-news-item" href="#">
                        <div>
                            <img class="logo-img" src="https://rocketpays.app/user/images/badge/top2.png">
                        </div>
                        <div class="nk-news-text">
                            <div class="progress progress-lg">
                                <div class="progress-bar" data-progress="89">89%</div>
                            </div>
                        </div>
                        <div>
                            <img class="logo-img" src="https://rocketpays.app/user/images/badge/top1.png">
                        </div>
                    </a>
                </div>
            </div><!-- .nk-header-news -->
            <div class="nk-header-tools d-flex">
                <a href="#" class=" btn btn-primary d-none d-md-inline-flex">
                    <em class="icon ni ni-cart"></em><span class="nk-menu-text">My Store</span>
                </a>
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <div class="user-info d-none d-md-block">
                                    <div class="user-name dropdown-indicator">
                                        <?php echo $user->name; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span></span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">
                                            <?php echo $user->name; ?>
                                        </span>
                                        <span class="sub-text">
                                            <?php echo $user->email; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a to="<?php echo site_url(); ?>/profile"><em
                                                class="icon ni ni-user-alt"></em><span>My profile</span></a></li>
                                    <li><a href="#"><em class="icon ni ni-user-check"></em><span>Kyc
                                                Verification</span></a></li>
                                    <li><a href="#"><em
                                                class="icon ni ni-account-setting-fill"></em><span>Support</span></a>
                                    </li>
                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark
                                                Mode</span></a></li>

                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a 
                                            href="<?php echo site_url(); ?>/logout">
                                            <em class="icon ni ni-signout"></em><span>Sign out</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li><!-- .dropdown -->
                    <li class="dropdown notification-dropdown me-n1">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="icon-status icon-status-info"><em class="icon ni ni-bell"></em></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end dropdown-menu-s1">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                <a href="#">Mark All as Read</a>
                            </div>
                            <div class="dropdown-body">
                                <div class="nk-notification">

                                </div><!-- .nk-notification -->
                            </div><!-- .nk-dropdown-body -->
                            <div class="dropdown-foot center">
                                <a href="#">View All</a>
                            </div>
                        </div>
                    </li><!-- .dropdown -->
                </ul><!-- .nk-quick-nav -->
            </div><!-- .nk-header-tools -->
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>