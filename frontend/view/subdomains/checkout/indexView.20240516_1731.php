<?php 
use Backend\Enums\Orderbump\EOrderbumpStatus;
use Backend\Enums\Product\EProductPaymentType;
?>
<content ready="ready" data-favicon="<?php echo $checkout?->favicon ?? 'https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png'; ?>">
  <!-- <script type="application/json" json-id="checkout_meta">
  {
    "pixels": <?php echo isset($pixels) ? json_encode($pixels ?? '[]') : '[]'; ?>
  }
  </script> -->
  <script type="application/json" json-id="customer">
  {
    "upsell_id": "<?php echo $upsell->id ?? ''; ?>",
    "price_var": "<?php echo $product_link->id ?? ''; ?>"
  }
  </script>
  <script type="application/json" json-id="checkout">
  {
    "id": "<?php echo $checkout?->id ?? ''; ?>"
  }
  </script>
  <style>
    [check] { display: none }
    [check][checked] { display: block }
    [data-summary-product-id] { display: none }
    [data-summary-product-id][active] { display: block }

    body.dark-mode.bg-lighter, .dual-listbox body.dark-mode.dual-listbox__item:hover, body.dark-mode.bg-light {
      background: #000000 !important;
    }
  </style>
  <div class="nk-header nk-header-fluid is-regular" style="background: <?php echo $checkout?->top_color ?? '#b53a3a'; ?>;">
    <div class="container-xl wide-xl">
      <div class="nk-header-wrap nk-header-brand" >
        <img src="<?php echo $checkout?->logo ?? 'https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png'; ?>" style="height:50px">
        <?php if ($checkout?->countdown_enabled): ?>
        <div class="cout" style="display:table;margin:0 auto">
          <div class="d-flex">
            <div><?php echo $checkout?->countdown_text ?: 'Essa oferta termina em'; ?></div>
            <div style="margin-left:10px">
              <countdown><?php echo $checkout?->countdown_time ?: '15:00'; ?></countdown>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  /** ***************************************************************
   *
   * IMAGEM NO HEADER
   *
   **************************************************************** */
  <?php if ($checkout?->top_banner): ?>
    <center><img src="<?php echo $checkout?->top_banner; ?>"></center> 
  <?php endif; ?>

  <div class="nk-content nk-content-fluid">
    <div class="container-xl">
      <div class="nk-block">
        <div class="row g-gs checkout_columns">
          <div class="checkout_col_1 <?php if (empty($checkout)): ?>col-lg-12<?php else: ?>col-lg-8<?php endif; ?>">
              <div class="card card-bordered">
                <div class="card-inner">

                <div class="summary_block_1_as_section"></div>


                  /* STEPPER SYS */
                  <div stepper="checkout">

                    /* STEP: HOME */
                    <div step="registration" class="active mb-4">
                      <div class="card-title d-flex mb-4">
                        <div class="number">1</div>
                        <div class="title bold ml-5px">Dados pessoais</div>
                      </div>
                      <div class="col-lg-12 col-sm-12 mt-2">
                            <div class="form-group">
                              <label class="form-label" for="cpf-cnpj">Como prefere ser chamado? </label>
                              <div class="d-flex">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="genderM" name="selectGender" class="custom-control-input" checked="">
                                    <label class="custom-control-label" for="genderM">Senhor</label>
                                </div>
                                <div class="custom-control custom-radio ms-5">
                                  <input type="radio" id="genderF" name="selectGender" class="custom-control-input">
                                  <label class="custom-control-label" for="genderF">Senhora</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 mt-2">
                            <div class="form-group">
                              <label class="form-label" for="full-name">Nome completo </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="full-name" checkout-input="name" required>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 mt-2">
                            <div class="form-group">
                              <label class="form-label" for="email-address">Seu e-mail </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="email-address" checkout-input="email" change="emailOnChange" blur="emailOnBlur" required>
                              </div>
                            </div>
                          </div>
                        <div class="row d-flex">
                          <div class="col-lg-6 col-sm-12 mt-2">
                            <div class="form-group">
                              <label class="form-label" for="phone-number">Celular </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="phone-number" checkout-input="phone" required>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 col-sm-12 mt-2">
                            <div class="form-group">
                              <label class="form-label" for="cpf-cnpj">CPF/CNPJ </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="cpf-cnpj" checkout-input="cpf_cnpj" required>
                              </div>
                            </div>
                          </div>
                          
                       
                       
                    
                          <div class="col-lg-12 col-sm-12 mt-2">
                            <div class="alert alert-danger error_registration" style="display: none"></div>
                          </div>
                        </div>/* END FORM */
                    </div>/* END STEP: HOME */

                    /* STEP: PAYMENT METHOD */
                    <div step="payment-method" class="checkout_step_payment">
                      <div class="card-title d-flex mb-4">
                        <div class="number">2</div>
                        <div class="title bold ml-5px">Forma de pagamento</div>
                      </div>
                      
                      <div class="block_options_payment_method custom-control-group row w-100" checkout-radio="payment-method" style="margin: 0; <?php if ($product->is_free): ?> display: none; <?php endif; ?>">
                        <div class="col-md-6" style="padding:0; display: <?php if (!empty($checkout) ? ($checkout?->credit_card_enabled ?: $checkout?->credit_card_enabled) : $checkout?->credit_card_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
                          <div class="custom-control custom-checkbox custom-control-pro no-control checked w-100" style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */ 
                                  name="payment_method" value="credit_card" 
                                  /* if(checked)show="[checkout-section=credit_card]"
                                  if(checked)hide="[checkout-section=address]" */
                                  if(checked)show="[checkout-section=credit_card], [checkout-section=address]"

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodCreditCard">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodCreditCard"
                              data-credit_card_discount_amount="<?php echo !empty($checkout) 
                              ? ($checkout->credit_card_discount_enabled 
                                  ? $checkout->credit_card_discount_amount 
                                  : ($product->credit_card_discount_enabled ? $product->credit_card_discount_amount : 0)
                                ) 
                              : ($product->credit_card_discount_enabled ? $product->credit_card_discount_amount : 0); ?>"
                              click="pmCreditCardOnClick">
                              <em class="icon ni ni-cc-alt"></em>
                              <span>Cartão de Crédito</span>
                              <div class="badge-off" style="display: <?php if (!empty($checkout) ? ($checkout->credit_card_discount_enabled ?: $product->credit_card_discount_enabled) : $product->credit_card_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;"
                                >- R$<?php echo number_format(doubleval(
                                  !empty($checkout) 
                                  ? ($checkout->credit_card_discount_enabled 
                                      ? $checkout->credit_card_discount_amount 
                                      : ($product->credit_card_discount_enabled ? $product->credit_card_discount_amount : 0)
                                    ) 
                                  : ($product->credit_card_discount_enabled ? $product->credit_card_discount_amount : 0)
                                ), 2, ',', '.'); ?></div>
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3" style="padding:0; display: <?php if (!empty($checkout) ? ($checkout->pix_enabled ?: $checkout->pix_enabled) : $checkout->pix_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
                          <div class="custom-control custom-checkbox custom-control-pro no-control w-100" 
                            style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */
                                  name="payment_method" value="pix"
                                  <?php if ($product->type == \Backend\Enums\Product\EProductType::PHYSICAL->value): ?>
                                    if(checked)hide="[checkout-section=credit_card]"
                                  <?php elseif ($product->type == \Backend\Enums\Product\EProductType::DIGITAL->value): ?>
                                    if(checked)hide="[checkout-section=credit_card], [checkout-section=address]"
                                  <?php endif; ?>

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodPix">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodPix"
                              data-pix_discount_amount="<?php echo !empty($checkout) 
                              ? ($checkout->pix_discount_enabled 
                                  ? $checkout->pix_discount_amount 
                                  : ($product->pix_discount_enabled ? $product->pix_discount_amount : 0)
                                )
                              : ($product->pix_discount_enabled ? $product->pix_discount_amount : 0); ?>"
                              click="pmPixOnClick">
                              <em class="icon ni ni-qr"></em>
                              <span>Pix</span>
                                <div class="badge-off" style="display: <?php if (!empty($checkout) ? ($checkout->pix_discount_enabled ?: $product->pix_discount_enabled) : $product->pix_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;"
                                  >- R$<?php echo number_format(doubleval(
                                    !empty($checkout) 
                                    ? ($checkout->pix_discount_enabled 
                                        ? $checkout->pix_discount_amount 
                                        : ($product->pix_discount_enabled ? $product->pix_discount_amount : 0)
                                      )
                                    : ($product->pix_discount_enabled ? $product->pix_discount_amount : 0)
                                  ), 2, ',', '.'); ?></div>
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3" style="padding:0; display: <?php if (!empty($checkout) ? ($checkout->billet_enabled ?: $checkout->billet_enabled) : $checkout->billet_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
                          <div class="custom-control custom-checkbox custom-control-pro no-control w-100" 
                            style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */
                                  name="payment_method" value="billet"
                                  if(checked)show="[checkout-section=address]"
                                  if(checked)hide="[checkout-section=credit_card]"

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodBillet">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodBillet"
                              data-billet_discount_amount="<?php echo !empty($checkout)
                              ? ($checkout->billet_discount_enabled 
                                  ? $checkout->billet_discount_amount 
                                  : ($product->billet_discount_enabled ? $product->billet_discount_amount : 0)
                                )
                              : ($product->billet_discount_enabled ? $product->billet_discount_amount : 0); ?>"
                              click="pmBilletOnClick">
                              <em class="icon ni ni-report-profit"></em>
                              <span>Boleto</span>
                                <div class="badge-off" style="display: <?php if (!empty($checkout) ? ($checkout->billet_discount_enabled ?: $product->billet_discount_enabled) : $product->billet_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;"
                                  >- R$<?php echo number_format(doubleval(
                                    !empty($checkout)
                                    ? ($checkout->billet_discount_enabled 
                                        ? $checkout->billet_discount_amount 
                                        : ($product->billet_discount_enabled ? $product->billet_discount_amount : 0)
                                      )
                                    : ($product->billet_discount_enabled ? $product->billet_discount_amount : 0)
                                  ), 2, ',', '.'); ?></div>
                            </label>
                          </div>
                        </div>
                        
                        <div class="col-md-3" style="padding: 0; display: none">
                          <div class="custom-control custom-checkbox custom-control-pro no-control w-100" style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */
                                  name="payment_method" value="free"
                                  <?php if ($product->is_free): ?> checked="" <?php endif; ?>

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodBillet">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodBillet">
                              <em class="icon ni ni-report-profit"></em>
                              <span>Grátis</span>
                            </label>
                          </div>
                        </div>
                      </div>                      
                      
                      <div checkout-section="credit_card" class="d-none mt-2">
                        <div checkout-subsection="type_credit_card">
                          <div class="mb-2">
                            <a href="javascript:;" click="savedCreditCardOnClick" style="font-weight: bold; font-size: 12px; text-decoration: underline;">
                            Usar um cartão de crédito salvo.</a>
                          </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <label class="form-label" for="holdername">Titular </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="holdername" checkout-input="holdername" blur="holdernameOnBlur" change="holdernameOnChange" />
                              </div>
                            </div>
                            <div class="form-group col-md-12">
                              <label class="form-label" for="card_number">Números do cartão </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="card_number" checkout-input="card_number">
                              </div>
                            </div>
                            <div style="display:flex;">
                              <div class="form-group" style="width: 33%">
                                <label class="form-label" for="month">Mês </label>
                                <div class="form-control-wrap">
                                  <!-- <input type="text" class="form-control" id="month" checkout-input="month"> -->
                                  <select class="form-control" id="month" checkout-input="month">
                                    <option value="" hidden="hidden" disabled="disabled"> MM </option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group" style="width: 33%; margin-left: 3px">
                                <label class="form-label" for="year">Ano </label>
                                <div class="form-control-wrap">
                                  <!-- <input type="text" class="form-control" id="year" checkout-input="year"> -->
                                  <select class="form-control" id="year" checkout-input="year">
                                    <option value="" hidden="hidden" disabled="disabled"> AA </option>
                                    <?php $y = doubleval(substr(date('Y'), 2)); for ($i = $y; $i <= $y + 17; $i++): ?>
                                    <option value="20<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group" style="width: 33%; margin-left: 3px">
                                <label class="form-label" for="cvv">CVV </label>
                                <div class="form-control-wrap">
                                  <input type="text" class="form-control" id="cvv" checkout-input="cvv">
                                </div>
                              </div>
                            </div>
                            <div class="form-group col-md-12">
                              <label class="form-label" for="card_number">Parcelas </label>
                              <div class="form-control-wrap">
                                <select class="form-select" id="installmentsQty"checkout-input="installments" change="installmentsOnChange">
                                  <option class="opt_installment_1x" value="1">À vista</option>
                                  <?php $max = $checkout?->max_installments ?? $product->max_installments ?? 12; ?>
                                  <?php for ($i = 2; $i <= $max; $i++): ?>
                                  <option class="opt_installment_<?php echo $i; ?>x" value="<?php echo $i; ?>"><?php echo $i; ?>x</option>
                                  <?php endfor; ?>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div checkout-subsection="saved_credit_card" class="d-none">
                          <div class="row mt-3">
                            <div class="form-group col-md-12">
                              <label class="form-label" for="holdername">Selecione um dos seus cartões salvos</label>
                              <div class="form-control-wrap">
                                
                                <div class="sp-plan-payopt">
                                  <div class="cc-pay">
                                    <ul class="cc-pay-method">
                                      <li class="w-100 cc-pay-dd dropdown">
                                        <a href="javascript:;" id="a_selected_card"
                                        class="w-100 justify-content-between btn btn-white btn-outline-light dropdown-toggle dropdown-indicator dropdown_select_card" 
                                        data-bs-toggle="dropdown">
                                          <em class="icon ni ni-visa"></em>
                                          <span>SELECIONE</span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-auto">
                                          <ul class="link-list-plain inv_sel_pm">
                                          </ul>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                          </div>
                          <div class="mt-3">
                            <a href="javascript:;" click="addCreditCardOnClick" style="font-weight: bold; font-size: 12px; text-decoration: underline;">
                            Adicionar cartão de crédito.</a>
                          </div>
                        </div>

                      </div>
                      
                      /* ADDRESS */
                      <div checkout-section="address" class="d-none mt-2">
                        <div class="card-title d-flex mt-4 mb-4">
                          <div class="number">3</div>
                          <div class="title bold ml-5px">Endereço</div>
                        </div>
                        <div class="row">
                          <div class="col-lg-12 div_address d-none">
                            <div class="form-group">
                              <div class="full_address">
                                /* address vars */
                                {street} N{number}, {neighborhood}, {complement} {city}-{state} / {zipcode}
                              </div>
                              <a click="editAddressOnClick" href="javascript:;">Editar endereço</a>
                            </div>
                          </div>
                          <div class="col-lg-6 div_zipcode">
                            <div class="form-group">
                              <label class="form-label" for="zipcode">CEP </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="zipcode" checkout-input="zipcode">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 div_street">
                            <div class="form-group">
                              <label class="form-label" for="street">Rua </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="street" checkout-input="street">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row g-4 mb-4 mt-1">
                          <div class="col-lg-6 div_street_n">
                            <div class="form-group">
                              <label class="form-label" for="number">Nº </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="number" checkout-input="number">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 div_neighborhood">
                            <div class="form-group">
                              <label class="form-label" for="neighborhood">Bairro </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="neighborhood" checkout-input="neighborhood">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-12 mb-4 div_a_complement">
                          <div class="form-group">
                            <label class="form-label" for="complement">Complemento </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="complement" checkout-input="complement">
                            </div>
                          </div>
                        </div>
                        <div class="row g-4 mb-4">
                          <div class="col-lg-6 div_a_city">
                            <div class="form-group">
                              <label class="form-label" for="city">Cidade </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="city" checkout-input="city">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 div_a_state">
                            <div class="form-group">
                              <label class="form-label" for="state">Estado </label>
                              <div class="form-control-wrap">
                                <select name="state" class="w-100 form-control" id="state" checkout-input="state">
                                  <option value="AC">Acre</option>
                                  <option value="AL">Alagoas</option>
                                  <option value="AP">Amapá</option>
                                  <option value="AM">Amazonas</option>
                                  <option value="BA">Bahia</option>
                                  <option value="CE">Ceará</option>
                                  <option value="DF">Distrito Federal</option>
                                  <option value="ES">Espírito Santo</option>
                                  <option value="GO">Goiás</option>
                                  <option value="MA">Maranhão</option>
                                  <option value="MT">Mato Grosso</option>
                                  <option value="MS">Mato Grosso do Sul</option>
                                  <option value="MG">Minas Gerais</option>
                                  <option value="PA">Pará</option>
                                  <option value="PB">Paraíba</option>
                                  <option value="PR">Paraná</option>
                                  <option value="PE">Pernambuco</option>
                                  <option value="PI">Piauí</option>
                                  <option value="RJ">Rio de Janeiro</option>
                                  <option value="RN">Rio Grande do Norte</option>
                                  <option value="RS">Rio Grande do Sul</option>
                                  <option value="RO">Rondônia</option>
                                  <option value="RR">Roraima</option>
                                  <option value="SC">Santa Catarina</option>
                                  <option value="SP">São Paulo</option>
                                  <option value="SE">Sergipe</option>
                                  <option value="TO">Tocantins</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>/* ADDRESS */
                      
                      /* ORDER BUMP */
                      <?php if (!empty($product->orderbumps)): ?>
                        <div class="title bold mt-3">Aproveite e compre Junto:</div>

                      <?php foreach ($product->orderbumps as $orderbump): 
                      if ($orderbump->status <> EOrderbumpStatus::PUBLISHED->value && $orderbump->product_as_checkout_id <> $product->id) continue; ?>                      
                      <div class="order-bump-container mt-4">
                        <div class="order-bump-card">
                          <div class="card-body">
                            <div class="order-info-block">
                              <div class="img-block">
                                <img src="<?php echo $orderbump->product->image; ?>" alt="Product">
                              </div>
                              <div class="info-block">
                                <label class="d-block title bold"><?php echo $orderbump->title ?: $orderbump->product->name; ?></label>
                                <div class="text"><?php echo $orderbump->description ?: $orderbump->product->description; ?></div>
                                <div>
                                  <span class="price-promo">R$ <?php echo number_format(doubleval($orderbump->price_promo ?: $orderbump->price ?: $orderbump->product->price_promo ?: $orderbump->product->price), 2, ',', '.'); ?></span>
                                  <span
                                    style="color: #999; text-decoration: line-through; font-size: 12px; margin-bottom: 7px;">R$
                                    <?php echo number_format(doubleval($orderbump->product->price_promo ?: $orderbump->product->price), 2, ',', '.'); ?>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="orderbump_button" click="orderbumpCheckboxOnClick" 
                              data-orderbump-id="<?php echo $orderbump->id; ?>" 
                              data-product-id="<?php echo $orderbump->product->id; ?>" 
                              data-product-price="<?php echo $orderbump->price_promo ?: $orderbump->price ?: $orderbump->product->price_promo ?: $orderbump->product->price; ?>">
                              <div class="orderbump-checkbutton">
                                <span class="material-symbols-outlined" check style="font-size: 31px;color: #44c485;">done</span>
                              </div>
                              <div class="orderbump_button-cta medium white"><?php echo $orderbump->text_button; ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                      <?php endif; ?>
                      /* END ORDER BUMP */
                      
                      <div class="payment_form_as_section">
                      </div>

                      <div class="summary_as_section">
                      </div>
                      
                      <button click="checkoutOnSubmit" 
                      data-product-id="<?php echo $product->id ?? ''; ?>" 
                      data-checkout-id="<?php echo $checkout?->id ?? ''; ?>" 
                      data-variation="<?php echo $variation ?? ''; ?>" 
                      data-path-param-1="<?php echo $sku ?? ''; ?>"
                      data-path-param-2="<?php echo $variation ?? ''; ?>"
                      data-product-credit_card_thanks_page_enabled="<?php echo $checkout?->credit_card_thanks_page_enabled ?: $product->credit_card_thanks_page_enabled; ?>"
                      data-credit_card_thanks_page_url="<?php echo $checkout?->credit_card_thanks_page_enabled ? ($checkout?->credit_card_thanks_page_url ?: $product->credit_card_thanks_page_url) : $product->credit_card_thanks_page_url; ?>"
                      type="button" class="mt-1 w-100 btn-success mt-4">Finalizar compra
                        <em class="icon ni ni-arrow-right"></em></button>
                      <div class="alert alert-success alert-icon mt-4"><em class="icon ni ni-lock-alt"></em> Este é
                        um <strong>pagamento 100% seguro criptografado com SSL</strong>.</div>
                      <div class="text-center mx-auto">
                        <img data-src="/img/checkout/compra-segura.png" alt="" class="lazy img-responsive w-100"
                          style="max-width: 275px" src="https://checkout.perfectpay.com.br/img/compra-segura.png"
                          data-was-processed="true">
                      </div>

                    </div>/* END STEP: PAYMENT METHOD */

                  </div>/* END STEPPER SYS */

                  <?php if ( !($product->stock_control && $purchased_qty >= $product->stock_qty) ): ?>
                    <div stepper-control class="mt-2 div_stepper_control" style="display:flex">
                      <button stepper-for="checkout" stepper-control="prev" class="btn btn-primary me-2" style="display:none">Voltar</button>
                      <button stepper-for="checkout" stepper-control="next" stepper-validation="validateOnNext" class="btn btn-primary btn_next_step_checkout">Próximo</button>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
          </div>

          
          /* Payment form as column */
          <div class="checkout_col_payment" style="display:none">
            <div class="wide-sm">
              <div class="card card-bordered">
                <div class="card-inner">
                    <div class="payment_form_as_column"></div>
                </div>
              </div>
            </div>
          </div>
          /* End payment form as column */


          /* Summary sidebar */
          <div class="checkout_col_summary_sidebar" style="display:none">
            <div class="  ">
              <div class="card card-bordered checkout_col_summary_sidebar_card">
                <div class="<?php if (($checkout?->checkout_theme_id)  == 2): ?><?php else: ?>card-inner<?php endif; ?>">
                  
                  <!--                   
                  <div class="card-title d-flex mb-4 mt-4">
                    <div class="title bold ml-5px">Resumo</div>
                  </div>  -->
                  <div class="summary_block_1">
                    <div class="custom-control custom-control-sm custom-checkbox custom-control-pro mb-4">
                      <span class="user-card">
                        <span class="product-image">
                          <img src="<?php echo $product->image; ?>" alt="">
                        </span>
                        <span class="user-info">
                          <span class="lead-text">
                            <?php echo $product_link?->qty ?: 1; ?>x <?php echo $product->name; ?>
                          </span>
                              <?php if ($product->stock_control && $purchased_qty >= $product->stock_qty): ?>
                                  <span>Produto sem estoque.</span>
                              <?php endif; ?>
                            <div class="d-flex">
                              <span class="lead-text product-price text-primary" style=" font-size: 24px; ">R$
                                <?php echo number_format($product_price, 2, ',', '.'); ?>
                              </span>
                              <?php if ($product->payment_type === EProductPaymentType::RECURRING->value): ?>
                              <span style="font-size: 12px; position: relative; top: 13px;">/<?php 
                              $aux = !empty($plan) ? $plan : $product;
                              if ($aux->recurrence_interval == 'day' && $aux->recurrence_interval_count == 1): echo 'dia';
                              elseif ($aux->recurrence_interval == 'month' && $aux->recurrence_interval_count == 1): echo 'mês';
                              elseif ($aux->recurrence_interval == 'month' && $aux->recurrence_interval_count == 2): echo 'bimestre';
                              elseif ($aux->recurrence_interval == 'month' && $aux->recurrence_interval_count == 3): echo 'trimestre';
                              elseif ($aux->recurrence_interval == 'month' && $aux->recurrence_interval_count == 6): echo 'semestre';
                              elseif ($aux->recurrence_interval == 'year' && $aux->recurrence_interval_count == 1): echo 'anual'; 
                              endif;
                              ?></span>
                              <?php endif; ?>
                              <?php if ($product->price_promo > 0): ?>
                              <div id="precoCortado" style="color: #999; text-decoration: line-through; font-size: 12px; margin-bottom: 7px;"> R$ <?php echo number_format($product->price, 2, ',', '.'); ?> </div>
                              <?php endif; ?>
                            </div>
                          <!-- <span>ou em 12 x de R$ 28,94 * no cartão</span> -->
                        </span>
                      </span>
                    </div>
                    
                    <?php if (!empty($product->orderbumps)): ?>
                    <?php foreach ($product->orderbumps as $orderbump): 
                    if ($orderbump->status <> EOrderbumpStatus::PUBLISHED->value && $orderbump->product_as_checkout_id <> $product->id) continue; ?>   
                      <div class="custom-control custom-control-sm custom-checkbox custom-control-pro mb-4" data-summary-product-id="<?php echo $orderbump->product->id; ?>">
                        <span class="user-card">
                          <span class="product-image">
                            <img src="<?php echo $orderbump->product->image; ?>" alt="">
                          </span>
                          <span class="user-info">
                            <span class="lead-text">
                              <?php echo $orderbump->product->name; ?>
                            </span>
                            <span class="lead-text product-price text-primary">R$
                            <?php echo number_format(doubleval($orderbump->price_promo ?: $orderbump->price ?: $orderbump->product->price_promo ?: $orderbump->product->price), 2, ',', '.'); ?>
                            </span>
                          </span>
                        </span>
                      </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($product->stock_control && $purchased_qty >= $product->stock_qty): ?>
                      <i>Produto sem estoque.</i><br /><br />
                    <?php endif; ?>
                  </div>

                  <?php if ($product->shipping_cost): ?>
                  <div class="d-flex" style="font-size:11px">
                    <label>Frete:</label>
                    <div style="font-weight:bold;margin-left:5px;font-size:13px;position: relative; top: -2px;color: #6576ff;">R$ <?php echo currency($product->shipping_cost); ?></div>
                  </div>
                  <?php endif; ?>

                  <div class="form-group mbt mt-2">
                      <label class="mb-2 d-block title bold">Você, tem um cupom?</label>
                      <div class="input-group" style="flex-wrap: nowrap; display: flex;"> 
                          <input type="text" class="form-control" id="campoAplicarCupom" name="" placeholder="Código do cupom"
                          checkout-input="coupon" style="width: calc(100% - 100px)"> 
                          <span class="input-group-append" style="display: table; margin-left: auto;"> 
                          <button class="btn btn-primary btn-apply pointer custom-btn" id="botaoAplicarCupom"
                          click="applyCoupon">Aplicar</button> 
                          </span> 
                      </div>
                  </div>
                  <div class="cart-resume mt10">
                      
                      <div class="cart-resume1 flex bold">
                          <div class="precoResumo">
                              Total
                          </div>
                          <div class="value js-cart-total">
                              <div id="precoResumo" class="display_total" 
                              data-current-total="<?php echo $total; ?>" 
                              data-initial-total="<?php echo $total; ?>">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                              <div id="precoResumo" class="display_total_installments" style="display:none">0</div>
                          </div>
                      </div>
                  </div>
                  <div class="desc-terms mt-2">
                    <div>* Parcelamento com tarifa adicional.</div>
                    <div class="mt-2">Ao clicar em 'Comprar agora', eu concordo que a RocketPays está processando este pedido em nome de <?php echo $product->author ?? ''; ?>; com os Termos de Compra que li e estou ciente dos <a target="_blank" href="https://rocketleads.com.br/termos-de-compra">Termos de Compra</a> e que sou maior de idade ou autorizado e acompanhado por um tutor legal.<br>
                    Precisa de ajuda?</div>
                    <div class="mt-2">
                      <b>Autor: </b> <?php echo $product->author ?? ''; ?><br>
                      <b>Email: </b> <?php echo $product->support_email ?? ''; ?><br>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          /* End summary sidebar */
          
          /* Image sidebar */
          <div class="checkout_col_image_sidebar" style="display:none">
            <div class="wide-sm">
             
              /** ***************************************************************
               *
               * IMAGEM NO SIDEBAR
               *
               **************************************************************** */
              <?php if ($checkout?->sidebar_banner): ?>
              <center><img src="<?php echo $checkout?->sidebar_banner; ?>"></center> 
              <?php endif; ?>
            </div>
          </div>
          /* End image sidebar */
          
        </div>

      </div>
    </div>
  </div>
  
  /** ***************************************************************
   *
   * IMAGEM NO FOOTER
   *
   **************************************************************** */
  <?php if ($checkout?->footer_banner): ?>
  <center><img src="<?php echo $checkout?->footer_banner; ?>"></center> 
  <?php endif; ?>
  
  <div class="nk-footer nk-footer-fluid bg-lighter">
    <div class="container-xl">
      <div class="nk-footer-wrap">
        <div class="nk-footer-copyright">PAGAMENTO PROCESSADO POR: <img class="logo-img"
            src="https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png">
        </div>
      </div>
    </div>
  </div>

  <?php if ($checkout?->notification_order_weekly_enabled): ?>
  <section class="custom-social-proof">
    <div id="notificationx-frontend0" class="notificationx-frontend">
      <div class="nx-container nxc-bottom_left">
        <?php if ($checkout?->notification_order24_enabled): ?>
          <div class="mt-1 notification-item nx-notification source-wp_stats position-bottom_left type-download_stats themes-actively_using themes-download_stats_actively_using notificationx-310 has-close-btn" style="max-width: 370px;">
            <div class="notificationx-inner">
              <div class="notificationx-image featured_image image-circle">
                <img src="https://rocketpays.app/images/successfully-done.gif">
              </div>
              <div class="notificationx-content ">
                <p class="nx-first-row">
                  <span class="nx-first-word"> <?php echo $checkout?->notification_order24_number; ?> pedidos do produto na últimas 24 horas</span>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($checkout?->notification_order_weekly_enabled): ?>
          <div class="mt-1 notification-item nx-notification source-wp_stats position-bottom_left type-download_stats themes-actively_using themes-download_stats_actively_using notificationx-310 has-close-btn" style="max-width: 370px;">
            <div class="notificationx-inner">
              <div class="notificationx-image featured_image image-circle">
                <img src="https://rocketpays.app/images/successfully-done.gif">
              </div>
              <div class="notificationx-content ">
                <p class="nx-first-row">
                  <span class="nx-first-word"> <?php echo $checkout?->notification_order_weekly_number; ?> pedidos do produto na últimas semanas</span>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if ($checkout?->notification_interested_weekly_enabled): ?>
          <div class="mt-1 notification-item nx-notification source-wp_stats position-bottom_left type-download_stats themes-actively_using themes-download_stats_actively_using notificationx-310 has-close-btn" style="max-width: 370px;">
            <div class="notificationx-inner">
              <div class="notificationx-image featured_image image-circle">
                <img src="https://rocketpays.app/images/successfully-done.gif">
              </div>
              <div class="notificationx-content ">
                <p class="nx-first-row">
                  <span class="nx-first-word"> <?php echo $checkout?->notification_interested_weekly_number; ?> pessoas interessadas nesse produto na últimas semanas</span>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if ($checkout?->notification_interested24_enabled): ?>
          <div class="mt-1 notification-item nx-notification source-wp_stats position-bottom_left type-download_stats themes-actively_using themes-download_stats_actively_using notificationx-310 has-close-btn" style="max-width: 370px;">
            <div class="notificationx-inner">
              <div class="notificationx-image featured_image image-circle">
                <img src="https://rocketpays.app/images/successfully-done.gif">
              </div>
              <div class="notificationx-content ">
                <p class="nx-first-row">
                  <span class="nx-first-word"> <?php echo $checkout?->notification_interested24_number; ?> pessoas interessadas nesse produto nas últimas 24 horas</span>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if ($checkout?->whatsapp_number): ?>
    <div class="float-wa"><a target="_blank"
      href="https://api.whatsapp.com/send?phone=55<?php echo $checkout?->whatsapp_number; ?>&text=<?php echo urlencode("Olá, gostaria de saber mais sobre o produto $product->name"); ?>"><img
      class="img-wa"
      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAABQCAMAAAC5zwKfAAAAS1BMVEVHcEwmz2omz2omz2omz2omz2omz2omz2omz2omz2omz2omz2omz2r///8u0W/4/vo91HmC46nf+OlO2IZm3Zbs+/K68NCg6r7P9N55IZbFAAAADHRSTlMArlOSHg80zPbed2NWckMYAAADgklEQVR42sVZ2XarMAwMENYQ79v/f+mluEEnGZnQctvOW4/pWKORLdu57KPt6qnqx3nF2FdT3bWXb+NaN/0wzE8Yhr6pr99ha+tmzGSIsarbrwY39cTGYOin65foxvktxsOU7a2fD6G/HRLeVSC2KLzqDoQHavcwvgvy2kB4+xia6y5fNX8Ze7K7fv4G+u4cH2LsjvCdj/FKfCcYz/lBqMDrtplPoXmtx1u5/oSyzmntnFWiXI+3F0PGEpvVwXh5XyC9CdqKQ1a3hQQqHfz9CTJoVUhj+1awcEneATJpwYt+VzE2fNJ5E+KCVXqO0rK1Q05PbHgmswX9YYZYzXlkwDguyGkvQKH9ShdfXLBxncdzsscrBYh8krRxmZDECCG2TIBaUhhc8Dxjn42u0WLn10TNBazp9Q6NrlfCBssvER8L+8FoLNbiaskImiIEwEmQQaAtm2L4Wn+S0/qFJEucs+YUi7AEmGcXOqWg5gz8KMFQw3ns5IdgikPzacwy0Odu5DIYxJb7e+JF589efe4whcps2dF58bliiN5i4UyoeAsq3FdEINtGNSaxAikbhUiZ0KiS0ai5urx6ItKmWIRdQuWZofEyMin0ioLlvKSpvQVC9ITmtX5NoSgQBq62L2geFYoIhQBJgD5MSLtOUv+PcI6SNJ8hVLSRsXspLSnIYdkU2vmI0Tr11hRYyua5GJyn5mKNTFrR1GzZ9DsrihjvRqusnygtW9iw9NYdIcwEkRml0eFxGhH0ITaBCVyR28TUkwi0+SauRhvYvkT+EFsxIQ+6wvaFBzmNxSy0IUpjs8eFDRZbgDWMFqWT/ORz1MkctgCuLUd2yxIuBmOSVlTx2EcbaqNoC0IoJR6rRGIGF9Rso9cwOXuWkrFw/moONQs8LgXFHUVIM7Y9BOnNZvOHpbZn2p7KOSselbPZ6PGKG6YwLmQuJqOBUmnzKB7ABEfirWJjDPmEbqIVT7eW3FpT1ouW4Jk4dyaC9Ck6qxZYF1Mek1HNpQAxRCeJbCM1xhv/+LNwUemv/MUn3jN8iG5bwgRJeYWLD3s1U+khU6wWrLkk/YHo4GrGXx6tMSvZButiSGZBCjRQvDyiaGXV/PpfQqgFAshA8A9ewM8/Efz8I8b5Zxae8e+flpDv/GPaLz73nX+QPP9k+vuPuuefnc8/jJ9/uh/o6f6PflzAnz+G4z9//AMWYPwAHbpE7gAAAABJRU5ErkJggg=="></a>
    </div>
  <?php endif; ?>

  <!-- pin -->
  <div class="modal fade" tabindex="-1" id="modalPin" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
          <div class="nk-modal">
            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-mail bg-success"></em>
            <h4 class="nk-modal-title">Finalize sua compra rapidamente.</h4>
            <div class="nk-modal-text">
              <p class="lead pin-description pin_description">Para usar os seus dados de pagamento já salvos, enviamos um código para o seu e-mail <span id="mCustomerEmail"></span>.</p>
              <div class="d-flex justify-content-center rocketpays-pin rocketpays_pin">
                <input type="text" value="" />
                <input type="text" value="" />
                <input type="text" value="" />
                <input type="text" value="" />
                <input type="text" value="" />
              </div>
            </div>
            <div class="nk-modal-action mt-5">
              <a href="javascript:;" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
              <button type="button" class="btn btn-lg btn-mw btn-primary ms-2" click="btnVerifyEmailOnClick" id="btnVerifyEmail">Confirmar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</content>
