<title>Boleto Gerado com Sucesso</title>
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
  </style>
<content>
  <link rel="stylesheet" href="<?php echo get_subdomain_serialized('checkout'); ?>/static/css/pix.css" />
  <div>

  <div class="nk-header nk-header-fluid is-regular is-theme">
    <div class="container-xl wide-xl">
      <div class="nk-header-wrap">
        <div class="nk-header-brand">
          <a href="javascript:;" class="logo-link">
            <img class="logo-light logo-img"
              src="<?php echo $checkout?->logo ?: 'https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png'; ?>"
              srcset="<?php echo $checkout?->logo ?: 'https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png'; ?> 2x" alt="logo">
            <img class="logo-dark logo-img"
              src="<?php echo $checkout?->logo ?: 'https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png'; ?>"
              srcset="<?php echo $checkout?->logo ?: 'https://painel.rocketleads.com.br/dashlite/compact/images/logo_branca.png'; ?> 2x" alt="logo-dark">
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

  <div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
      <div class="nk-block">
        <div class="row g-gs">
          <div class="col-lg-8">
              <div class="card card-bordered">
                <div class="card-inner">
                    <div class="">
                      <div class="d-flex mb-2 background-header topo-pix">
                        <div class="d-flex">
                            <div class="icon-pix">
                            <em class="icon ni ni-clock"></em>
                            </div>
                            <div class="topo-obg">
                              <p>Parabéns <b><?php echo $customer->name; ?></b>, Seu pedido foi realizado!</p>
                            </div>
                        </div>
                        <div class="styles_countdown__9UhUS">
                          <strong>Pague seu Boleto dentro de: </strong>
                          /**
                          * CONTADOR REGRESSIVO
                          */
                          
                          <ul class="pixCountdown">
                            <li>
                              <h1>3</h1>
                            </li>
                            <li>
                              <h1>Dias</h1>
                            </li>
                          </ul>

                          <span>para garantir sua compra.</span>


                          <div class="d-none" load="countdown('.pixCountdown', [document.querySelector('#pcMin'), document.querySelector('#pcSec')]);"></div>


                        </div>
                      </div>
                                <div class="d-flex topo-pix">
                                  <div class="styles_buttons__pXAf8 mb-5" style="width: 100%;">
                                      <button class="mt-1 w-100 btn-success mt-4" click="billetCopyCode" data-code="<?php echo !empty($order) ? $order?->meta('payment_billet_code') ?? '' : ''; ?>">
                                        Copiar o código
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                          viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                          <path fill="none" d="M0 0h24v24H0V0z"></path>
                                          <path
                                            d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z">
                                          </path>
                                        </svg>
                                      </button>
                                      <a class="mt-1 w-100 btn-success mt-4 d-block text-center" href="<?php echo !empty($order) ? $order?->meta('payment_billet_link') ?? '' : ''; ?>" target="_blank">
                                        Imprimir boleto
                                      </a>
                                  </div>
                                </div>
                    </div>
                </div>
              </div>
          </div>
          <div class="col-lg-4">
            <div class="wide-sm">
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
                      <p><b>Código do pedido: </b> <span><?php echo strtoupper($order->uuid); ?></span></p>

                      <p>Precisa de ajuda?</p>
                      <p><b>Nome: </b> <?php echo $product->author ?? ''; ?></p>
                      <p><b>Email: </b> <?php echo $product->support_email ?? ''; ?> </p>
                      <span>Se precisa de suporte relacionado à sua compra, entre em contato com a pessoa responsável pela venda.</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="nk-footer nk-footer-fluid bg-lighter">
    <div class="container-xl">
      <div class="nk-footer-wrap">
        <div class="nk-footer-copyright">PAGAMENTO PROCESSADO POR: <img class="logo-img"
            src="https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png">
        </div>
        <div class="nk-footer-links">
          <ul class="nav nav-sm">
            <li class="nav-item dropup">
              <a href="#" class="dropdown-toggle dropdown-indicator has-indicator nav-link text-base"
                data-bs-toggle="dropdown" data-offset="0,10">
                <span>English</span>
              </a>
              <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                <ul class="language-list">
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">English</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Español</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Français</span>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="language-item">
                      <span class="language-name">Türkçe</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a data-bs-toggle="modal" href="#region" class="nav-link">
                <em class="icon ni ni-globe"></em>
                <span class="ms-1">Select Region</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>


</content>