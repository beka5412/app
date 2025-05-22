<title>Relatório de Vendas Diárias</title>

<content>
<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Vendas Diárias</h3>
            </div>
        </div>
    </div>
    <div class="">
    <div class="row row-cols-1 mt-3 mb-4 row-cols-md-2 row-cols-xl-3">
      <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Total de pedidos</p>
                <h4 class="my-1 text-info">
                  <div class="col">
                    <span class="total-orders"><?= $totalSales; ?></span>
                  </div>
                </h4>
              </div>
              <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                <i class="fa fa-shopping-cart"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-success">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Pedidos Aprovados</p>
                <h4 class="my-1 text-success">
                  <div class="col">
                    <span class="completed-orders"> R$ <?= number_format($totalRevenue, 2, ',', '.'); ?></span>
                  </div>
                </h4>
                
              </div>
              <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                <i class="fa fa-bar-chart"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
    <!-- Filtros -->
    <div class="card card-bordered card-preview">
        <div class="card-inner">
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
                        <label for="user_id">Vendedor</label>
                        <select name="user_id" class="form-control">
                            <option value="">Selecione o Vendedor</option>
                            <?php foreach ($userNames as $userId => $userName) : ?>
                                <option value="<?= $userId ?>" <?= ($_GET['user_id'] ?? '') == $userId ? 'selected' : '' ?>><?= $userName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="product_id">Produto</label>
                        <select name="product_id" class="form-control">
                            <option value="">Selecione o Produto</option>
                            <?php foreach ($productNames as $productId => $productName) : ?>
                                <option value="<?= $productId ?>" <?= ($_GET['product_id'] ?? '') == $productId ? 'selected' : '' ?>><?= $productName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search">Busca por Pedido, Nome ou E-mail</label>
                        <input type="text" name="search" class="form-control" value="<?= $_GET['search'] ?? '' ?>" placeholder="ID, Nome, E-mail">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary" style="margin-top: 30px;">Filtrar</button>
                        <a href="?" class="btn btn-secondary" style="margin-top: 30px;">Limpar Filtro</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Tabela de Vendas -->
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="tab-content">
                <div class="tab-pane active" id="tabItem1">
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
                                <span>Comprador</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Produto</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Vendedor</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Valor Pago</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Status</span>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span class="d-none d-sm-block">Data</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools"> ... </div>
                        </div>

                        <!-- Listagem de Vendas -->
                        <?php if (!empty($salesData)): ?>
                            <?php foreach ($salesData as $sale): ?>
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col nk-tb-col-check">
                                        <div class="custom-control custom-control-sm custom-checkbox notext">
                                            <input type="checkbox" class="custom-control-input" id="<?= $sale['transaction_id']; ?>">
                                            <label class="custom-control-label" for="<?= $sale['transaction_id']; ?>"></label>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">
                                            <a href="#"><?= $sale['transaction_id']; ?></a>
                                        </span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <div class="user-info">
                                            <a href="#">
                                                <span class="tb-lead"><?= $sale['customer_name']; ?></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $sale['product_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $sale['seller_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">R$ <?= number_format($sale['sale_value'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Aprovado</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub"><?= date('d/m/Y - H:i:s', strtotime($sale['sale_date'])); ?></span>
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
                                                            <li><a href="#"><em class="icon ni ni-eye"></em><span>Detalhes do Pedido</span></a></li>
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
                                    <span class="tb-lead">Nenhuma venda encontrada.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="card-inner">
                    <div class="nk-block-between-md g-3">
                        <div class="g">
                            <ul class="pagination justify-content-center">
                                <!-- Paginação dinâmica -->
                                <?= $pagination; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</content>
