<?php use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;

?>
<title>Minhas Vendas</title>
<content>
<?php if ((json_decode(json_encode($orders))->total ?? 0) == 0): ?>
  <div class="nk-block nk-block-middle wide-md mx-auto">
    <div class="nk-block-content nk-error-ld text-center">
      <center>
      <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player src="https://assets1.lottiefiles.com/packages/lf20_fKk8BKYneU.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>                        </center>
      <div class="wide-xs mx-auto">
        <h3 class="nk-error-title">Não há Pedidos! : (</h3>
        <p class="nk-error-text">Quando surgir um novo pedido, você poderá gerenciá-los por aqui. </p>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
          <em class="icon ni ni-help"></em>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
          <em class="icon ni ni-help"></em>
          <span>Aprenda mais sobre esse recurso</span>
        </a>
      </div>
    </div>
  </div>
<?php else: ?>
<div class="nk-content-body">
  <!-- header page -->
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Minhas Vendas
            </h3>
          </div>
          </div>
        </div>
    </div>
    <!-- cards -->
  <div class="">
    <div class="row row-cols-1 mt-3 mb-4 row-cols-md-2 row-cols-xl-3">
      <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Total em pedidos</p>
                <h4 class="my-1 text-info">
                  <div class="col">
                    <span class="total-orders">R$ <?php echo currency(\Backend\Models\Order::where('user_id', $user->id)->sum('total') ?? 0); ?></span>
                  </div>
                </h4>
                <div class="smaller">
                    <font style="vertical-align: inherit;">
                    Total de pedidos: <?php echo \Backend\Models\Order::where('user_id', $user->id)->count() ?? 0; ?>
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
      <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-success">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Pedidos Aprovados</p>
                <h4 class="my-1 text-success">
                  <div class="col">
                    <span class="completed-orders"> R$ <?php echo currency(\Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::APPROVED->value)->where('user_id', $user->id)->sum('total') ?? 0); ?></span>
                  </div>
                </h4>
                <div class="smaller">
                    <font style="vertical-align: inherit;">
                    Total de pedidos aprovados: <?php echo \Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::APPROVED->value)->where('user_id', $user->id)->count() ?? 0; ?>
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
                <p class="mb-0 text-secondary">Pedidos Pendentes</p>
                <h4 class="my-1 text-warning">
                  <div class="col">
                    <span class="pending-orders"> R$ <?php echo currency(\Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::PENDING->value)->where('user_id', $user->id)->sum('total') ?? 0); ?></span>
                   
                  </div>
                </h4>
                <div class="smaller">
                    <font style="vertical-align: inherit;">
                    Total de pedidos Pendentes: <?php echo \Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::PENDING->value)->where('user_id', $user->id)->count() ?? 0; ?>
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
      <!-- <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-danger">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Pedidos Cancelados</p>
                <h4 class="my-1 text-danger">
                  <div class="col">
                    <span class="cancled-orders"> R$ <?php echo currency(\Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::CANCELED->value)->where('user_id', $user->id)->sum('total') ?? 0); ?></span>
                  </div>
                </h4>
                <div class="smaller">
                            <font style="vertical-align: inherit;">
                            Total de pedidos cancelados: <?php echo \Backend\Models\Order::where('status', \Backend\Enums\Order\EOrderStatus::CANCELED->value)->where('user_id', $user->id)->count() ?? 0; ?>
                          </font>
                        </div>
              </div>
              <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                <i class="fa fa-dollar"></i>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </div>

  <!-- content -->

  <div class="mb-4 d-flex grid-filter">
        <div class="col-lg-10 mb-3">
          <div class="form-control-wrap">
              <form id="search-form" class="input-group" action="javascript:void(0);">
                  <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                  <em class="icon ni ni-search"></em>
                </span>
                  </div>
                  <input type="text" class="form-control search" placeholder="Buscar por transação, e-mail ou nome..." name="search" value="<?php if(isset($_GET['search'])) { echo $_GET['search']; } ?>" required="">
                  <button click="fetchSalesData" class="btn btn-primary">Buscar</button>
              </form>
          </div>
        </div>
        <div class="col-lg-2 mb-3" style=" margin-left: 10px; ">
            <a data-target="demoML" class="toggle btn btn-primary" href="" aria-label="Main Demo Preview" data-bs-original-title="Main Demo Preview">
                <em class="icon ni ni-opt-dot-alt"></em>
                <span>Filtrar</span>
            </a>
        </div>
      </div>
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
                        <span>Pagamento</span>
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
                    <?php foreach ($orders as $order): ?>
                      <?php $ordermeta = $order->metas(); ?>
                    <div class="nk-tb-item">
                      <div class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                          <input type="checkbox" class="custom-control-input" id="oid02">
                          <label class="custom-control-label" for="oid02"></label>
                        </div>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead">
                          <a href="<?php echo site_url()."/sale/$order->id/show"; ?>">
                          <?php echo $order->id; ?></a>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                        <div class="user-info">
                          <a href="<?php echo site_url()."/sale/$order->id/show"; ?>">
                              <span class="tb-lead"><?php echo $ordermeta?->customer_name ?? ''; ?>
                              </span><span><?php echo $ordermeta?->customer_email ?? ''; ?></span>
                          </a>
                        </div>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead"><?php echo $order?->product()?->name ?? ''; ?></span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead">R$ <?php echo currency($order->total); ?></span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead"><?php $pm = $order->meta('info_payment_method'); ?>
                          <?php if ($pm == 'credit_card'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="1.5" d="M4 10.09h16"></path><rect width="16.5" height="12.5" x="3.75" y="5.75" stroke="currentColor" stroke-width="1.5" rx="3.25"></rect></svg>
                          <?php elseif ($pm == 'pix'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M6.644 6.692c.785 0 1.522.302 2.077.858l3.008 3.007a.554.554 0 0 0 .785 0l3.007-2.995a2.913 2.913 0 0 1 2.077-.858h.363l-3.817-3.816a3.047 3.047 0 0 0-4.3 0L6.04 6.692h.604ZM17.6 17.296a2.913 2.913 0 0 1-2.078-.858l-2.995-2.995a.577.577 0 0 0-.785 0L8.734 16.45a2.914 2.914 0 0 1-2.077.858h-.592l3.804 3.804a3.047 3.047 0 0 0 4.3 0l3.816-3.816h-.386Zm1.207-9.747 2.306 2.307a3.032 3.032 0 0 1 0 4.287l-2.306 2.307a.427.427 0 0 0-.17-.036h-1.05a2.069 2.069 0 0 1-1.45-.604l-2.995-2.995c-.543-.544-1.497-.544-2.04 0l-3.008 3.007a2.027 2.027 0 0 1-1.45.604H5.352a.335.335 0 0 0-.157.036l-2.306-2.307a3.047 3.047 0 0 1 0-4.3l2.319-2.318a.334.334 0 0 0 .157.036h1.292a2.07 2.07 0 0 1 1.45.604l3.006 3.007a1.426 1.426 0 0 0 2.03 0l2.995-2.995a2.027 2.027 0 0 1 1.449-.604h1.05c.06 0 .121-.012.17-.036Z" clip-rule="evenodd"></path></svg>
                          <?php elseif ($pm == 'free'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><g stroke="currentColor" stroke-miterlimit="10" stroke-width="1.5" clip-path="url(#Diamond_svg__a)"><path d="m3.43 8.71 3.03-3.89c.19-.26.45-.46.74-.61.29-.14.6-.22.92-.22h7.76c.32 0 .64.07.92.22.3.14.54.35.75.61l3.03 3.89c.29.36.43.82.41 1.28-.03.46-.21.9-.53 1.24l-7.78 8.46c-.08.1-.19.18-.31.23a.91.91 0 0 1-.74 0 .788.788 0 0 1-.31-.23l-7.79-8.45A1.92 1.92 0 0 1 3 9.99c-.02-.47.13-.93.42-1.29l.01.01ZM3.1 10.02h17.81"></path><path d="m13 4 1.98 6.02-2.97 9.96"></path></g><defs><clipPath id="Diamond_svg__a"><path fill="#fff" d="M0 0h24v24H0z"></path></clipPath></defs></svg>  
                          <?php elseif ($pm == 'billet'): ?>
                          <img class="img-pay"  src="" />
                          <?php endif; ?>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                      <?php if ($order->status == EOrderStatusDetail::INITIATED->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Checkout iniciado</span>
                      <?php endif; ?>
                      <?php if ($order->status == EOrderStatusDetail::APPROVED->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Aprovado</span>
                      <?php endif; ?>

                      <?php if ($order->status == EOrderStatusDetail::PENDING->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Pendente</span>
                      <?php endif; ?>

                      <?php if ($order->status == EOrderStatusDetail::CANCELED->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Cancelado</span>
                      <?php endif; ?>
                      
                      <?php if ($order->status == EOrderStatusDetail::REFUNDED->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Reembolsado</span>
                      <?php endif; ?>
                      
                      <?php if ($order->status == EOrderStatusDetail::CHARGEDBACK->value): ?>
                      <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Estornado</span>
                      <?php endif; ?>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="tb-sub"><?php echo date("d/m/Y - H:i:s", strtotime($order->created_at ?? '')) ?></span>
                      </div>
                      <div class="nk-tb-col nk-tb-col-tools">
                        <!-- <ul class="nk-tb-actions gx-1">
                          <li>
                            <div class="drodown me-n1">
                              <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                <em class="icon ni ni-more-h"></em>
                              </a>
                              <div class="dropdown-menu dropdown-menu-end">
                                <ul class="link-list-opt no-bdr">
                                  <li>
                                    <a href="<?php echo site_url()."/sale/$order->id/show"; ?>" to="<?php echo site_url()."/sale/$order->id/show"; ?>">
                                      <em class="icon ni ni-eye"></em>
                                      <span>Detalhes do pedido</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="#">
                                      <em class="icon ni ni-trash"></em>
                                      <span>Cancelar pedido</span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </li>
                        </ul> -->
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

    <div class="nk-demo-panel nk-demo-panel-2x toggle-slide toggle-slide-right toggle-screen-any sidebar" data-content="demoML" data-toggle-overlay="true" data-toggle-body="true" data-toggle-screen="any">
        <div class="nk-demo-head px-2">
            <h6 class="mb-0"></h6>
            <a class="nk-demo-close toggle btn btn-icon btn-trigger revarse mr-n2" data-target="demoML" href="#">
                <em class="icon ni ni-cross"></em>
            </a>
        </div>
        <div class="card-inner">
            <div class="row gy-4">
                <form id="filter-form" class="col-lg-12" action="javascript:void(0);">
                    <div class="form-group">
                        <label class="form-label">Período </label>
                        <div class="form-control-wrap focused">
                            <div class="form-icon form-icon-left">
                                <em class="icon ni ni-calendar"></em>
                            </div>
                            <input type="text" class="form-control date-picker py-0 date" name="date" data-date-format="yyyy-mm-dd" value="<?php if(isset($_GET['date'])) { echo $_GET['date']; } ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Produto</label>
                        <div class="form-control-wrap">
                            <select class="form-select" name="product">
                                <option>Selecione uma opção</option>
                                <?php foreach ($products as $product) { ?>
                                    <option value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-block w-100 mt-4">
                        <label class="form-label w-100 mb-2">Status do pedido</label>
                        <div class="custom-control custom-checkbox w-100 mb-2">
                            <input type="checkbox" class="custom-control-input status-approved" id="customCheck3" name="approved" <?php if(isset($_GET['approved'])) {  echo 'checked'; } ?>>
                            <label class="custom-control-label" for="customCheck3">Aprovado</label>
                        </div>
                        <div class="custom-control custom-checkbox w-100 mb-2">
                            <input type="checkbox" class="custom-control-input status-pending" id="customCheck4" name="pending" <?php if(isset($_GET['pending'])) {  echo 'checked'; } ?>>
                            <label class="custom-control-label" for="customCheck4">Pendente</label>
                        </div>
                        <div class="custom-control custom-checkbox w-100 mb-2">
                            <input type="checkbox" class="custom-control-input status-cancelled" id="customCheck5" name="cancelled" <?php if(isset($_GET['cancelled'])) {  echo 'checked'; } ?>>
                            <label class="custom-control-label" for="customCheck5"> Cancelado</label>
                        </div>
                    </div>
                    <button click="fetchSalesData" data-target="demoML" class="toggle btn btn-primary mt-4 me-3">Buscar</button>
                    <input class="btn btn-primary mt-4" type="button" click="reset" value="Limpar">
                </form>
            </div>
        </div>
    </div>

</content>

