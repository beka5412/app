<?php use Backend\Enums\Subscription\ESubscriptionStatus; ?>
<title>Minhas Assinaturas</title>
<content>
  <?php if ((json_decode(json_encode($subscriptions))->total ?? 0) == 0): ?>
    <div class="nk-block nk-block-middle wide-md mx-auto">
      <div class="nk-block-content nk-error-ld text-center">
        <center>
          <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player
            src="https://assets1.lottiefiles.com/packages/lf20_fKk8BKYneU.json" background="transparent" speed="1"
            style="width: 300px; height: 300px;" loop autoplay></lottie-player>
        </center>
        <div class="wide-xs mx-auto">
          <h3 class="nk-error-title">Não há assinaturas! : (</h3>
          <p class="nk-error-text">Quando surgir um novo pedido, você poderá gerenciá-los por aqui. </p>
         
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="nk-content-body">
      <!-- header page -->
      <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Minhas Assinaturas
            </h3>
          </div>
          <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
              
              <a href="#" class="btn btn-white btn-dim btn-outline-light d-md-none">
                <em class="icon ni ni-download-cloud"></em>
              </a>
              <a href="#" class="btn btn-white btn-dim btn-outline-light  d-none d-md-inline-flex">
                <em class="icon ni ni-download-cloud"></em>
                <span>Export</span>
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- cards -->
      <div class="">
        <div class="row row-cols-1 mt-3 mb-4 row-cols-md-2 row-cols-xl-4">
          <div class="col">
            <div class="card radius-10 border-start border-0 border-3 border-info">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div>
                    <p class="mb-0 text-secondary">Total em assinaturas</p>
                    <h4 class="my-1 text-info">
                      <div class="col">
                        <span class="total-orders">R$
                          <?php echo currency(0); ?>
                        </span>
                      </div>
                    </h4>
                    <div class="smaller">
                      <font style="vertical-align: inherit; font-size: 12px">
                        Total de assinaturas:
                        <?php echo currency(0); ?>
                      </font>
                    </div>
                  </div>
                  <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                    <i class="fa fa-shopping-cart"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10 border-start border-0 border-3 border-success">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div>
                    <p class="mb-0 text-secondary">Assinaturas ativas</p>
                    <h4 class="my-1 text-success">
                      <div class="col">
                        <span class="completed-orders"> R$
                          <?php echo currency($total_active); ?>
                        </span>
                      </div>
                    </h4>
                    <div class="smaller">
                      <font style="vertical-align: inherit; font-size: 12px">
                        Total de assinaturas ativas:
                        <?php echo $count_active; ?>
                      </font>
                    </div>
                  </div>
                  <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                    <i class="fa fa-bar-chart"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10 border-start border-0 border-3 border-warning">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div>
                    <p class="mb-0 text-secondary">Assinaturas pendentes</p>
                    <h4 class="my-1 text-warning">
                      <div class="col">
                        <span class="pending-orders"> R$
                          <?php echo currency($total_pending); ?>
                        </span>

                      </div>
                    </h4>
                    <div class="smaller">
                      <font style="vertical-align: inherit; font-size: 12px">
                        Total de assinaturas pendentes:
                        <?php echo $count_pending; ?>
                      </font>
                    </div>
                  </div>
                  <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                    <i class="fa fa-users"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10 border-start border-0 border-3 border-danger">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div>
                    <p class="mb-0 text-secondary">Assinaturas canceladas</p>
                    <h4 class="my-1 text-danger">
                      <div class="col">
                        <span class="cancled-orders"> R$
                          <?php echo currency($total_canceled); ?>
                        </span>
                      </div>
                    </h4>
                    <div class="smaller">
                      <font style="vertical-align: inherit; font-size: 12px">
                        Total de assinaturas canceladas:
                        <?php echo $count_canceled; ?>
                      </font>
                    </div>
                  </div>
                  <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                    <i class="fa fa-dollar"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- content -->
      <div class="nk-block">
        <div class="card card-bordered">
          <div class="card-inner-group">
           
            <div class="card-inner p-0">
              <div class="nk-tb-list is-separate is-medium mb-3">
                <div class="nk-tb-item nk-tb-head">
                  <div class="nk-tb-col nk-tb-col-check">
                    <div class="custom-control custom-control-sm custom-checkbox notext">
                      <input type="checkbox" class="custom-control-input" id="oid">
                      <label class="custom-control-label" for="oid"></label>
                    </div>
                  </div>
                  <div class="nk-tb-col">
                    <span>Pedido</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Nome</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Produto</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Total</span>
                  </div>
                 
                  <div class="nk-tb-col">
                    <span>Status</span>
                  </div>
                  <div class="nk-tb-col tb-col-md">
                    <span class="d-none d-sm-block">Data</span>
                  </div>
                  <div class="nk-tb-col nk-tb-col-tools">
                    ...
                  </div>
                </div>
                <?php foreach ($subscriptions as $subscription): ?>
                  <?php $order = $subscription->order; $ordermeta = $order->metas(); ?>
                  <div class="nk-tb-item tr" data-subscription-id="<?php echo $subscription->id; ?>">
                    <div class="nk-tb-col nk-tb-col-check">
                      <div class="custom-control custom-control-sm custom-checkbox notext">
                        <input type="checkbox" class="custom-control-input" id="oid01">
                        <label class="custom-control-label" for="oid01"></label>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead">
                        <a href="<?php echo site_url() . "/sale/$order->id/show"; ?>"
                          to="<?php echo site_url() . "/sale/$order->id/show"; ?>">
                          <?php echo $order->id; ?></a>
                      </span>
                    </div>
                    <div class="nk-tb-col">
                      <div class="user-info">
                        <a href="<?php echo site_url() . "/sale/$order->id/show"; ?>"
                          to="<?php echo site_url() . "/sale/$order->id/show"; ?>">
                          <span class="tb-lead">
                            <?php echo $ordermeta?->customer_name ?? ''; ?>
                          </span><span>
                            <?php echo $ordermeta?->customer_email ?? ''; ?>
                          </span>
                        </a>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead">
                        <?php echo $order?->product()?->name ?? ''; ?>
                      </span>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead">R$
                        <?php echo number_format($order->total, 2, ',', '.'); ?>
                      </span>
                    </div>
                  
                    <div class="nk-tb-col">
                      <?php if ($subscription->status == ESubscriptionStatus::ACTIVE->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Aprovado</span>
                      <?php endif; ?>

                      <?php if ($subscription->status == ESubscriptionStatus::PENDING->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Pendente</span>
                      <?php endif; ?>
                      <?php if ($subscription->status == ESubscriptionStatus::CANCELED->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Cancelado</span>
                      <?php endif; ?>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="tb-sub">
                        <?php echo date("d/m/Y - H:i:s", strtotime($order->created_at ?? '')) ?>
                      </span>
                    </div>
                    <div class="nk-tb-col nk-tb-col-tools">
                      <ul class="nk-tb-actions gx-1">
                        <li>
                          <div class="drodown me-n1">
                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                              <em class="icon ni ni-more-h"></em>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                              <ul class="link-list-opt no-bdr">
                                <li>
                                  <a href="javascript:;" click="cancelCustomerSubscriptionOnClick">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Cancelar</span>
                                  </a>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <Pagination />

          </div>
        </div>
      </div>


    </div>
  <?php endif; ?>



</content>