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
                                    <a href="<?= site_url() . '/app/astronmembers' ?>" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
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

            <div class="card-bordered card bg-blur frm_edit_app_sellflux">
                <div class="card-inner">
                    
                    <div class="d-flex justify-content-end">
                        <div class="form-group">
                            <div class="custom-control custom-switch checked">
                                <input type="checkbox" class="custom-control-input inp_sellflux_enabled" id="switchEnableAppSellflux"
                                <?php if ($integration->status ?? false): ?> checked="" <?php endif; ?>>
                                <label class="custom-control-label" for="switchEnableAppSellflux">
                                <?= __('Activate') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= __('Product') ?></label>
                        <div class="form-control-wrap">
                            <select class="form-control inp_sellflux_product_id">
                                <?php foreach($products as $product): ?>
                                    <option value="<?= $product->id ?>" <?php if ($product->id == $integration->product_id): ?> selected="" <?php endif ?>><?= $product->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= __('Link') ?></label>
                        <div class="form-control-wrap">
                            <input class="form-control inp_sellflux_link" placeholder="" value="<?= ($integration->link ?? false) ? aes_decode_db($integration->link) : '' ?>">
                        </div>
                    </div>

                    <div class="flex justify-content-end">
                        <button type="button" class="btn btn-primary" click="onSubmitSellflux"><?= __('Save') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</content>