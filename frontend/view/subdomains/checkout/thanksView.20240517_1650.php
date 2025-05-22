<title>Pagamento Realizado - Pix</title>
<content ready="ready">
  <script type="application/json" json-id="checkout_meta">
  {
    "pixels": <?php echo isset($pixels) ? json_encode($pixels ?? '[]') : '[]'; ?>,
    "total": <?php echo $order?->total ?: 0; ?>
  }
  </script>
  <link rel="stylesheet" href="<?php echo get_subdomain_serialized('checkout'); ?>/static/css/thanks.css" />

  <div>
    <div class="nk-header nk-header-fluid is-regular is-theme">
    <div class="container-xl wide-xl">
      <div class="nk-header-wrap">
        <div class="nk-header-brand">
          <a href="https://painel.rocketleads.com.br" class="logo-link">
            <img class="logo-light logo-img"
              src="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png"
              srcset="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png 2x" alt="logo">
            <img class="logo-dark logo-img"
              src="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png"
              srcset="https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png 2x" alt="logo-dark">
          </a>
        </div>
        <div class="nk-header-tools">
          <div class="nk-sidebar-brand">
            <div class="d-flex">
              <img class="logo-light logo-img" src=https://painel.rocketleads.com.br/images/1.png>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="nk-block nk-block-middle wide-md mx-auto">
  <div class="nk-block-content nk-error-ld text-center">
    <img class="nk-error-gfx" src="/demo1/images/gfx/error-404.svg" alt="">
    <div class="wide-xs mx-auto">
          
    <h3 class="nk-error-title">Pagamento realizado com sucesso</h3>
      <p class="nk-error-text">Parabéns, <b><?php echo $customer->name; ?></b>! acabamos de receber e aprovar seu pagamento.</p>
              <div class="card card-bordered">
                  <div class="card-inner">
                      <div class="custom-control custom-control-sm custom-checkbox custom-control-pro mb-4">
                        <span class="user-card">
                          <span class="user-avatar sq">
                            <img src="<?php echo $product->image ?? ''; ?>" alt="">
                          </span>
                          <span class="user-info">
                            <span class="lead-text"><?php echo $product->name ?? ''; ?></span>
                            <span class="lead-text product-price text-primary"> <?php echo number_format((Double) $order->total, 2, ',', '.'); ?></span>
                          </span>
                        </span>
                      </div>
                      <p><b>Número do pedido: </b> <span><?php echo strtoupper($order->id); ?></span></p>

                      <p>Fale com o Produtor</p>
                      <p><b>Nome: </b> <?php echo $product->author ?? ''; ?></p>
                      <p><b>Email: </b> <?php echo $product->support_email ?? ''; ?> </p>
                      <span>Se precisa de suporte relacionado à sua compra, entre em contato com a pessoa responsável pela venda.</span>
                </div>
              </div>
      <a href="https://purchase.rocketpays.app/" class="btn btn-lg btn-primary mt-2">Acessar Minhas Compras</a>
      
          <!-- <div class="order-bump-container mt-4">
              <div class="order-bump-card">
                  <div class="card-header">
                    <label class="blink_me">Oportunidade Única!</label>
                  </div>
                <div class="card-body">
                  <div class="order-info-block">
                    <div class="img-block">
                      <img src="https://checkout.bluedrops.com.br/uploads/1/22/12/1669997520.jpg" alt="Product">
                    </div>
                    <div class="info-block">
                      <label class="d-block title bold">Compre + 2 Curcumix</label>
                      <div class="text">Leve mais dois Curcumix pela metade do valor</div>
                      <div>
                        <span class="price-promo">R$ 99,00</span>
                        <span style="color: #999; text-decoration: line-through; font-size: 12px; margin-bottom: 7px;">R$ 198,00</span>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
          </div>
          <button click="checkoutOnSubmit" type="button" class="mt-1 w-100 btn-success mt-4">Quero aproveitar a Oferta
                        <em class="icon ni ni-arrow-right"></em></button> -->
    </div>
  </div>
</div>
  </div>
  
</content>