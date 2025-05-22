<?php

use Backend\Enums\AwardRequest\EAwardRequestStatus;

 ?>
<title>Clientes</title>

<content>
  <div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">Lista de premiados</h3>
          <div class="nk-block-des text-soft">
          </div>
        </div>
        <div class="nk-block-head-content">
          <div class="toggle-wrap nk-block-tools-toggle">
            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
              <em class="icon ni ni-menu-alt-r"></em>
            </a>
           
          </div>
        </div>
      </div>
    </div>
    <div class="nk-block">
      <div class="card card-bordered">
        <div class="card-inner-group">
          
          <div class="card-inner p-0">
            <div class="nk-tb-list">
              <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col nk-tb-col-check">
                  <div class="custom-control custom-control-sm custom-checkbox notext">
                    <input type="checkbox" class="custom-control-input" id="pid">
                    <label class="custom-control-label" for="pid"></label>
                  </div>
                </div>
                <div class="nk-tb-col">
                  <span>Cliente</span>
                </div>
                <div class="nk-tb-col tb-col-sm">
                  <span>Endereço</span>
                </div>
               <div class="nk-tb-col">
                  <span>Premiação</span>
                </div>
                <div class="nk-tb-col tb-col-md">
                  <span>Ações</span>
                </div>
              </div>
              <?php foreach ($award_requests as $key => $award_request):
                $kyc = $award_request->user->kyc; ?>
                <div class="nk-tb-item tr">
                  <div class="nk-tb-col nk-tb-col-check">
                    <div class="custom-control custom-control-sm custom-checkbox notext">
                      <input type="checkbox" class="custom-control-input" id="<?php echo $key; ?>">
                      <label class="custom-control-label" for="<?php echo $key; ?>"></label>
                    </div>
                  </div>
                  <div class="nk-tb-col">
                    <a class="user-card">
                      <div class="user-avatar">
                        <img src="/images/default.png" alt="">
                      </div>
                      <div class="user-info">
                        <span class="tb-lead">
                            <?php echo $award_request->user->name; ?>
                            <span class="dot dot-success d-md-none ms-1"></span>
                        </span>
                        <span><?php echo $award_request->user->email; ?></span>
                      </div>
                    </a>
                  </div>
                  <div class="nk-tb-col  tb-col-sm">
                    <span class="tb-sub"><?= $kyc ? "$kyc->street, $kyc->address_no $kyc->neighborhood $kyc->city/$kyc->state - $kyc->zipcode" : '' ?></span>
                  </div>
                  <div class="nk-tb-col  tb-col-sm">
                    <span class="tb-sub">10mil</span>
                  </div>
                  <div class="nk-tb-col nk-tb-col-tools">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                            data-bs-toggle="dropdown">
                            <em class="icon ni ni-more-h"></em>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 270px;">
                            <ul class="link-list-opt no-bdr">
                                <li>
                                    <a href="javascript:;" click="adminAwardSentOnClick" data-id="<?= $award_request->id ?>">
                                        <em class="icon ni ni-map-pin"></em>
                                        <span><?= __('Marcar como postado nos correios') ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;" click="adminAwardCanceledOnClick" data-id="<?= $award_request->id ?>">
                                        <em class="icon ni ni-trash"></em>
                                        <span><?= __('Cancelar') ?></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                   

                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <Pagination />
        </div>
      </div>
    </div>
  </div>

</content>