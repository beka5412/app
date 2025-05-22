<content>
<div class="nk-content-body">
    <div class="page-header mb-4">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Relatório de Vendas por Produto por Dia</h3>
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
                    <form method="GET" action="<?= site_url(); ?>/reports/salesbyproduct">
                        <div class="d-flex">
                            <div class="form-group">
                                <label for="start_date">Data Inicial:</label>
                                <input type="date" id="start_date" name="start_date" value="<?= $_GET['start_date'] ?? ''; ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="end_date">Data Final:</label>
                                <input type="date" id="end_date" name="end_date" value="<?= $_GET['end_date'] ?? ''; ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="product_id">Produto:</label>
                                <select id="product_id" name="product_id" class="form-control">
                                    <option value="">Todos os Produtos</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= $product->id; ?>" <?= isset($_GET['product_id']) && $_GET['product_id'] == $product->id ? 'selected' : ''; ?>>
                                            <?= $product->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                            <div class="form-group mt-4">
                                <label></label>
                                <button type="submit" class="btn btn-primary">
                                    <em class="icon ni ni-opt-dot-alt"></em><span>Filtrar</span>
                                </button>
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
                <div class="nk-tb-col tb-col-sm">
                    <span>Produto</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                    <span>Quantidade Vendida</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                    <span>Total Vendido</span>
                </div>
            </div>
            <?php foreach ($salesByProduct as $sale): ?>
            <div class="nk-tb-item">
                <div class="nk-tb-col tb-col-sm">
                    <span class="tb-sub"><?= $sale->sale_date; ?></span>
                </div>
                <div class="nk-tb-col tb-col-sm">
                    <span class="tb-sub"><?= $sale->product_name; ?></span>
                </div>
                <div class="nk-tb-col tb-col-md">
                    <span class="tb-sub"><?= $sale->total_sold; ?></span>
                </div>
                <div class="nk-tb-col tb-col-md">
                    <span class="tb-sub">R$ <?= number_format($sale->total_value, 2, ',', '.'); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card-inner">
            <div class="nk-block-between-md g-3">
                <div class="g">
                    <ul class="pagination justify-content-center justify-content-md-start">
                        <?php for ($i = 1; $i <= $salesByProduct->lastPage(); $i++): ?>
                            <li class="page-item <?= $i === $salesByProduct->currentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i; ?>&start_date=<?= $_GET['start_date'] ?? '' ?>&end_date=<?= $_GET['end_date'] ?? '' ?>&product_id=<?= $_GET['product_id'] ?? '' ?>">
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
