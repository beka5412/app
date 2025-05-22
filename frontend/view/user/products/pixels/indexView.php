<?php use Backend\Enums\Pixel\EPixelPlatform; ?>

<title>Pixels</title>

<content>
  <div class="nk-content-body">
    <ProductMenu />

    <div class="tab-content">
      <!-- Pixel -->
      <div class="tab-pane active" id="tabPixel">
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
                      <a href="javascript:void(0);" to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/pixel/new" class="btn btn-primary d-none d-md-inline-flex">
                        <em class="icon ni ni-plus"></em>
                        <span>Novo pixel</span>
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
                <span>Pixel ID</span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <span>Plataforma</span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <span>Status</span>
              </div>
              <div class="nk-tb-col nk-tb-col-tools">
              </div>
            </div>
            <?php foreach ($pixels as $pixel): ?>
            <div class="nk-tb-item tr">
              <div class="nk-tb-col nk-tb-col-check">
                <div class="custom-control custom-control-sm custom-checkbox notext">
                  <input type="checkbox" class="custom-control-input" id="pid1">
                  <label class="custom-control-label" for="pid1"></label>
                </div>
              </div>
              <div class="nk-tb-col tb-col-sm">
                <a class="tb-product"
                    href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/pixel/<?php echo $pixel->id; ?>/edit">
                  <span class="title"><?php echo $pixel->name; ?></span>
                </a>
              </div>
              <div class="nk-tb-col">
                <span class="tb-lead"><?php echo $pixel->content; ?></span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <span class="tb-lead">
                  <?php switch ($pixel->platform) 
                  { 
                    case EPixelPlatform::FACEBOOK->value: 
                      ?><img src="<?php echo site_url(); ?>/images/social/facebook.svg" alt="<?php echo EPixelPlatform::FACEBOOK->value; ?>" width="80"><?php
                      break;
                      
                    case EPixelPlatform::INSTAGRAM->value: 
                      ?><img src="<?php echo site_url(); ?>/images/social/instagram.png" alt="<?php echo EPixelPlatform::INSTAGRAM->value; ?>" width="80"><?php
                      break;
                      
                    case EPixelPlatform::TIKTOK->value: 
                      ?><img src="<?php echo site_url(); ?>/images/social/tiktok.svg" alt="<?php echo EPixelPlatform::TIKTOK->value; ?>" width="80"><?php
                      break;
                  }
                  ?>
                </span>
              </div>
              <div class="nk-tb-col tb-col-md">
                <span class="badge badge-dot badge-dot-xs bg-success">Ativo</span>
              </div>
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
                              href="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/pixel/<?php echo $pixel->id; ?>/edit">
                              <em class="icon ni ni-edit"></em>
                              <span>Editar Pixel</span>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:;" click="pixelDestroy" data-id="<?php echo $pixel->id; ?>">
                              <em class="icon ni ni-trash"></em>
                              <span>Deletar Pixel</span>
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
      <!-- Fim Pixel -->
    </div>
  </div>
</content>
