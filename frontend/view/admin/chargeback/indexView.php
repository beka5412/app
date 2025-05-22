<title>Suporte</title>

<content>
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Chargeback</h3>
                </div>
            </div>
        </div>


        <div class="mb-4 grid-filter">
            <div class=" mb-3">
                <div class="form-control-wrap">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <em class="icon ni ni-search"></em>
                            </span>
                        </div>
                        <input type="text" class="form-control" placeholder="Buscar por CPF, transação, e-mail ou nome..." required="">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <a href="#" class="btn btn-primary">
                    <em class="icon ni ni-opt-dot-alt"></em>
                    <span>Filtrar</span>
                </a>
            </div>
        </div>
        <div class="nk-block" data-select2-id="20">
            <div class="card card-bordered card-stretch" data-select2-id="19">
                <div class="card-inner-group" data-select2-id="18">
                    <div class="card-inner p-0">
                        <div class="nk-tb-list nk-tb-ulist">
                            <div class="nk-tb-item nk-tb-head">
                                
                                <div class="nk-tb-col">Id Interno</div>
                                <div class="nk-tb-col">Id do pedido</div>
				<div class="nk-tb-col">Valor do alerta</div>
                                <div class="nk-tb-col">Valor do pedido</div>
                                <div class="nk-tb-col">Id do alerta</div>
                                <div class="nk-tb-col">Data da transação</div>
                                <!-- <div class="nk-tb-col">Valor do alerta</div> -->
                                <div class="nk-tb-col">Código de autenticação</div>
                                <div class="nk-tb-col">Números do cartão</div>
                                <div class="nk-tb-col">Plataforma</div>
                                <div class="nk-tb-col">Descritor</div>
                                <div class="nk-tb-col">Data do recebimento</div>
                                <div class="nk-tb-col">Emissor</div>
                                <!-- <div class="nk-tb-col">Tipo de transação</div> -->
                                <div class="nk-tb-col">Origem</div>
                                <!-- <div class="nk-tb-col">Status</div> -->
                                <!-- <div class="nk-tb-col">Tipo</div> -->
                                <div class="nk-tb-col">Retorno Paylab</div>
                            </div>
                            <?php foreach ($chargebacks as $chargeback) : ?>
                                <div class="nk-tb-item">
                                    
                                    <div class="nk-tb-col"><?= $chargeback->alert_id ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->order->id ?? '' ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_amount ?></div>
				    <div class="nk-tb-col"><?= $chargeback->order->total ?? '' ?></div>

                                    <div class="nk-tb-col"><?= $chargeback->api_alert_id ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_transaction_date ?></div>
                                    <!-- <div class="nk-tb-col"><?= $chargeback->api_amount ?></div> -->
                                    <div class="nk-tb-col"><?= $chargeback->api_auth_code ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_card_number ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_merchant ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_merchant_descriptor ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_received_date ?></div>
                                    <div class="nk-tb-col"><?= $chargeback->api_issuer ?></div>
                                    <!-- <div class="nk-tb-col"><?= $chargeback->api_transaction_type ?></div> -->
                                    <div class="nk-tb-col"><?= $chargeback->api_source ?></div>
                                    <!-- <div class="nk-tb-col"><?= $chargeback->api_status ?></div> -->
                                    <!-- <div class="nk-tb-col"><?= $chargeback->api_type ?></div> -->
                                    <div class="nk-tb-col"><?= $chargeback->paylab_result_status ?></div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                    <div class="card-inner">
                        <div class="nk-block-between-md g-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <Pagination />
    </div>
</content>
