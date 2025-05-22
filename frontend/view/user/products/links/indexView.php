<?php $sku = $default_checkout?->sku; ?>
<content>
  <ProductMenu />
  <div>
    <!-- card pagina de vendas -->
    <div class="card card-bordered card-inner card-preview mb-2">
      <div class="row gy-4">
        <div class="col-sm-6">
          <div class="form-group">
            <label class="form-label" for="default-01">Página de Vendas</label>
            <div class="form-control-wrap">
              <input type="text" class="form-control" id="default-01" placeholder="Input placeholder"
                value="<?php echo $product?->landing_page; ?>" onkeypress="return false" onkeyup="return false"
                onkeydown="return false">
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <label class="form-label" for="default-01">&nbsp;</label>
          <div class="form-control-wrap d-flex" style="gap: 10px">
            <a href="javascript:;" data-link="<?php echo $product?->landing_page; ?>" click="copyLink"
              class="btn btn-primary"><em class="icon ni ni-copy"></em></a>
            <a href="javascript:;" class="btn btn-outline-primary"><em class="icon ni ni-share"></em></a>
            <a href="javascript:;" class="btn btn-outline-primary"><em class="icon ni ni-external"></em></a>
          </div>
        </div>
      </div>
    </div>

    <?php if (!count($plans)): ?>
      <!-- card links -->
      <div class="card card-bordered card-inner card-preview mb-2">
        <div class="row gy-4">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="form-label" for="default-01">Checkout Padrão</label>
              <div class="form-control-wrap">
                <input type="text" class="form-control" id="default-01" placeholder="Input placeholder"
                  value="<?php echo get_subdomain_serialized('checkout') . "/{$sku}"; ?>" onkeypress="return false"
                  onkeyup="return false" onkeydown="return false">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="default-01">&nbsp;</label>
            <div class="form-control-wrap d-flex" style="gap: 10px">
              <a data-link="<?php echo get_subdomain_serialized('checkout') . "/{$sku}"; ?>" click="copyLink"
                class="btn btn-primary"><em class="icon ni ni-copy"></em></a>
              <a href="#" class="btn btn-outline-primary"><em class="icon ni ni-share"></em></a>
              <a href="<?php echo get_subdomain_serialized('checkout') . "/{$sku}"; ?>" target="_blank"
                class="btn btn-outline-primary"><em class="icon ni ni-external"></em></a>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($product->checkouts as $checkout): ?>
        <label class="form-label">Planos</label>
        <?php foreach ($plans as $plan):
          $link = get_subdomain_serialized('checkout') . "/{$plan?->slug}/{$checkout->sku}"; ?>
          <div class="card card-bordered card-inner card-preview mb-2">
            <div class="row gy-4">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="form-label" for="default-01"><?php echo $plan?->name; ?></label>
                  <div class="form-control-wrap">
                    <input type="text" class="form-control" id="default-01" placeholder="" value="<?php echo $link; ?>"
                      onkeypress="return false" onkeyup="return false" onkeydown="return false">
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="default-01">&nbsp;</label>
                <div class="form-control-wrap d-flex" style="gap: 10px">
                  <a href="javascript:;" class="btn btn-primary" data-link="<?php echo $link; ?>" click="copyLink"><em
                      class="icon ni ni-copy"></em></a>
                  <a href="#" class="btn btn-outline-primary"><em class="icon ni ni-share"></em></a>
                  <a href="<?php echo $link; ?>" target="_blank" class="btn btn-outline-primary"><em
                      class="icon ni ni-external"></em></a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- card links -->
    <?php foreach ($product->checkouts as $checkout): ?>
      <?php if (count($product->product_links)): ?><label class="form-label">Variação de preço de
          <?php echo $checkout->name; ?></label><?php endif; ?>
      <?php foreach ($product->product_links as $item):
        $link = get_subdomain_serialized('checkout') . "/{$checkout->sku}/{$item?->slug}"; ?>
        <div class="card card-bordered card-inner card-preview mb-2">
          <div class="row gy-4">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="form-label" for="default-01"><?php echo $item?->slug; ?></label>
                <div class="form-control-wrap">
                  <input type="text" class="form-control" id="default-01" placeholder="" value="<?php echo $link; ?>"
                    onkeypress="return false" onkeyup="return false" onkeydown="return false">
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label" for="default-01">&nbsp;</label>
              <div class="form-control-wrap d-flex" style="gap: 10px">
                <a href="javascript:;" class="btn btn-primary" data-link="<?php echo $link; ?>" click="copyLink"><em
                    class="icon ni ni-copy"></em></a>
                <a href="#" class="btn btn-outline-primary"><em class="icon ni ni-share"></em></a>
                <a href="<?php echo $link; ?>" target="_blank" class="btn btn-outline-primary"><em
                    class="icon ni ni-external"></em></a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
</content>