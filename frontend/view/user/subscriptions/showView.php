<!-- 
<title>Pedido ID </title>
<content>

   <div class="container-fluid">
    <div class="nk-content-inner">
      <div class="nk-content-body">
        <div class="nk-content-wrap">
          <div class="nk-block-head">
            <div class="nk-block-head-sub">
              <a class="back-to" href="<?= site_url() ?>/sales" to="<?= site_url() ?>/sales">
                <em class="icon ni ni-arrow-left"></em>
                <span>Minhas Vendas</span>
              </a>
            </div>
            <?php if ($order->user_id == $user->id) : ?>
              <div class="nk-block-between-md g-4">
                <div class="nk-block-head-content">
                  <h2 class="nk-block-title fw-normal">Pedido ID #<?php echo $order->id; ?></h2>
                </div>
                <div class="nk-block-head-content">
                  <ul class="nk-block-tools justify-content-md-end g-4 flex-wrap">
                    <li class="order-md-last">
                  
                    </li>
                  </ul>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-lg-12">
            <div class="row mt-3 mb-4">
              <div class="col">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <p class="mb-0 text-secondary">Status</p>
                        <h4 class="my-1 text-primary">
                          <div class="col">
                            <span class="btn btn-outline-success outbtn-small">Ativo</span>
                          </div>
                        </h4>
                      </div>
                      <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                        <i class="fa fa-users"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <p class="mb-0 text-secondary">Periodicidade </p>
                        <h4 class="my-1 text-warning">
                          <div class="col">
                            <span class="cancled-orders">A cada 1 mês </span>
                          </div>
                        </h4>
                      </div>
                      <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                        <i class="fa fa-dollar"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <p class="mb-0 text-secondary">Forma de pagamento </p>
                        <h4 class="my-1 text-info">
                          <div class="col">
                            <span class="total-orders">Cartão de crédito </span>
                          </div>
                        </h4>
                      </div>
                      <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                        <i class="fa fa-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <p class="mb-0 text-secondary">Data de início </p>
                        <h4 class="my-1 text-info">
                          <div class="col">
                            <span class="total-orders">02/12/2023 </span>
                          </div>
                        </h4>
                      </div>
                      <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                        <i class="fa fa-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="nk-block">
            <div class="row">
              <div class="col-xl-8">
                <div class="card card-bordered">
                  <div class="card-inner-group">
                    <div class="card-inner">

                      <div class="sp-plan-desc sp-plan-desc-mb">
                        <ul class="row gx-1">
                          <li class="col-sm-4">
                            <p><span class="text-soft">Id da Transação</span></p>
                            <?php echo $order?->transaction_id ?? ''; ?>
                          </li>
                          <li class="col-sm-4">
                            <p><span class="text-soft">Criação</span></p>
                            <?php echo date("d/m/Y - H:i:s", strtotime($order?->created_at ?? '')) ?>
                          </li>
                          <li class="col-sm-4">
                            <p><span class="text-soft">Atualizado</span></p>
                            <?php echo date("d/m/Y - H:i:s", strtotime($order?->updated_at ?? '')) ?>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="card-inner">
                      <div class="sp-plan-head-group">
                        <div class="sp-plan-head">
                          <div class="nk-tb-list">
                            <div class="nk-tb-item nk-tb-head">

                              <div class="nk-tb-col">
                                <span>Nome</span>
                              </div>
                              <div class="nk-tb-col">
                                <span>Val. unit.</span>
                              </div>
                            
                              <div class="nk-tb-col tb-col-md">
                                <span>Val. total</span>
                              </div>
                            </div>
                            <div class="nk-tb-item tr">
                              <div class="nk-tb-col">
                                <span class="tb-product">
                                  <img src="<?php echo $order?->product()?->image ?? ''; ?>" alt="" class="thumb">
                                  <span class="title"><?php echo $order?->product()?->name ?? ''; ?></span>
                                </span>
                              </div>
                              <div class="nk-tb-col">
                                <span class="tb-sub"><?php if ($order->currency_symbol == 'usd'): ?>$<?php endif; ?> <?php echo currency($order->product()?->price); ?></span>
                             
                              <div class="nk-tb-col tb-col-md">
                                <span class="tb-sub"><?php if ($order->currency_symbol == 'usd'): ?>$<?php endif; ?>  <?php echo currency($ordermeta?->product_price); ?></span>
                              </div>

                            </div>
                            <?php foreach ($orderbumps as $orderbump) : ?>
                              <div class="nk-tb-item tr">
                                <div class="nk-tb-col">
                                  <span class="tb-product">
                                    <img src="<?php print($orderbump->info?->product?->image); ?>" alt="" class="thumb">
                                    <span class="title"><?php print($orderbump->info?->product?->name); ?></span>
                                  </span>
                                </div>
                                <div class="nk-tb-col">
                                  <span class="tb-sub">R$ <?php echo currency($orderbump?->meta?->total); ?></span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                  <span class="tb-sub">-</span>
                                </div>
                                <div class="nk-tb-col">
                                  <span class="tb-sub">-</span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                  <span class="tb-sub">R$ <?php echo currency($orderbump?->meta?->total); ?></span>
                                </div>

                              </div>
                            <?php endforeach; ?>
                          </div>
                          <div class="invoice-bills table-responsive">
                            <table class="table table-striped">
                              <tfoot>
                                <tr>
                                  <td colspan="2" style="font-size: 16px; font-weight: 600;"></td>
                                </tr>
                                <tr>
                                  <td colspan="2" style="font-size: 12px;font-weight: 400;">Subtotal</td>
                                  <td style="font-size: 14px;color: #798bff;font-weight: 400;">R$ <?php echo currency($order->total); ?>
                                </tr>
                                <tr>
                                  <td colspan="2" style="font-size: 12px;font-weight: 400;">Taxa</td>
                                  <td style="font-size: 14px;color: #e85347;font-weight: 400;">- R$ <?php echo currency($order->total - $order->total_seller); ?></td>
                                </tr>
                               
                                <tr></tr>


                                <tr>
                                  <td colspan="2" style="font-size: 16px; font-weight: 600;">Valor Líquido</td>
                                  <td style="font-size: 14px;color: #4ab7a8;font-weight: 600;">R$ <?php echo currency($order->total_seller); ?></td>
                                </tr>
                               
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-inner">
                      <div class="sp-plan-desc sp-plan-desc-mb">
                        <ul class="row gx-1">
                          <li class="col-sm-12">
                          <tr>
                                      <td colspan="2" style="font-size: 12px;font-weight: 400;">O valor da compra é convertido em reais no momento da compra</td>
                                      <td style="font-size: 14px;color: #e85347;font-weight: 400;"></td>
                                    </tr>
                            <?php if ($order?->product()?->type == 'physical') : ?>
                              <span class="order-details"><strong class="w-25">Endereço : </strong> <?php echo $ordermeta?->address_street ?? ''; ?>,<?php echo $ordermeta?->address_number ?? ''; ?> (<?php echo $ordermeta?->address_complement ?? ''; ?>) - <?php echo $ordermeta?->address_district ?? ''; ?> - <?php echo $ordermeta?->address_city ?? ''; ?>/<?php echo $ordermeta?->address_state ?? ''; ?> - </strong> <?php echo $ordermeta?->address_zipcode ?? ''; ?></span>
                            <?php endif; ?>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4">
                <div class="card card-bordered">
                  <div class="nk-help-plain card-inner">
                    <div class="card">
                      <div class="card-inner">
                        <h5 class="card-title">Detalhes do Cliente</h5>
                        <span class="order-details"><strong class="mw-85"><em class="icon ni ni-user-alt"></em></strong> <?php echo $ordermeta?->customer_name ?? ''; ?></span>
                        <span class="order-details"><strong class="mw-85"><em class="icon ni ni-mail"></em></strong> <?php echo $ordermeta?->customer_email ?? ''; ?></span>
                        <span class="order-details"><strong class="mw-85"><em class="icon ni ni-call"></em></strong> <a href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>"><?php echo $ordermeta?->customer_phone ?? ''; ?></a></span>
                        <span class="order-details"><strong class="mw-85"><em class="icon ni ni-file-check"></em></strong> <?php echo $ordermeta?->customer_cpf_cnpj ?? ''; ?></span>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-inner">
                        <h5 class="card-title">Forma de Pagamento</h5>
                        <span class="order-details"><strong class="mr10px">Status do pagamento: <p></strong>
                          <?php if ($order->status == EOrderStatus::APPROVED->value) : ?>
                            <span class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Aprovado</span>
                          <?php endif; ?>

                          <?php if ($order->status == EOrderStatus::PENDING->value) : ?>
                            <span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Pendente</span>
                          <?php endif; ?>
                          <?php if ($order->status == EOrderStatus::CANCELED->value) : ?>
                            <span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Cancelado</span>
                          <?php endif; ?>
                          </p></span>
                        <span class="order-details">
                          <strong class="mr10px">Método de Pagamento: </strong>
                          <p><span class="tb-lead">
                              <?php $pm = $order->meta('info_payment_method'); ?>
                              <?php if ($pm == 'credit_card') : ?>
                                <img class="img-pay" src="https://rocketpays.app/images/pay/mastercard.svg" /> <?php echo $order->meta('payment_installments'); ?>x no Cartão de crédito
                              <?php elseif ($pm == 'pix') : ?>
                                <img class="img-pay" src="https://rocketpays.app/images/pay/pix.svg" /> Pix
                              <?php elseif ($pm == 'free') : ?>
                                Grátis
                              <?php elseif ($pm == 'billet') : ?>
                                <img class="img-pay" src="https://rocketpays.app/images/pay/billet.svg" /> Boleto
                              <?php endif; ?>
                            </span>
                          </p>
                        </span>

                        <?php if ($order->reason): ?>
                        <span class="order-details">
                          <strong class="mr10px">Motivo:</strong>
                          <p>
                            <span class="tb-lead">
                              <?= $order->reason ?>
                            </span>
                          </p>
                        </span>
                        <?php endif; ?>

                        <?php if ($order->status == EOrderStatus::APPROVED->value) : ?>
                          <p>
                            <span class="text-soft">Liberação:</span>
                            <?php if ($order->seller_was_credited == 1) : ?>
                              <span class="badge bg-success">Liberado em <?php echo date("d/m/Y - H:i:s", strtotime($order->seller_credited_at ?? '')) ?></span>
                            <?php else : ?>
                              <span class="badge bg-warning">Data de liberação estimada<?php echo date("d/m/Y - H:i:s", strtotime($order->seller_credited_at ?? '')) ?></span>
                            <?php endif; ?>
                          </p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php if ($order->user_id == $user->id) : ?>
          <div class="nk-content-wrap mt-2">
            <div class="nk-block">
              <div class="card card-bordered sp-plan">
                <div class="card-inner">
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" data-bs-toggle="tab" href="#resumo">
                        <em class="icon ni ni-user"></em>
                        <span>Faturas</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#utm">
                        <em class="icon ni ni-link"></em>
                        <span>UTM / Trackeamento</span>
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane active" id="resumo">
                      <div class="col-12 col-md-12">
                        <div class="d-flex">
                          
                        </div>
                      </div>
                    </div>
                   
                    <div class="tab-pane" id="utm">
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">sck</th>
                            <th scope="col">src</th>
                            <th scope="col">source</th>
                            <th scope="col">campaign</th>
                            <th scope="col">medium</th>
                            <th scope="col">content</th>
                            <th scope="col">term</th>
                            <th scope="col">xcod</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><?= $ordermeta->tracking_sck ?? '' ?></td>
                            <td><?= $ordermeta->tracking_src ?? '' ?></td>
                            <td><?= $ordermeta->tracking_utm_source ?? '' ?></td>
                            <td><?= $ordermeta->tracking_utm_campaign ?? '' ?></td>
                            <td><?= $ordermeta->tracking_utm_medium ?? '' ?></td>
                            <td><?= $ordermeta->tracking_utm_content ?? '' ?></td>
                            <td><?= $ordermeta->tracking_utm_term ?? '' ?></td>
                            <td><?= $ordermeta->tracking_xcod ?? '' ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div> 
</content> -->