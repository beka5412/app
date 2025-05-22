<?php

use Backend\Enums\Checkout\ECheckoutStatus; ?>
<title>Checkouts</title>

<content>
    <div class="nk-content-body">

        <ProductMenu />

        <div class="tab-content">

            <!-- Checkout -->
            <div class="tab-pane active" id="tabCheckout">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-more-v"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li class="nk-block-tools-opt">
                                            <a href="#" data-target="addProduct"
                                                class="toggle btn btn-icon btn-primary d-md-none">
                                                <em class="icon ni ni-plus"></em>
                                            </a>
                                            <a href="javascript:void(0);"
                                                to="<?php echo site_url(); ?>/product/{id}/checkout/new"
                                                class="toggle btn btn-primary d-none d-md-inline-flex">
                                                <em class="icon ni ni-plus"></em>
                                                <span><?= __('Create checkout') ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" id="pid">
                                    <label class="custom-control-label" for="pid"></label>
                                </div>
                            </div>
                            <div class="nk-tb-col tb-col-sm">
                                <span><?= __('Nombre') ?></span>
                            </div>
                            <div class="nk-tb-col">
                                <span><?= __('Checkout link') ?></span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span><?= __('Description') ?></span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span><?= __('Info') ?></span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span><?= __('Status') ?></span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">

                            </div>
                        </div>
                        <?php foreach ($checkouts as $checkout) : ?>
                        <div class="nk-tb-item tr">
                            <div class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" id="pid1">
                                    <label class="custom-control-label" for="pid1"></label>
                                </div>
                            </div>
                            <div class="nk-tb-col tb-col-sm">
                                <a class="tb-product"
                                    href="<?php echo site_url() . "/product/$product->id/checkout/$checkout->id/edit"; ?>">
                                    <span class="title"><?php echo $checkout->name; ?></span>
                                </a>
                            </div>
                            <div class="nk-tb-col">
                                <span class="tb-lead">
                                    <a href="<?= get_subdomain_serialized('checkout') ?>/<?php echo $checkout->sku; ?>"
                                        target="_blank">
                                        <b><?= __('Checkout link') ?></b>
                                    </a>
                                </span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span class="tb-lead">
                                   
                                </span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <?php if ($checkout->default) : ?>
                                <span class="badge bg-warning"><?= __('Default') ?></span>
                                <?php else : ?>
                                <span class="badge bg-success"><?= __('Customized') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <?php if ($checkout->status == ECheckoutStatus::PUBLISHED->value) : ?>
                                <span class="badge badge-dot badge-dot-xs bg-success"><?= __('Active') ?></span>
                                <?php elseif ($checkout->status == ECheckoutStatus::DISABLED->value) : ?>
                                <span class="badge badge-dot badge-dot-xs bg-danger"><?= __('Disabled') ?></span>
                                <?php elseif ($checkout->status == ECheckoutStatus::DRAFT->value) : ?>
                                <span class="badge badge-dot badge-dot-xs bg-secondary"><?= __('Draft') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1 my-n1">
                                    <li class="me-n1">
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="<?php echo site_url() . "/product/$product->id/checkout/$checkout->id/edit"; ?>">
                                                            <em class="icon ni ni-edit"></em>
                                                            <span><?= __('Edit') ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if (!$checkout->default) : ?>
                                                    <li>
                                                        <a href="javascript:;" click="checkoutDestroy"
                                                            data-product-id="<?php echo $product->id; ?>"
                                                            data-checkout-id="<?php echo $checkout->id; ?>">
                                                            <em class="icon ni ni-trash"></em>
                                                            <span><?= __('Delete') ?></span>
                                                        </a>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <!-- Fim Chcekout -->

        </div>
    </div>
</content>