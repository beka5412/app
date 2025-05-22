<content>
<div class="nk-content-body">
        <div class="page-header mb-4">
            <div class="page-block">
            <div class="row align-items-center">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Relatório de Vendas por Dia</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="<?= site_url(); ?>/reports" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>Voltar</span>
                                </a>
                                <a href="<?= site_url(); ?>/reports" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
                                <em class="icon ni ni-arrow-left"></em>
                                </a>
                            </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="card card-full">
            <div class="card-inner">
                <div class="card-title-group">
                   
                    <div class="card-tools d-flex">
                        <form method="GET" action="<?= site_url(); ?>/reports/salesbyday">
                          <div class="d-flex">
                            <div class="form-group">
                                    <label for="start_date">Data Inicial:</label>
                                    <input type="date" id="start_date" name="start_date" value="<?= $_GET['start_date'] ?? ''; ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Data Final:</label>
                                    <input type="date" id="end_date" name="end_date" value="<?= $_GET['end_date'] ?? ''; ?>" class="form-control">
                                </div>
                                <div class="form-group mt-4">
                                <label></label>
                                <button type="submit" class="btn btn-primary"><em class="icon ni ni-opt-dot-alt"></em><span>Filtrar</span></button>
                            </div>
                           </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="nk-tb-list mt-n2">
                <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col tb-col-sm">
                        <span>Data</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span>Quant. de pedidos</span>
                    </div>
                    <div class="nk-tb-col">
                        <span>Valor</span>
                    </div>
                </div>
                <?php foreach ($salesData as $sale): ?>
                <div class="nk-tb-item">
                    <div class="nk-tb-col tb-col-sm">
                        <span class="tb-sub"><?= htmlspecialchars($sale->date); ?></span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span class="tb-sub tb-amount"><?= htmlspecialchars($sale->total_orders); ?></span>
                    </div>
                    <div class="nk-tb-col">
                        <span class="tb-sub">R$ <?= number_format($sale->total_value, 2, ',', '.'); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="card-inner">
                <div class="nk-block-between-md g-3">
                    <div class="g">
                        <ul class="pagination justify-content-center justify-content-md-start">
                            <?php for ($i = 1; $i <= $salesData->lastPage(); $i++): ?>
                                <li class="page-item <?= $i === $salesData->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>&start_date=<?= $_GET['start_date'] ?? '' ?>&end_date=<?= $_GET['end_date'] ?? '' ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
            </div>


        </div>
</div>
</content>