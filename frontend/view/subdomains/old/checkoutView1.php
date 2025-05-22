<?php use Backend\Enums\Orderbump\EOrderbumpStatus; ?>
<content ready="ready">
  <style>
    [check] { display: none }
    [check][checked] { display: block }
    [data-summary-product-id] { display: none }
    [data-summary-product-id][active] { display: block }

  </style>
   <div class="nk-header nk-header-fluid is-regular is-theme" style="background: #b53a3a;">
    <div class="container-xl wide-xl">
      <div class="nk-header-wrap nk-header-brand" >
        <div class="cout" style="display:table;margin:0 auto">
          <div class="d-flex">
            <div style="line-height: 44px;">Essa oferta termina em</div>
            <div style="margin-left:10px">
              <countdown>15:00</countdown>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- <div class="nk-header nk-header-fluid is-regular is-theme">
    <div class="container-xl wide-xl">
      <div class="nk-header-wrap">
        <div class="nk-header-brand">
          <a href="#" class="logo-link">
            <img class="logo-light logo-img"
              src="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png"
              srcset="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png 2x" alt="logo">
            <img class="logo-dark logo-img"
              src="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png"
              srcset="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png 2x" alt="logo-dark">
          </a>
        </div>
        <div class="cout" style="display:table;margin:0 auto">
          <div class="d-flex">
            <div style="line-height: 44px;">Essa oferta termina em</div>
            <div style="margin-left:10px">
              <countdown>15:00</countdown>
            </div>
          </div>
        </div>
        <div class="nk-header-tools">
          <div class="nk-sidebar-brand">
            <div class="d-flex">
              <img class="logo-light logo-img" src=https://painel.rocketleads.com.br/images/1.png>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  /** ***************************************************************
   *
   * IMAGEM NO HEADER
   *
   **************************************************************** */
  <center><img src="https://rocketpays.app/images/t.png"></center> 

  <div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
      <div class="nk-block">
        <div class="row g-gs">
          <div class="col-lg-8">
              <div class="card card-bordered">
                <div class="card-inner">


                  /* STEPPER SYS */
                  <div stepper="checkout">

                    /* STEP: HOME */
                    <div step="registration" class="active">
                      <div class="card-title d-flex mb-4">
                        <div class="number">1</div>
                        <div class="title bold ml-5px">Dados pessoais</div>
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
                                <input type="text" class="form-control" id="email-address" checkout-input="email" required>
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
                    <div step="payment-method">
                      <div class="card-title d-flex mb-4">
                        <div class="number">2</div>
                        <div class="title bold ml-5px">Forma de pagamento</div>
                      </div>
                      
                      <div class="custom-control-group row w-100" checkout-radio="payment-method" style="margin: 0; <?php if ($product->is_free): ?> display: none; <?php endif; ?>">
                        <div class="col-md-6" style="padding:0; display: <?php if ($product->credit_card_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
                          <div class="custom-control custom-checkbox custom-control-pro no-control checked w-100" style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */ 
                                  name="payment_method" value="credit_card" 
                                  if(checked)show="[checkout-section=credit_card]"
                                  if(checked)hide="[checkout-section=address]"

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodCreditCard">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodCreditCard"
                              data-credit_card_discount_amount="<?php echo $product->credit_card_discount_amount; ?>"
                              click="pmCreditCardOnClick">
                              <em class="icon ni ni-cc-alt"></em>
                              <span>Cartão de Crédito</span>
                              <div class="badge-off" style="display: <?php if ($product->credit_card_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;">- R$<?php echo number_format($product->credit_card_discount_amount, 2, ',', '.'); ?></div>
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3" style="padding:0; display: <?php if ($product->pix_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
                          <div class="custom-control custom-checkbox custom-control-pro no-control w-100" 
                            style="padding: 2px;">
                            <input 
                                  /* Usados no JavaScript */
                                  name="payment_method" value="pix"
                                  if(checked)hide="[checkout-section=credit_card], [checkout-section=address]"

                              type="radio"
                              class="custom-control-input"
                              id="btnPaymentMethodPix">
                            <label class="w-100 custom-control-label" for="btnPaymentMethodPix"
                              data-pix_discount_amount="<?php echo $product->pix_discount_amount; ?>"
                              click="pmPixOnClick">
                              <em class="icon ni ni-qr"></em>
                              <span>Pix</span>
                                <div class="badge-off" style="display: <?php if ($product->pix_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;">- R$<?php echo number_format($product->pix_discount_amount, 2, ',', '.'); ?></div>
                            </label>
                          </div>
                        </div>
                        <div class="col-md-3" style="padding:0; display: <?php if ($product->billet_enabled): ?>block<?php else: ?>none<?php endif; ?>;">
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
                              data-billet_discount_amount="<?php echo $product->billet_discount_amount; ?>"
                              click="pmBilletOnClick">
                              <em class="icon ni ni-report-profit"></em>
                              <span>Boleto</span>
                                <div class="badge-off" style="display: <?php if ($product->billet_discount_enabled): ?>table<?php else: ?>none<?php endif; ?>;">- R$<?php echo number_format($product->billet_discount_amount, 2, ',', '.'); ?></div>
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
                        <!-- <div class="card-title d-flex mb-4">
                          <div class="number">3</div>
                          <div class="title bold ml-5px">Cartão de crédito</div>
                        </div> -->
                        <div class="row">
                          <div class="form-group col-md-12">
                            <label class="form-label" for="holdername">Titular </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="holdername" checkout-input="holdername">
                            </div>
                          </div>
                          <div class="form-group col-md-12">
                            <label class="form-label" for="card_number">Números do cartão </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="card_number" checkout-input="card_number">
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label class="form-label" for="month">Mês </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="month" checkout-input="month">
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label class="form-label" for="year">Ano </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="year" checkout-input="year">
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label class="form-label" for="cvv">CVV </label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control" id="cvv" checkout-input="cvv">
                            </div>
                          </div>
                          <div class="form-group col-md-12">
                            <label class="form-label" for="card_number">Parcelas </label>
                            <div class="form-control-wrap">
                              <select class="form-select" id="installmentsQty" checkout-input="installments">
                                <option value="1">À vista</option>
                                <option value="2">2x</option>
                                <option value="3">3x</option>
                                <option value="4">4x</option>
                                <option value="5">5x</option>
                                <option value="6">6x</option>
                                <option value="7">7x</option>
                                <option value="8">8x</option>
                                <option value="9">9x</option>
                                <option value="10">10x</option>
                                <option value="11">11x</option>
                                <option value="12">12x</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      /* ADDRESS */
                      <div checkout-section="address" class="d-none mt-2">
                        <!-- <div class="card-title d-flex mb-4">
                          <div class="number">2</div>
                          <div class="title bold ml-5px">Endereço</div>
                        </div> -->
                        <div class="row mb-4">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="zipcode">CEP </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="zipcode" checkout-input="zipcode">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="street">Rua </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="street" checkout-input="street">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row g-4 mb-4">
                          <div class="col-lg-2">
                            <div class="form-group">
                              <label class="form-label" for="number">Nº </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="number" checkout-input="number">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label class="form-label" for="neighborhood">Bairro </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="neighborhood" checkout-input="neighborhood">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="complement">Complemento </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="complement" checkout-input="complement">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row g-4 mb-4">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-label" for="city">Cidade </label>
                              <div class="form-control-wrap">
                                <input type="text" class="form-control" id="city" checkout-input="city">
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6">
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
                                  <span class="price-promo">R$ <?php echo number_format(doubleval($orderbump->price ?: $orderbump->product->price_promo), 2, ',', '.'); ?></span>
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
                              data-product-price="<?php echo $orderbump->price ?: $orderbump->price_promo ?: $orderbump->product->price_promo ?: $orderbump->product->price; ?>">
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

                      
                      <!-- <div class="card-title d-flex mt-4">
                        <div class="number">3</div>
                        <div class="title bold ml-5px">Resumo</div>
                      </div> -->

                      <button click="checkoutOnSubmit" 
                      data-product-id="<?php echo $product->id ?? 0; ?>" 
                      data-product-credit_card_thanks_page_enabled="<?php echo $product->credit_card_thanks_page_enabled; ?>"
                      data-credit_card_thanks_page_url="<?php echo $product->credit_card_thanks_page_url; ?>"
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
                    <div stepper-control class="d-flex mt-2">
                      <button stepper-for="checkout" stepper-control="prev" class="btn btn-primary me-2" style="display:none">Voltar</button>
                      <button stepper-for="checkout" stepper-control="next" stepper-validation="validateOnNext" class="btn btn-primary">Próximo</button>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
          </div>

          /* inicio segunda coluna */
          <div class="col-lg-4">
            <div class="wide-sm">
              <div class="card card-bordered">
                <div class="card-inner">
                 
                   <div class="card-title d-flex">
                        <div class="number">3</div>
                        <div class="title bold ml-5px">Resumo</div>
                      </div> 
                  <div class="custom-control custom-control-sm custom-checkbox custom-control-pro mb-4">
                    <span class="user-card">
                      <span class="product-image">
                        <img src="<?php echo $product->image; ?>" alt="">
                      </span>
                      <span class="user-info">
                        <span class="lead-text">
                          <?php echo $product->name; ?>
                        </span>
                            <?php if ($product->stock_control && $purchased_qty >= $product->stock_qty): ?>
                                <span>Produto sem estoque.</span>
                            <?php endif; ?>
                          <div class="d-flex">
                              <span class="lead-text product-price text-primary" style=" font-size: 24px; ">R$
                                  <?php echo number_format($total, 2, ',', '.'); ?>
                              </span>
                              <?php if ($product->price_promo  > 0): ?>
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

                  <div class="form-group mbt">
                      <label class="mb-2 d-block title bold">Você, tem um cupom?</label>
                      <div class="input-group" style="flex-wrap: nowrap; display: flex;"> 
                          <input type="text" class="form-control" id="campoAplicarCupom" name="" placeholder="Código do cupom" style="width: calc(100% - 100px)"> 
                          <span class="input-group-append" style="display: table; margin-left: auto;"> 
                          <button class="btn btn-primary btn-apply pointer custom-btn" id="botaoAplicarCupom">Aplicar</button> 
                          </span> 
                      </div>
                  </div>
                  <div class="cart-resume mt10">
                      <div class="cart-resume1 flex">
                          <div class="desc-pay">
                              Sub-total
                          </div>
                          <div style="text-align: end; color: #a5a5a5;">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                      </div>
                      <div class="cart-resume1 flex">
                          <div class="desc-pay">
                              Desconto
                          </div>
                          <div id="descontoDe" style="text-align: end; color: #a5a5a5;">
                          R$ <?php echo number_format($total, 2, ',', '.'); ?>
                        </div>
                      </div>
                      <div class="cart-resume1 flex bold">
                          <div class="precoResumo">
                              Total
                          </div>
                          <div class="value js-cart-total">
                              <div id="precoResumo" class="display_total" 
                              data-current-total="<?php echo $total; ?>" 
                              data-initial-total="<?php echo $total; ?>">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                          </div>
                      </div>
                  </div>
                  <div class="desc-pay mt20">
                    *Parcelamento com tarifa adicional<br>
                    Ao prosseguir você concorda com os <a target="_blank" href="https://rocketleads.com.br/termos-de-compra">Termos de Compra</a><br>
                  </div>
                  <div class="selo">
                     <img src="https://checkout.rocketpays.app/images/checkout/7.png" style="max-width: calc(100% - 40px); display: table; margin-left: auto; margin-right: auto; width: 150px; margin: 0 auto; margin-top: 10px;"> 
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>

      </div>
    </div>
  </div>
  
  /** ***************************************************************
   *
   * IMAGEM NO FOOTER
   *
   **************************************************************** */
  <center><img src="https://rocketpays.app/images/f.png"></center> 
  
  <div class="nk-footer nk-footer-fluid bg-lighter">
    <div class="container-xl">
      <div class="nk-footer-wrap">
        <div class="nk-footer-copyright">PAGAMENTO PROCESSADO POR: <img class="logo-img"
            src="https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png">
        </div>
        <div class="nk-footer-links">
          <ul class="nav nav-sm">
            <li class="nav-item dropup">
              <a href="#" class="dropdown-toggle dropdown-indicator has-indicator nav-link text-base"
                data-bs-toggle="dropdown" data-offset="0,10">
                <span>English</span>
              </a>
              <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                <ul class="language-list">
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">English</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Español</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Français</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Türkçe</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a data-bs-toggle="modal" href="#region" class="nav-link">
                <em class="icon ni ni-globe"></em>
                <span class="ms-1">Select Region</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</content>