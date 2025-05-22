<title>Configurações</title>

<content>
  <div class="nk-content-body">

    <div class="nk-block-head">
      <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">
            <?php echo $product->sku; ?>
          </h3>
        </div>
        <div class="nk-block-head-content">
          <a to="<?php echo site_url(); ?>/products" href="<?php echo site_url(); ?>/products"
            class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
            <em class="icon ni ni-arrow-left"></em>
            <span>Voltar</span>
          </a>
          <a to="<?php echo site_url(); ?>/products" href="<?php echo site_url(); ?>/products"
            class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
            <em class="icon ni ni-arrow-left"></em>
          </a>
        </div>
      </div>
    </div>

    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>" to="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>">
          <em class="icon ni ni-box"></em>
          <span class="tb-col-sm">Geral</span>
        </a>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link active" href="<?php echo site_url() . "/product/" . $product->id . "/settings"; ?>" to="<?php echo site_url() . "/product/" . $product->id . "/settings"; ?>">
          <em class="icon ni ni-setting"></em>
          <span class="tb-col-sm">Configurações</span>
        </a>
      </li> -->
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_url() . "/product/" . $product->id . "/pixels"; ?>"
          to="<?php echo site_url() . "/product/" . $product->id . "/pixels"; ?>">
          <em class="icon ni ni-code"></em>
          <span class="tb-col-sm">Pixel</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_url() . "/product/" . $product->id . "/checkouts"; ?>" to="<?php echo site_url() . "/product/" . $product->id . "/checkouts"; ?>">
          <em class="icon ni ni-cart"></em>
          <span class="tb-col-sm">Checkout</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>" to="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>">
          <em class="icon ni ni-user-add"></em>
          <span class="tb-col-sm">Visibilidade</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>" to="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>">
          <em class="icon ni ni-link-alt"></em>
          <span class="tb-col-sm">Links</span>
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active frm_edit_product_tab_settings">
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
                            <label class="form-label" for="default-01">Método de pagamento</label>
                            <div class="form-group grids">
                              <div class="custom-control custom-checkbox">
                                <input <?php if ($product->pix_enabled): ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="pmPix" value="pix">
                                <label class="custom-control-label" for="pmPix">Pix</label>
                              </div>
                              <div class="custom-control custom-checkbox ms-2">
                                <input <?php if ($product->credit_card_enabled): ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="pmCreditCard" value="credit_card">
                                <label class="custom-control-label" for="pmCreditCard">Cartão de crédito</label>
                              </div>
                              <div class="custom-control custom-checkbox ms-2">
                                <input <?php if ($product->billet_enabled): ?> checked="" <?php endif; ?> type="checkbox" class="custom-control-input" id="pmBillet" value="billet">
                                <label class="custom-control-label" for="pmBillet">Boleto</label>
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
                            <label class="form-label" for="default-01">Desconto no Pix?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="pix" 
                                  id="pixDiscountEnabled" 
                                  toggle=".div_edit_discount_pix" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->pix_discount_enabled): ?> checked="" <?php endif; ?>
                                >
                                <label class="custom-control-label" for="pixDiscountEnabled"></label>
                              </div>
                            </div>
                            <div class="div_edit_discount_pix">
                              <label class="form-label" for="pixDiscountAmount">Valor do Desconto</label>
                              <div class="form-control-wrap">
                                <input type="text" 
                                  class="form-control" 
                                  id="pixDiscountAmount" 
                                  placeholder="" 
                                  value="<?php echo $product->pix_discount_amount; ?>"
                                  keyup="$inputCurrency" 
                                  keydown="$onlyNumbers"
                                  blur="$inputCurrency" 
                                  load="element.value = currency(element.value)" />
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="form-label" for="default-01">Desconto no Cartão?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="credit_card" 
                                  id="creditCardDiscountEnabled"
                                  toggle=".div_edit_discount_credit_card" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->credit_card_discount_enabled): ?> checked="" <?php endif; ?>
                                >
                                <label class="custom-control-label" for="creditCardDiscountEnabled"></label>
                              </div>
                            </div>
                            <div class="div_edit_discount_credit_card">
                              <label class="form-label" for="creditCardDiscountAmount">Valor do Desconto</label>
                              <div class="form-control-wrap">
                                <input 
                                  type="text" 
                                  class="form-control" 
                                  id="creditCardDiscountAmount" 
                                  placeholder="" 
                                  value="<?php echo $product->credit_card_discount_amount; ?>"
                                  keyup="$inputCurrency" 
                                  keydown="$onlyNumbers"
                                  blur="$inputCurrency" 
                                  load="element.value = currency(element.value)" />
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="form-label" for="default-01">Desconto no Boleto?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="billet" 
                                  id="billetDiscountEnabled"
                                  toggle=".div_edit_discount_billet" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->billet_discount_enabled): ?> checked="" <?php endif; ?>
                                >
                                <label class="custom-control-label" for="billetDiscountEnabled"></label>
                              </div>
                            </div>
                            <div class="div_edit_discount_billet">
                              <label class="form-label" for="billetDiscountAmount">Valor do
                                Desconto</label>
                              <div class="form-control-wrap">
                                <input 
                                  type="text" 
                                  class="form-control" 
                                  id="billetDiscountAmount" 
                                  placeholder="" 
                                  value="<?php echo $product->billet_discount_amount; ?>"
                                  keyup="$inputCurrency" 
                                  keydown="$onlyNumbers"
                                  blur="$inputCurrency" 
                                  load="element.value = currency(element.value)" />
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
                              <select class="form-select" id="installmentsQtySelect">
                                <option <?php if ($product->max_installments == 1): ?> selected="" <?php endif; ?> value="1">Apenas à vista</option>
                                <option <?php if ($product->max_installments == 2): ?> selected="" <?php endif; ?> value="2">Até 2x</option>
                                <option <?php if ($product->max_installments == 3): ?> selected="" <?php endif; ?> value="3">Até 3x</option>
                                <option <?php if ($product->max_installments == 4): ?> selected="" <?php endif; ?> value="4">Até 4x</option>
                                <option <?php if ($product->max_installments == 5): ?> selected="" <?php endif; ?> value="5">Até 5x</option>
                                <option <?php if ($product->max_installments == 6): ?> selected="" <?php endif; ?> value="6">Até 6x</option>
                                <option <?php if ($product->max_installments == 7): ?> selected="" <?php endif; ?> value="7">Até 7x</option>
                                <option <?php if ($product->max_installments == 8): ?> selected="" <?php endif; ?> value="8">Até 8x</option>
                                <option <?php if ($product->max_installments == 9): ?> selected="" <?php endif; ?> value="9">Até 9x</option>
                                <option <?php if ($product->max_installments == 10): ?> selected="" <?php endif; ?> value="10">Até 10x</option>
                                <option <?php if ($product->max_installments == 11): ?> selected="" <?php endif; ?> value="11">Até 11x</option>
                                <option <?php if ($product->max_installments == 12): ?> selected="" <?php endif; ?> value="12">Até 12x</option>
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
                            <label class="form-label">Habilitar página de obrigado no Pix?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                  id="pixThanksPageEnabled" 
                                  toggle=".div_input_pix_thanks_page_url" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->pix_thanks_page_enabled): ?> checked="" <?php endif; ?>>
                                <label class="custom-control-label" for="pixThanksPageEnabled"></label>
                              </div>
                            </div>
                            <div class="div_input_pix_thanks_page_url">
                              <label class="form-label" for="pixThanksPageURL">Url da página</label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="pixThanksPageURL" placeholder="" value="<?php echo $product->pix_thanks_page_url; ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-sm-12">
                          <div class="form-group">
                            <label class="form-label">Habilitar página de obrigado no cartão de crédito?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                  id="creditCardThanksPageEnabled" 
                                  toggle=".div_input_credit_card_thanks_page_url" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->credit_card_thanks_page_enabled): ?> checked="" <?php endif; ?>>
                                <label class="custom-control-label" for="creditCardThanksPageEnabled"></label>
                              </div>
                            </div>
                            <div class="div_input_credit_card_thanks_page_url">
                              <label class="form-label" for="creditCardThanksPageURL">Url da página</label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="creditCardThanksPageURL" placeholder="" value="<?php echo $product->credit_card_thanks_page_url; ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 d-none">
                          <div class="form-group">
                            <label class="form-label">Habilitar página de obrigado no boleto?</label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                  id="billetThanksPageEnabled" 
                                  toggle=".div_input_billet_thanks_page_url" 
                                  load="toggleStatement(element)"
                                  <?php if ($product->billet_thanks_page_enabled): ?> checked="" <?php endif; ?>>
                                <label class="custom-control-label" for="billetThanksPageEnabled"></label>
                              </div>
                            </div>
                            <div class="div_input_billet_thanks_page_url">
                              <label class="form-label" for="billetThanksPageURL">Url da página</label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="billetThanksPageURL" placeholder="" value="<?php echo $product->billet_thanks_page_url; ?>">
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
        
          <div class="c-right ps-1 mt-1 d-flex justify-content-end">
            <button click="productSettingsOnSubmit" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</content>
