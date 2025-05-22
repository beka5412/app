<title>My Orders
</title>
<content>
    <div class="nk-wrap nk-wrap-nosidebar">
        <div class="">
            <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                <div class="brand-logo pb-4 text-center">
                <a class="logo-link nk-sidebar-logo" href="#">
      <img class="logo-light logo-img logo-img-lg" src="/images/logo.png" srcset="/images/logo.png" alt="logo">
      <img class="logo-dark logo-img logo-img-lg" src="/images/logo.png" srcset="/images/logo.png 2x" alt="logo-dark">
      </a>
                </div>
                <!-- <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="nk-block-title">Acessar</h5>
                        
                    </div>
                </div> -->

                <div class="frm_customer_login">
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label link link-primary link-sm">Email</label>
                        </div>
                        <div class="form-control-wrap">
                            <input name="login" type="text" class="form-control form-control-lg" id="login" placeholder="Seu email ou Usuário">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label link link-primary link-sm">Senha</label>
                            <a class="link link-primary link-sm" tabindex="-1"
                            href="<?php echo get_subdomain_serialized('purchase'); ?>/reset/password" to="<?php echo get_subdomain_serialized('purchase'); ?>/reset/password">
                            Esqueci minha senha</a>
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                <em class="d-block icon ni ni-eye" click="eyeOnClick" id="iconEyeOn"></em>
                                <em class="d-none icon ni ni-eye-off" click="eyeOffOnClick" id="iconEyeOff"></em>
                            </a>
                            <input class="form-control form-control-lg" name="password" type="password" id="inputPassword" enter="loginOnSubmit" placeholder="Sua Senha">
                        </div>
                    </div>
                    <div class="alert alert-danger div_purcharse_login_error" style="display: none">Login incorreto.</div>
                    <div class="form-group">
                        <button click="loginOnSubmit" class="btn btn-lg btn-light btn-block">Entrar</button>
                    </div>
                </div>
                </div>
            </div>
          
        </div>
        <div class="nk-split-content nk-split-stretch"></div>
    </div>
</content>