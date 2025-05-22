<title>Afiliados</title>
<content>
  <!-- tabela vazia -->
  <?php if ((json_decode(json_encode($affiliates))->total ?? 0) == 0): ?>
                    <div class="nk-block nk-block-middle wide-md mx-auto">
                      <div class="nk-block-content nk-error-ld text-center">
                        <center>
                        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_wohcxlyr.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
                        </center>
                        <div class="wide-xs mx-auto">
                          <h3 class="nk-error-title">Não há Afiliados! : (</h3>
                          <p class="nk-error-text">Quando surgir um novo pedido de afiliação, você poderá gerenciá-los por aqui. </p>
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
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Meus Afiliados</h3>
            </div>
            <div class="nk-block-head-content">
              <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light  d-md-none">
                  <em class="icon ni ni-help"></em>
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light d-none d-md-inline-flex">
                  <em class="icon ni ni-help"></em>
                  <span>Ajuda</span>
                </a>
                <a href="#" class="btn btn-white btn-dim btn-outline-light d-md-none">
                  <em class="icon ni ni-download-cloud"></em>
                </a>
                <a href="#" class="btn btn-white btn-dim btn-outline-light  d-none d-md-inline-flex">
                  <em class="icon ni ni-download-cloud"></em>
                  <span>Export</span>
                </a>
              </div>
            </div>
          </div>
        </div>
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabItem1">Ativos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabItem2">Solicitações pendentes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabItem3">Recusados, bloqueados ou cancelados</a>
      </li>

    </ul>
    <div class="tab-content">
        <!-- Ativos -->
      <div class="tab-pane active" id="tabItem1">
          <div class="nk-block">
            <div class="card card-bordered">
              <div class="card-inner-group">
                <div class="card-inner position-relative card-tools-toggle">
                  <div class="card-title-group" data-select2-id="16">
                    <div class="card-tools" data-select2-id="15"></div>
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
                        <span>Afiliações</span>
                      </div>
                      <div class="nk-tb-col">
                        <span>Total de Vendas</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="d-none d-sm-block">Afiliado desde</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="d-none d-sm-block">Comissão</span>
                      </div>
                      <div class="nk-tb-col">
                        <span>Status</span>
                      </div>
                      <div class="nk-tb-col nk-tb-col-tools"> ... </div>
                    </div>
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
                        <span class="tb-lead">5 Afiliações</span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead">89 Vendas</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="tb-sub">26/02/2023 - 01:45:16</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="tb-sub">30% de comissão</span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Ativo</span>
                      </div>
                      <div class="nk-tb-col nk-tb-col-tools">
                        <ul class="nk-tb-actions gx-1">
                          <li>
                            <div class="drodown me-n1">
                              <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                                <em class="icon ni ni-more-h"></em>
                              </a>
                              <div class="dropdown-menu dropdown-menu-end" style="">
                                <ul class="link-list-opt no-bdr">
                                  <li>
                                    <a href="#">
                                      <span>Editar Comissão</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="#">
                                      <span>Cancelar Afiliado</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="#">
                                      <em class="icon ni ni-report-profit"></em>
                                      <span>Enviar Nota</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="#">
                                      <em class="icon ni ni-trash"></em>
                                      <span>Cancelar pedido</span>
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
                <Pagination />
              </div>
            </div>
          </div>
      </div>
        <!-- Solicitações pendentess -->
      <div class="tab-pane" id="tabItem2">
        <p>Solicitações pendentess</p>
      </div>
        <!-- Recusados, bloqueados ou canceladoss -->

      <div class="tab-pane" id="tabItem3">
        <p>Recusados, bloqueados ou cancelados</p>
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
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">Afiliações</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenda como gerenciar seus afiliados.</p>
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