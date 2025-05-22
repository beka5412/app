<title>Vendas por dia</title>

<content>
<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Relatório de Vendas Diárias por Usuário</h3>
            </div>
        </div>
    </div>

    <!-- Filtro por Data e Vendedor -->
    <form method="GET" action="">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date">Data de Início</label>
                <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date">Data de Fim</label>
                <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
            </div>
            <div class="col-md-3">
                <label for="user_id">Vendedor</label>
                <select name="user_id" class="form-control">
                    <option value="">Selecione o Vendedor</option>
                    <?php foreach ($userNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($userId == $id) ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary" style="margin-top: 30px;">Filtrar</button>
                <a href="?" class="btn btn-secondary" style="margin-top: 30px;">Limpar Filtro</a>
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
                                <span>Data da Venda</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Vendedor</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Quantidade de Pedidos</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Valor Total Vendido</span>
                            </div>
                        </div>

                        <!-- Listagem de Vendas por Usuário -->
                        <?php if (!empty($formattedSales)): ?>
                            <?php foreach ($formattedSales as $sale): ?>
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= date('d/m/Y', strtotime($sale['sale_date'])); ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $sale['user_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $sale['total_orders']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">R$ <?= number_format($sale['total_revenue'], 2, ',', '.'); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">Nenhuma venda encontrada.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</content>