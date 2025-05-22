<?php

use Backend\Models\Product;

 ?>
<title>Integrações da Cademi</title>

<content>
    <?php if ((json_decode(json_encode($integrations))->total ?? 0) == 0) : ?>
        <div class="nk-block nk-block-middle wide-md mx-auto">
            <div class="nk-block-content nk-error-ld text-center">
                <center>
                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                    <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_yseim94k.json" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></lottie-player>
                </center>
                <div class="wide-xs mx-auto">
                    <h3 class="nk-error-title">Não há integrações! :(</h3>
                    <p class="nk-error-text">Cadastre sua primeira integração para gerenciá-los por aqui. </p>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light  d-md-none">
                        <em class="icon ni ni-help"></em>
                    </a>
                    <!-- <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light d-none d-md-inline-flex">
                        <em class="icon ni ni-help"></em>
                        <span>Preciso de ajuda</span>
                    </a> -->
                    <a href="javascript:void(0);" to="<?php echo site_url(); ?>/app/cademi/new" class="toggle btn btn-primary d-none d-md-inline-flex mt-2">Cadastrar nova integração</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="nk-content-body">
            <!-- cabeçalho -->
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Integrações da Cademi
                        </h3>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light d-md-none">
                                <em class="icon ni ni-help"></em>
                            </a>
                            <a to="<?php echo site_url(); ?>/app/cademi/new" to="<?php echo site_url(); ?>/app/cademi/new" class="toggle btn btn-icon btn-primary d-md-none">
                                <em class="icon ni ni-plus"></em>
                            </a>
                            <a to="<?php echo site_url(); ?>/app/cademi/new" to="<?php echo site_url(); ?>/app/cademi/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                                <em class="icon ni ni-plus"></em>
                                <span>Adicionar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- tabela -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
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
                                        <span>Produto</span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span>Turma</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span>Status</span>
                                    </div>
                                    <div class="nk-tb-col">
                                    </div>
                                </div>

                                <?php foreach ($integrations as $integration): 
                                    $product = Product::find($integration->product_id); ?>
                                    <div class="nk-tb-item tr">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input" id="pid1">
                                                <label class="custom-control-label" for="pid1"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <a href="<?php echo site_url(); ?>/app/cademi/<?php echo $integration->id; ?>/edit" 
                                                
                                                class="tb-product">
                                                <span class="title">
                                                    <?php echo $product->name ?? 'Sem produto'; ?>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <span class="tb-lead">
                                                <?php if ($integration->status === 1): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger">Desativado</span>
                                                <?php endif ?>
                                            </span>
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
                                                                    <a href="<?php echo site_url(); ?>/app/cademi/<?php echo $integration->id; ?>/edit">
                                                                        <em class="icon ni ni-edit"></em>
                                                                        <span>Editar</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" click="destroyCademiIntegration" data-id="<?php echo $integration->id; ?>">
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
</content>