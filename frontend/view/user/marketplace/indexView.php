<content load="NioApp.Slick('.marketplace-slider')">
  <div class="nk-content-body">
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <!-- <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Top 10 para afiliados</h3>
        <span>Conheça os Top 10 produtos otimizados para afiliados.</span>
        </div> -->
      <div class="nk-block-head-content">
        <div class="toggle-wrap nk-block-tools-toggle">
          <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
          <em class="icon ni ni-more-v"></em>
          </a>
          <div class="toggle-expand-content" data-content="pageMenu">
            <ul class="nk-block-tools g-3">
              <li>
                <div class="form-control-wrap">
                  <div class="form-icon form-icon-right">
                    <em class="icon ni ni-search"></em>
                  </div>
                  <input type="text" class="form-control" id="default-04" placeholder="Quick search by id">
                </div>
              </li>
              <li>
                <div class="drodown">
                  <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white" data-bs-toggle="dropdown" aria-expanded="false">Status</a>
                  <div class="dropdown-menu dropdown-menu-end" style="">
                    <ul class="link-list-opt no-bdr">
                      <li>
                        <a href="#">
                        <span>New Items</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                        <span>Featured</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                        <span>Out of Stock</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="nk-block">
    <div class="nk-block-head-content">
      <h3 class="nk-block-title page-title">Produtos mais vendidos</h3>
      <span>Esta seleção é baseada nos produtos que bateram recorde de vendas.</span>
    </div>
    <div class="row g-gs mt-1">
      <div
        class="marketplace-slider" data-slick='{"arrows": true, "dots": false, "slidesToShow": 4, "slidesToScroll": 1, "infinite":false, "responsive":[ {"breakpoint": 992,"settings":{"slidesToShow": 2}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}} ]}'>
        <?php foreach ($bestsellers as $bestseller): $product = $bestseller?->product; if (empty($product)) continue; ?>
        <div class="p-2">
          <div class="card card-bordered product-card">
            <div class="product-thumb">
              <a href="<?php echo site_url(); ?>/marketplace/<?php echo $product->id; ?>/view" to="<?php echo site_url(); ?>/marketplace/<?php echo $product->id; ?>/view">
              <img class="card-img-top img-mkt" src="<?php echo $product->image ?? 'https://rocketpays.app/images/images.png'; ?>" alt="">
              </a>
              <ul class="product-badges">
                <li>
                  <span class="badge bg-success"><?php echo $product->category->name ?? 'Sem categoria'; ?></span>
                </li>
              </ul>
            </div>
            <div class="card-inner">
              <div class="progress progress-lg mb-4">
                <div class="progress-bar progress-bra-gradient" data-progress="60" style="width: 60%;"></div>
                <em class="icon ni ni-hot-fill fire-market"></em>
              </div>
              <h5 class="product-title">
                <a href="#"><?php echo $product->name; ?></a>
              </h5>
              <div class="product-price text-primary h5">
                <small class="text-muted fs-13px  mb-2 d-block">Comissão até</small>
                <h5 class="product-price text-primary h5">
                  <span>R$ <?php echo number_format(doubleval($product->price), 2, ',', '.'); ?></span>
                </h5>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="nk-block mt-5">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Novos produtos no mercado</h3>
        <span>Lista dos produtos que acabaram de ser criados.</span>
      </div>
      <div class="row g-gs mt-1">
        <div
          class="marketplace-slider" data-slick='{"arrows": true, "dots": false, "slidesToShow": 4, "slidesToScroll": 1, "infinite":false, "responsive":[ {"breakpoint": 992,"settings":{"slidesToShow": 2}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}} ]}'>
          <?php foreach ($last_products as $product): ?>
          <div class="p-2">
            <div class="card card-bordered product-card">
              <div class="product-thumb">
                <a href="<?php echo site_url(); ?>/marketplace/<?php echo $product->id; ?>/view" to="<?php echo site_url(); ?>/marketplace/<?php echo $product->id; ?>/view">
                <img class="card-img-top img-mkt" src="<?php echo $product->image ?? 'https://rocketpays.app/images/images.png'; ?>" alt="">
                </a>
                <ul class="product-badges">
                  <li>
                    <span class="badge bg-success"><?php echo $product->category->name ?? 'Sem categoria'; ?></span>
                  </li>
                </ul>
              </div>
              <div class="card-inner">
                <div class="progress progress-lg mb-4">
                  <div class="progress-bar progress-bra-gradient" data-progress="60" style="width: 60%;"></div>
                  <em class="icon ni ni-hot-fill fire-market"></em>
                </div>
                <h5 class="product-title">
                  <a href="#"><?php echo $product->name; ?></a>
                </h5>
                <div class="product-price text-primary h5">
                  <small class="text-muted fs-13px  mb-2 d-block">Comissão até</small>
                  <h5 class="product-price text-primary h5">
                    <span>R$ <?php echo number_format(doubleval($product->price), 2, ',', '.'); ?></span>
                  </h5>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</content>