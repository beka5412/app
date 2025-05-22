<title>Apps</title>

<content>
    <div class="nk-content-body">

        <div class="container">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-between g-3">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title"><?= __('Configure') ?></h3>
                                </div>
                                <div class="nk-block-head-content">
                                    <a to="<?= site_url() . '/apps' ?>" href="<?= site_url() . '/apps' ?>" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span><?= __('Back') ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-12"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 mb-4">
                <div>
                    <h4><?= __('How to generate an API Token?') ?></h4>
                    <p><?= __('Simply create an account on UTMify, navigate to the "Integrations" tab and create an API Credential.') ?></p>
                </div>
            </div>

            <div class="card-bordered card bg-blur frm_edit_app_utmify">
                <div class="card-inner">
                    <div class="form-group">
                        <label class="form-label"><?= __('API Key') ?></label>
                        <div class="form-control-wrap">
                            <input class="form-control inp_utmify_apikey" placeholder="Sua chave aqui" value="<?= $utmify->apikey ?? '' ?>">
                        </div>
                    </div>
                    <div class="flex justify-content-end">
                        <button type="button" class="btn btn-primary" click="onSubmitAppUTMify"><?= __('Save') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</content>