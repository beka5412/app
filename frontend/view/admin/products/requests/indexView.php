<?php
use Backend\Enums\Product\EProductRequestStatus;
?>
<title>Produtos</title>

<content>
    <div>
        <content class="content_products_list">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Solicitações para aprovar produtos</h3>
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
                                <div class="nk-tb-list">
                                    <div class="nk-tb-item nk-tb-head">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input" id="pid">
                                                <label class="custom-control-label" for="pid"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <span>ID</span>
                                        </div>
                                        <div class="nk-tb-col">
                                            <span>Nome</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-sm">
                                            <span>SKU</span>
                                        </div>
                                        <div class="nk-tb-col">
                                            <span>Preço Base</span>
                                        </div>
                                        <div class="nk-tb-col">
                                            <span>Preço de venda</span>
                                        </div>
                                        <div class="nk-tb-col">
                                            <span>Status</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span>Produto Base</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span>Cliente</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span>Ações</span>
                                        </div>
                                    </div>
                                    <?php foreach ($product_requests as $key => $product_request): 
                                        $product = $product_request->product; ?>
                                        <div class="nk-tb-item tr">
                                            <div class="nk-tb-col nk-tb-col-check">
                                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                                    <input type="checkbox" class="custom-control-input" id="<?php echo $key; ?>">
                                                    <label class="custom-control-label" for="<?php echo $key; ?>"></label>
                                                </div>
                                            </div>
                                            <div class="nk-tb-col"> #<?php echo $product->id; ?> </div>
                                            <div class="nk-tb-col">
                                                <span class="tb-product">
                                                    <img src="<?php echo $product->image; ?>" alt="" class="thumb">
                                                    <a to="<?php echo $product->landing_page; ?>" href="<?php echo $product->landing_page; ?>">
                                                        <span class="title"><?php echo $product->name; ?></span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="nk-tb-col  tb-col-sm">
                                                <span class="tb-sub"><?php echo $product->sku; ?></span>
                                            </div>
                                            <div class="nk-tb-col">
                                                <span class="tb-lead">Ver depois</span>
                                            </div>
                                            <div class="nk-tb-col">
                                                <span class="tb-lead"><?php echo $product->price; ?></span>
                                            </div>
                                            <div class="nk-tb-col">
                                                <span class="tb-lead">
                                                <?php $status = $product->last_request()->status; ?>
                                                <?php if ($status == EProductRequestStatus::PENDING->value): ?>
                                                    <span class="badge bg-warning">Pendente</span>
                                                <?php elseif ($status == EProductRequestStatus::REJECTED->value): ?>
                                                    <span class="badge bg-danger">Rejeitado</span>
                                                <?php elseif ($status == EProductRequestStatus::APPROVED->value): ?>
                                                    <span class="badge bg-success">Aprovado</span>
                                                <?php endif ?>
                                                </span>
                                            </div>
                                            <div class="nk-tb-col tb-col-md">
                                                <span class="tb-sub"><?php echo text_etc($product->description, 35); ?></span>
                                            </div>
                                            <div class="nk-tb-col tb-col-md"> <?php echo $product->user->name; ?> </div>
                                            <div class="nk-tb-col nk-tb-col-tools">
                                                <ul class="">
                                                    <li class="me-n1">
                                                        <div class="dropdown">
                                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    <li>
                                                                        <a href="javascript:;" click="adminProductRequestApprove" 
                                                                            data-id="<?= $product_request->id ?>"
                                                                            >
                                                                            <em class="icon ni ni-check"></em>
                                                                            <span>Aprovar produto</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;" click="adminProductRequestReject" 
                                                                            data-id="<?= $product_request->id ?>"
                                                                            >
                                                                            <em class="icon ni ni-trash"></em>
                                                                            <span>Reprovar produto</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?php echo $product->landing_page; ?>" target="_blank">
                                                                            <em class="icon ni ni-edit"></em>
                                                                            <span>Ver Landing Page</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#">
                                                                            <em class="icon ni ni-eye"></em>
                                                                            <span>Ver Produto</span>
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
            <div class="modal" tabindex="-1" id="modalHelp" aria-modal="true" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <a href="#" class="close" data-bs-dismiss="modal">
                            <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-body modal-body text-center">
                            <div class="nk-modal">
                                <div class="header-section-help" style="align-items: center;">
                                    <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">Produtos</h4>
                                </div>
                                <div class="help-description" style="overflow-y: auto;">
                                    <span id="description">
                                        <p>Aprenda como criar um novo produto, personalizar seu checkout, implementar pixel, selecionar formas de pagamento</p>
                                        <p>
                                            <br>
                                        </p>
                                        <iframe width="460" height="315" src="https://www.youtube.com/embed/NUemSEdQbGc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
                                        <p>
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
                                    <a href="javascript:;" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
                                    <a href="javascript:void(0);" click="destroyCoupon" data-id="">Sim, Quero Excluir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" tabindex="-1" id="modalNewProduct" aria-modal="true" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body modal-body-lg text-center">
                            <div class="nk-modal">
                                <h4 class="nk-modal-title">Novo produto </h4>
                                <div class="nk-modal-text">
                                    <div class="form-group">
                                        <input class="form-control inp_product_name" placeholder="Nome do produto  ">
                                    </div>
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <select class="form-select inp_payment_type">
                                                <option value="unique">Pagamento único</option>
                                                <option value="recurring">Recorrente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-control-wrap">
                                        <select class="form-select inp_product_type">
                                            <option value="digital">Digital</option>
                                            <option value="physical">Físico</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="nk-modal-action mt-5">
                                    <a href="javascript:;" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">Cancelar</a>
                                    <a href="javascript:void(0);" class="btn btn-lg btn-mw btn-primary" click="newProduct">Criar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </content>
    </div>

</content>