<title>Pix Gerado com Sucesso</title>
<style>
    @media (min-width: 992px) {
        .nk-content-fluid {
            padding-left: 24px;
            background: #fff !important;
            padding-right: 24px;
        }}
        .dark-mode .card, .dark-mode .kanban-add-task, .dark-mode .kanban-board-header, .dark-mode .kanban-item {
            background: #fff !important;
            border-color: #fff !important;
            box-shadow: 0px 1px 3px 0px rgb(0 0 0 / 13%);
        }
        body.dark-mode {
            background: #101924 !important;
            color: var(--bs-body-color) !important;
        }
        .dark-mode h1, .dark-mode h2, .dark-mode h3, .dark-mode h4, .dark-mode h5, .dark-mode h6, .dark-mode .h1, .dark-mode .h2, .dark-mode .h3, .dark-mode .h4, .dark-mode .h5, .dark-mode .h6, .dark-mode .lead-text, .dark-mode .dropdown-title, .dark-mode pre {
          color: var(--bs-body-color) !important;
        }
        .alert-info {
            color: #06889b !important;
            background-color: #e1f8fb !important;
            border-color: #b5edf5 !important;
        }
    }
  </style>
<content>
  <link rel="stylesheet" href="<?php echo get_subdomain_serialized('checkout'); ?>/static/css/pix.css" />
  <div load="App.Subdomains.Checkout.Pix.prototype.watchPix('<?php echo $order->transaction_id; ?>', '<?php echo $order->uuid; ?>');">
  
  <input type="hidden" id="pixThanksEnabled" value="<?php echo $checkout?->pix_thanks_page_enabled ?: $product->pix_thanks_page_enabled; ?>">
  <input type="hidden" id="pixThanksUrl" value="<?php echo $checkout?->pix_thanks_page_enabled ? ($checkout?->pix_thanks_page_url ?: $product->pix_thanks_page_url) : $product->pix_thanks_page_url; ?>">

  <script type="application/json" json-id="customer">
  {
    "email": "<?php echo $customer->email ?? ''; ?>",
    "token": "<?php echo $customer->upsell_token ?? ''; ?>",
    "order_id": "<?php echo $order->id ?? ''; ?>",
    "upsell_id": "<?php echo $upsell->id ?? ''; ?>",
    "price_var": "<?php echo $product_link->id ?? ''; ?>",
    "total": "<?php echo $order->total ?? '0'; ?>"
  }
  </script>

  <div class="nk-content nk-content-fluid">
    <div class="mx-auto max-w-3xl">
      <div class="nk-block">
        <div class="row g-gs">
          <div class="col-lg-8">
              <div class="card card-bordered">
                <div class="card-inner">
                    <div class="">
                      <div class="d-flex mb-2 mt-4 background-header topo-pix">
                        <div class="d-flex">
                            <div class="icon-pix">
                            <em class="icon ni ni-clock"></em>
                            </div>
                            <div class="topo-obg">
                              <p>Parabéns <b><?php echo $customer->name; ?></b>, Seu pedido foi realizado!</p>
                            </div>
                        </div>
                        <div class="styles_countdown__9UhUS">
                          <strong>Pague seu Pix dentro de: </strong>
                          /**
                          * CONTADOR REGRESSIVO
                          */
                          <center>
                          <ul class="pixCountdown d-flex" style="gap: 12px;">
                            <li>
                              <h1 class="count-number" id="pcMin">15</h1>
                            </li>
                            <li style="font-size: 2rem;">:</li>
                            <li>
                              <h1 class="count-number"id="pcSec">00</h1>
                            </li>
                          </ul>
                          </center>

                          <span>para garantir sua compra.</span>
                          <div class="d-none" load="countdown('.pixCountdown', [document.querySelector('#pcMin'), document.querySelector('#pcSec')]);"></div>
                        </div>
                      </div>

                      <div class="d-flex topo-pix">
                        <div class="">
                            <img src="data:image/png;base64,<?php echo !empty($order) ? $order?->meta('payment_pix_image') ?? '' : ''; ?>" style="width: 300px;">
                        </div>
                        <div class="">
                              <div class="mt-2">
                                <div class="alert-icon" style=" font-size: 12px; ">
                                  <em class="icon ni ni-alert-circle"></em>
                                  Abra o aplicativo do seu banco e acesse a área Pix
                                </div>
                                <div class="alert alert-icon" style=" font-size: 12px; ">
                                  <em class="icon ni ni-alert-circle"></em>
                                  Selecione a opção pagar com código Pix Copia e Cola e cole o código no espaço indicado no aplicativo
                                </div>
                                <div class="alert alert-icon" style=" font-size: 12px; ">
                                  <em class="icon ni ni-alert-circle"></em>
                                  Após o pagamento, você receberá por email as informações de acesso à sua compra
                                </div>
                              </div>
                            <div class="styles_buttons__pXAf8 mt-2">
                              <button class="mt-1 w-100 btn-success mt-4 flex" style="align-items: center; gap: 12px;" click="pixCopyCode" data-code="<?php echo !empty($order) ? $order?->meta('payment_pix_code') ?? '' : ''; ?>">
                                  <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                       viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                      <path fill="none" d="M0 0h24v24H0V0z"></path>
                                      <path
                                              d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z">
                                      </path>
                                  </svg>
                                  Clique aqui para copiar o código pix
                              </button>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</content>