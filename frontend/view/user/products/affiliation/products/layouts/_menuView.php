<content>
  <div class="nk-block-head">
    <div class="nk-block-between g-3">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">
          <?php echo $product->sku; ?>
        </h3>
      </div>
      <div class="nk-block-head-content">
        <a to="<?php echo site_url(); ?>/aff/products" href="<?php echo site_url(); ?>/aff/products"
          class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
          <em class="icon ni ni-arrow-left"></em>
          <span>Voltar</span>
        </a>
        <a to="<?php echo site_url(); ?>/aff/products" href="<?php echo site_url(); ?>/aff/products"
          class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
          <em class="icon ni ni-arrow-left"></em>
        </a>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs menu_aff_product_show mb-2">
    <li class="nav-item">
      <a menu-button="info" class="nav-link <?php if (route_is("/aff/product/$product->id")): ?> active <?php endif; ?>"
        href="<?php echo site_url() . "/aff/product/" . $product->id; ?>" 
        to="<?php echo site_url() . "/aff/product/" . $product->id; ?>">
        <em class="icon ni ni-box"></em>
        <span class="tb-col-sm">Produto</span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="materials" class="nav-link <?php if (route_is("/aff/product/$product->id/materials")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . " /aff/product/" . $product->id . "/materials"; ?>" 
        to="<?php echo site_url() . "/aff/product/" . $product->id . "/materials"; ?>">
        <em class="icon ni ni-cart"></em>
        <span class="tb-col-sm">Materiais</span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="links" class="nav-link <?php if (route_is("/aff/product/$product->id/links")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . " /aff/product/" . $product->id . "/links"; ?>" 
        to="<?php echo site_url() . "/aff/product/" . $product->id . "/links"; ?>">
        <em class="icon ni ni-cart"></em>
        <span class="tb-col-sm">Links</span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="support" class="nav-link <?php if (route_is("/aff/product/$product->id/support")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . " /aff/product/" . $product->id . "/support"; ?>" 
        to="<?php echo site_url() . "/aff/product/" . $product->id . "/support"; ?>">
        <em class="icon ni ni-cart"></em>
        <span class="tb-col-sm">Suporte</span>
      </a>
    </li>
  </ul>
</content>