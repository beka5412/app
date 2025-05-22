<title>Clientes</title>
<content>
      <div class="nk-content-body" data-select2-id="21">
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">
                <font style="vertical-align: inherit;">
                  <font style="vertical-align: inherit;">Usuários</font>
                </font>
              </h3>
              <div class="nk-block-des text-soft">
                <p>
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">Você tem <?php echo $total; ?> usuários</font>
                  </font>
                </p>
              </div>
            </div>
          
          </div>
        </div>
              <div class="mb-4 grid-filter">
                <div class=" mb-3">
                  <div class="form-control-wrap">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                          <em class="icon ni ni-search"></em>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="Buscar por CPF, transação, e-mail ou nome..." required="">
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <a href="#" class="btn btn-primary">
                    <em class="icon ni ni-opt-dot-alt"></em>
                    <span>Filtrar</span>
                  </a>
                </div>
              </div>
        <div class="nk-block" data-select2-id="20">
          <div class="card card-bordered card-stretch" data-select2-id="19">
            <div class="card-inner-group" data-select2-id="18">
              <div class="card-inner p-0">
                <div class="nk-tb-list nk-tb-ulist">
                  <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col nk-tb-col-check">
                      <div class="custom-control custom-control-sm custom-checkbox notext">
                        <input type="checkbox" class="custom-control-input" id="uid">
                        <label class="custom-control-label" for="uid"></label>
                      </div>
                    </div>
                    <div class="nk-tb-col">
                      <span class="sub-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Do utilizador</font>
                        </font>
                      </span>
                    </div>
                    <div class="nk-tb-col tb-col-mb">
                      <span class="sub-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Carteira</font>
                        </font>
                      </span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="sub-text">Telefone</span>
                    </div>
                    <div class="nk-tb-col tb-col-lg">
                      <span class="sub-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">KYC</font>
                        </font>
                      </span>
                    </div>
                    <div class="nk-tb-col tb-col-lg">
                      <span class="sub-text">Último Login</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                      <span class="sub-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Status</font>
                        </font>
                      </span>
                    </div>
                    <div class="nk-tb-col nk-tb-col-tools text-end"></div>
                  </div>
                  <?php foreach ($clients as $key => $client) : ?>
                      <div class="nk-tb-item">
                        <div class="nk-tb-col nk-tb-col-check">
                          <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="uid1">
                            <label class="custom-control-label" for="uid1"></label>
                          </div>
                        </div>
                        <div class="nk-tb-col">
                          <a href="<?php echo site_url(); ?>/admin/customer/<?php echo $client->id; ?>/show">
                            <div class="user-card">
                              <div class="user-avatar bg-primary">
                                <span>
                                    <img src="https://app.migraz.com<?php echo $client->photo; ?>" />
                                </span>
                              </div>
                              <div class="user-info">
                                <span class="tb-lead">
                                  <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;"><?php echo $client->name; ?></font>
                                  </font>
                                  <span class="dot dot-success d-md-none ms-1"></span>
                                </span>
                                <span>
                                  <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;"><?php echo $client->email; ?></font>
                                  </font>
                                </span>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div class="nk-tb-col tb-col-mb">
                          <span class="tb-amount">
                            <font style="vertical-align: inherit;">
                              <div>
                                  <span class="fs-12px text-soft">Disponível:</span> <font style="vertical-align: inherit;">R$ <?php echo number_format($client->balance->available, 2, ",", ".");  ?> </font>
                              </div>
                              <div>
                                  <span class="fs-12px text-soft">Lançamentos futuros:</span> <font style="vertical-align: inherit;">R$ <?php echo number_format($client->balance->future_releases, 2, ",", ".");  ?> </font>
                              </div>
                            </font>
                          </span>
                        </div>
                        <div class="nk-tb-col tb-col-md">
                          <span>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">-</font>
                            </font>
                          </span>
                        </div>
                        <div class="nk-tb-col tb-col-lg">
                          <ul class="list-status">

                            <li>
                            <?php if ($client->kyc_confirmed == '0'): ?>
                              <em class="icon ni ni-alert-circle"></em>
                              <span>
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">Não Verificado</font>
                                </font>
                              </span>
                                      <?php elseif ($client->kyc_confirmed == '1'): ?>
                                        <em class="icon text-success ni ni-check-circle"></em>
                              <span>
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">Verificado</font>
                                </font>
                              </span>
                                        <?php endif; ?>
                            </li>
                          </ul>
                        </div>
                        <div class="nk-tb-col tb-col-lg">
                          <span>
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;"><?php echo $client->updated_at; ?></font>
                            </font>
                          </span>
                        </div>
                        <div class="nk-tb-col tb-col-md">
                          <span class="tb-status text-success">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;"><?php echo $client->status; ?></font>
                            </font>
                          </span>
                        </div>
                        <div class="nk-tb-col nk-tb-col-tools">
                          <ul class="nk-tb-actions gx-1">
                            <li>
                              <div class="drodown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                                  <em class="icon ni ni-more-h"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                  <ul class="link-list-opt no-bdr">
                                    <li>
                                      <a href="<?php echo site_url(); ?>/admin/customer/<?php echo $client->id; ?>/show">
                                        <em class="icon ni ni-eye"></em>
                                        <span>
                                          <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">Ver detalhes</font>
                                          </font>
                                        </span>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <em class="icon ni ni-repeat"></em>
                                        <span>
                                          <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">Historico</font>
                                          </font>
                                        </span>
                                      </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                      <a href="#">
                                        <em class="icon ni ni-shield-star"></em>
                                        <span>
                                          <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">Redefinir Senha</font>
                                          </font>
                                        </span>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <em class="icon ni ni-na"></em>
                                        <span>
                                          <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">Suspender usuário</font>
                                          </font>
                                        </span>
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
                </div>
              </div>
              <Pagination />
              <div class="card-inner">
                <div class="nk-block-between-md g-3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

</content>