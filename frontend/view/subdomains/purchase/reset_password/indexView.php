<title>My Order</title>
<content>
    <div class="nk-split nk-split-page nk-split-md">
        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-dark">
            <div class="nk-block nk-block-middle nk-auth-body">
                <div class="brand-logo pb-5">
                    <a href="html/index.html" class="logo-link">
                    <img class="logo-light logo-img logo-img-lg" src="/images/logo.png" srcset="/images/logo.png" alt="logo">
                    <img class="logo-dark logo-img logo-img-lg" src="/images/logo-dark.png" srcset="/images/logo-dark.png 2x" alt="logo-dark">
                    </a>
                </div>
                <!-- <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="nk-block-title">Acessar</h5>
                    </div>
                </div> -->
                <div class="frm_customer_reset_password">
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label">Email</label>
                            <a class="link link-primary link-sm" tabindex="-1"
                            href="<?php echo get_subdomain_serialized('purchase'); ?>/login"
                            to="<?php echo get_subdomain_serialized('purchase'); ?>/login">Voltar</a>
                        </div>
                        <div class="form-control-wrap">
                            <input name="email" type="text" class="form-control form-control-lg" id="login" placeholder="Seu email ou Usuário">
                        </div>
                    </div>
                    <div class="form-group">
                        <button click="resetPasswordOnSubmit" class="btn btn-lg btn-primary btn-block">Redefinir senha</button>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="nk-split-content nk-split-stretch"></div>
    </div>
</content>