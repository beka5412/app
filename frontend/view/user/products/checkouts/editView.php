<?php

use Backend\Enums\Checkout\ECheckoutStatus; ?>
<title>Editar checkout</title>

<content ready="ready">

  <!-- Codigo para a pagina de criar e editar checkout -->
  <style>
    .btn-afilie {
      background: #798bff;
      color: #fff;
      font-weight: 500;
      border: 0;
      padding: 14px 30px;
      margin: 0 auto;
      border-radius: 10px;
      font-size: 15px;
      width: 100%;
    }

    #topLoading,
    #sidebarLoading,
    #footerLoading,
    #logoLoading,
    #faviconLoading,
    #banner2Loading {
      display: none
    }
  </style>

  <div class="nk-block-head">
    <div class="nk-block-between g-3">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">
          Editar Checkout -
          <?php echo $checkout->name; ?>
        </h3>
      </div>
      <div class="nk-block-head-content">
        <a href="<?php echo site_url() . "/product/" . $product->id . "/checkouts"; ?>" class="btn btn-outline-light bg-white
          d-none d-sm-inline-flex">
          <em class="icon ni ni-arrow-left"></em>
          <span>
            <?= __('Back') ?>
          </span>
        </a>
        <a to="<?php echo site_url() . " /product/" . $product->id . "/checkouts"; ?>" href="
          <?php echo site_url() . "/product/" . $product->id . "/checkouts"; ?>" class="btn btn-icon btn-outline-light
          bg-white d-inline-flex d-sm-none">
          <em class="icon ni ni-arrow-left"></em>
        </a>
      </div>
    </div>
  </div>

  <div class="row frm_edit_checkout">
    <div class="col-sm-12">
      <div class="card card-bordered">
        <div class="card-inner">

          <ProductCheckoutMenu />

          <div class="tab-content">
            <div class="tab-pane active" id="tabLayouts">
              <div class="d-flex">
                <div class="col-lg-8 card-inner">
                  <div class="row gy-4">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="title_text">
                          <?= __('Checkout name') ?>
                        </label>
                        <input class="form-control inp_checkout_name" placeholder="" name="name" type="text" value="<?php echo $checkout->name; ?>" id="name">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="title_text preview-title">
                          <?= __('Status') ?>
                        </label>
                        <select class="form-control inp_checkout_status">
                          <option <?php if ($checkout?->status == ECheckoutStatus::DRAFT->value) : ?> selected="" <?php endif; ?> value="<?php echo ECheckoutStatus::DRAFT->value; ?>">Rascunho
                          </option>
                          <option <?php if ($checkout?->status == ECheckoutStatus::PUBLISHED->value) : ?> selected="" <?php endif; ?> value="<?php echo ECheckoutStatus::PUBLISHED->value; ?>">Ativo
                          </option>
                          <option <?php if ($checkout?->status == ECheckoutStatus::DISABLED->value) : ?> selected="" <?php endif; ?> value="<?php echo ECheckoutStatus::DISABLED->value; ?>">
                            Desativado
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="title_text preview-title">
                          <?= __('Set checkout as default?') ?>
                        </label>
                        <div class="custom-control w-100 custom-switch checked">
                          <input <?php if ($checkout->default == 1) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input inp_checkout_set_default" id="setDefaultCheckout">
                          <label class="custom-control-label" for="setDefaultCheckout">
                            <?= __('Yes') ?>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label for="title_text preview-title">Dark Mode? (Modelo Escuro)</label>
                      <div class="custom-control w-100 custom-switch checked">
                        <input <?php if ($checkout->dark_mode == 1) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input inp_checkout_darkmode" id="checkoutEnableDarkmode">
                        <label class="custom-control-label" for="checkoutEnableDarkmode">
                          <?= __('Yes') ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <hr class="preview-hr">
                  <div class="card card-inner preview-block">
                    <div class="row gy-4">
                      <div class="col-sm-12">
                        <div class="row">
                          <div class="col-md-6">
                            <img id="imgTopBanner" src="<?php echo $checkout->top_banner ? site_url() . $checkout->top_banner : site_url() . '/images/default.png' ?>" default-img="<?php echo site_url() ?>/images/default.png" />
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-label" for="customFileLabel">
                                Top banner
                                <div id="topLoading">
                                  <Loading />
                                </div>
                              </label>
                              <div>
                                <div class="form-control-wrap">
                                  <div class="form-file">
                                    <input type="hidden" class="inp_checkout_top_banner" value="<?php echo $checkout->top_banner; ?>">
                                    <input type="file" class="form-file-input" id="checkoutTopBanner" change="checkoutUploadTopBanner">
                                    <label class="form-file-label" for="customFile">Choose file</label>
                                  </div>
                                </div>
                                <div class="d-flex justify-content-end ml-1 mt-1">
                                  <button class="btn btn-danger" click="checkoutRemoveTopBanner">
                                    <em class="icon ni ni-trash"></em>
                                    <?= __('Remove') ?>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-sm-12">
                        <div class="form-group">

                        <div class="row">
                            <div class="col-md-6">
                              <img id="imgTop2Banner" src="<?php echo $checkout->top_2_banner ? site_url() . $checkout->top_2_banner : site_url() . '/images/default.png' ?>" default-img="<?php echo site_url() ?>/images/default.png" />
                            </div>
                            
                            <div class="col-md-6">
                              <label class="form-label" for="customFileLabel">
                                Banner 2
                                <div id="banner2Loading">
                                  <Loading />
                                </div>
                              </label>
                              <div>
                                <div class="form-control-wrap">
                                  <div class="form-file">
                                    <input type="hidden" class="inp_checkout_top_2_banner" value="<?php echo $checkout->top_2_banner; ?>">
                                    <input type="file" class="form-file-input" id="checkoutTop2Banner" change="checkoutUploadTop2Banner">
                                    <label class="form-file-label" for="customFile">
                                      <?= __('Choose file') ?>
                                    </label>
                                  </div>
                                </div>
                                <div class="d-flex justify-content-end mt-1">
                                  <button class="btn btn-danger" click="checkoutRemoveTop2Banner">
                                    <em class="icon ni ni-trash"></em>
                                    <?= __('Remove') ?>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-12">
                        <div class="form-group">

                          <div class="row">
                            <div class="col-md-6">
                              <img id="imgFooterBanner" src="<?php echo $checkout->top_2_banner ? site_url() . $checkout->top_2_banner : site_url() . '/images/default.png' ?>" default-img="<?php echo site_url() ?>/images/default.png" />
                            </div>
                            <div class="col-md-6">
                              <label class="form-label" for="customFileLabel">
                                Banner no rodapé
                                <div id="footerLoading">
                                  <Loading />
                                </div>
                              </label>
                              <div>
                                <div class="form-control-wrap">
                                  <div class="form-file">
                                    <input type="hidden" class="inp_checkout_footer_banner" value="<?php echo $checkout->footer_banner; ?>">
                                    <input type="file" class="form-file-input" id="checkoutFooterBanner" change="checkoutUploadFooterBanner">
                                    <label class="form-file-label" for="customFile">
                                      <?= __('Choose file') ?>
                                    </label>
                                  </div>
                                </div>
                                <div class="d-flex justify-content-end mt-1">
                                  <button class="btn btn-danger" click="checkoutRemoveFooterBanner">
                                    <em class="icon ni ni-trash"></em>
                                    <?= __('Remove') ?>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <hr class="preview-hr">
                  <div class="card card-inner preview-block mt-4">
                    <div class="row gy-4">
                      <div class="col-sm-12">
                        <div class="row">
                          <div class="col-md-4">
                            <img id="imgLogoBanner" src="<?php echo $checkout->logo ? site_url() . $checkout->logo : site_url() . '/images/default.png' ?>" default-img="<?php echo site_url() ?>/images/default.png" class="default-img" />
                          </div>
                          <div class="col-md-8">
                            <label class="form-label" for="customFileLabel">
                              Logo
                              <div id="logoLoading">
                                <Loading />
                              </div>
                            </label>
                            <div class="form-control-wrap">
                              <div class="form-file">
                                <input type="hidden" class="inp_checkout_logo" value="<?php echo $checkout->logo; ?>">
                                <input type="file" class="form-file-input" id="checkoutLogoBanner" change="checkoutUploadLogoBanner">
                                <label class="form-file-label" for="customFile">Choose
                                  file</label>
                              </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                              <button class="btn btn-danger" click="checkoutRemoveLogoBanner">
                                <em class="icon ni ni-trash"></em>
                                <?= __('Remove') ?>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- <div class="col-sm-12">
                          <div class="row">
                            <div class="col-md-4">
                              <img id="imgFaviconBanner" src="<?php echo $checkout->favicon ? site_url() . $checkout->favicon : site_url() . '/images/default.png' ?>" default-img="<?php echo site_url() ?>/images/default.png" class="default-img" />
                            </div>
                            <div class="col-md-8">
                              <label class="form-label" for="customFileLabel">
                                Favicon
                                <div id="faviconLoading"><Loading /></div>
                              </label>
                              <div class="form-control-wrap">
                                <div class="form-file">
                                  <input type="hidden" class="inp_checkout_favicon" value="<?php echo $checkout->favicon; ?>">
                                  <input type="file" class="form-file-input" id="checkoutFaviconBanner" change="checkoutUploadFaviconBanner">
                                  <label class="form-file-label" for="customFile">Choose file</label>
                                </div>
                              </div>
                              <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-danger" click="checkoutRemoveFaviconBanner">
                                  <em class="icon ni ni-trash"></em>
                                  <?= __('Remove') ?>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div> -->
                    </div>
                  </div>
                  <hr class="preview-hr">
                  <!-- <div class="d-flex">
                      <div class="col-sm-4">
                        <label class="form-label">Cor do Topo</label>
                        <div class="form-control-wrap" style=" width:60px;">
                          <input class="form-control inp_top_color" type="color" value="<?php echo $checkout->top_color; ?>">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <label class="form-label">Cor Primaria</label>
                        <div class="form-control-wrap" style=" width:60px;">
                          <input class="form-control inp_primary_color" type="color" value="<?php echo $checkout->primary_color; ?>">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <label class="form-label">Cor Secundaria</label>
                        <div class="form-control-wrap" style=" width:60px;">
                          <input class="form-control inp_secondary_color" type="color" value="<?php echo $checkout->secondary_color; ?>">
                        </div>
                      </div>
                    </div> -->
                  <hr class="preview-hr">
                  <div class="row mt-4">
                    <div class="d-block col-md-4">
                      <label for="title_text preview-title">
                        <?= __('Enable Timer?') ?>
                      </label>
                      <div class="custom-control mt-1 w-100 custom-switch checked">
                        <input <?php if ($checkout->countdown_enabled == 1) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input inp_checkout_countdown_enabled" id="checkoutEnableCountdown">
                        <label class="custom-control-label" for="checkoutEnableCountdown">
                          <?= __('Yes') ?>
                        </label>
                      </div>
                    </div>
                    <div class="form-group col-md-8">
                      <label for="title_text">
                        <?= __('Countdown timer text') ?>
                      </label>
                      <input class="form-control inp_checkout_countdown_text" placeholder="Ex: Essa Oferta termina em:" name="title_text" type="text" value="<?php echo $checkout->countdown_text; ?>" id="title_text">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="countdown_time">
                        <?= __('Time') ?>
                      </label>
                      <input class="form-control inp_checkout_countdown_time" load="$('.inp_checkout_countdown_time').mask('00:00');" placeholder="<?= __('Eg.:') ?> 15:00" maxlength="5" name="countdown_time" type="text" value="<?php echo $checkout->countdown_time; ?>" id="countdown_time">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="header_bg_color">
                        <?= __('Background color') ?>
                      </label>
                      <input class="form-control inp_checkout_countdown_color" placeholder="<?= __('Eg.:') ?> #ff0000" name="header_bg_color" type="color" value="<?php echo $checkout->header_bg_color; ?>" id="header_bg_color" style="padding: 0; height: 53px;">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="header_text_color">
                        <?= __('Text color') ?>
                      </label>
                      <input class="form-control inp_checkout_countdown_color" placeholder="<?= __('Eg.:') ?> #ff0000" name="header_text_color" type="color" value="<?php echo $checkout->header_text_color; ?>" id="header_text_color" style="padding: 0; height: 53px;">
                    </div>
                  </div>
                  <hr class="preview-hr">
                </div>
                <div class="col-lg-4">
                  <iframe id="iframeCheckoutPreview" src="<?php echo get_subdomain_serialized('checkout') ?>/<?php echo $checkout->sku ?? '' ?>" data-base-src="<?php echo get_subdomain_serialized('checkout') ?>/<?php echo $checkout->sku ?? '' ?>" height="1000" width="100%">
                  </iframe>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="tabPagamento">
              <div class="nk-block">
                <div class="row g-gs">
                  <div class="col-12 d-flex">
                    <div class="col-md-12">
                      <div class="card card-bordered card-preview">
                        <div class="card-inner">
                          <div class="preview-block">
                            <div class="row gy-4">
                              <div class="col-sm-12">
                                <div class="form-group">
                                  <label class="form-label" for="default-01">Método de
                                    pagamento</label>
                                  <div class="form-group grids">
                                    <div class="custom-control custom-checkbox">
                                      <input <?php if ($checkout->pix_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="checkoutPmPix" value="pix">
                                      <label class="custom-control-label" for="checkoutPmPix">Pix</label>
                                    </div>
                                    <div class="custom-control custom-checkbox ms-2">
                                      <input <?php if ($checkout->credit_card_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="checkoutPmCreditCard" value="credit_card">
                                      <label class="custom-control-label" for="checkoutPmCreditCard">Cartão de
                                        crédito</label>
                                    </div>
                                    <div class="custom-control custom-checkbox ms-2">
                                      <input <?php if ($checkout->billet_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="checkoutPmBillet" value="billet">
                                      <label class="custom-control-label" for="checkoutPmBillet">Boleto</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-inner">
                          <div class="preview-block">
                            <div class="row gy-4">
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <label class="form-label" for="default-01">Desconto
                                    no Pix?</label>
                                  <div class="form-group">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" name="pix" id="checkoutPixDiscountEnabled" toggle=".div_checkout_edit_discount_pix" load="toggleStatement(element)" <?php if ($checkout->pix_discount_enabled) : ?> checked="" <?php endif; ?>>
                                      <label class="custom-control-label" for="checkoutPixDiscountEnabled"></label>
                                    </div>
                                  </div>
                                  <div class="div_checkout_edit_discount_pix">
                                    <label class="form-label" for="checkoutPixDiscountAmount">Valor do
                                      Desconto</label>
                                    <div class="form-control-wrap">
                                      <input type="text" class="form-control" id="checkoutPixDiscountAmount" placeholder="" value="<?php echo $checkout->pix_discount_amount; ?>" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)" />
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <label class="form-label" for="default-01">Desconto
                                    no Cartão?</label>
                                  <div class="form-group">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" name="credit_card" id="checkoutCreditCardDiscountEnabled" toggle=".div_checkout_edit_discount_credit_card" load="toggleStatement(element)" <?php if ($checkout->credit_card_discount_enabled) : ?> checked="" <?php endif; ?>>
                                      <label class="custom-control-label" for="checkoutCreditCardDiscountEnabled"></label>
                                    </div>
                                  </div>
                                  <div class="div_checkout_edit_discount_credit_card">
                                    <label class="form-label" for="checkoutCreditCardDiscountAmount">Valor
                                      do Desconto</label>
                                    <div class="form-control-wrap">
                                      <input type="text" class="form-control" id="checkoutCreditCardDiscountAmount" placeholder="" value="<?php echo $checkout->credit_card_discount_amount; ?>" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)" />
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <label class="form-label" for="default-01">Desconto
                                    no Boleto?</label>
                                  <div class="form-group">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" name="billet" id="checkoutBilletDiscountEnabled" toggle=".div_checkout_edit_discount_billet" load="toggleStatement(element)" <?php if ($checkout->billet_discount_enabled) :
                                                                                                                                                                                                                        ?> checked="" <?php endif; ?>>
                                      <label class="custom-control-label" for="checkoutBilletDiscountEnabled"></label>
                                    </div>
                                  </div>
                                  <div class="div_checkout_edit_discount_billet">
                                    <label class="form-label" for="checkoutBilletDiscountAmount">Valor do
                                      Desconto</label>
                                    <div class="form-control-wrap">
                                      <input type="text" class="form-control" id="checkoutBilletDiscountAmount" placeholder="" value="<?php echo $checkout->billet_discount_amount; ?>" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)" />
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-inner">
                          <div class="preview-block">
                            <div class="row gy-4">
                              <div class="col-sm-12">
                                <div class="form-group">
                                  <label class="form-label">Parcelamento</label>
                                  <div class="form-control-wrap">
                                    <select class="form-select" id="checkoutInstallmentsQtySelect">
                                      <option <?php if ($checkout->max_installments == 1) : ?> selected="" <?php endif; ?> value="1">
                                        Apenas à vista
                                      </option>
                                      <option <?php if ($checkout->max_installments == 2) : ?> selected="" <?php endif; ?> value="2">
                                        Até 2x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 3) : ?> selected="" <?php endif; ?> value="3">
                                        Até 3x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 4) : ?> selected="" <?php endif; ?> value="4">
                                        Até 4x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 5) : ?> selected="" <?php endif; ?> value="5">
                                        Até 5x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 6) : ?> selected="" <?php endif; ?> value="6">
                                        Até 6x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 7) : ?> selected="" <?php endif; ?> value="7">
                                        Até 7x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 8) : ?> selected="" <?php endif; ?> value="8">
                                        Até 8x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 9) : ?> selected="" <?php endif; ?> value="9">
                                        Até 9x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 10) : ?> selected="" <?php endif; ?> value="10">
                                        Até 10x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 11) : ?> selected="" <?php endif; ?> value="11">
                                        Até 11x
                                      </option>
                                      <option <?php if ($checkout->max_installments == 12) : ?> selected="" <?php endif; ?> value="12">
                                        Até 12x
                                      </option>
                                    </select>
                                  </div>
                                </div>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 d-flex">

                    <div class="col-md-12">
                      <div class="card card-bordered card-preview">
                        <div class="card-inner">
                          <div class="preview-block">
                            <div class="row gy-4">
                              <div class="col-sm-12">
                                <div class="form-group">
                                  <label class="form-label">Habilitar página de
                                    obrigado no Pix?</label>
                                  <div class="form-group">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="checkoutPixThanksPageEnabled" toggle=".div_input_pix_thanks_page_url" load="toggleStatement(element)" <?php if ($checkout->pix_thanks_page_enabled) :
                                                                                                                                                                                                    ?> checked="" <?php endif; ?>>
                                      <label class="custom-control-label" for="checkoutPixThanksPageEnabled"></label>
                                    </div>
                                  </div>
                                  <div class="div_input_pix_thanks_page_url">
                                    <label class="form-label" for="pixThanksPageURL">Url da página</label>
                                    <div class="form-control-wrap">
                                      <input type="text" class="form-control" id="pixThanksPageURL" placeholder="" value="<?php echo $checkout->pix_thanks_page_url; ?>">
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="col-sm-12">
                                <div class="form-group">
                                  <label class="form-label">Habilitar página de
                                    obrigado no cartão de crédito?</label>
                                  <div class="form-group">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="checkoutCreditCardThanksPageEnabled" toggle=".div_checkout_input_credit_card_thanks_page_url" load="toggleStatement(element)" <?php if ($checkout->credit_card_thanks_page_enabled) : ?> checked="" <?php endif; ?>>
                                      <label class="custom-control-label" for="checkoutCreditCardThanksPageEnabled"></label>
                                    </div>
                                  </div>
                                  <div class="div_checkout_input_credit_card_thanks_page_url">
                                    <label class="form-label" for="checkoutCreditCardThanksPageURL">Url da
                                      página</label>
                                    <div class="form-control-wrap">
                                      <input type="text" class="form-control" id="checkoutCreditCardThanksPageURL" placeholder="" value="<?php echo $checkout->credit_card_thanks_page_url; ?>">
                                    </div>
                                  </div>
                                </div>
                              </div>


                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- <div class="c-right ps-1 mt-1 d-flex justify-content-end">
                  <button click="productSettingsOnSubmit" class="btn btn-primary">Salvar</button>
                </div> -->
              </div>
            </div>

            <div class="tab-pane card card-bordered card-preview" id="tabFerramentas">
              <div class="card-inner">
                <div class="preview-block">
                  <div class="nk-block-head">
                    <div class="nk-block-head-content">
                      <h4 class="title nk-block-title">Notificações</h4>
                    </div>
                  </div>
                  <div class="row gy-4">
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Pessoas interessadas nas
                          últimas 24 horas</label>
                        <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input <?php if ($checkout->notification_interested24_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input
                            swt_notification_interested24_enabled" name="" id="inter24Enabled" toggle=".div_edit_inter24" load="toggleStatement(element)">
                            <label class="custom-control-label" for="inter24Enabled"></label>
                          </div>
                        </div>
                        <div class="div_edit_inter24 d-none">
                          <label class="form-label" for="inter24Amount">Quantidade</label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_notification_interested24_number" id="pinter24Amount" placeholder="" value="<?php echo $checkout->notification_interested24_number; ?>" keydown="$onlyNumbers">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Pessoas interessadas na
                          última semana</label>
                        <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input <?php if ($checkout->notification_interested_weekly_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input
                            swt_notification_interested_weekly_enabled" name="" id="interuEnabled" toggle=".div_edit_interu" load="toggleStatement(element)">
                            <label class="custom-control-label" for="interuEnabled"></label>
                          </div>
                        </div>
                        <div class="div_edit_interu d-none">
                          <label class="form-label" for="interuAmount">Quantidade</label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_notification_interested_weekly_number" id="interuAmount" placeholder="" value="<?php echo $checkout->notification_interested_weekly_number; ?>" keydown="$onlyNumbers">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Compras feitas nas últimas 24
                          horas</label>
                        <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input <?php if ($checkout->notification_order24_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input
                            swt_notification_order24_enabled" name="" id="compras24Enabled" toggle=".div_edit_compras24" load="toggleStatement(element)">
                            <label class="custom-control-label" for="compras24Enabled"></label>
                          </div>
                        </div>
                        <div class="div_edit_compras24 d-none">
                          <label class="form-label" for="compras24Amount">Quantidade</label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_notification_order24_number" id="compras24Amount" placeholder="" value="<?php echo $checkout->notification_order24_number; ?>" keydown="$onlyNumbers">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Compras feitas na última
                          semana</label>
                        <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input <?php if ($checkout->notification_order_weekly_enabled) : ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input
                            swt_notification_order_weekly_enabled" name="" id="comprasuEnabled" toggle=".div_edit_comprasu" load="toggleStatement(element)">
                            <label class="custom-control-label" for="comprasuEnabled"></label>
                          </div>
                        </div>
                        <div class="div_edit_comprasu d-none">
                          <label class="form-label" for="comprasuAmount">Quantidade</label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_notification_order_weekly_number" id="comprasuAmount" placeholder="" value="<?php echo $checkout->notification_order_weekly_number; ?>" keydown="$onlyNumbers">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-inner">
                <div class="preview-block">
                  <div class="row gy-4">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">Integração</label>
                        <div class="form-control-wrap">
                          <select class="form-select" id="checkoutInstallmentsQtySelect">
                            <option value="1">Whatsapp</option>
                            <option value="2">JivoChat</option>
                            <option value="3">Facebook</option>
                            <option value="4">Intercom</option>
                            <option value="5">Manychat</option>
                            <option value="6">Hubspot</option>
                            <option value="7">Zendesk</option>
                            <option value="8">Tawk</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label" for="phone-number">Celular </label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control inp_whatsapp_number" id="phone-number" required="" placeholder="" maxlength="14" value="<?php echo $checkout->whatsapp_number; ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="tabTestimonials">
              <ProductCheckoutTestimonials />
            </div>

            <div class="tab-pane" id="tabBackRedirect">

              <div class="frm_edit_upsell">
                <div class="card card-bordered card-preview">
                  <div class="card-inner">
                    <div class="row">

                      <div>
                        <div class="form-group">
                          <label class="form-label" for="default-01">Habilitar o Back Redirect neste checkout?</label>
                          <div class="form-group">
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input inp_backredirect_enabled" 
                              <?php if ($checkout->backredirect_enabled): ?> checked="" <?php endif ?>
                              name="reg-public" id="switch-backredirect-enabled">
                              <!-- load="toggleStatement(element, true)" -->
                              <label class="custom-control-label" for="switch-backredirect-enabled"></label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="mt-2">
                        <label class="form-label" for="default-01">
                          Link do redirecionamento
                        </label>
                        <div class="form-group">
                          <input type="text" class="form-control inp_backredirect_url" value="<?= $checkout->backredirect_url ?>" />
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="c-right ps-1 mt-1 d-flex justify-content-end">
                  <button click="checkoutBackRedirectOnSubmit" class="btn btn-primary">Salvar</button>
                </div>

              </div>



            </div>

            <div class="c-right ps-1 d-flex justify-content-end">
              <button class="mt-1 w-100 btn-afilie btn-demote mt-4 btn_save_checkout" click="checkoutOnSubmit">Salvar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Codigo para a pagina de criar e editar checkout -->
</content>