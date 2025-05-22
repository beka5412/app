<title>Upsell</title>

<content>
    <script type="application/json" json-id="snippet">
    <?php
        $site_url = site_url();

        $accept_button = "
            <button id=\"btnUpsell\" class=\"lotuzpay-button\" data-product-id=\"$product->id\">
                [accept_text]
            </button>
        ";
        
        $reject_button = "
            <a href=\"[rejection_link]\" id=\"btnUpsellReject\" class=\"lotuzpay-button-reject\" data-product-id=\"$product->id\">
                [reject_text]
            </a>
        ";

        $str = "
            <link href=\"https://fonts.cdnfonts.com/css/satoshi\" rel=\"stylesheet\">
            <link href=\"$site_url/snippets/upsell.min.css\" rel=\"stylesheet\">

            <div class=\"upsell-wrapper\">
                <div class=\"lotuzpay-flex lotuzpay-gap\">
                    [accept_button]
                    [reject_button]
                </div>

                <div style=\"text-align: center\">
                    <div class=\"error-payment\" id=\"elementErrorPayment\"></div>
                </div>
            </div>

            <script src=\"$site_url/snippets/upsell.min.js\"></script>
        ";
        $str = str_replace('  ', '', $str);

        echo json_encode([
            "template" => $str,
            "accept_button" => $accept_button,
            "reject_button" => $reject_button,
        ]);
    ?>
    </script>

    <div class="nk-content-body">
        <ProductMenu />

        <div class="frm_edit_upsell">

            <div class="card card-bordered card-preview">
                <div class="card-inner">

                    <div class="row gy-4 mt-4">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="default-01">O produto tem upsell?</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input inp_has_upsell" <?php if ($product->has_upsell > 0) : ?> checked="" <?php endif; ?> name="reg-public" id="switch-has-upsell">
                                            <!-- toggle=".div_product_has_upsell" -->
                                        <!-- load="toggleStatement(element, true)" -->
                                        <label class="custom-control-label" for="switch-has-upsell"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 div_product_has_upsell">
                            <div class="form-group">
                                <label class="form-label" for="default-01">Link da página de redirecionamento para o upsell</label>
                                <div class="form-control-wrap">
                                    <input class="form-control inp_upsell_link" value="<?php echo $product->upsell_link; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 40px; border-top: 1px solid #1d2d40 !important"></div>

                    <div class="row gy-4 mt-4">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="default-01">Tem a opção negar oferta? link de direcionamento para o downsell</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input inp_has_upsell_rejection" <?php if ($product->has_upsell_rejection > 0) : ?> checked="" <?php endif; ?> name="reg-public" 
                                            id="switch-has-reject-upsell">
                                            <!-- toggle=".div_product_has_upsell_rejection" -->
                                            <!-- load="toggleStatement(element, true)" -->
                                        <label class="custom-control-label" for="switch-has-reject-upsell"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 div_product_has_upsell_rejection">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="default-01">Link da página de negativa</label>
                                        <div class="form-control-wrap">
                                            <input class="form-control inp_upsell_rejection_link" value="<?php echo $product->upsell_rejection_link; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="default-01">Texto do botão negar</label>
                                        <div class="form-control-wrap">
                                            <input class="form-control inp_upsell_rejection_text" value="<?php echo $product->upsell_rejection_text ?: __('I don\'t want'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label" for="default-01">Texto do botão comprar</label>
                                <div class="form-control-wrap">
                                    <input class="form-control inp_upsell_text" value="<?php echo $product->upsell_text ?: __('Buy'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="default-01">&nbsp;</label>
                                <div class="form-control-wrap">
                                    <buttton class="btn btn-primary w-100 text-center d-block" click="productUpsellGenerateSnippet">Gerar snippet</buttton>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div>
                            <label class="form-label" for="default-01">Copie o snippet abaixo e use este produto como
                                upsell</label>
                            <div class="form-group">
                                <textarea class="form-control inp_snippet" click="productCopyUpsellSnippet" style="width: 100%"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row gy-4 mt-4">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="form-label" for="default-01">Tem o botão negar oferta?</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input inp_has_deny_offer" <?php if ($product->has_deny_offer > 0) : ?> checked="" <?php endif; ?> name="reg-public" 
                                            id="switch-has-deny-upsell" toggle=".div_product_has_deny_offer">
                                        <label class="custom-control-label" for="switch-has-deny-upsell"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 div_product_has_deny_offer" toggle-change-item="product-has-upsell" style="display:<?php echo $product->has_deny_offer > 0 ? 'block' : 'none'; ?> ">
                            <div class="form-group">
                                <label class="form-label" for="default-01">Link do botão negar oferta</label>
                                <div class="form-control-wrap">
                                    <input class="form-control inp_upsell_deny_link" value="<?php echo $product->upsell_deny_link; ?>">
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="c-right ps-1 mt-1 d-flex justify-content-end">
                <button click="productUpsellOnSubmit" class="btn btn-primary">Salvar</button>
            </div>

        </div>
    </div>
</content>