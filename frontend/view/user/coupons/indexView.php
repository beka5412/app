<?php
  use Backend\Enums\Coupon\ECouponStatus; 
  use Backend\Enums\Coupon\ECouponType;
?>
<title>Cupons de desconto</title>
<content ready="ready">
<?php if ((json_decode(json_encode($coupons))->total ?? 0) == 0): ?>
  <div class="nk-block nk-block-middle wide-md mx-auto">
    <div class="nk-block-content nk-error-ld text-center">
      <center>
      <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
      <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_yseim94k.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
      </center>
      <div class="wide-xs mx-auto">
        <h3 class="nk-error-title">Não há Cupons! : (</h3>
        <p class="nk-error-text">Cadastre seu primeiro cupom, para gerenciá-los por aqui. </p>
        <button data-bs-toggle="modal" data-bs-target="#modalCreate" class="toggle btn btn-primary d-none d-md-inline-flex">
          <em class="icon ni ni-plus"></em>
          <span>Cadastrar cupom</span>
        </button>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="nk-content-body">
        <!-- cabeçalho -->
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Cupons de desconto
              </h3>
            </div>
            <div class="nk-block-head-content">
              <div class="toggle-wrap nk-block-tools-toggle">
                <a href="javascript:void(0);" to="<?php echo site_url(); ?>/coupon/new" class="toggle btn btn-icon btn-primary d-md-none">
                  <em class="icon ni ni-plus"></em>
                </a>
                <button data-bs-toggle="modal" data-bs-target="#modalCreate" class="toggle btn btn-primary d-none d-md-inline-flex">
                  <em class="icon ni ni-plus"></em>
                  <span>Adicionar cupom</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- tabela -->
        <div class="nk-block">
          <div class="card card-bordered">
              <div class="card-inner-group">
                <div class="card-inner position-relative card-tools-toggle">
                    <div class="card-title-group" data-select2-id="16">
                      <div class="card-tools" data-select2-id="15">
                        <div class="form-inline flex-nowrap gx-3" data-select2-id="14">
                          <div class="form-wrap w-150px">
                            <select class="form-select js-select2 select2-hidden-accessible" data-search="off" data-placeholder="Bulk Action" data-select2-id="1" tabindex="-1" aria-hidden="true">
                              <option value="" data-select2-id="3">Ações em massa</option>
                              <option value="deletar" data-select2-id="20">Deletar</option>
                              <option value="desativar" data-select2-id="21">Desativar</option>
                              <option value="ativar" data-select2-id="21">Atovar</option>
                            </select>
                          </div>
                          <div class="btn-wrap">
                            <span class="d-none d-md-block">
                              <button class="btn btn-dim btn-outline-light disabled">Aplicar</button>
                            </span>
                            <span class="d-md-none">
                              <button class="btn btn-dim btn-outline-light btn-icon disabled">
                                <em class="icon ni ni-arrow-right"></em>
                              </button>
                            </span>
                          </div>
                        </div>
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
                  <div class="nk-tb-list is-separate mb-3">
                    <div class="nk-tb-item nk-tb-head">
                      <div class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                          <input type="checkbox" class="custom-control-input" id="pid">
                          <label class="custom-control-label" for="pid"></label>
                        </div>
                      </div>
                      <div class="nk-tb-col">
                        <span>Codigo</span>
                      </div>
                      <div class="nk-tb-col">
                        <span>Desconto</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span>Descrição</span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span>Status</span>
                      </div>
                      <div class="nk-tb-col">
                      </div>
                    </div>

                    <?php foreach ($coupons as $coupon): ?>
                    <div class="nk-tb-item tr">
                      <div class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                          <input type="checkbox" class="custom-control-input" id="pid1">
                          <label class="custom-control-label" for="pid1"></label>
                        </div>
                      </div>
                      <div class="nk-tb-col">
                          <a href="#" to="<?php echo site_url(); ?>/coupon/<?php echo $coupon->id; ?>/edit" class="tb-product">
                          <span class="title">
                            <?php echo $coupon->code; ?>
                          </span>
                        </a>
                      </div>
                      <div class="nk-tb-col">
                        <span class="tb-lead">R$
                          <?php echo number_format((Double) ($coupon?->discount ?? 0), 2, ',', '.'); ?>
                        </span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <span class="tb-lead">
                          <?php echo $coupon->description; ?>
                        </span>
                      </div>
                      <div class="nk-tb-col tb-col-md">
                        <?php echo match ($coupon->status)
                        {
                          ECouponStatus::PUBLISHED->value => '<span class="badge badge-dot badge-dot-xs bg-success">Ativo</span>',
                          ECouponStatus::DISABLED->value => '<span class="badge badge-dot badge-dot-xs bg-danger">Inativo</span>',
                          ECouponStatus::DRAFT->value => '<span class="badge badge-dot badge-dot-xs bg-info">Rascunho</span>',
                        }
                        ?>
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
                                    <a href="#" to="<?php echo site_url(); ?>/coupon/<?php echo $coupon->id; ?>/edit">
                                      <em class="icon ni ni-edit"></em>
                                      <span>Editar</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="javascript:void(0);" click="destroyCoupon" data-id="<?php echo $coupon->id; ?>">
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
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">Cupom de Desconto</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenca como gerenciar cupons de desconto</p>
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

  <div class="modal fade" tabindex="-1" id="modalCreate" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-body-lg">
          <form class="nk-modal" id="create-coupon-form">
            <h4 class="nk-modal-title text-center">Novo cupom</h4>
            <div class="nk-modal-text">

              <div class="form-group">
                <label class="form-label">Código do cupom</label>
                <input class="form-control" type="text" name="coupon-code" />
              </div>

              <div class="form-group">
                <label class="form-label">Tipo de desconto</label>
                <div class="form-control-wrap">
                  <select class="form-select" id="coupon-type" name="coupon-type">
                    <option value="<?= ECouponType::PRICE->value; ?>">Valor fixo</option>
                    <option value="<?= ECouponType::PERCENT->value; ?>">Percentual</option>
                  </select>
                </div>
              </div>

              <div class="d-block">
                <div class="form-group">
                  <label class="form-label">Valor do desconto</label>
                  <div class="form-control-wrap">
                    <input
                      class="form-control inp_price"
                      keyup="$inputCurrency"
                      keydown="$onlyNumbers"
                      blur="$inputCurrency"
                      load="element.value = currency(element.value)"
                      name="coupon-value"
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="nk-modal-action text-center mt-5">
              <button class="btn btn-lg btn-mw btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" form="create-coupon-form" class="btn btn-lg btn-mw btn-primary">Criar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- href="javascript:void(0);" click="destroyCoupon" data-id="<?php echo $coupon->id; ?>" -->

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
</content>