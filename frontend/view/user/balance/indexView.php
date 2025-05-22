<?php

use Backend\Enums\Withdrawal\EWithdrawalStatus;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Withdrawal\EWithdrawalTransferType;
?>

<title>Financeiro</title>

<content>
  <script type="application/json" json-id="config">
    <?php echo json_encode([
      "minimum_withdrawal" => get_setting('minimum_withdrawal'),
      "bank_account" => $bank_account,
      "kyc_confirmed" => $user->kyc_confirmed ? true : false
    ]); ?>
  </script>
  <div class="row mb-3">
    <div class="col-lg-8">
      <div class="row mt-3 mb-4">
        <div class="col-lg-12 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div>
                  <p class="mb-0 text-secondary">Saldo Disponível</p>
                  <h4 class="my-1 text-success">
                    <div class="col">
                      <span class="completed-orders" style=" font-size: 32px; "><?php echo __('R$') ?> <?php echo currency($balance->available ?? 0); ?></span>
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
        <div class="col">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div>
                  <p class="mb-0 text-secondary">Lançamentos futuros</p>
                  <h4 class="my-1 text-primary">
                    <div class="col">
                      <span class="pending-orders"><?php echo __('R$') ?> <?php echo currency($balance->future_releases ?? 0); ?></span>
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
                  <p class="mb-0 text-secondary">Saque em análise</p>
                  <h4 class="my-1 text-warning">
                    <div class="col">
                      <span class="cancled-orders"><?php echo __('R$') ?> <?php echo currency($balance->withdrawal_requested ?? 0); ?></span>
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
                  <p class="mb-0 text-secondary">Reservado</p>
                  <h4 class="my-1 text-info">
                    <div class="col">
                      <span class="total-orders"><?php echo __('R$') ?> <?php echo currency($balance->reserved_as_guarantee ?? 0); ?></span>
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
    <div class="col-md-4">
      <div class="card  mt-3 mb-4">
        <div class="card-body">
          <div class="form-group frm_request_withdrawal">
            <select class="d-none form-control inp_w_transfer_type">
              <option selected value="<?php echo EWithdrawalTransferType::PIX->value; ?>">Pix</option>
              <option value="<?php echo EWithdrawalTransferType::BANK->value; ?>">TED</option>
            </select>
            <label class="form-label" for="default-1-03">Saque</label>
            <p>Insira o valor para receber via chave PIX.</p>
            <input class="inp_withdrawal_amount form-control" data-withdrawal-fee="<?php echo $withdrawal_fee; ?>" keyup="calcWithdrawal" keydown="$onlyNumbers" blur="$inputCurrencyAlways" load="element.value = currency(element.value)" />
            <div class="mt-2">O saque será de: R$ <span class="span_result_withdrawal">0</span></div>
            <button type="button" click="withdrawRequestOnClick" class="btn btn-primary mt-4 w-100 text-center justify-content-center">
              <em class="icon ni ni-coin-alt me-1"></em> Solicitar Saque
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="nk-block nk-block-lg">
    <div class="card card-bordered card-stretch">
      <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" data-bs-toggle="tab" href="#lancamento" aria-selected="false" role="tab">
            <em class="icon ni ni-tranx-fill"></em>
            <span>Extrato</span>
          </a>
        </li>
        <!-- <li class="nav-item" role="presentation">
          <a class="nav-link" data-bs-toggle="tab" href="#bank" aria-selected="true" role="tab">
            <em class="icon ni ni-coin-alt"></em>
            <span>Dados bancários</span>
          </a>
        </li> -->
        <!-- <li class="nav-item" role="presentation">
          <a class="nav-link" data-bs-toggle="tab" href="#saque" aria-selected="false" role="tab" tabindex="-1">
            <em class="icon ni ni-wallet-out"></em>
            <span>Histórico de saques</span>
          </a>
        </li>
        
        <li class="nav-item" role="presentation">
          <a class="nav-link" data-bs-toggle="tab" href="#taxs" aria-selected="false" role="tab">
            <em class="icon ni ni-sign-mxn"></em>
            <span>Taxas</span>
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" data-bs-toggle="tab" href="#withdrawal" aria-selected="false" role="tab">
            <em class="icon ni ni-sign-mxn"></em>
            <span>Saque</span>
          </a>
        </li> -->
      </ul>
      <div class="tab-content">
        <div class="tab-pane active show" id="lancamento" role="tabpanel">
          <table class="table table-tranx table-billing">
            <thead>
              <tr class="tb-tnx-head">
                <th scope="col">Valor</th>
                <th scope="col">Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($info as $row): ?>
                <tr class="tb-tnx-item">
                  <td> 
                    <a>R$ <?= currency($row->operation == 'D' ? $row->amount * -1 : $row->amount) ?></a>
                  </td>
                  <td>
                    <?php if ($row->type === 'available'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Balance available') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'blocked'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Blocked') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'withdrawn'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Withdraw') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'pending'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Pending') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'withdrawal_requested'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Withdrawal request') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'reserved_as_guarantee'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Reserved as guarantee') ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php elseif ($row->type === 'future_release'): ?>
                      <?= (__($row->operation == 'C' ? 'Added' : 'Removed')) . ' | ' . __('Future release for') ?> <?= explode(" ", date_br($row->scheduled_at ?? today()))[0] ?? '' ?>
                      <?= ($row->description ?? false) ? '(' . __($row->description ?? '') . ')' : '' ?>
                    <?php endif ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <Pagination />
        </div>
        <div class="tab-pane" id="saque" role="tabpanel">
          <div class="nk-block">
            <div class="card card-bordered">
              <table class="table table-tranx table-billing">
                <thead>
                  <tr class="tb-tnx-head">
                    <th class="tb-tnx-id">
                      <span class="">#</span>
                    </th>
                    <th class="tb-tnx-info">
                      <span class="tb-tnx-desc d-none d-sm-inline-block">
                        <span>Conta de destino</span>
                      </span>
                      <span class="tb-tnx-date d-md-inline-block d-none">
                        <span class="d-none d-md-block">
                          <span>Data do pedido</span>
                          <span>Efetivado em</span>
                        </span>
                      </span>
                    </th>
                    <th class="tb-tnx-amount">
                      <span class="tb-tnx-total">Total</span>
                      <span class="tb-tnx-status d-none d-md-inline-block">Status</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($withdrawals as $withdrawal) : ?>
                    <tr class="tb-tnx-item">
                      <td class="tb-tnx-id">
                        <a href="#">
                          <span><?php echo $withdrawal->id; ?></span>
                        </a>
                      </td>
                      <td class="tb-tnx-info">
                        <div class="tb-tnx-desc">
                          <span"><?php echo $user->name; ?></span>
                        </div>
                        <div class="tb-tnx-date">
                          <span class="date"><?php echo date("d/m/Y - H:i:s", strtotime($withdrawal->created_at ?? '')) ?></span>
                          <span class="date">
                            <?php
                            switch ($withdrawal->status)
                            {
                              case EWithdrawalStatus::PENDING->value:
                            ?>-<?php
                                break;

                              case EWithdrawalStatus::APPROVED->value:
                                ?><?php echo date("d/m/Y - H:i:s", strtotime($withdrawal->approved_at ?? '')) ?>
                            <?php
                                break;

                              case EWithdrawalStatus::CANCELED->value:
                            ?>-<?php
                                break;
                            }
                                ?>
                          </span>
                        </div>
                      </td>
                      <td class="tb-tnx-amount">
                        <div class="tb-tnx-total">
                          <span class="amount"><?php echo __('R$') ?> <?php echo currency($withdrawal->total ?? 0); ?></span>
                        </div>
                        <div class="tb-tnx-status">
                          <?php
                          switch ($withdrawal->status)
                          {
                            case EWithdrawalStatus::PENDING->value:
                          ?><span class="badge badge-dot bg-warning">Pendente</span><?php
                                                                                    break;

                                                                                  case EWithdrawalStatus::APPROVED->value:
                                                                                    ?><span class="badge badge-dot bg-success">Pago</span><?php
                                                                                                                                          break;

                                                                                                                                        case EWithdrawalStatus::CANCELED->value:
                                                                                                                                          ?><span class="badge badge-dot bg-danger">Rejeitado</span><?php
                                                                                                                                                                                                    break;
                                                                                                                                                                                                }
                                                                                                                                                                                                    ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="bank" role="tabpanel">
          <div class="card-inner pt-0">
            
          </div>
        </div>
        <div class="tab-pane" id="taxs" role="tabpanel">


          <div class="card-inner pt-0">
            <div class="nk-block-head">
              <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Suas taxas</h4>

                <div class="nk-block">
                  <div class="row g-gs">
                    <div class="col-md-6 col-xxl-3">
                      <div class="card card-bordered pricing recommend">
                        <span class="pricing-badge badge bg-primary">Plano Ativo</span>
                        <div class="pricing-head">
                          <div class="pricing-title">
                            <h4 class="card-title title">D+14</h4>
                            <p class="sub-text">Pagamento em 14 dias corridos</p>
                          </div>
                          <div class="card-text">
                            <div class="row">
                              <div class="col-6">
                                <span class="h4 fw-500">7,99%</span>
                                <span class="sub-text">Taxa</span>
                              </div>
                              <div class="col-6">
                                <span class="h4 fw-500"><?php echo __('US$') ?> 2,50</span>
                                <span class="sub-text">por transação aprovada</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="pricing-body">
                          <ul class="pricing-features">
                            <li>
                              <span class="w-50">Cartão de Crédito</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>14 Dias</span></span></span>
                            </li>
                            <li>
                              <span class="w-50">Pix</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>NA HORA</span></span></span>
                            </li>
                            <li>
                              <span class="w-50">Boleto</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>3 Dias</span></span></span>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                      <div class="card card-bordered pricing recommend">
                        <div class="pricing-head">
                          <div class="pricing-title">
                            <h4 class="card-title title">D+2</h4>
                            <p class="sub-text">Pagamento em 2 dias corridos</p>
                          </div>
                          <div class="card-text">
                            <div class="row">
                              <div class="col-6">
                                <span class="h4 fw-500">9,99%</span>
                                <span class="sub-text">Taxa</span>
                              </div>
                              <div class="col-6">
                                <span class="h4 fw-500"><?php echo __('US$') ?> 2,50</span>
                                <span class="sub-text">por transação aprovada</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="pricing-body">
                          <ul class="pricing-features">
                            <li>
                              <span class="w-50">Cartão de Crédito</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>2 Dias</span></span></span>
                            </li>
                            <li>
                              <span class="w-50">Pix</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>NA HORA</span></span></span>
                            </li>
                            <li>
                              <span class="w-50">Boleto</span> - <span class="ms-auto"><span class="badge badge-dim bg-success"><span>3 Dias</span></span></span>
                            </li>
                          </ul>
                          <div class="pricing-action">
                            <button class="btn btn-outline-light">Solicitar D+2</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="row g-gs">
              <div class="example-alert">
                <div class="alert alert-info alert-icon">
                  <em class="icon ni ni-alert-circle"></em>
                  Atenção: nossa plataforma realizar uma reserva de segurança para cobrir reembolsos e chargebacks: 5% por 30 dias
                  <p>Ao aplicar para receber pagamentos via cartão em 2 dias a nossa equipe fará uma análise da sua conta</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="tab-pane frm_request_withdrawal" id="withdrawal" role="tabpanel">
          <div class="card-inner pt-0">
            <div class="nk-block-head">
              <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Saque</h4>

              </div>
            </div>
            <div class="card-inner pt-0">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="form-label" for="default-1-03">Taxa de saque</label>
                    <input type="text" class="form-control" id="default-1-03" disabled="" value="R$ 3,50">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="form-label" for="default-1-03">Valor disponível para saque</label>
                    <input type="text" class="form-control" id="default-1-03" disabled="" value="R$ <?php echo currency($balance->available ?? 0); ?>">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="form-label" for="default-1-03">Chave PIX</label>
                    <input type="text" class="form-control" id="default-1-03" disabled="" value="<?php echo $bank_account->pix ?? ''; ?>">
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="form-group">
                    <label class="form-label" for="default-1-03">Tipo de transferência</label>
                    <select class="form-control inp_w_transfer_type">
                      <option value="<?php echo EWithdrawalTransferType::PIX->value; ?>">Pix</option>
                      <option value="<?php echo EWithdrawalTransferType::BANK->value; ?>">TED</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mt-2">
                  <div class="form-group">
                    <label class="form-label" for="default-1-03">valor</label>

                    <input class="inp_withdrawal_amount form-control" data-withdrawal-fee="<?php echo $withdrawal_fee; ?>" keyup="calcWithdrawal" keydown="$onlyNumbers" blur="$inputCurrencyAlways" load="element.value = currency(element.value)" />
                    <button type="button" click="withdrawRequestOnClick" class="btn btn-primary mt-1">Sacar</button>
                    <div class="mt-2">O saque será de: R$ <span class="span_result_withdrawal">0</span></div>
                    <div>Taxa de saque R$ <?php echo currency($withdrawal_fee); ?></div>
                    <p>O valor mínimo de saque é de R$ <?php echo currency($minimum_withdrawal); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" id="modalWithdrawalRegisterPix">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cadastrar conta bancária</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="$('#modalWithdrawalRegisterPix').modal('hide');"></button>
        </div>
        <div class="modal-body">

          <form action="#" class="gy-3 form-settings frm_edit_bank_account">
            <div class="example-alert">
              <div class="alert alert-info alert-icon">
                <em class="icon ni ni-alert-circle"></em>
                Evite falhas no saque: a conta bancária deve ser de sua titularidade.
              </div>
            </div>

            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-name">Seu nome / Razão social</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_name" id="comp-name" value="<?php echo $bank_account->name ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-name">Documento</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_doc" id="comp-name" value="<?php echo $bank_account->doc ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-name">Banco</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <select class="form-select inp_bankacc_bank">
                      <?php foreach (banks() as $bank) : ?>
                        <option <?php if (($user_bank_id = $bank_account?->bank_id) && $user_bank_id == $bank?->id) : ?> selected="" <?php endif; ?> value="<?php echo $bank->id; ?>"><?php echo $bank->code; ?> - <?php echo $bank->name; ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-copyright">Tipo de conta</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <select class="form-select inp_bankacc_type">
                      <option <?php if (($bank_account->type ?? '') == 'current_account') : ?> selected="" <?php endif; ?> value="current">Conta Corrente</option>
                      <option <?php if (($bank_account->type ?? '') == 'savings_account') : ?> selected="" <?php endif; ?> value="savings">Conta Poupança</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-email">Agência</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_agency" id="comp-email" value="<?php echo $bank_account->agency ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-copyright">Conta</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_account" id="comp-copyright" value="<?php echo $bank_account->account ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-copyright">Dígito Conta</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_digit" id="comp-copyright" value="<?php echo $bank_account->digit ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-copyright">Tipo de chave pix</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <select class="form-select inp_bankacc_pix_type">
                      <option <?php if (($bank_account->pix_type ?? '') == 'cpf') : ?> selected="" <?php endif; ?> value="cpf">CPF</option>
                      <option <?php if (($bank_account->pix_type ?? '') == 'cnpj') : ?> selected="" <?php endif; ?> value="cnpj">CNPJ</option>
                      <option <?php if (($bank_account->pix_type ?? '') == 'phone') : ?> selected="" <?php endif; ?> value="phone">Telefone</option>
                      <option <?php if (($bank_account->pix_type ?? '') == 'email') : ?> selected="" <?php endif; ?> value="email">E-mail</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-3 align-center">
              <div class="col-lg-5">
                <div class="form-group">
                  <label class="form-label" for="comp-copyright">Chave Pix</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <div class="form-control-wrap">
                    <input type="text" class="form-control inp_bankacc_pix" id="comp-copyright" value="<?php echo $bank_account->pix ?? ''; ?>">
                  </div>
                </div>
              </div>
            </div>
          </form>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-id="">Cancelar</button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" click="bankAccountOnSubmit" data-id="">Registrar</button>
        </div>
      </div>
    </div>
  </div>

</content>