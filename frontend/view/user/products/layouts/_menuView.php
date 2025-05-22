<?php use Backend\Enums\Product\EProductPaymentType; ?>
<content>
  <?php if (!empty($product)): ?>
  <div class="nk-block-head">
    <div class="nk-block-between g-3">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">
          <?php echo $product->sku; ?>
        </h3>
      </div>
      <div class="nk-block-head-content">
        <a href="<?php echo site_url(); ?>/products" href="<?php echo site_url(); ?>/products"
          class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
          <em class="icon ni ni-arrow-left"></em>
          <span><?= __('Back') ?></span>
        </a>
        <a href="<?php echo site_url(); ?>/products" href="<?php echo site_url(); ?>/products"
          class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
          <em class="icon ni ni-arrow-left"></em>
        </a>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs menu_product_edit mb-2">
    <li class="nav-item">
      <a menu-button="editProduct" class="nav-link <?php if (route_is("/product/$product->id/edit")): ?> active <?php endif; ?>"
        href="<?php echo site_url() . "/product/" . $product->id . "/edit"; ?>" >
        <em class="icon ni ni-box"></em>
        <span class="tb-col-sm"><?= __('General') ?></span>
      </a>
    </li>
    <li class="nav-item" style="display:<?php if (EProductPaymentType::RECURRING->value === $product?->payment_type): ?>block<?php else: ?>none<?php endif; ?>">
      <a menu-button="plans" class="nav-link <?php if (route_is("/product/$product->id/plans")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . "/product/" . $product->id . "/plans"; ?>" 
        >
        <em class="icon ni ni-cards"></em>
        <span class="tb-col-sm"><?= __('Plans') ?></span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="pixels" class="nav-link <?php if (route_is("/product/$product->id/pixels")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . "/product/" . $product->id . "/pixels"; ?>" 
       >
        <em class="icon ni ni-code"></em>
        <span class="tb-col-sm">Pixel</span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="checkouts" class="nav-link <?php if (route_is("/product/$product->id/checkouts")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . "/product/" . $product->id . "/checkouts"; ?>" 
        t>
        <em class="icon ni ni-cart"></em>
        <span class="tb-col-sm">Checkout</span>
      </a>
    </li>
   
    <li class="nav-item">
      <a menu-button="links" class="nav-link <?php if (route_is("/product/$product->id/links")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . "/product/" . $product->id . "/links"; ?>" 
       >
        <em class="icon ni ni-link-alt"></em>
        <span class="tb-col-sm">Links</span>
      </a>
    </li>
    <li class="nav-item">
      <a menu-button="upsell" class="nav-link <?php if (route_is("/product/$product->id/upsell")): ?> active <?php endif; ?>" 
        href="<?php echo site_url() . "/product/" . $product->id . "/upsell"; ?>" 
       >
        <em class="icon ni ni-arrow-up"></em>
        
        <span class="tb-col-sm">Upsell</span>
      </a>
    </li>
  
  </ul>
  <?php endif; ?>
</content>