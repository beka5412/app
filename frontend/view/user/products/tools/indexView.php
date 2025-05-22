


/* FORA DE USO */



<title>Ferramentas</title>

<content>
  <div class="nk-content-body">

    <ProductCheckoutMenu />

    <div class="tab-content">

        <div>
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
                        <label class="form-label" for="default-01">Pessoas interessadas nas últimas 24 horas</label>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="pix" id="inter24Enabled" toggle=".div_edit_inter24" load="toggleStatement(element)">
                            <label class="custom-control-label" for="inter24Enabled"></label>
                            </div>
                        </div>
                        <div class="div_edit_inter24 d-none">
                            <label class="form-label" for="inter24Amount">Quantidade mínima</label>
                            <div class="form-control-wrap">
                            <input type="text" class="form-control" id="pinter24Amount" placeholder="" value="" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="form-label" for="default-01">Pessoas interessadas na última semana</label>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="pix" id="interuEnabled" toggle=".div_edit_interu" load="toggleStatement(element)">
                            <label class="custom-control-label" for="interuEnabled"></label>
                            </div>
                        </div>
                        <div class="div_edit_interu d-none">
                            <label class="form-label" for="interuAmount">Quantidade mínima</label>
                            <div class="form-control-wrap">
                            <input type="text" class="form-control" id="interuAmount" placeholder="" value="" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="form-label" for="default-01">Compras feitas nas últimas 24 horas</label>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="pix" id="compras24Enabled" toggle=".div_edit_compras24" load="toggleStatement(element)">
                            <label class="custom-control-label" for="compras24Enabled"></label>
                            </div>
                        </div>
                        <div class="div_edit_compras24 d-none">
                            <label class="form-label" for="compras24Amount">Quantidade mínima</label>
                            <div class="form-control-wrap">
                            <input type="text" class="form-control" id="compras24Amount" placeholder="" value="" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="form-label" for="default-01">Compras feitas na última semana</label>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="credit_card" id="comprasuEnabled" toggle=".div_edit_comprasu" load="toggleStatement(element)">
                            <label class="custom-control-label" for="comprasuEnabled"></label>
                            </div>
                        </div>
                        <div class="div_edit_comprasu d-none">
                            <label class="form-label" for="comprasuAmount">Quantidade mínima</label>
                            <div class="form-control-wrap">
                            <input type="text" class="form-control" id="comprasuAmount" placeholder="" value="" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)">
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
                            <input type="text" class="form-control" id="phone-number" checkout-input="phone" required="" placeholder="(__) ____-____" maxlength="14">
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>