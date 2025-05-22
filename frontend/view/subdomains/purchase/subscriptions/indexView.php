<?php use Backend\Enums\Subscription\ESubscriptionStatus; ?>

<title><?php echo __('My Subscriptions') ?></title>

<content>
  <div class="nk-content-body">

    <div class="nk-block-head nk-block-head-sm mt-3">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title"><?php echo __('My Subscriptions') ?></h3>
        </div>
        <div class="nk-block-head-content">
          <!-- <div class="toggle-wrap nk-block-tools-toggle">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light  d-md-none">
              <em class="icon ni ni-help"></em>
            </a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"
              class="btn btn-outline-light d-none d-md-inline-flex">
              <em class="icon ni ni-help"></em>
              <span>Ajuda</span>
            </a>
            <a href="#" class="btn btn-white btn-dim btn-outline-light d-md-none">
              <em class="icon ni ni-download-cloud"></em>
            </a>
            <a href="#" class="btn btn-white btn-dim btn-outline-light  d-none d-md-inline-flex">
              <em class="icon ni ni-download-cloud"></em>
              <span>Export</span>
            </a>
          </div> -->
        </div>
      </div>
    </div>

    <div class="nk-block">
      <div class="card card-bordered">
        <div class="card-inner-group">
          <div class="card-inner position-relative card-tools-toggle">
            <div class="card-title-group" data-select2-id="16">
              <div>
                <!-- left                -->
              </div>
              <div class="card-tools me-n1">
                <ul class="btn-toolbar gx-1">
                  <li>
                    <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search">
                      <em class="icon ni ni-search"></em>
                    </a>
                  </li>
                  <li class="btn-toolbar-sep"></li>
                  <li>
                    <div class="toggle-wrap">
                      <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                        <em class="icon ni ni-menu-right"></em>
                      </a>
                      <div class="toggle-content" data-content="cardTools">
                        <ul class="btn-toolbar gx-1">
                          <li class="toggle-close">
                            <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                              <em class="icon ni ni-arrow-left"></em>
                            </a>
                          </li>
                          <li>
                            <div class="dropdown">
                              <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <em class="icon ni ni-setting"></em>
                              </a>
                              <div class="dropdown-menu dropdown-menu-xs dropdown-menu-end" style="">
                                <ul class="link-check">
                                  <li>
                                    <span>Show</span>
                                  </li>
                                  <li class="active">
                                    <a href="#">10</a>
                                  </li>
                                  <li>
                                    <a href="#">20</a>
                                  </li>
                                  <li>
                                    <a href="#">50</a>
                                  </li>
                                </ul>
                                <ul class="link-check">
                                  <li>
                                    <span>Order</span>
                                  </li>
                                  <li class="active">
                                    <a href="#">DESC</a>
                                  </li>
                                  <li>
                                    <a href="#">ASC</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card-search search-wrap" data-search="search">
              <div class="card-body">
                <div class="search-content">
                  <a href="#" class="search-back btn btn-icon toggle-search" data-target="search">
                    <em class="icon ni ni-arrow-left"></em>
                  </a>
                  <input type="text" class="form-control border-transparent form-focus-none"
                    placeholder="Search by name">
                  <button class="search-submit btn btn-icon">
                    <em class="icon ni ni-search"></em>
                  </button>
                </div>
              </div>
            </div>
          </div>
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
                <!-- <div class="nk-tb-col">
                  <span>Nome</span>
                </div> -->
                <div class="nk-tb-col">
                  <span>Produto</span>
                </div>
                <div class="nk-tb-col">
                  <span>Total</span>
                </div>
                <div class="nk-tb-col">
                  <span>Pagamento</span>
                </div>
                <div class="nk-tb-col">
                  <span>Status</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                  <span class="d-none d-sm-block">Ativa até</span>
                </div>
                <div class="nk-tb-col nk-tb-col-tools">
                  ...
                </div>
              </div>
              
              <?php foreach ($subscriptions as $subscription): ?>
                <?php $order = $subscription->order ?? null;
                $products = $order?->products() ?? [];
                ?>
                <div class="nk-tb-item">
                  <div class="nk-tb-col nk-tb-col-check">
                    <div class="custom-control custom-control-sm custom-checkbox notext">
                      <input type="checkbox" class="custom-control-input" id="oid01">
                      <label class="custom-control-label" for="oid01"></label>
                    </div>
                  </div>
                  <div class="nk-tb-col">
                    <span class="tb-lead">
                      <a href="#"><?php echo $subscription->id; ?></a>
                    </span>
                  </div>
                  <!-- <div class="nk-tb-col">
                    <div class="user-info">
                      <a href="#">
                        <span class="tb-lead">Ezequiel Moraes Mello </span><span>quielbala@gmail.com</span>
                      </a>
                    </div>
                  </div> -->
                  <div class="nk-tb-col">
                    <span class="tb-lead"><?php echo join(", ", objval($products)->product_names ?? []); ?></span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="tb-lead">R$ <?php echo currency($order->total ?? 0); ?></span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="tb-lead"> <img class="img-pay" src="https://rocketpays.app/images/pay/billet.svg">
                    </span>
                  </div>
                  <div class="nk-tb-col">
                    <?php if ($subscription->status == ESubscriptionStatus::ACTIVE->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Ativa</span>
                    <?php elseif ($subscription->status == ESubscriptionStatus::CANCELED->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Cancelada</span>
                    <?php elseif ($subscription->status == ESubscriptionStatus::PENDING->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Pendente</span>
                    <?php endif; ?>
                  </div>
                  <div class="nk-tb-col tb-col-md">
                    <span class="tb-sub"><?php echo date("d/m/Y", strtotime($subscription->expires_at)); ?></span>
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
                                <a href="javascript:;" click="cancelSubscriptionOnClick" data-id="<?php echo $subscription->id; ?>">
                                  <em class="icon ni ni-eye"></em>
                                  <span>Cancelar assinatura</span>
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
</content>