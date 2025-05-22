<?php use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Kyc\{EKycStatus, EKycType}; ?>

<title>Usuário</title>
<content>

<div class="nk-content-body">
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">
          <font style="vertical-align: inherit;">
            <font style="vertical-align: inherit;">Usuário / <strong class="text-primary small"><?php echo $user->kyc->name; ?></strong></font>
          </font>
        </h3>
        <div class="nk-block-des text-soft">
          <p>
            <font style="vertical-align: inherit;"></font>
          </p>
        </div>
      </div>
      <div class="nk-block-head-content">
        <a href="https://app.migraz.com/admin/customers/" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
          <em class="icon ni ni-arrow-left"></em>
          <span>
            <font style="vertical-align: inherit;">
              <font style="vertical-align: inherit;">Voltar</font>
            </font>
          </span>
        </a>
        <a href="https://app.migraz.com/admin/customers/" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
          <em class="icon ni ni-arrow-left"></em>
        </a>
      </div>
    </div>
  </div>
  <div class="nk-block">
    <div class="row g-gs">
      <div class="col-lg-4 col-xl-4 col-xxl-3">
        <div class="card card-bordered">
          <div class="card-inner-group">
            <div class="card-inner">
              <div class="user-card user-card-s2">
                <div class="user-avatar lg bg-primary">
                  <img src="https://app.migraz.com<?php echo $user->photo; ?>" alt="">
                </div>
                <div class="user-info">
                  <h5>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->kyc->name; ?></font>
                    </font>
                  </h5>
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->email; ?></font>
                    </font>
                  </span>
                </div>
              </div>
            </div>
            <div class="card-inner card-inner-sm">
              <ul class="btn-toolbar justify-center gx-1">
                <li>
                  <a href="#" class="btn btn-trigger btn-icon">
                    <em class="icon ni ni-shield-off"></em>
                  </a>
                </li>
                <li>
                  <a href="#" class="btn btn-trigger btn-icon">
                    <em class="icon ni ni-mail"></em>
                  </a>
                </li>
                <li>
                  <a href="#" class="btn btn-trigger btn-icon">
                    <em class="icon ni ni-bookmark"></em>
                  </a>
                </li>
                <li>
                  <a href="#" class="btn btn-trigger btn-icon text-danger">
                    <em class="icon ni ni-na"></em>
                  </a>
                </li>
              </ul>
            </div>
           
            <div class="card-inner">
              <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">ID do usuário:</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->id ?> </font>
                    </font>
                  </span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Telefone:</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->kyc->phone; ?></font>
                    </font>
                  </span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Endereço</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->kyc->street; ?> - <?php echo $user->kyc->address_no; ?> , <?php echo $user->kyc->neighborhood; ?>,  <?php echo $user->kyc->city; ?>/ <?php echo $user->kyc->state; ?> - <?php echo $user->kyc->zipcode; ?> </font>
                    </font>
                  </span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Último login:</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->updated_at ?></font>
                    </font>
                  </span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Status KYC:</font>
                    </font>
                  </span>                  
                    <?php if ($user->kyc_confirmed == '0'): ?>
                          <span>
                            <em class="icon ni ni-alert-circle"></em>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">Não Verificado</font>
                            </font>
                          </span>
                    <?php elseif ($user->kyc_confirmed == '1'): ?>
                          <span class="lead-text text-success">
                          <em class="icon text-success ni ni-check-circle"></em>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">Verificado</font>
                            </font>
                          </span>
                    <?php endif; ?>

                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Status da conta:</font>
                    </font>
                  </span>
                  <?php if ($user->account_under_analysis == '1'): ?>
                          <span class="lead-text text-warning">
                            <em class="icon ni ni-alert-circle"></em>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">Em analise</font>
                            </font>
                          </span>
                    <?php elseif ($user->account_under_analysis == '0'): ?>
                          <span class="lead-text text-success">
                          <em class="icon text-success ni ni-check-circle"></em>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">Boa</font>
                            </font>
                          </span>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Percentual de ChargeBack:</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->chargeback_percent ?></font>
                    </font>
                  </span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-12">
                  <span class="sub-text">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Registrou em:</font>
                    </font>
                  </span>
                  <span>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?php echo $user->created_at ?></font>
                    </font>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-8 col-xl-8 col-xxl-9">
        <div class="card card-bordered">
          <div class="card-inner">
            <div class="nk-block">
              <div class="overline-title-alt mb-2 mt-2">
                <font style="vertical-align: inherit;">
                  <font style="vertical-align: inherit;">Carteira</font>
                </font>
              </div>
              <div class="profile-balance">
                <div class="profile-balance-group gx-4">
                  <div class="profile-balance-sub">
                    <div class="profile-balance-amount">
                      <div class="number">
                        <small class="currency currency-usd">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">R$</font>
                          </font>
                        </small>
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;"><?php echo number_format($user->balance->available, 2, ",", ".");  ?></font>
                        </font>
                      </div>
                    </div>
                    <div class="profile-balance-subtitle">Saldo Disponivel</div>
                  </div>
                  <div class="profile-balance-sub">
                    <span class="profile-balance-plus text-soft">
                      <em class="icon ni ni-plus"></em>
                    </span>
                    <div class="profile-balance-amount">
                      <div class="number">
                        <small class="currency currency-usd">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">R$</font>
                          </font>
                        </small>
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;"><?php echo number_format($user->balance->future_releases, 2, ",", ".");  ?> </font>
                        </font>
                      </div>
                    </div>
                    <div class="profile-balance-subtitle">Saldo a Liberar</div>
                  </div>
                  <div class="profile-balance-sub">
                  <span class="profile-balance-plus text-soft">
                      <em class="icon ni ni-plus"></em>
                    </span>
                    <div class="profile-balance-amount">
                      <div class="number">
                        <small class="currency currency-usd">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">R$</font>
                          </font>
                        </small>
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;"><?php echo number_format($user->balance->reserved_as_guarantee, 2, ",", ".");  ?></font>
                        </font>
                      </div>
                    </div>
                    <div class="profile-balance-subtitle">Reservado</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="nk-block">
              <h6 class="lead-text mb-3">Ultimas Vendas</h6>
              <div class="nk-tb-list border round-sm">
                <div class="nk-tb-item nk-tb-head">
                  <div class="nk-tb-col">
                    <span class="sub-text">Pedido</span>
                  </div>
                  <div class="nk-tb-col tb-col-sm">
                    <span class="sub-text">Produto</span>
                  </div>
                  <div class="nk-tb-col tb-col-xxl">
                    <span class="sub-text">Valor</span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Status</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">Criado em</span>
                  </div>
                </div>

                <?php foreach ($user->lastApprovedOrders()->limit(5)->get() as $lastOrder) { ?>
                    <div class="nk-tb-item">
                      <div class="nk-tb-col">
                        <a href="#">
                          <span class="fw-bold">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;"># <?php echo $lastOrder->id; ?></font>
                            </font>
                          </span>
                        </a>
                      </div>
                      <div class="nk-tb-col tb-col-sm">
                        <span class="tb-product">
                          <img src="<?php echo $lastOrder->product()?->image; ?>" alt="" class="thumb">
                          <span class="title">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;"><?php echo $lastOrder?->product()?->name ?? ''; ?></font>
                            </font>
                          </span>
                        </span>
                      </div>
                      <div class="nk-tb-col tb-col-xxl">
                        <span class="amount">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">R$ <?php echo $lastOrder->total; ?></font>
                          </font>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                          <?php if ($lastOrder->status == EOrderStatus::APPROVED->value) : ?>
                              <span class="lead-text text-success">Aprovado</span>
                          <?php endif; ?>
                      </div>
                      <div class="nk-tb-col">
                        <span class="sub-text">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lastOrder->created_at); ?></font>
                          </font>
                        </span>
                      </div>
                    </div>
                <?php } ?>

              </div>
            </div>

            <div class="nk-block">
              <h6 class="lead-text mb-3">
                <font style="vertical-align: inherit;">
                  <font style="vertical-align: inherit;">Últimos saques</font>
                </font>
              </h6>

              <div class="nk-tb-list border round-sm">
                <div class="nk-tb-item nk-tb-head">
                  <div class="nk-tb-col">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">EU IA</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col tb-col-xxl">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Valor</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Status</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">Data do saque</span>
                  </div>
                </div>

                <?php if($user->withLastDrawals !== null) { ?>
                    <?php foreach ($user->withLastDrawals as $withDrawal) { ?>
                    <div class="nk-tb-item">
                      <div class="nk-tb-col">
                        <a href="#">
                          <span class="fw-bold">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">#<?php echo $withDrawal->id; ?></font>
                            </font>
                          </span>
                        </a>
                      </div>
                      <div class="nk-tb-col tb-col-xxl">
                        <span class="amount">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">R$ <?php echo number_format($withDrawal->amount, 2, ",", ".");  ?></font>
                          </font>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="lead-text text-success">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Pagar</font>
                          </font>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="sub-text">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $withDrawal->created_at); ?></font>
                          </font>
                        </span>
                      </div>
                    </div>
                    <?php } ?>
                <?php } ?>
              </div>
            </div>

            <div class="nk-block">
              <h6 class="lead-text mb-3">Premiações</h6>
              <div class="nk-tb-list border round-sm">
                <div class="nk-tb-item nk-tb-head">
                  <div class="nk-tb-col">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">ID</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col tb-col-xxl">
                    <span class="sub-text">Premiação</span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Status</font>
                      </font>
                    </span>
                  </div>
                  <div class="nk-tb-col">
                    <span class="sub-text">Data da premiação</span>
                  </div>
                </div>

                <?php if($user->awardRequests !== null) { ?>
                    <?php foreach ($user->awardRequests as $award) { ?>
                    <div class="nk-tb-item">
                      <div class="nk-tb-col">
                        <a href="#">
                          <span class="fw-bold">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">#<?php echo $award->id; ?></font>
                            </font>
                          </span>
                        </a>
                      </div>
                      <div class="nk-tb-col tb-col-xxl">
                        <span class="amount">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo $award->award->name; ?></font>
                          </font>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="lead-text text-warning">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo $award->status; ?></font>
                          </font>
                        </span>
                      </div>
                      <div class="nk-tb-col">
                        <span class="sub-text">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $award->created_at); ?></font>
                          </font>
                        </span>
                      </div>
                    </div>
                    <?php } ?>
                <?php } ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</content>