<?php use Backend\Enums\Order\EOrderStatus;?>

<title>Pedidos</title>

<style>
.s1 {
  color: #d49730!important;
  }
  .s2 {
  color: #4a90e2!important;
  }
  .s3 {
  color: #24a028!important;
  }
  .s4 {
  color: #fe509c!important;
  }
  .s5 {
  color: #6e4e19!important;
  }
  .s6 {
  color: #725bc2!important;
  }
  .s7 {
  color: #16ac9a!important;
  }
  .s8 {
  color: #666!important;
  }
  .s9 {
  color: #e50f38!important;
  }
</style>
<content>
  <div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">Vendas</h3>
        </div>
      
      </div>
    </div>
      <div class="mb-4 grid-filter">
        <div class=" mb-3">
          <div class="form-control-wrap">
            <form id="search-form" class="input-group" action="javascript:void(0);">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                  <em class="icon ni ni-search"></em>
                </span>
              </div>
              <input type="text" class="form-control search" placeholder="Buscar por transação, e-mail ou nome..." name="search" value="<?php if(isset($_GET['search'])) { echo $_GET['search']; } ?>" required="">
              <button click="fetchOrderData">Buscar</button>
            </form>
          </div>
        </div>
        <div class="mb-3">
          <a data-target="demoML" class="toggle btn btn-primary" data-target="demoML" href="" aria-label="Main Demo Preview" data-bs-original-title="Main Demo Preview">
            <em class="icon ni ni-opt-dot-alt"></em>
            <span>Filtrar</span>
          </a>
        </div>
      </div>
    <div class="card card-bordered card-preview">
      <div class="card-inner">
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="<?php echo site_url(); ?>/orders">Todos pedidos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link s3" href="<?php echo site_url() ?>/admin/orders/approved">Aprovado</a>
          </li>
           <li class="nav-item">
            <a class="nav-link s9" href="<?php echo site_url() ?>/admin/orders/cancelled">Cancelado</a>
          </li> 
          
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tabItem1">
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
                    <span>Comprador</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Produto</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Vendedor</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Valor Pago</span>
                  </div>
                  <div class="nk-tb-col">
                    <span>Status</span>
                  </div>
                  <div class="nk-tb-col tb-col-md">
                    <span class="d-none d-sm-block">Data</span>
                  </div>
                  
                  <div class="nk-tb-col nk-tb-col-tools"> ... </div>
                </div>
                <?php foreach ($orders as $key => $order)  : ?>
                  <?php $ordermeta = $order->metas(); ?>
                  <div class="nk-tb-item">
                    <div class="nk-tb-col nk-tb-col-check">
                      <div class="custom-control custom-control-sm custom-checkbox notext">
                        <input type="checkbox" class="custom-control-input" id="<?php echo $key; ?>">
                        <label class="custom-control-label" for="<?php echo $key; ?>"></label>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead">
                        <a href="<?php echo site_url(); ?>/admin/order/<?php echo $order->id; ?>/show"><?php echo $order->id; ?></a>
                      </span>
                    </div>
                    <div class="nk-tb-col">
                      <div class="user-info">
                        <a href="<?php echo site_url(); ?>/admin/order/<?php echo $order->id; ?>/show">
                          <span class="tb-lead"><?php echo $ordermeta?->customer_name ?? 'Não identificado'; ?></span>
                          <span><?php echo $ordermeta?->customer_email ?? ''; ?></span>
                        </a>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead"><?php echo $order?->product()?->name ?? ''; ?></span>
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead"><?php echo $order?->user()?->name ?? ''; ?></span>
                      <span><?php echo $order?->user()?->email ?? ''; ?></span>
                      
                    </div>
                    <div class="nk-tb-col">
                      <span class="tb-lead">R$ <?php echo currency($order->total ?? 0); ?></span>
                    </div>
                    <div class="nk-tb-col">
                      <?php if ($order->status == EOrderStatus::APPROVED->value): ?>
                          <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Aprovado</span>

                        <?php elseif ($order->status == EOrderStatus::PENDING->value): ?>
                          <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Pendente</span>

                        <?php elseif ($order->status == EOrderStatus::CANCELED->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Cancelado</span>

                        <?php elseif ($order->status == EOrderStatus::INITIATED->value): ?>
                        <span class="badge badge-sm badge-dot has-bg bg-warning d-sm-inline-flex">Iniciado</span>

                        <?php else: ?>
                        <span class="badge badge-sm badge-dot has-bg bg-danger d-sm-inline-flex">Erro</span>
                        <?php endif; ?>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="tb-sub"><?php echo date("d/m/Y - H:i:s", strtotime($order->created_at ?? '')) ?></span>
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
                                <a href="<?php echo site_url(); ?>/admin/order/<?php echo $order->id; ?>/show">
                                    <em class="icon ni ni-eye"></em>
                                    <span>Detalhes do pedido</span>
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
            <Pagination />
          </div>
        </div>
      </div>
    </div>
    
  </div>

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
                            <option><?php echo $product->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
<!--          <div class="col-md-3 mt-2 col-sm-6">-->
<!--            <label class="form-label mb-2">Tipo de oferta</label>-->
<!--            <div class="custom-control custom-checkbox mb-2">-->
<!--              <input type="checkbox" class="custom-control-input" id="customCheck1" name="unique">-->
<!--              <label class="custom-control-label" for="customCheck1">Preço único</label>-->
<!--            </div>-->
<!--            <div class="custom-control custom-checkbox">-->
<!--              <input type="checkbox" class="custom-control-input" id="customCheck2" name="recurrency">-->
<!--              <label class="custom-control-label" for="customCheck2">Assinatura</label>-->
<!--            </div>-->
<!--          </div>-->
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
          <button click="fetchOrderData" class="btn btn-primary mt-4">Buscar</button>
          <input class="btn btn-primary mt-4" type="button" click="reset" value="Limpar">
        </form>
      </div>
    </div>
  </div>

</content>