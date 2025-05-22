<App>

    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->


                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="" class="logo-link">
                                <img style="width: 300px; max-height: fit-content;" class="logo-light logo-img logo-img-lg" src="/images/logo.png" alt="logo">
                                <img style="width: 300px; max-height: fit-content;" class="logo-dark logo-img logo-img-lg" src="/images/logo-dark.png" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Acesse sua conta</h4>
                                    </div>
                                </div>
                                <form class=" form-centered" id="loginForm">
                                    <div class="form-group">
                                        <div>
                                            <div class="error login-error"></div>
                                        </div>

                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg"  id="inpLogin" placeholder="Insira seu email">
                                        </div>
                                    </div><!-- .form-group -->
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password">Senha</label>
                                            <!-- <a class="link link-primary link-sm" tabindex="-1" href="html/pages/auths/auth-reset-v3.html">Esqueceu a senha?</a> -->
                                        </div>
                                        <div class="form-control-wrap align-items-center">
                                            <!-- <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password"
                                            style="display: block; height: 100%; display: flex;">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a> -->
                                            <input type="password" style="padding: 15px 40px;" class="form-control form-control-lg" id="inpPassword" enter="onSubmit" placeholder="Insira sua senha">
                                        </div>
                                    </div><!-- .form-group -->
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block" type="button" click="onSubmit">Entrar</button>
                                    </div>
                                </form><!-- form -->
                                <div>
                                    <span>Não tem uma conta?</span> 
                                    <a class="link-color" style="font-weight: 500" tabindex="-1" href="/register">Cadastre-se</a>.
                                </div>
                               
                            </div>
                        </div>
                    </div>
                   
                </div>


               
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>

    
    <Footer />
</App>
