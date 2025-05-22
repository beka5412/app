<content>
<?php if ((json_decode(json_encode($recurrences))->total ?? 0) == 0): ?>
    <div class="nk-block nk-block-middle wide-md mx-auto">
      <div class="nk-block-content nk-error-ld text-center">
        <center>
          <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> <lottie-player
            src="https://assets1.lottiefiles.com/packages/lf20_fKk8BKYneU.json" background="transparent" speed="1"
            style="width: 300px; height: 300px;" loop autoplay></lottie-player>
        </center>
        <div class="wide-xs mx-auto">
          <h3 class="nk-error-title">Não há assinaturas! : (</h3>
          <p class="nk-error-text">Quando surgir um novo pedido, você poderá gerenciá-los por aqui. </p>
         
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="nk-content-body">
      <!-- header page -->
      <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Minhas Assinaturas
            </h3>
          </div>
        
        </div>
      </div>

<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner p-0">
                <div class="nk-tb-list is-separate is-medium mb-3">
                    <?php if (isset($recurrences) && count($recurrences) > 0): ?>
                        <?php foreach ($recurrences as $recurrence): ?>
                            <div class="nk-tb-item tr" data-subscription-id="<?= $recurrence->id ?>">
                                <div class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input" id="oid<?= $recurrence->id ?>">
                                        <label class="custom-control-label" for="oid<?= $recurrence->id ?>"></label>
                                    </div>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <a href="/recurrence/<?= $recurrence->id ?>/show">
                                            <?= $recurrence->id ?>
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <div class="user-info">
                                        <a href="/recurrence/<?= $recurrence->id ?>/show">
                                            <span class="tb-lead"><?= $recurrence->customer->name ?></span>
                                            <span><?= $recurrence->customer->email ?></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead"><?= $recurrence->product->name ?? 'Produto desconhecido' ?></span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">R$ <?= number_format($recurrence->order->total, 2, ',', '.') ?></span>
                                </div>
                                <div class="nk-tb-col">
                                <?php if ($recurrence->status == 'canceled'): ?>
                                    <span class="badge badge-sm badge-dim bg-outline-danger d-none d-md-inline-flex">Cancelado</span>
                                <?php elseif ($recurrence->status == 'active'): ?>
                                    <span class="badge badge-sm badge-dim bg-outline-success d-none d-md-inline-flex">Ativo</span>
                                <?php elseif ($recurrence->status == 'pending'): ?>
                                    <span class="badge badge-sm badge-dim bg-outline-warning d-none d-md-inline-flex">Pendente</span>
                                <?php endif; ?>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub"><?= date('d/m/Y - H:i:s', strtotime($recurrence->created_at)) ?></span>
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
                        <p>Não há assinaturas cadastradas.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Navegação de Paginação -->
            <div class="card-inner">
                <div class="nk-block-between-md g-3">
                    <div class="g">
                        <ul class="pagination justify-content-center justify-content-md-start">
                            <?php for ($i = 1; $i <= $recurrences->lastPage(); $i++): ?>
                                <li class="page-item <?= $i == $recurrences->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

</div>
  <?php endif; ?>
</content>