<content>
<?php use Backend\Enums\Order\EOrderStatus; ?>
<?php
    // Mapeando o status para texto e cores
    $statusText = '';
    $statusClass = '';

    switch (strtolower($recurrence->status)) {
        case 'active':
            $statusText = 'Ativo';
            $statusClass = 'success';
            break;
        case 'pedding':
            $statusText = 'Pendente';
            $statusClass = 'warning';
            break;
        case 'canceled':
            $statusText = 'Cancelado';
            $statusClass = 'danger';
            break;
        default:
            $statusText = 'Desconhecido';
            $statusClass = 'secondary';
            break;
    }
?>
<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-content-wrap">
                <div class="nk-block-head">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="<?= site_url() ?>/recurrence">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Minhas Vendas</span>
                        </a>
                    </div>
                    <div class="nk-block-between-md g-4">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">Assinatura <?= $recurrence->id ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Status, Periodicidade, Forma de pagamento, Data de início -->
                <div class="col-lg-12">
                    <div class="row mt-3 mb-4">
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Status</p>
                                            <h4 class="my-1 text-primary">
                                                <div class="col">
                                                    <span class="btn btn-outline-<?= $statusClass ?> outbtn-small"><?= $statusText ?></span>
                                                </div>
                                            </h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                                            <i class="fa fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Periodicidade</p>
                                            <h4 class="my-1 text-warning">
                                                <div class="col">
                                                    <span class="cancled-orders">A cada 
                                                    <?php
                                                            // Inicializando o intervalo em branco
                                                            $interval = '';

                                                            // Verificando o valor de $recurrence->interval e ajustando conforme a necessidade
                                                            switch ($recurrence->interval) {
                                                                case 'day':
                                                                    $interval = ($recurrence->interval_count > 1) ? 'dias' : 'dia';
                                                                    break;
                                                                case 'week':
                                                                    $interval = ($recurrence->interval_count > 1) ? 'semanas' : 'semana';
                                                                    break;
                                                                case 'month':
                                                                    $interval = ($recurrence->interval_count > 1) ? 'meses' : 'mês';
                                                                    break;
                                                                case 'year':
                                                                    $interval = ($recurrence->interval_count > 1) ? 'anos' : 'ano';
                                                                    break;
                                                                default:
                                                                    $interval = 'intervalo indefinido';
                                                            }

                                                            // Exibir o valor da contagem e o intervalo no formato correto
                                                            echo $recurrence->interval_count . ' ' . $interval;
                                                        ?>
                                                        </span>
                                                </div>
                                            </h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                                            <i class="fa fa-dollar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Forma de pagamento</p>
                                            <h4 class="my-1 text-info">
                                                <div class="col">
                                                    <span class="total-orders">Cartão de Crédito</span>
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
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Data de início</p>
                                            <h4 class="my-1 text-info">
                                                <div class="col">
                                                    <span class="total-orders">    
                                                    <?php echo date("d/m/Y - H:i:s", strtotime($recurrence->created_at ?? '')) ?>
                                                    </span>
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
                    </div>
                </div>

                <!-- Informações da Transação -->
                <div class="nk-block">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card card-bordered">
                                <div class="card-inner-group">
                                    <div class="card-inner">
                                        <div class="sp-plan-desc sp-plan-desc-mb">
                                            <ul class="row gx-1">
                                                <li class="col-sm-4">
                                                    <p><span class="text-soft">Id da Transação</span></p>
                                                    <?= $recurrence->order->transaction_id ?>
                                                </li>
                                                <li class="col-sm-4">
                                                    <p><span class="text-soft">Criação</span></p>
                                                    <?= date("d/m/Y - H:i:s", strtotime($recurrence->created_at)) ?>
                                                </li>
                                                <li class="col-sm-4">
                                                    <p><span class="text-soft">Atualizado</span></p>
                                                    <?= date("d/m/Y - H:i:s", strtotime($recurrence->updated_at)) ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Informações dos Produtos -->
                                    <div class="card-inner">
                                        <div class="sp-plan-head-group">
                                            <div class="sp-plan-head">
                                                <div class="nk-tb-list">
                                                    <div class="nk-tb-item nk-tb-head">
                                                        <div class="nk-tb-col">
                                                            <span>Nome</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span>Val. unit.</span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <span>Val. total</span>
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-item tr">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-product">
                                                                <img src="<?= $recurrence->order->product()->image ?>" alt="" class="thumb">
                                                                <span class="title"><?= $recurrence->order->product()->name ?></span>
                                                            </span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="tb-sub">R$ <?= currency($recurrence->order->product()->price) ?></span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <span class="tb-sub">R$ <?= currency($recurrence->order->total) ?></span>
                                                        </div>
                                                    </div>

                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fatura -->
                                    <div class="invoice-bills table-responsive">
                                        <table class="table table-striped">
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" style="font-size: 16px; font-weight: 600;"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="font-size: 12px;font-weight: 400;">Subtotal</td>
                                                    <td style="font-size: 14px;color: #798bff;font-weight: 400;">R$ <?= currency($recurrence->order->total) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="font-size: 12px;font-weight: 400;">Taxa</td>
                                                    <td style="font-size: 14px;color: #e85347;font-weight: 400;">- R$ <?= currency($recurrence->order->total - $recurrence->order->total_seller) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="font-size: 16px; font-weight: 600;">Valor Líquido</td>
                                                    <td style="font-size: 14px;color: #4ab7a8;font-weight: 600;">R$ <?= currency($recurrence->order->total_seller) ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes do Cliente -->
                        <div class="col-xl-4">
                            <div class="card card-bordered">
                                <div class="nk-help-plain card-inner">
                                    <div class="card-inner">
                                        <h5 class="card-title">Detalhes do Cliente</h5>
                                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-user-alt"></em></strong> <?= $recurrence->customer->name ?></span>
                                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-mail"></em></strong> <?= $recurrence->customer->email ?></span>
                                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-call"></em></strong> <a href="https://api.whatsapp.com/send?phone=55<?= $recurrence->customer->phone ?>"><?= $recurrence->customer->phone ?></a></span>
                                        <span class="order-details d-block"><strong class="mw-85"><em class="icon ni ni-file-check"></em></strong> <?= $recurrence->customer->cpf_cnpj ?></span>
                                    </div>
                                </div>

                                <!-- Detalhes do Pagamento -->
                              
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção de UTM e Faturas -->
                <?php if ($recurrence->order->user_id == $user->id) : ?>
                    <div class="nk-content-wrap mt-2">
                        <div class="nk-block">
                            <div class="card card-bordered sp-plan">
                                <div class="card-inner">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#resumo">
                                                <em class="icon ni ni-user"></em>
                                                <span>Faturas</span>
                                            </a>
                                        </li>
                                       
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="resumo">
                                            <div class="col-12 col-md-12">
                                                <div class="d-flex">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">ID da Fatura</th>
                                                                <th scope="col">Data de Criação</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col">Data de Pagamento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($invoices as $invoice): ?>
                                                                <tr>
                                                                    <td><?= $invoice->id ?></td>
                                                                    <td><?= date("d/m/Y", strtotime($invoice->created_at)) ?></td>
                                                                    <td>
                                                                        <?php if ($invoice->paid == 1): ?>
                                                                            <span class="badge badge-sm badge-dim bg-outline-success d-none d-md-inline-flex">Pago</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-sm badge-dim bg-outline-warning d-none d-md-inline-flex">Não Pago</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($invoice->paid_at): ?>
                                                                            <?= date("d/m/Y", strtotime($invoice->paid_at)) ?>
                                                                        <?php else: ?>
                                                                            <span>N/A</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</content>
