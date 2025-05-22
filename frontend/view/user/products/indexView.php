<?php use Backend\Enums\Product\{EProductWarrantyTime, EProductType, EProductPaymentType, EProductDelivery, EProductRequestStatus}; ?>

<title>Meus produtos</title>
<content class="content_products_list" ready="productsReady">
  <!-- tabela vazia -->
  <?php if ((json_decode(json_encode($products))->total ?? 0) == 0) : ?>
    <div class="nk-block nk-block-middle wide-md mx-auto">
      <div class="nk-block-content nk-error-ld text-center">
        <center>
          <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_3ysy72ke.json" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></lottie-player>
        </center>
        <div class="wide-xs mx-auto">
          <h3 class="nk-error-title"><?= __('There are no products registered!') ?> :(</h3>
          <p class="nk-error-text"><?= __('You can create and manage your products here.') ?> </p>
          <a href="javascript:void(0);" to="<?php echo site_url(); ?>/product/new" class="toggle btn btn-icon btn-primary d-md-none">
            <em class="icon ni ni-plus"></em>
          </a>
          <a class="toggle btn btn-primary d-none d-md-inline-flex" data-bs-target="#modalNewProduct" data-bs-toggle="modal">
            <em class="icon ni ni-plus"></em>
            <span><?= __('Add product') ?></span>
          </a>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light  d-md-none mt-2">
            <em class="icon ni ni-help"></em>
          </a>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light d-none d-md-inline-flex mt-2">
            <em class="icon ni ni-help"></em>
            <span><?= __('Learn more about this feature') ?></span>
          </a>
        </div>
      </div>
    </div>
  <?php else : ?>
    <div class="nk-content-body">
      <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><?= __('My products') ?>
            </h3>
          </div>
          <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light  d-md-none">
                <em class="icon ni ni-help"></em>
              </a>
              <!-- <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
                     <em class="icon ni ni-help"></em>
                      <span><?= __('Help') ?></span>
                    </a> -->
              <a href="javascript:void(0);" to="<?php echo site_url(); ?>/product/new" class="toggle btn btn-icon btn-primary d-md-none">
                <em class="icon ni ni-plus"></em>
              </a>
              <a class="toggle btn btn-primary d-none d-md-inline-flex" data-bs-target="#modalNewProduct" data-bs-toggle="modal">
                <em class="icon ni ni-plus"></em>
                <span><?= __('Add product') ?></span>
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="nk-block">
        <div class="row">
          <?php foreach ($products as $product) : ?>
            <div class="col-lg-3 mb-4">
              <div class="card card-bordered product-card">
                <div class="h-100">
                  <div class="product-thumb d-flex flex-row" style=" height: 330px; ">
                    <a href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                     
                      tabindex="0">
                      <img class="card-img-top img-mkt" src="<?php echo $product->image ?? 'images/demo-product.webp'; ?>" alt="" style="min-width: 100%;object-fit: cover;width: 100%;min-height: 100%;height: 100%;position: absolute;/* border-radius: var(--bs-card-border-radius); */">
                    </a>
                  </div>
                  <div style=" padding: 14px; ">
                    <div>
                      <div class="mt-2">
                        <h5 class="title mt-1 mb-1 " style="font-size: 1.125rem;height: 22px;width:100%;overflow: hidden;">
                          <div>
                            <a style="--tw-text-opacity: 1;color: #f4bd0e;font-size: 18px;" 
                              initial-height="44"
                              href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                              tabindex="0" >
                               <?= ucwords(strtolower($product->name)) ?></a>
                          </div>
                        </h5>
                      </div>
                      <div>
                        <span style="font-size: 12px">ID: <?php echo $product->id; ?></span>
                      </div>
                      <div class="product-price text-primary h5 mt-2" style="position: absolute;top: 12px;left: 25px;">
                         <!-- <?php if (($product->last_request()->status ?? '') == EProductRequestStatus::APPROVED->value): ?>
                            <a 
                          href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          class="btn btn-outline-success outbtn-small" style="border-color: #272727;"><?= __('Active') ?></a>                
                          <?php elseif (($product->last_request()->status ?? '') == EProductRequestStatus::PENDING->value): ?>
                          <a 
                          href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          class="btn btn-outline-warning outbtn-small" style="border-color: #272727;"><?= __('Em análise') ?></a>
                        <?php elseif (($product->last_request()->status ?? '') == EProductRequestStatus::REJECTED->value): ?>
                          <a 
                          href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          class="btn btn-outline-danger outbtn-small" style="border-color: #272727;"><?= __('Reprovado') ?></a>
                        <?php endif ?> -->
                        <a 
                          href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit" 
                          class="btn btn-outline-success outbtn-small" ><?= __('Active') ?></a>   
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        
          <?php endforeach; ?>
        </div>
        <Pagination />

      </div>
    </div>
  <?php endif; ?>
  <!-- modal tutorial -->
  <div class="modal" tabindex="-1" id="modalHelp" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <a href="#" class="close" data-bs-dismiss="modal">
          <em class="icon ni ni-cross"></em>
        </a>
        <div class="modal-body modal-body text-center">
          <div class="nk-modal">
            <div class="header-section-help" style="align-items: center;">
              <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;"><?= __('Products') ?></h4>
            </div>
            <div class="help-description" style="overflow-y: auto;">
              <span id="description">
                <p>
                  <?= __('Learn how to create a new product, customize your checkout, implement pixel, select payment methods') ?>.</p>
                <p>
                  <br>
                </p>
                <iframe width="460" height="315" src="#" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <p>
                  <br>
                </p>
              </span>
            </div>
            <div class="nk-modal-action">
              <a href="#" class="btn btn-lg btn-mw btn-primary" data-bs-dismiss="modal"> Aprenda mais sobre este recurso </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- delete modal -->
  <div class="modal fade" tabindex="-1" id="modalDelet" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
          <div class="nk-modal">
            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
            <h4 class="nk-modal-title">Você tem certeza? </h4>
            <div class="nk-modal-text">
              <p class="lead">Esta ação não poderá ser desfeita. </p>
            </div>
            <div class="nk-modal-action mt-5">
              <a href="javascript:;" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
              <a href="javascript:void(0);" click="destroyCoupon" data-id="<?php echo $coupon->id; ?>">Sim, Quero Excluir</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" tabindex="-1" id="modalNewProduct" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
          <div class="nk-modal">
            <h4 class="nk-modal-title">Novo produto </h4>
            <div class="nk-modal-text">

              <div class="form-group">
                <input class="form-control inp_product_name" placeholder="Nome do produto  " />
              </div>

              <div class="form-group">
                <div class="form-control-wrap">
                  <select class="form-select inp_payment_type" style="font-size: 0.8125rem; color: white !important;">
                    <option value="<?php echo EProductPaymentType::UNIQUE->value; ?>">Pagamento único</option>
                    <option value="<?php echo EProductPaymentType::RECURRING->value; ?>">Recorrente</option>
                  </select>
                </div>
              </div>

              <div class="form-control-wrap">
                <select class="form-select inp_product_type d-none">
                  <option selected value="<?php echo EProductType::DIGITAL->value; ?>">Digital</option>
                  <option value="<?php echo EProductType::PHYSICAL->value; ?>">Físico</option>
                </select>
              </div>

            </div>
            <div class="nk-modal-action mt-5">
              <a href="javascript:;" class="btn btn-lg btn-mw btn-secondary" data-bs-dismiss="modal">Cancelar</a>
              <a href="javascript:void(0);" class="btn btn-lg btn-mw btn-primary" click="newProduct">Criar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



</content>