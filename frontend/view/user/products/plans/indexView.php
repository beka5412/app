<title>Plans</title>

<content>
  <div class="nk-content-body">
    <ProductMenu />

    <div class="tab-content">
      <div class="tab-pane active" id="tabPlan">
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
            </div>
            <div class="nk-block-head-content">
              <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                  <em class="icon ni ni-more-v"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                  <ul class="nk-block-tools g-3">
                    <li class="nk-block-tools-opt">
                      <a href="#" data-target="addProduct" class="toggle btn btn-icon btn-primary d-md-none">
                        <em class="icon ni ni-plus"></em>
                      </a>
                      <a href="javascript:void(0);" to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/plan/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                        <em class="icon ni ni-plus"></em>
                        <span>Novo plano</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="nk-block">
          <div class="nk-tb-list is-separate mb-3">
            <div class="nk-tb-item nk-tb-head">
              <div class="nk-tb-col nk-tb-col-check">
                <div class="custom-control custom-control-sm custom-checkbox notext">
                  <input type="checkbox" class="custom-control-input" id="pid">
                  <label class="custom-control-label" for="pid"></label>
                </div>
              </div>
              <div class="nk-tb-col tb-col-sm">
                <span>Nome</span>
              </div>
              <div class="nk-tb-col">
                <span>Preço</span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <span>Status</span>
              </div>
              <div class="nk-tb-col nk-tb-col-tools">
              </div>
            </div>
            <?php foreach ($plans as $plan): ?>
            <div class="nk-tb-item tr">
              <div class="nk-tb-col nk-tb-col-check">
                <div class="custom-control custom-control-sm custom-checkbox notext">
                  <input type="checkbox" class="custom-control-input" id="pid1">
                  <label class="custom-control-label" for="pid1"></label>
                </div>
              </div>
              <div class="nk-tb-col tb-col-sm">
                <a class="tb-product"
                    href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/plan/<?php echo $plan->id; ?>/edit"
                    to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/plan/<?php echo $plan->id; ?>/edit">
                  <span class="title"><?php echo $plan->name; ?></span>
                </a>
              </div>
              <div class="nk-tb-col">
                <span class="tb-lead">R$ <?php echo currency($plan->price); ?></span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <a href="<?= get_subdomain_serialized('checkout') ?>/<?php echo $plan->slug; ?>/<?php echo $product->defaultCheckout()?->sku ?: $product->sku; ?>" target="_blank">
                    <em class="icon ni ni-eye"></em>
                    <span class="tb-sub">Links do checkout</span>
                  </a>
              </div>
              <!-- <div class="nk-tb-col tb-col-md">
                <span class="badge badge-dot badge-dot-xs bg-success">Ativo</span>
              </div> -->
              <div class="nk-tb-col nk-tb-col-tools">
                <ul class="nk-tb-actions gx-1 my-n1">
                  <li class="me-n1">
                    <div class="dropdown">
                      <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                        <em class="icon ni ni-more-h"></em>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                          <li>
                            <a 
                              href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/plan/<?php echo $plan->id; ?>/edit"
                              to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/plan/<?php echo $plan->id; ?>/edit">
                              <em class="icon ni ni-edit"></em>
                              <span>Editar</span>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:;" click="planDestroy" data-id="<?php echo $plan->id; ?>">
                              <em class="icon ni ni-trash"></em>
                              <span>Deletar</span>
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
      </div>
    </div>
  </div>
</content>
