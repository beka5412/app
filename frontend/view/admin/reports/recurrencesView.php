
<title>Relatórios</title>

<content>
    
<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Relatório de Recorrências</h3>
            </div>
        </div>
    </div>

<div class="row row-cols-1 mt-3 mb-4 row-cols-md-2 row-cols-xl-3">
      <div class="col-lg-4">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Total de faturas</p>
                <h4 class="my-1 text-info">
                  <div class="col">
                    <span class="total-orders"><?= $totalInvoices; ?> faturas</span>
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
                <p class="mb-0 text-secondary">Valor Total das Faturas</p>
                <h4 class="my-1 text-success">
                  <div class="col">
                    <span class="completed-orders">R$ <?= number_format($totalValue, 2, ',', '.'); ?></span>
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
    <!-- Filtros -->
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
                <label for="product_id">Produto</label>
                <select name="product_id" class="form-control">
                    <option value="">Selecione o Produto</option>
                    <?php foreach ($productNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($productId == $id) ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search">Buscar por Cliente ou ID do Pedido</label>
                <input type="text" name="search" class="form-control" value="<?= $search ?>" placeholder="Nome, ID do Pedido">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary" style="margin-top: 22px;">Filtrar</button>
                <a href="?" class="btn btn-secondary" style="margin-top: 22px;">Limpar Filtro</a>
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
                                <span>Cliente</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Vendedor</span>
                            </div> 
                            <div class="nk-tb-col">
                                <span>Produto</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Data do Pagamento</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>ID do Pedido</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Data do Pedido</span>
                            </div>
                            <div class="nk-tb-col">
                                <span>Valor Total</span>
                            </div>
                        </div>

                        <!-- Listagem de Invoices -->
                        <?php if (!empty($formattedInvoices)): ?>
                            <?php foreach ($formattedInvoices as $invoice): ?>
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $invoice['customer_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $invoice['user_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $invoice['product_name']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= date('d/m/Y H:i:s', strtotime($invoice['paid_at'])); ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= $invoice['order_id']; ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead"><?= date('d/m/Y H:i:s', strtotime($invoice['created_order'])); ?></span>
                                    </div>
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">R$ <?= number_format($invoice['total'], 2, ',', '.'); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">Nenhuma recorrência encontrada.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="card-inner">
                <ul class="pagination">
                        <!-- Botão para a primeira página -->
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1&<?= $pagination['pagination_query_params'] ?>">Primeira</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['previous_page'] ?>&<?= $pagination['pagination_query_params'] ?>">Anterior</a>
                            </li>
                        <?php endif; ?>

                        <!-- Exibir apenas algumas páginas -->
                        <?php for ($i = $pagination['start_page']; $i <= $pagination['end_page']; $i++): ?>
                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&<?= $pagination['pagination_query_params'] ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botão para a próxima e última página -->
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['next_page'] ?>&<?= $pagination['pagination_query_params'] ?>">Próxima</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['total_pages'] ?>&<?= $pagination['pagination_query_params'] ?>">Última</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>


</content>