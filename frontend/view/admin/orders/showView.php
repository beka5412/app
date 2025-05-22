
<?php use Backend\Enums\Order\EOrderStatus;?>
<style>
    .badge-orderbump {
    border-radius: 3px;
    padding: 0 0.4rem;
    font-size: 11px;
    color: #798bff;
    background: #eff1ff;
    margin-left: 10px;
}
</style>
<title>Pedido ID #<?php echo $order->id; ?></title>
<content>
  <div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
          <div class="nk-content-wrap">
            <div class="nk-block-head">
              <div class="nk-block-head-sub">
                <a class="back-to" href="https://app.migraz.com/admin/orders">
                  <em class="icon ni ni-arrow-left"></em>
                  <span>Minhas Vendas</span>
                </a>
              </div>
              <div class="nk-block-between-md g-4">
                <div class="nk-block-head-content">
                  <h2 class="nk-block-title fw-normal">Pedido ID #<?php echo $order->id; ?></h2>
                </div>
                <div class="nk-block-head-content">
                  <ul class="nk-block-tools justify-content-md-end g-4 flex-wrap">
                    <li class="order-md-last">
                      <div class="nk-help-action mt-2">
                       
                        <div class="dropdown">
                          <a href="#" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                          <em class="icon ni ni-cart"></em>
                            <span>Alterar Status</span>
                            <em class="icon ni ni-chevron-down"></em>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-menu-auto mt-1">
                            <ul class="link-list-plain">
                            <?php if ($order->status == EOrderStatus::APPROVED->value): ?>
                              <li>
                                <a href="#">Reembolsar pedido</a>
                              </li>                             
                              <?php endif; ?>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
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
                                      <span>Qtd.</span>
                                    </div>
                                    <div class="nk-tb-col">
                                      <span>Desconto</span>
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
                                      <span class="tb-sub"><?php if ($order?->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$<?php endif; ?>  <?php echo currency($order->product()?->price); ?></span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                      <span class="tb-sub">-</span>
                                    </div>
                                    <div class="nk-tb-col">
                                      <span class="tb-sub"><?php if ($order?->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$<?php endif; ?>  <?php echo currency($ordermeta?->product_price_promo_diff ?? '0'); ?></span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                      <span class="tb-sub"><?php if ($order?->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$<?php endif; ?>  <?php echo currency($ordermeta?->product_price); ?></span>
                                    </div>
                                    
                                  </div>
                                  <?php foreach ($orderbumps as $orderbump): ?>
                                    <div class="nk-tb-item tr">
                                    <div class="nk-tb-col">
                                      <span class="tb-product">
                                        <img src="<?php print($orderbump->info?->product?->image); ?>" alt="" class="thumb">
                                          <span class="title"><?php print($orderbump->info?->product?->name); ?><span class="badge-orderbump" >Order Bump</span> </span>
                                      </span>
                                    </div>
                                    <div class="nk-tb-col">
                                      <span class="tb-sub"> <?php if ($order?->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$<?php endif; ?> <?php echo currency($orderbump?->meta?->total); ?></span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                      <span class="tb-sub">-</span>
                                    </div>
                                    <div class="nk-tb-col">
                                      <span class="tb-sub">-</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                      <span class="tb-sub"> <?php if ($order?->currency_symbol == 'usd'): ?>
                              Dolar
                            <?php elseif ($order?->currency_symbol == 'brl'): ?>
                              Reais
                            <?php endif; ?><?php echo currency($orderbump?->meta?->total); ?></span>
                                    </div>
                                    
                                  </div>
                                     <?php endforeach; ?> 
                            </div>
                              <div class="invoice-bills table-responsive">
                                <table class="table table-striped">
                                  <tfoot>
                                  <tr><td colspan="2" style="font-size: 16px; font-weight: 600;"></td></tr>
                                    <tr>
                                      <td colspan="2" style="font-size: 12px;font-weight: 400;">Subtotal</td>
                                      <td style="font-size: 14px;color: #798bff;font-weight: 400;">R$ <?php echo currency($order?->total); ?>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="font-size: 12px;font-weight: 400;">Frete</td>
                                      <td style="font-size: 14px;color: #9f8e15; font-weight: 400;">R$ 0,00</td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="font-size: 12px;font-weight: 400;">Taxa Operacional e plataforma</td>
                                      <td style="font-size: 14px;color: #e85347;font-weight: 400;">- R$ <?php echo currency($order->total_vendor); ?></td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="font-size: 12px;font-weight: 400;">Gateway</td>
                                      <td style="font-size: 14px;color: #e85347;font-weight: 400;">- R$ <?php echo currency($order->total_gateway); ?></td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="font-size: 16px; font-weight: 600;">Valor Líquido</td>
                                      <td style="font-size: 14px;color: #4ab7a8;font-weight: 600;">R$ <?php echo currency($order->total_seller); ?></td>
                                    </tr>
                                    <tr></tr>
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
                            <?php if ($order?->product()?->type == 'physical'): ?>
                              <span class="order-details"><strong class="">Endereço : </strong> <span class="ms-1"><?php echo $ordermeta?->address_street ?? ''; ?>,<?php echo $ordermeta?->address_number ?? ''; ?> (<?php echo $ordermeta?->address_complement ?? ''; ?>) - <?php echo $ordermeta?->address_district ?? ''; ?> - <?php echo $ordermeta?->address_city ?? ''; ?>/<?php echo $ordermeta?->address_state ?? ''; ?> -  <?php echo $ordermeta?->address_zipcode ?? ''; ?></span></span>
                            <?php endif; ?>
                            </li>
                            <li class="col-sm-12">
                              <span class="order-details"> <?php if ($order->status == EOrderStatus::APPROVED->value): ?>
                              <p>
                                <span class="text-soft">Liberação:</span>
                                    <?php if ($order->seller_was_credited == 1): ?>
                                      <span class="badge bg-success">Liberado em <?php echo date("d/m/Y - H:i:s", strtotime($order->seller_credited_at ?? '')) ?></span>                
                                    <?php else: ?>
                                      <span class="badge bg-warning">Data de liberação estimada <?php echo date("d/m/Y - H:i:s", strtotime($order->seller_credited_at ?? '')) ?></span>
                                    <?php endif; ?>
                              </p>
                              <?php endif; ?></span>
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
                          <h5 class="card-title">Detalhes do Vendedor</h5>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-user-alt"></em></strong> <?php echo $order?->user()?->name ?? ''; ?></span>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-mail"></em></strong> <?php echo $order?->user()?->email ?? ''; ?></span>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-call"></em></strong> <a href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>"><?php echo $ordermeta?->customer_phone ?? ''; ?></a></span>
                        </div>
                        <div class="card-inner">
                          <h5 class="card-title">Detalhes do Cliente</h5>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-user-alt"></em></strong> <?php echo $ordermeta?->customer_name ?? ''; ?></span>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-mail"></em></strong> <?php echo $ordermeta?->customer_email ?? ''; ?></span>
                          <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-call"></em></strong> <a href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>"><?php echo $ordermeta?->customer_phone ?? ''; ?></a></span>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-inner">
                          <h5 class="card-title">Pedido e Pagamento</h5>
                          <span class="order-details"><strong  class="mr10px">Status do pagamento: <p></strong>
                                <?php if ($order->status == EOrderStatus::APPROVED->value): ?>
                                <span class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Aprovado</span>
                                <?php endif; ?>

                                <?php if ($order->status == EOrderStatus::PENDING->value): ?>
                                <span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Pendente</span>
                                <?php endif; ?>
                                <?php if ($order->status == EOrderStatus::CANCELED->value): ?>
                                <span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Cancelado</span>
                                <?php endif; ?>
                                </p></span>
                            <span class="order-details">
                                <strong class="mr10px">Método de Pagamento: </strong> 
                                <p><span class="tb-lead">
                                    <img class="img-pay" style=" width: 17px; " src="https://app.migraz.com/images/pay/mastercard.svg" />  Cartão de crédito
                                  </span>
                                </p>
                            </span>
                            <span class="order-details"><strong  class="mr10px">Moeda:</strong>
                            <?php if ($order?->currency_symbol == 'usd'): ?>
                              Dolar
                            <?php elseif ($order?->currency_symbol == 'brl'): ?>
                              Reais
                            <?php endif; ?>
                            
                            </span>
                           
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="nk-content-wrap mt-2">
            <div class="nk-block">
              <div class="card card-bordered sp-plan">
                <div class="card-inner">
                    <ul class="nav nav-tabs">
                      <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#resumo">
                          <em class="icon ni ni-user"></em>
                          <span>Resumo</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#fiscal">
                        <em class="icon ni ni-file-check"></em>
                        <span>Nota Fiscal</span>
                        </a>
                      </li>
                      <?php if ($order?->product()?->type == 'physical'): ?>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rastreamento">
                          <em class="icon ni ni-truck"></em>
                          <span>Rastreamento</span>
                        </a>
                      </li>
                      <?php endif; ?>
                      <?php if ($order?->product()?->affiliate_enabled): ?>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#afiliado">
                          <em class="icon ni ni-users-fill"></em>
                          <span>Afiliado / Co-Produtores</span>
                        </a>
                      </li>
                      <?php endif; ?>

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
                            <div class="order-tracking <?php if ($order->status == EOrderStatus::PENDING->value): ?>completed<?php elseif ($order->status == EOrderStatus::APPROVED->value): ?>completed<?php else: ?><?php endif; ?>">
                              <span class="is-complete"></span>
                              <p>Aguardando pagamento<br>
                                <span class="fs12px"><?php echo date("d/m/Y - H:i:s", strtotime($order?->created_at ?? '')) ?></span>
                              </p>
                            </div>
                            <div class="order-tracking <?php if ($order->status == EOrderStatus::APPROVED->value): ?>completed<?php else: ?><?php endif; ?>">
                              <span class="is-complete"></span>
                              <p>Pagamento aprovado<br>
                                <span class="fs12px"><?php if ($order->status == EOrderStatus::APPROVED->value): ?><?php echo date("d/m/Y - H:i:s", strtotime($order?->updated_at ?? '')) ?><?php else: ?><?php endif; ?></span>
                              </p>
                            </div>
                            <?php if ($order?->product()?->type == 'physical'): ?>
                              <div class="order-tracking">
                                <span class="is-complete"></span>
                                <p>Produtos em separação<br>
                                  <span></span>
                                </p>
                              </div>
                              <div class="order-tracking">
                                <span class="is-complete"></span>
                                <p>Faturado<br>
                                  <span></span>
                                </p>
                              </div>
                              <div class="order-tracking">
                                <span class="is-complete"></span>
                                <p>Protudo em Transporte<br>
                                  <span></span>
                                </p>
                              </div>
                              <div class="order-tracking">
                                <span class="is-complete"></span>
                                <p>Entregue<br>
                                  <span></span>
                                </p>
                              </div>
                              <?php endif; ?>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane" id="fiscal">
                        <p>
                          <a href="#" class="btn btn-outline-primary">Incluir Nota Fiscal</a></li>
                        <div class="example-alert"><div class="alert alert-light alert-icon"><em class="icon ni ni-alert-circle"></em>Esse pedido ainda não possui nota fiscal</div></div>
                      </p>
                      </div>
                      <div class="tab-pane" id="rastreamento">
                      <p>
                        <a href="#" class="btn btn-outline-primary">Informar Codigo de Rastreio</a></li>
                        <div class="example-alert"><div class="alert alert-light alert-icon"><em class="icon ni ni-alert-circle"></em>Informações de rastreamento indisponíveis.</div></div>
                      </p>
                      </div>
                      <div class="tab-pane" id="afiliado">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">Afiliados</th>
                              <th scope="col">Comissão</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><?php echo $aff?->email; ?></td>
                              <td><?php echo currency($order->total_aff); ?></td>
                            </tr>
                          </tbody>
                        </table>
                        /* --------------------------------
                          <table class="table mt-4">
                            <thead>
                              <tr>
                                <th scope="col">Co-Produtores</th>
                                <th scope="col">Comissão</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>Email</td>
                                <td>Valor</td>
                              </tr>
                            </tbody>
                          </table>
                        -------------------------------- */
                        </div>
                        <div class="tab-pane" id="utm">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Source</th>
                                <th scope="col">Campaign</th>
                                <th scope="col">Media</th>
                                <th scope="col">Content</th>
                                <th scope="col">Tracker</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                              </tr>
                            </tbody>
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
</content>
