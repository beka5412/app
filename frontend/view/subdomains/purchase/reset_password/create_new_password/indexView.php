<title>Redefinir senha</title>
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
                <div class="frm_customer_create_new_password">
                    <input type="hidden" value="<?php echo $token; ?>" name="token">
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label">Criar nova senha</label>
                        </div>
                        <div class="form-control-wrap">
                            <input name="password" type="password" class="form-control form-control-lg" id="login" placeholder="******">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label">Confirmar senha</label>
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input class="form-control form-control-lg" name="confirm_password" type="password" placeholder="******">
                        </div>
                    </div>
                    <div class="alert alert-danger div_purcharse_login_error" style="display: none">A senha é inválida.</div>
                    <div class="form-group">
                        <button click="savePasswordSubmit" class="btn btn-lg btn-primary btn-block">Salvar nova senha</button>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="nk-split-content nk-split-stretch"></div>
    </div>
</content>