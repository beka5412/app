<?php use Backend\Enums\Product\{EProductAffPaymentType, ECookieMode}; ?>

<title>Afiliação</title>

<content>
  <e>
    $ = this;
    $.product = <?php echo json_encode($product ?? (object) []); ?>;
    $.price = $.product?.price;
    $.fee_percent = <?php echo $fee_percent = (string) (get_setting('transaction_fee') / 100) ?>;
    $.fee_extra = <?php echo $fee_extra = (string) get_setting('transaction_fee_extra') ?>;
    $.fees = ($.price * $.fee_percent) + $.fee_extra;
    $.calcAffComission();
  </e>

	<ProductMenu />

	<div class="frm_edit_affiliation">
		<div>
      <div>
        <div class="col-12 d-flex">
          <div class="col-md-12">
            <div class="card card-bordered card-preview">
              <div class="card-inner">
                <div class="preview-block">
                  <div class="row gy-4">
										<div class="row gy-4">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="form-label" for="default-01">Habilitar programa de afiliados</label>
													<div class="custom-control w-100 custom-switch checked">
														<input <?php if ($product->affiliate_enabled == 1): ?> checked="" <?php endif; ?> type="checkbox"
														class="custom-control-input inp_aff_enabled" id="enableAffiliation">
														<label class="custom-control-label" for="enableAffiliation">Sim</label>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="form-label" for="default-01">Mostrar no Marketplace</label>
													<div class="form-group">
														<div class="custom-control custom-switch">
															<input <?php if ($product->marketplace_enabled == 1): ?> checked="" <?php endif; ?> type="checkbox"
                                type="checkbox" class="custom-control-input inp_marketplace_enabled" name="pix" id="page-card-off">
															<label class="custom-control-label" for="page-card-off">Sim</label>
														</div>
													</div>
												</div>
                      </div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="form-label" for="default-01">Aprovar cada solicitação de afiliação
														manualmente</label>
													<div class="form-group">
														<div class="custom-control custom-switch">
															<input type="checkbox" class="custom-control-input" name="pix" id="page-card-off">
															<label class="custom-control-label" for="page-card-off">Não</label>
														</div>
													</div>
												</div>
                      </div>
											<div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label" for="default-01">Exibir dados dos clientes aos afiliados</label>
                          <div class="form-group">
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="shareCustomerData">
                              <label class="custom-control-label" for="shareCustomerData">Não</label>
                            </div>
                          </div>
                        </div>
                      </div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="form-label" for="default-01">Porcentagem ou valor fixo</label>
													<div class="form-group">
														<div class="custom-control custom-switch">
															<input <?php if ($product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value): ?> checked="" <?php endif; ?> type="checkbox"
                                type="checkbox" class="custom-control-input inp_aff_payment_type" id="commission_type" 
                                change="percentOrPriceOnChange">
															<label class="custom-control-label label_comission_type" for="commission_type">
                                <?php if ($product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value): ?>
                                  Porcentagem
                                <?php elseif ($product->affiliate_payment_type == EProductAffPaymentType::PRICE->value): ?>
                                  Preço
                                <?php endif; ?>
                              </label>
														</div>
													</div>
												</div>
                      </div>
											<div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label" for="default-01">Comissão 
                            (<span class="span_comission"><?php echo $product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value ? 'porcentagem' : 'preço'; ?></span>)</label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_comission" id="default-01"
                            value="<?php echo $product->affiliate_amount; ?>"
                            keydown="$onlyNumbers" keyup="$inputCurrency" load="element.value = currency(element.value)" blur="$inputCurrencyAlways"
                            />
                          </div>
                        </div>
                      </div>
											<div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label" for="default-01">Tipo de comissionamento</label>
                          <div class="form-control-wrap">
                            <div class="custom-control custom-radio">
                              <input type="radio" id="cookieModeFirstClick" name="customRadio" class="custom-control-input"
                              <?php if ($product->cookie_mode == ECookieMode::FIRST_CLICK->value): ?> checked <?php endif; ?>
                              value="<?php echo ECookieMode::FIRST_CLICK->value; ?>">
                              <label class="custom-control-label" for="cookieModeFirstClick">Primeiro clique</label>
                            </div>
                            <div class="custom-control custom-radio ms-2">
                              <input type="radio" id="cookieModeLastClick" name="customRadio" class="custom-control-input"
                              <?php if ($product->cookie_mode == ECookieMode::LAST_CLICK->value): ?> checked <?php endif; ?>
                              value="<?php echo ECookieMode::LAST_CLICK->value; ?>">
                              <label class="custom-control-label" for="cookieModeLastClick">Último clique</label>
                            </div>
                          </div>
                        </div>
                      </div>
											<div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label" for="default-01">Duração do cookie</label>
                          <div class="form-control-wrap">
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" <?php if (!$product->cookie_duration): ?> checked <?php endif; ?>
                                  id="checkboxCookieDuration" !toggle=".div_cookie_duration">
                                <label class="custom-control-label" for="checkboxCookieDuration">Eterno</label>
                              </div>
                            </div>
                            <div class="div_cookie_duration <?php if (!$product->cookie_duration): ?> d-none <?php endif; ?>">
                              <div class="form-control-wrap">
                                <div class="input-group">
                                  <input type="text" class="form-control inp_cookie_duration" id="default-01"
                                    value="<?php echo $product->cookie_duration / (24 * 60 * 60); ?>" />
                                  <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">dias</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="mb-2">
                        <div class="row">
                          <div class="col-lg-12 d-flex">
                            <div class="col-lg-6">
                              <div class="card card-inner card-bordered">
                                <div class="">
                                  <p>
                                    <strong>Comissão sem Afiliado</strong>
                                  </p>
                                  <p class="m-0"> Seu produto custa até <strong>R$ <e>currency($.price)</e></span></strong>. </p>
                                  <p class="m-0"> Você receberá <strong class="text-success">R$ <e>currency($.sellerComissionWithoutAff)</e></strong> por venda. </p>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-6 ms-1">
                              <div class="card card-inner card-bordered">
                                <div class="">
                                  <p>
                                    <strong>Comissão do Afiliado por venda</strong>
                                  </p>
                                  <p class="m-0"> Seu produto custa<strong>R$ <e>currency($.price)</e></strong>.</p>
                                  <p class="m-0"> Você receberá <strong class="text-success">R$ <e>currency($.sellerComissionWithAff)</e></strong> por venda. </p>
                                  <p class="m-0"> Seus afiliados receberão até <strong>R$ <e>currency($.affComission)</e></strong>. </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="default-01">E-mail de suporte para afiliados </label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="default-01"
                            placeholder="Suporte@rocketleads.com.br">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="default-01">Descrição para afiliados </label>
                        <div class="form-control-wrap">
                          <textarea class="form-control no-resize" id="default-textarea">Large text area content</textarea>
                        </div>
                      </div>
                      <div class="mt-3">                        
                        <label class="form-label" for="default-01">Material para afiliados</label>
                        <div class="d-flex justify-end my-3">
                          <div class="nk-block-tools-opt">
                            <div class="dropdown quick-add-btn">
                              <a class="btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <em class="icon ni ni-plus"></em>
                                <span>Add Link/Anexo</span>
                              </a>
                              <div class="dropdown-menu">
                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalLink">
                                  <span>Link</span>
                                </a>
                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalFile">
                                  <span>Arquivo</span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card card-bordered mb-3">
                          <div class="card-inner nk-block-between">
                            <div class="card-title-group">
                              <div class="card-title">
                                <h6 class="title">Materiais</h6>
                              </div>
                            </div>
                          </div>
                          <div class="card-inner p-0 border-top">
                            <table class="table table-tranx">
                              <thead class="tb-tnx-head">
                                <tr>
                                  <th scope="col">Nome</th>
                                  <th scope="col">Tipo</th>
                                  <th scope="col" class="text-right">Ações</th>
                                </tr>
                              </thead>
                              <tbody id="tb-attachment"></tbody>
                            </table>
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
		</div>
		<div class="c-right ps-1 mt-1 d-flex justify-content-end">
			<button click="affOnSubmit" class="btn btn-primary">Salvar</button>
		</div>
	</div>
</content>


