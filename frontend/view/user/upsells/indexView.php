<title>Upsells</title>

<content>
    <!-- tabela vazia -->
    <?php if ((json_decode(json_encode($upsells))->total ?? 0) == 0): ?>
                    <div class="nk-block nk-block-middle wide-md mx-auto">
                      <div class="nk-block-content nk-error-ld text-center">
                        <center>
                        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_yf4hb6ol.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
                        </center>
                        <div class="wide-xs mx-auto">
                          <h3 class="nk-error-title">Não há Nenhum UpSell :(</h3>
                          <p class="nk-error-text">você pode cadastrar um novo e gerenciá-los por aqui. </p>
                          <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
                            <em class="icon ni ni-help"></em>
                          </a>
                          <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
                            <em class="icon ni ni-help"></em>
                            <span>Aprenda mais sobre esse recurso</span>
                          </a>
                          <a href="javascript:void(0);" to="<?php echo site_url(); ?>/upsell/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                      <em class="icon ni ni-plus"></em>
                      <span>Cadastrar UpSell</span>
                    </a>
                        </div>
                      </div>
                    </div>
  <?php else: ?>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">UpSell
          </div>
          <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
                    <em class="icon ni ni-help"></em>
                   </a>
                   <a  href="javascript:void(0);" to="<?php echo site_url(); ?>/upsell/new" class="toggle btn btn-icon btn-primary d-md-none">
                      <em class="icon ni ni-plus"></em>
                    </a>
                    <a href="javascript:void(0);" to="<?php echo site_url(); ?>/upsell/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                      <em class="icon ni ni-plus"></em>
                      <span>Cadastrar UpSell</span>
                    </a>
            </div>
          </div>
        </div>
    </div>
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
            <div class="nk-tb-list is-separate mb-3">
              <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col nk-tb-col-check">
                  <div class="custom-control custom-control-sm custom-checkbox notext">
                    <input type="checkbox" class="custom-control-input" id="pid">
                    <label class="custom-control-label" for="pid"></label>
                  </div>
                </div>
                <div class="nk-tb-col">
                  <span>Descrição</span>
                </div>
                <div class="nk-tb-col">
                  <span>Produto</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                  <span>Embed</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                  <span>Status</span>
                </div>
                <div class="nk-tb-col"></div>
              </div>
              <?php foreach ($upsells as $upsell): ?>
              <div class="nk-tb-item tr">
                <div class="nk-tb-col nk-tb-col-check">
                  <div class="custom-control custom-control-sm custom-checkbox notext">
                    <input type="checkbox" class="custom-control-input" id="pid1">
                    <label class="custom-control-label" for="pid1"></label>
                  </div>
                </div>
                <div class="nk-tb-col">
                  <a href="<?php echo site_url(); ?>/upsell/<?php echo $upsell->id; ?>/edit" to="<?php echo site_url(); ?>/upsell/<?php echo $upsell->id; ?>/edit">
                    <span><?php echo $upsell->name; ?></span>
                  </a>
                </div>
                <div class="nk-tb-col">
                  <span class="tb-lead"><?php echo $upsell->checkout->product->name ?? ''; ?></span>
                </div>
                <div class="nk-tb-col tb-col-md">
                  <span class="tb-lead">
                    <a href="javascript:;" click="copyHTML" 
                    data-accept-text="<?php echo $upsell->accept_text; ?>"
                    data-refuse-text="<?php echo $upsell->refuse_text; ?>"
                    data-accept-page="<?php echo $upsell->accept_page; ?>"
                    data-refuse-page="<?php echo $upsell->refuse_page; ?>"
                    class="toggle btn btn-primary d-none d-md-inline-flex" data-bs-toggle="modal" data-bs-target="#modalUpsellCode">
                    <em class="icon ni ni-code"></em>
                      <span>Copiar HTML</span>
                    </a>
                  </span>
                </div>
                <div class="nk-tb-col tb-col-md">
                    <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Ativo</span>
                </div>
                <div class="nk-tb-col nk-tb-col-tools">
                  <ul class="nk-tb-actions gx-1 my-n1">
                    <li class="me-n1">
                      <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                          <em class="icon ni ni-more-h"></em>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="">
                          <ul class="link-list-opt no-bdr">
                            <li>
                              <a href="#">
                                <em class="icon ni ni-edit"></em>
                                <span>Editar</span>
                              </a>
                            </li>
                            <li>
                              <a href="javascript:void(0);" click="destroyUpsell" data-id="<?php echo $upsell->id; ?>">
                                <em class="icon ni ni-trash"></em>
                                <span>Deletar</span>
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
          <Pagination />
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
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">UpSell</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenca como gerenciar seus UpSell e converter muito mais...</p>
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

  <!-- delete modal -->
  <div class="modal fade" tabindex="-1" id="modalDelet" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
          <div class="nk-modal">
            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
            <h4 class="nk-modal-title">Você tem certeza? </h4>
            <div class="nk-modal-text">
              <p class="lead">Esta ação não poderá ser desfeita. </p>
            </div>
            <div class="nk-modal-action mt-5">
              <a href="#" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
              <a href="javascript:void(0);" click="destroyCoupon" data-id="<?php echo $coupon->id; ?>">Sim, Quero Excluir</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- upsell code modal -->
  <div class="modal fade" tabindex="-1" id="modalUpsellCode" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
          <div class="nk-modal">
            <h4 class="nk-modal-title">Copiar código do Upsell</h4>
            <div class="nk-modal-text">

              <div class="form-group">
                <div class="form-control-wrap">
                  <textarea class="form-control" id="textUpsellCode"></textarea>
                </div>
              </div>

            </div>
            <div class="nk-modal-action mt-5">
              <a href="javascript:;" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
              <a href="javascript:void(0);" class="btn btn-lg btn-mw btn-primary" click="copyHTMLModal">Copiar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</content>