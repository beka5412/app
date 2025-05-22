<title>Cliente <?php echo $customer->name; ?></title>

<content>
<div class="nk-content-body">
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Cliente / <strong class="text-primary small"><?php echo $customer->name; ?></strong>
        </h3>
        <div class="nk-block-des text-soft">
          <ul class="list-inline">
            <li>ID: <span class="text-base">UD00<?php echo $customer->id; ?></span>
            </li>
          </ul>
        </div>
      </div>
      <div class="nk-block-head-content">
        <a href="https://rocketpays.app/customer" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
          <em class="icon ni ni-arrow-left"></em>
          <span>Back</span>
        </a>
        <a href="https://rocketpays.app/customer" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
          <em class="icon ni ni-arrow-left"></em>
        </a>
      </div>
    </div>
  </div>
<div class="nk-block">
    <div class="card mb-4">
        <!-- card body -->
        <div class="card-body">
            <div class="d-flex align-items-center">
            <!-- img -->
            <img src="https://geeksui.codescandy.com/geeks/assets/images/avatar/avatar-12.jpg" class="avatar-xl rounded-circle" alt="">
            <div class="ms-4">
                <!-- text -->
                <h3 class="mb-1"><?php echo $customer->name; ?><span class="ms-2 badge bg-success">Afiliado</span></h3>

                <div>
                <em class="icon ni ni-mail"></em>
                <span><?php echo $customer->email; ?></span>
                </div>
            </div>
            </div>
        </div>
        <!-- card body -->
        <div class="card-body border-top">
            <div class="d-flex justify-content-between">
            <!-- text -->
            <div class="mb-3">
                <span class="fw-semi-bold">Afiliado</span>
                <div class="mt-2">
                <span class="badge bg-success">Sim</span>
                <span> </span>
                </div>
            </div>
            <!-- text -->
            <div class="mb-3">
                <span class="fw-semi-bold">Total de compras</span>
                <div class="mt-2">
                <h5 class="h2 fw-bold mb-1">R$ <?php echo currency(\Backend\Models\Order::where('customer_id', $customer->id)->sum('total') ?? 0); ?></h5>
                <span>Total de <?php echo \Backend\Models\Order::where('customer_id', $customer->id)->count() ?? 0; ?> pedidos</span>
                </div>
            </div>
            <!-- text -->
            <div>
                <span class="fw-semi-bold">Ticket Médio</span>
                <div class="mt-2">
                <h5 class="h3 fw-bold mb-1">R$ 210,18</h5>
                <span>Média de gasto</span>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-12">
            <!-- card -->
            <!-- card -->
            <div class="mb-4">
            <div class="card">
                <!-- card body -->
                <div class="card-header">
                <h4 class="mb-0">Pedidos</h4>
                </div>
                <div class="card-body">
                <ul class="list-group list-group-flush">
                <?php foreach ($customer?->purchases ?? [] as $purchase): ?>
                    <li class="list-group-item px-0">
                        <div>
                            <!-- order id -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="text-primary mb-0">Pedido: #<?php echo $purchase->order_id; ?></h6>
                            </div>
                            <div>
                                <span>R$ <?php echo currency($purchase->order->total); ?></span>
                            </div>
                            </div>
                            <!-- text -->
                            <div class="d-flex justify-content-between">
                            <div>
                                <a href="#" class="text-inherit">
                                <div class="d-lg-flex align-items-center">
                                    <!-- img -->
                                    <div>
                                    <img src="<?php echo $purchase->product->image; ?>" alt="" class="img-4by3-md rounded">
                                    </div>
                                    <!-- text -->
                                    <div class="ms-lg-3 mt-2 mt-lg-0">
                                    <h5 class="mb-0"><?php echo $purchase->product->name; ?></h5>
                                    <span class="fs-6">SKU: <?php echo $purchase->product_id; ?></span>
                                    </div>
                                </div>
                                </a>
                            </div>
                            <div>
                                <!-- button -->
                                <a href="#" class="btn btn-sm"><?php echo $purchase->status; ?></a>
                            </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
                    
                </ul>
                </div>
                <!-- text -->
                <div class="card-footer d-flex justify-content-end">
                <Pagination />

                </div>
            </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- card -->
            <div class="card mt-4 mt-lg-0">
            <!-- card body -->
            <div class="card-body border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Contato</h4>
                </div>
                <!-- text email -->
                <div>
                <div class="d-flex align-items-center mb-2">
                <em class="icon ni ni-user-alt"></em>
                    <a href="#" class="ms-2"><?php echo $customer->name; ?></a>
                </div>
                <div class="d-flex align-items-center mb-2">
                <em class="icon ni ni-mail"></em>
                    <a href="#" class="ms-2"><?php echo $customer->email; ?></a>
                </div>
                <div class="d-flex align-items-center mb-2">
                <em class="icon ni ni-call"></em>
                    <a href="#" class="ms-2"><?php echo $customer->phone; ?></a>
                </div>
                <!-- text phone -->
                <div class="d-flex align-items-center">
                <em class="icon ni ni-calendar"></em>
                    <span class="ms-2"><?php echo $customer->birthdate; ?></span>
                </div>
                </div>
            </div>
            <!-- card body -->
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- text -->
                <h4 class="mb-0">Endereço</h4>
                </div>
                <div>
                <!-- address -->
                <p class="mb-0"> <?php echo $customer->address_street; ?>,<?php echo $customer->address_number; ?> <br> <?php echo $customer->address_district; ?>, <br> <?php echo $customer->address_city; ?>, <br> <?php echo $customer->address_state; ?> / <?php echo $customer->address_zipcode; ?></p>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

</div>


  
</content>