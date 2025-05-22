<title>Carrinhos Abandonados</title>
<content>
<?php if ((json_decode(json_encode($abandoned_carts))->total ?? 0) == 0) : ?>
  <div class="nk-block nk-block-middle wide-md mx-auto">
    <div class="nk-block-content nk-error-ld text-center">
      <center>
      <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player src="https://assets3.lottiefiles.com/private_files/lf30_x2lzmtdl.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>                        <div class="wide-xs mx-auto">
        <h3 class="nk-error-title">Não há Carrinho Abandonado! :(</h3>
        <p class="nk-error-text">Quando surgir, você poderá gerenciá-los por aqui. </p>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
          <em class="icon ni ni-help"></em>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
          <em class="icon ni ni-help"></em>
          <span>Aprenda mais sobre esse recurso</span>
        </a>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="nk-content-body">
      <!-- cabecalho sem botao -->
      <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Carrinhos Abandonados
              </h3>
            </div>
            <div class="nk-block-head-content">
              <div class="toggle-wrap nk-block-tools-toggle">
                      <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
                      <em class="icon ni ni-help"></em>
                    </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
                      <em class="icon ni ni-help"></em>
                      <span>Ajuda</span>
                    </a>
              </div>
            </div>
          </div>
      </div>

    <div class="">
      <div class="row row-cols-1 mt-3 mb-4 row-cols-md-2 row-cols-xl-3">
        <div class="col">
          <div class="card radius-10 border-start border-0 border-3 border-info">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div>
                  <p class="mb-0 text-secondary">Pix</p>
                  <h4 class="my-1 text-info">
                    <div class="col">
                      <span class="total-orders">75</span>
                    </div>
                  </h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                  <i class="fa fa-shopping-cart"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card radius-10 border-start border-0 border-3 border-success">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div>
                  <p class="mb-0 text-secondary">Boleto</p>
                  <h4 class="my-1 text-success">
                    <div class="col">
                      <span class="completed-orders">8</span>
                    </div>
                  </h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                  <i class="fa fa-bar-chart"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div>
                  <p class="mb-0 text-secondary">Cartão de Crétido</p>
                  <h4 class="my-1 text-warning">
                    <div class="col">
                      <span class="pending-orders">67</span>
                    </div>
                  </h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                  <i class="fa fa-users"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="nk-block">
  <div class="card card-bordered">
    <div class="card-inner-group">
      <div class="card-inner position-relative card-tools-toggle">
        <div class="card-title-group" data-select2-id="16">
          <div class="card-tools" data-select2-id="15">
           
          </div>
          <div class="card-tools me-n1">
            <ul class="btn-toolbar gx-1">
              <li>
                <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search">
                  <em class="icon ni ni-search"></em>
                </a>
              </li>
              <li class="btn-toolbar-sep"></li>
              <li>
                <div class="toggle-wrap">
                  <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                    <em class="icon ni ni-menu-right"></em>
                  </a>
                  <div class="toggle-content" data-content="cardTools">
                    <ul class="btn-toolbar gx-1">
                      <li class="toggle-close">
                        <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                          <em class="icon ni ni-arrow-left"></em>
                        </a>
                      </li>
                      <li>
                        <div class="dropdown">
                          <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <em class="icon ni ni-setting"></em>
                          </a>
                          <div class="dropdown-menu dropdown-menu-xs dropdown-menu-end" style="">
                            <ul class="link-check">
                              <li>
                                <span>Show</span>
                              </li>
                              <li class="active">
                                <a href="#">10</a>
                              </li>
                              <li>
                                <a href="#">20</a>
                              </li>
                              <li>
                                <a href="#">50</a>
                              </li>
                            </ul>
                            <ul class="link-check">
                              <li>
                                <span>Order</span>
                              </li>
                              <li class="active">
                                <a href="#">DESC</a>
                              </li>
                              <li>
                                <a href="#">ASC</a>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="card-search search-wrap" data-search="search">
          <div class="card-body">
            <div class="search-content">
              <a href="#" class="search-back btn btn-icon toggle-search" data-target="search">
                <em class="icon ni ni-arrow-left"></em>
              </a>
              <input type="text" class="form-control border-transparent form-focus-none" placeholder="Search by name">
              <button class="search-submit btn btn-icon">
                <em class="icon ni ni-search"></em>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-inner p-0">
        <div class="nk-tb-list is-separate is-medium mb-3">
          <div class="nk-tb-item nk-tb-head">
            <div class="nk-tb-col nk-tb-col-check">
              <div class="custom-control custom-control-sm custom-checkbox notext">
                <input type="checkbox" class="custom-control-input" id="oid">
                <label class="custom-control-label" for="oid"></label>
              </div>
            </div>
            <div class="nk-tb-col">
              <span>Pedido</span>
            </div>
            <div class="nk-tb-col">
              <span>Nome</span>
            </div>
            <div class="nk-tb-col">
              <span>Produto</span>
            </div>
            <div class="nk-tb-col">
              <span>Total</span>
            </div>
            <div class="nk-tb-col">
              <span>Whatsapp</span>
            </div>
            <div class="nk-tb-col tb-col-md">
              <span class="d-none d-sm-block">Data</span>
            </div>
            <div class="nk-tb-col nk-tb-col-tools"></div>
          </div>
          <?php foreach ($abandoned_carts as $abandoned_cart): ?>
          <div class="nk-tb-item">
            <div class="nk-tb-col nk-tb-col-check">
              <div class="custom-control custom-control-sm custom-checkbox notext">
                <input type="checkbox" class="custom-control-input" id="oid01">
                <label class="custom-control-label" for="oid01"></label>
              </div>
            </div>
            <div class="nk-tb-col">
              <span class="tb-lead">
                <a href="https://rocketpays.app/sale/100/show" to="https://rocketpays.app/sale/100/show"> 100</a>
              </span>
            </div>
            <div class="nk-tb-col">
              <div class="user-info">
                <a href="https://rocketpays.app/sale/100/show" to="https://rocketpays.app/sale/100/show">
                  <span class="tb-lead">Gledson Poggioni </span>
                  <span>contato@rocketleads.com.br</span>
                </a>
              </div>
            </div>
            <div class="nk-tb-col">
              <span class="tb-lead">Checklist Milionário</span>
            </div>
            <div class="nk-tb-col">
              <span class="tb-lead">R$ 47,00</span>
            </div>
            <div class="nk-tb-col">
                        <a href="#" data-toggle="modal" data-target="#modalLinks_11">
                          <em class="icon ni ni-whatsapp"></em>
                        </a>
            </div>
            <div class="nk-tb-col tb-col-md">
              <span class="tb-sub">26/02/2023 - 01:45:16</span>
            </div>
            <div class="nk-tb-col nk-tb-col-tools">
              <ul class="nk-tb-actions gx-1">
                <li>
                  <div class="drodown me-n1">
                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                      <em class="icon ni ni-more-h"></em>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <ul class="link-list-opt no-bdr">
                        <li>
                          <a href="https://rocketpays.app/sale/100/show" to="https://rocketpays.app/sale/100/show">
                            <em class="icon ni ni-eye"></em>
                            <span>Detalhes do pedido</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <em class="icon ni ni-money"></em>
                            <span>Marcar como Convertido</span>
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
  <?php endif; ?>



  <!-- modal tutorial -->
  <div class="modal" tabindex="-1" id="modalHelp" aria-modal="true" role="dialog">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <a href="#" class="close" data-bs-dismiss="modal">
                  <em class="icon ni ni-cross"></em>
              </a>
              <div class="modal-body modal-body text-center">
                  <div class="nk-modal">
                      <div class="header-section-help" style="align-items: center;">
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">Carrinho Abandonado</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenca como recuperar suas vendas com carrinho abandonado</p>
                          <p>
                              <br>
                          </p>
                          <iframe width="460" height="315" src="https://www.youtube.com/embed/NUemSEdQbGc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>                  <p>
                              <br>
                          </p>
                          </span>
                      </div>
                      <div class="nk-modal-action">
                          <a href="#" class="btn btn-lg btn-mw btn-primary" data-bs-dismiss="modal"> Aprenda mais sobre este recurso </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</content>