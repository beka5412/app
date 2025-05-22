<title>Apps</title>

<content ready="editCademiOnReady">
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
                                    <a  href="<?= site_url() . '/app/cademi' ?>" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
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
                </div>
            </div>

            <div class="card-bordered card bg-blur frm_edit_app_cademi">
                <div class="card-inner">
                    
                    <div class="d-flex justify-content-end">
                        <div class="form-group">
                            <div class="custom-control custom-switch checked">
                                <input type="checkbox" class="custom-control-input inp_cademi_enabled" id="switchEnableAppCademi"
                                <?php if ($integration->status ?? false): ?> checked="" <?php endif; ?>>
                                <label class="custom-control-label" for="switchEnableAppCademi">
                                <?= __('Activate') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= __('Product') ?></label>
                        <div class="form-control-wrap">
                            <select class="form-control inp_cademi_product_id" change="appCademiProductOnChange">
                                <?php foreach($products as $product): ?>
                                    <option value="<?= $product->id ?>" <?php if (cmp_both_valid($product->id, '==', $integration->product_id ?? '')): ?> selected="" <?php endif ?>><?= $product->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= __('Product ID') ?></label>
                        <div class="form-control-wrap">
                            <input disabled class="form-control inp_cademi_product_id_view" placeholder="" value="<?= $integration->product_id ?? '' ?>" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= __('Subdomain') ?></label>
                        <div class="form-control-wrap">
                            <input class="form-control inp_cademi_subdomain" placeholder="" value="<?= $integration->subdomain ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= __('Token') ?></label>
                        <div class="form-control-wrap">
                            <input class="form-control inp_cademi_token" placeholder="" value="<?= ($integration->token ?? false) ? aes_decode_db($integration->token) : '' ?>">
                        </div>
                    </div>

                    <div class="flex justify-content-end">
                        <button type="button" class="btn btn-primary" click="onSubmitCademi"><?= __('Save') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</content>
