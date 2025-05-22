<title>Pagamento Realizado - Pix</title>
<content>
  <link rel="stylesheet" href="<?php echo get_subdomain_serialized('checkout'); ?>/static/css/pix-paid.css" />

  <div class="nk-block nk-block-middle wide-md mx-auto">
    <div class="mx-auto max-w-3xl">
         <h3 class="nk-error-title">Pagamento realizado com sucesso</h3>
          <p class="nk-error-text">Parabéns, /* <b>Gledson Poggioni</b>! */ acabamos de Receber e aprovar seu pagamento.</p>
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
          <a class="mt-4" href="<?php echo get_subdomain_serialized('purchase'); ?>" style="background-color:#6576ff;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">Acessar Minhas Compras</a>
    </div>
  </div>
</content>