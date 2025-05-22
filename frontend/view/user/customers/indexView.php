<title>Clientes</title>

<content>
<?php if ((json_decode(json_encode($customers))->total ?? 0) == 0): ?>
<div class="nk-block nk-block-middle wide-md mx-auto">
  <div class="nk-block-content nk-error-ld text-center">
    <center>
      <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
      <lottie-player src="https://assets8.lottiefiles.com/packages/lf20_z3wd7moi.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
    </center>
    <div class="wide-xs mx-auto">
      <h3 class="nk-error-title">Não há Clientes! : (</h3>
      <p class="nk-error-text">Quando surgirem clientes, você poderá gerenciá-los por aqui. </p>
      <!-- <a href="/demo1/index.html" class="btn btn-lg btn-primary mt-2">Back To Home</a> -->
    </div>
  </div>
</div>
<?php else: ?>
<div class="nk-content-body">
      <!-- cabeçalho -->
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Lista de Clientes</h3>
              <div class="nk-block-des text-soft">
                <p>Você tem x clientes</p>
              </div>
            </div>
            <div class="nk-block-head-content">
                  <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
                    <em class="icon ni ni-help"></em>
                   </a>
                   <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
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
                   <a href="#" data-target="addProduct" class="toggle btn btn-icon btn-primary d-md-none">
                      <em class="icon ni ni-plus"></em>
                    </a>
                    <a href="javascript:void(0);" to="<?php echo site_url(); ?>/customer/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                      <em class="icon ni ni-plus"></em>
                      <span>Adicionar cliente</span>
                    </a>
              </div>
          </div>
        </div>
        <!-- Content -->
        <div class="nk-block">
          <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
              <div class="card-inner position-relative card-tools-toggle">
                <div class="card-title-group">
                  <div class="card-tools">
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
                                  <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-bs-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                  </a>
                                  <div class="dropdown-menu dropdown-menu-xs dropdown-menu-end">
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
                <div class="nk-tb-list nk-tb-ulist">
                  <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col nk-tb-col-check">
                      <div class="custom-control custom-control-sm custom-checkbox notext">
                        <input type="checkbox" class="custom-control-input" id="cid">
                        <label class="custom-control-label" for="cid"></label>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="sub-text">Cliente</span>
                    </div>
                    <div class="nk-tb-col tb-col-sm">
                      <span class="sub-text">Email</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="sub-text">Telefone</span>
                    </div>
                    <div class="nk-tb-col tb-col-lg">
                      <span class="sub-text">Pedidos</span>
                    </div>
                    <div class="nk-tb-col tb-col-xxl">
                      <span class="sub-text">Desde</span>
                    </div>
                    <div class="nk-tb-col text-end">
                      <span class="sub-text">Ações</span>
                    </div>
                  </div>
                  <?php foreach ($customers as $customer): ?>
                  <div class="nk-tb-item">
                    <div class="nk-tb-col nk-tb-col-check">
                      <div class="custom-control custom-control-sm custom-checkbox notext">
                        <input type="checkbox" class="custom-control-input" id="cid1">
                        <label class="custom-control-label" for="cid1"></label>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <a href="javascript:void(0);" to="<?php echo site_url(); ?>/customer/<?php echo $customer->id; ?>/show">
                        <div class="user-card">
                          <div class="user-avatar xs bg-primary">
                            <span>BG</span>
                          </div>
                          <div class="user-name">
                            <span class="tb-lead"><?php echo $customer->name; ?><span class="dot dot-success d-lg-none ms-1"></span>
                            </span>
                          </div>
                        </div>
                      </a>
                    </div>
                    <div class="nk-tb-col tb-col-sm">
                      <span class="sub-text"><?php echo $customer->email; ?></span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="sub-text"><?php echo $customer->phone; ?></span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <div class="icon-text">
                        <span class="sub-text">3 pedidos</span>
                      </div>
                    </div>
                    <div class="nk-tb-col tb-col-xxl">
                      <span class="sub-text"><?php echo $customer->created; ?></span>
                    </div>
                    <div class="nk-tb-col nk-tb-col-tools">
                      <ul class="nk-tb-actions gx-1">
                        <li>
                          <div class="drodown">
                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                              <em class="icon ni ni-more-h"></em>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                              <ul class="link-list-opt no-bdr">
                                <li>
                                  <a href="#">
                                    <em class="icon ni ni-eye"></em>
                                    <span>View Details</span>
                                  </a>
                                </li>
                                <li>
                                  <a href="#">
                                    <em class="icon ni ni-mail"></em>
                                    <span>Send Mail</span>
                                  </a>
                                </li>
                                <li>
                                  <a href="#">
                                    <em class="icon ni ni-cart"></em>
                                    <span>Orders</span>
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
              <!-- paginacao -->
              <Pagination />
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
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">Clientes</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenca como gerenciar seus clientes</p>
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