<?php

use Backend\Enums\Order\EOrderStatus; ?>
<title>Pedido ID #<?php echo $order->id; ?></title>
<content>
  <div class="container-fluid">
    <div class="nk-content-inner">
      <div class="nk-content-body">
        <div class="nk-content-wrap">
          <div class="nk-block-head">
            <div class="nk-block-head-sub">
              <a class="back-to" href="<?= site_url() ?>/sales">
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
                      <!-- <div class="nk-help-action mt-2">
                        <div class="dropdown">
                          <a href="#" class="btn btn-success" data-bs-toggle="dropdown" aria-expanded="false">
                            <em class="icon ni ni-whatsapp"></em>
                            <span>Enviar Whatsapp</span>
                            <em class="icon ni ni-chevron-down"></em>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-menu-auto mt-1" style="">
                            <ul class="link-list-plain">
                              <li>
                                <a target="_blank" href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>&text=Ol%C3%A1%20<?php echo $ordermeta?->customer_name ?? ''; ?>,%20tudo%20bem?%0A%0ABom,%20preciso%20te%20avisar%20que%20a%20aprova%C3%A7%C3%A3o%20do%20pagamento%20da%20sua%20inscri%C3%A7%C3%A3o%20no%20<?php echo $order?->product()?->name ?? ''; ?>%20foi%20recusada!%20%F0%9F%99%81%0A%0AN%C2%BA%20do%20pedido%20<?php echo $order->id; ?>.%0A%0AAlguma%20coisa%20pode%20ter%20acontecido%20no%20seu%20cart%C3%A3o%20de%20cr%C3%A9dito!%0A%0AFique%20atento%20aos%20itens%20abaixo:%0A%0A%E2%98%91%EF%B8%8F%20Confira%20o%20n%C3%BAmero%20CVV%20do%20cart%C3%A3o%20digitado%20no%20momento%20da%20compra;%0A%E2%98%91%EF%B8%8F%20Confira%20a%20bandeira%20do%20cart%C3%A3o%20escolhida%20no%20momento%20da%20compra;%0A%E2%98%91%EF%B8%8F%20Confira%20a%20data%20de%20validade%20digitada%20no%20momento%20da%20compra;%0A%E2%98%91%EF%B8%8F%20Falta%20de%20limite%20no%20cart%C3%A3o;%0A%E2%98%91%EF%B8%8F%20Dados%20inseridos%20de%20forma%20incorreta%20(data%20de%20vencimento,%20c%C3%B3digo%20de%20seguran%C3%A7a);%0A%E2%98%91%EF%B8%8F%20Se%20voc%C3%AA%20%C3%A9%20estrangeiro,%20selecione%20a%20op%C3%A7%C3%A3o%20estrangeiro;%0A%E2%98%91%EF%B8%8F%20Se%20voc%C3%AA%20%C3%A9%20estrangeiro,%20verifique%20se%20o%20seu%20cart%C3%A3o%20est%C3%A1%20liberado%20para%20transa%C3%A7%C3%B5es%20internacionais;%0A%0AConfira%20se%20esses%20dados%20est%C3%A3o%20corretos%20e%20tente%20novamente.%0A%0A%F0%9F%9B%91%20Se%20ainda%20assim%20n%C3%A3o%20conseguir%20efetivar%20a%20sua%20inscri%C3%A7%C3%A3o,%20basta%20responder%20esta%20mensagem%20que%20nosso%20time%20ir%C3%A1%20lhe%20auxiliar.">
                                  Cartão Recusado
                                </a>
                              </li>
                              <li>
                                <a target="_blank" href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>&text=Ol%C3%A1%20<?php echo $ordermeta?->customer_name ?? ''; ?>,%20tudo%20bem?%20%F0%9F%99%82%0A%0AParab%C3%A9ns%20pela%20sua%20compra:%20<?php echo $order?->product()?->name ?? ''; ?>!%20%0A%0AA%20partir%20da%20agora%20voc%C3%AA%20estar%C3%A1%20recebendo%20em%20seu%20e-mail%20de%20cadastro%20%F0%9F%91%89%20<?php echo $ordermeta?->customer_email ?? ''; ?>%20as%20informa%C3%A7%C3%B5es%20relacionadas%20a%20sua%20compra.%0A%0A%F0%9F%9B%91%20Confira%20sua%20caixa%20de%20entrada,%20spam%20e%20lixeira.%20Caso%20n%C3%A3o%20receba,%20entre%20em%20contato%20com%20o%20nosso%20time%20de%20suporte%20por%20algum%20dos%20canais%20abaixo.%0A%0A%F0%9F%93%B2%20WhatsApp%0A%5BLink%20do%20Suporte%5D%0A%0A%F0%9F%93%A5%20E-mail%0A<?php echo $order?->product()?->support_email ?? ''; ?>%0A%0ASeja%20muito%20bem-vindo!">
                                  Pagamento aprovado
                                </a>
                              </li>
                              <?php $pm = $order->meta('info_payment_method'); ?>
                              <?php if ($pm == 'pix') : ?>
                                <li>
                                  <a target="_blank" href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>&text=Ol%C3%A1%20<?php echo $ordermeta?->customer_name ?? ''; ?>,%20tudo%20bem?%0A%0APassando%20aqui%20para%20dizer%20que%20j%C3%A1%20recebemos%20o%20seu%20pedido%20de%20inscri%C3%A7%C3%A3o%20no%20<?php echo $order?->product()?->name ?? ''; ?>,%20e%20seu%20pedido%20<?php echo $order?->id ?? ''; ?>%20foi%20confirmado!%0A%0AComo%20voc%C3%AA%20optou%20por%20pagamento%20via%20PIX,%20o%20prazo%20de%20compensa%C3%A7%C3%A3o%20pode%20levar%20at%C3%A9%202%20horas.%0A%0A%F0%9F%9B%91%20Caso%20voc%C3%AA%20n%C3%A3o%20tenha%20conclu%C3%ADdo%20a%20transa%C3%A7%C3%A3o,%20vou%20deixar%20aqui%20abaixo%20os%20c%C3%B3digos%20para%20voc%C3%AA%20finalizar%20a%20sua%20transa%C3%A7%C3%A3o:%0A%0A%E2%9E%A1%EF%B8%8F%20PIX%20COPIA%20E%20COLA:%0A%0A<?php echo $ordermeta?->payment_pix_code ?? ''; ?>%0A%0AAh,%20mais%20uma%20coisa!%20Adicione%20nosso%20contato%20e%20responda%20confirmar,%20para%20continuar%20recebendo%20nossas%20notifica%C3%A7%C3%B5es,%20caso%20voc%C3%AA%20n%C3%A3o%20queira%20mais%20receber,%20basta%20responder%20sair.%0A%0AAt%C3%A9%20logo!">
                                    Lembrete de Pagamento
                                  </a>
                                </li>
                              <?php elseif ($pm == 'billet') : ?>
                                <li>
                                  <a target="_blank" href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>&text=Ol%C3%A1,%20<?php echo $ordermeta?->customer_name ?? ''; ?>,%20Tudo%20bem?%20%0A%0AEstamos%20passando%20pra%20te%20lembrar%20que%20o%20seu%20boleto%20est%C3%A1%20prestes%20a%20vencer.%20Fa%C3%A7a%20o%20pagamento%20o%20mais%20r%C3%A1pido%20poss%C3%ADvel%20para%20que%20possamos%20liberar%20o%20seu%20acesso!%0A%0A%F0%9F%93%91%20Se%20voc%C3%AA%20precisar,%20o%20link%20do%20seu%20boleto%20j%C3%A1%20est%C3%A1%20aqui%20logo%20abaixo:%0A<?php echo $ordermeta?->payment_billet_link ?? ''; ?><%0A%0ASe%20voc%C3%AA%20j%C3%A1%20pagou%20o%20boleto,%20%C3%A9%20s%C3%B3%20aguardar!%20E%20se%20voc%C3%AA%20desistiu%20da%20compra,%20basta%20desconsiderar%20o%20boleto%20e%20seu%20pedido%20ser%C3%A1%20cancelado.%0A%0AAt%C3%A9%20mais!">
                                          Lembrete de Pagamento
                                          </a>
                                        </li>                                    
                                <?php endif; ?>
                            </ul>
                          </div>
                        </div>
                        <div class="dropdown">
                          <a href="#" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                          <em class="icon ni ni-whatsapp"></em>
                            <span>Alterar Status</span>
                            <em class="icon ni ni-chevron-down"></em>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-menu-auto mt-1" style="">
                            <ul class="link-list-plain">
                            <?php if ($order->status == EOrderStatus::APPROVED->value) : ?>
                              <li>
                                <a href="#">Reembolsar pedido</a>
                              </li>
                              <?php else : ?>
                              <li>
                                <a href="#">Aprovar pedido</a>
                              </li>
                              <?php endif; ?>
                              <?php if ($order?->product()?->type == 'physical') : ?>
                              <li>
                                <a href="#">Produto Faturado</a>
                              </li>
                              <li>
                                <a href="#">Produto em Transporte</a>
                              </li>
                              <li>
                                <a href="#">Produto Entregue</a>
                              </li>
                              <?php endif; ?>
                            </ul>
                          </div>
                        </div>
                      </div> -->
                    </li>
                  </ul>
                </div>
              </div>
            <?php endif; ?>
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
                              <!-- <div class="nk-tb-col tb-col-md">
                                <span>Qtd.</span>
                              </div>
                              <div class="nk-tb-col">
                                <span>Desconto</span>
                              </div> -->
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
                                <span class="tb-sub"><?php if ($order->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$<?php endif; ?> <?php echo currency($order->product()?->price); ?></span>
                              </div>
                              <!-- <div class="nk-tb-col tb-col-md">
                                <span class="tb-sub">-</span>
                              </div>
                              <div class="nk-tb-col">
                                <span class="tb-sub">R$ <?php echo currency($ordermeta?->product_price_promo_diff ?? '0'); ?></span>
                              </div> -->
                              <div class="nk-tb-col tb-col-md">
                                <span class="tb-sub"><?php if ($order->currency_symbol == 'usd'): ?>$<?php elseif ($order?->currency_symbol == 'brl'): ?>R$
                                  <?php endif; ?>  <?php echo currency($ordermeta?->product_price); ?></span>
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
                                  <td colspan="2" style="font-size: 12px;font-weight: 400;">Valor do pagamento (Convertido)</td>
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
                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-user-alt"></em></strong> <?php echo $ordermeta?->customer_name ?? ''; ?></span>
                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-mail"></em></strong> <?php echo $ordermeta?->customer_email ?? ''; ?></span>
                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-call"></em></strong> <a href="https://api.whatsapp.com/send?phone=55<?php echo $ordermeta?->customer_phone ?? ''; ?>"><?php echo $ordermeta?->customer_phone ?? ''; ?></a></span>
                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-file-check"></em></strong> <?php echo $ordermeta?->customer_cpf_cnpj ?? ''; ?></span>
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
                              <span class="badge bg-warning"><?php echo date("d/m/Y - H:i:s", strtotime($order->seller_credited_at ?? '')) ?></span>
                            <?php endif; ?>
                          </p>
                        <?php endif; ?>
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
        <?php if ($order->user_id == $user->id) : ?>
          <div class="nk-content-wrap mt-2">
            <div class="nk-block">
              <div class="card card-bordered sp-plan">
                <div class="card-inner">
                  <ul class="nav nav-tabs">
                    <!-- <li class="nav-item">
                      <a class="nav-link active" data-bs-toggle="tab" href="#resumo">
                        <em class="icon ni ni-user"></em>
                        <span>Resumo</span>
                      </a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#fiscal">
                        <em class="icon ni ni-file-check"></em>
                        <span>Nota Fiscal</span>
                        </a>
                      </li> -->
                    <?php if ($order?->product()?->type == 'physical') : ?>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rastreamento">
                          <em class="icon ni ni-truck"></em>
                          <span>Rastreamento</span>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php if ($order?->product()?->affiliate_enabled) : ?>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#afiliado">
                          <em class="icon ni ni-users-fill"></em>
                          <span>Afiliado / Co-Produtores</span>
                        </a>
                      </li>
                    <?php endif; ?>

                    <li class="nav-item active">
                      <a class="nav-link" data-bs-toggle="tab" href="#utm">
                        <em class="icon ni ni-link"></em>
                        <span>UTM / Trackeamento</span>
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane " id="resumo">
                      <div class="col-12 col-md-12">
                        <div class="d-flex">
                          <div class="order-tracking <?php if ($order->status == EOrderStatus::PENDING->value) : ?>completed<?php elseif ($order->status == EOrderStatus::APPROVED->value) : ?>completed<?php else : ?><?php endif; ?>">
                            <span class="is-complete"></span>
                            <p>Aguardando pagamento<br>
                              <span class="fs12px"><?php echo date("d/m/Y - H:i:s", strtotime($order?->created_at ?? '')) ?></span>
                            </p>
                          </div>
                          <div class="order-tracking <?php if ($order->status == EOrderStatus::APPROVED->value) : ?>completed<?php else : ?><?php endif; ?>">
                            <span class="is-complete"></span>
                            <p>Pagamento aprovado<br>
                              <span class="fs12px"><?php if ($order->status == EOrderStatus::APPROVED->value) : ?><?php echo date("d/m/Y - H:i:s", strtotime($order?->updated_at ?? '')) ?><?php else : ?><?php endif; ?></span>
                            </p>
                          </div>
                          <?php if ($order?->product()?->type == 'physical') : ?>
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
                      <div class="example-alert">
                        <div class="alert alert-light alert-icon"><em class="icon ni ni-alert-circle"></em>Esse pedido ainda não possui nota fiscal</div>
                      </div>
                      </p>
                    </div>
                    <div class="tab-pane" id="rastreamento">
                      <p>
                        <a href="#" class="btn btn-outline-primary">Informar Codigo de Rastreio</a></li>
                      <div class="example-alert">
                        <div class="alert alert-light alert-icon"><em class="icon ni ni-alert-circle"></em>Informações de rastreamento indisponíveis.</div>
                      </div>
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
                    <div class="tab-pane active" id="utm">
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
</content>