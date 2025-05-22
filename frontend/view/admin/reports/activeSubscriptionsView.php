<title>Assinaturas Ativas</title>

<content>
<div class="nk-content-body">
    <div class="page-header  mb-4">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Assinaturas Ativas</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards com total de assinaturas e valor -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Assinaturas Ativas</h5>
                    <p class="card-text"><?= $totalActiveSubscriptions; ?> assinaturas</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Valor Total das Assinaturas Ativas</h5>
                    <p class="card-text">R$ <?= number_format($totalActiveValue, 2, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro -->
    <form method="GET" action="">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date">Data de Início</label>
                <input type="date" name="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date">Data de Fim</label>
                <input type="date" name="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label for="user_id">Usuario</label>
                <select name="user_id" class="form-control">
                    <option value="">Selecione um usuário</option>
                    <?php foreach ($userNames as $userId => $userName) : ?>
                        <option value="<?= $userId ?>" <?= ($_GET['user_id'] ?? '') == $userId ? 'selected' : '' ?>>
                            <?= $userName ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary" style="margin-top: 30px;">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="nk-tb-list is-separate is-medium mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col">
                                <span>Vendedor</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Comprador</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Produto</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Valor Pago</span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span class="d-none d-sm-block">Data</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Ciclo de Cobrança</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools"> ... </div>
                        </div>
                        <?php if (!empty($reportData)): ?>
                            <?php foreach ($reportData as $data): ?>
                                <div class="nk-tb-item tr">
                                    <div class="nk-tb-col">
                                        <div class="user-info">
                                            <a href="">
                                                <span class="tb-lead"><?= $data['owner_name']; ?></span>
                                                <span><?= $data['owner_email']; ?></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $data['customer_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $data['product_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">R$ <?= number_format($data['subscription_value'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub"><?= date('d/m/Y - H:i:s', strtotime($data['created_at'])); ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $data['billing_cycle']; ?></span>
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
                                                                <a href="javascript:;" click="cancelCustomerSubscriptionOnClick">
                                                                    <em class="icon ni ni-trash"></em>
                                                                    <span>Cancelar</span>
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
                        <?php else: ?>
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">Nenhuma assinatura ativa encontrada.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="card-inner">
                    <div class="nk-block-between-md g-3">
                        <div class="g">
                        <!-- Paginação -->
                                <div class="pagination justify-content-center">
                                    <?php if ($totalPages > 1): ?>
                                        <ul class="pagination">
                                            <?php 
                                            $maxPages = 5; // Quantidade máxima de páginas a serem exibidas
                                            $startPage = max(1, $currentPage - 2);
                                            $endPage = min($totalPages, $currentPage + 2);
                                            ?>

                                            <?php if ($currentPage > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $currentPage - 1; ?>">Anterior</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                                <li class="page-item <?= $i == $currentPage ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($currentPage < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $currentPage + 1; ?>">Próximo</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</content>
