<?php use Backend\Enums\Kyc\EKycStatus; ?>
<title>Kyc</title>

<content>
<div class="nk-content-body">
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Kyc</h3>
      </div>
    </div>
  </div>
  <div class="nk-block">
    <div class="nk-tb-list is-separate is-medium mb-3">
      <div class="nk-tb-item nk-tb-head">
        <div class="nk-tb-col nk-tb-col-check">
          <div class="custom-control custom-control-sm custom-checkbox notext">
            <input type="checkbox" class="custom-control-input" id="oid">
            <label class="custom-control-label" for="oid"></label>
          </div>
        </div>
        <div class="nk-tb-col">
          <span>Nome</span>
        </div>
        <div class="nk-tb-col">
          <span class="d-none d-sm-block">Status</span>
        </div>
        <div class="nk-tb-col">
          <span>Documento</span>
        </div>
        <!-- <div class="nk-tb-col tb-col-md">
          <span>Aprovado em</span>
        </div> -->
        <div class="nk-tb-col tb-col-md">
          <span>Criado em</span>
        </div>
        <div class="nk-tb-col tb-col-sm">
          <span></span>
        </div>
<!--        <div class="nk-tb-col nk-tb-col-tools">-->
<!--          <ul class="nk-tb-actions gx-1 my-n1">-->
<!--            <li>-->
<!--              <div class="drodown">-->
<!--                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger me-n1" data-bs-toggle="dropdown">-->
<!--                  <em class="icon ni ni-more-h"></em>-->
<!--                </a>-->
<!--                <div class="dropdown-menu dropdown-menu-end">-->
<!--                  <ul class="link-list-opt no-bdr">-->
<!--                    <li>-->
<!--                      <a href="#">-->
<!--                        <em class="icon ni ni-edit"></em>-->
<!--                        <span>Update Status</span>-->
<!--                      </a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                      <a href="#">-->
<!--                        <em class="icon ni ni-truck"></em>-->
<!--                        <span>Mark as Delivered</span>-->
<!--                      </a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                      <a href="#">-->
<!--                        <em class="icon ni ni-money"></em>-->
<!--                        <span>Mark as Paid</span>-->
<!--                      </a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                      <a href="#">-->
<!--                        <em class="icon ni ni-report-profit"></em>-->
<!--                        <span>Send Invoice</span>-->
<!--                      </a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                      <a href="#">-->
<!--                        <em class="icon ni ni-trash"></em>-->
<!--                        <span>Remove Orders</span>-->
<!--                      </a>-->
<!--                    </li>-->
<!--                  </ul>-->
<!--                </div>-->
<!--              </div>-->
<!--            </li>-->
<!--          </ul>-->
<!--        </div>-->
      </div>
      <?php foreach ($kycs as $kyc): 
      $is_cpf = strlen($kyc?->doc ?? '') <= 14;
      $is_cnpj = strlen($kyc?->doc ?? '') > 14;
      ?>
      <div class="nk-tb-item tr">
        <div class="nk-tb-col nk-tb-col-check">
          <div class="custom-control custom-control-sm custom-checkbox notext">
            <input type="checkbox" class="custom-control-input" id="oid01">
            <label class="custom-control-label" for="oid01"></label>
          </div>
        </div>
        <div class="nk-tb-col">
          <span class="tb-lead">
            <a href="<?php echo site_url(); ?>/admin/kyc/<?php echo $kyc->id; ?>/edit"
              ><?php echo $is_cpf ? $kyc->name : $kyc->fantasy_name; ?></a>
          </span>
        </div>
        <div class="nk-tb-col">
            <div class="div_wd_status_approved" 
                style="display:<?php if ($kyc->status == \Backend\Enums\Kyc\EKycStatus::CONFIRMED->value): ?> block <?php else: ?> none <?php endif; ?>;">
                <span class="dot bg-success d-sm-none"></span>
                <span class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Confirmado</span>
            </div>
            <div class="div_wd_status_pending" style="display:<?php if ($kyc->status == \Backend\Enums\Kyc\EKycStatus::PENDING->value): ?> block <?php else: ?> none <?php endif; ?>;">
                <span class="dot bg-warning d-sm-none"></span>
                <span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Pendente</span>
            </div>
            <div class="div_wd_status_canceled" 
                style="display:<?php if ($kyc->status == \Backend\Enums\Kyc\EKycStatus::REJECTED->value): ?> block <?php else: ?> none <?php endif; ?>;">
                <span class="dot bg-danger d-sm-none"></span>
                <span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Rejeitado</span>
            </div>
        </div>
        <div class="nk-tb-col">
          <span class="tb-lead">
            <a href="<?php echo site_url(); ?>/admin/kyc/<?php echo $kyc->id; ?>/edit"
              ><?php echo $kyc->doc; ?></a>
          </span>
        </div>
        <!-- <div class="nk-tb-col tb-col-md">
          <span class="tb-sub"><?php echo $kyc->approved_at ?? ''; ?></span>
        </div> -->
        <div class="nk-tb-col">
          <span class="tb-sub"><?php echo $kyc->created_at ?? ''; ?></span>
        </div>
        <div class="nk-tb-col nk-tb-col-tools">
          <ul class="nk-tb-actions gx-1">
            <!-- <li class="nk-tb-action-hidden">
              <a href="#" class="btn btn-icon btn-trigger btn-tooltip" title="" data-bs-original-title="Marcar como entregue">
                <em class="icon ni ni-truck"></em>
              </a>
            </li>
            <li class="nk-tb-action-hidden">
              <a href="javascript:;" data-bs-toggle="modal">
                <em class="icon ni ni-eye"></em>
              </a>
            </li> -->
            <li>
              <?php /*<div class="drodown me-n1">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                  <em class="icon ni ni-more-h"></em>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                  <ul class="link-list-opt no-bdr">
                    <!-- <li>
                      <a href="https://checkout.bluedrops.com.br/user/orders/75">
                        <em class="icon ni ni-eye"></em>
                        <span>Detalhes do pedido</span>
                      </a>
                    </li> -->
                    <!-- <li>
                      <a href="javascript:;" click="approveKycOnClick" data-id="">
                        <em class="icon ni ni-truck"></em>
                        <span>Aprovar</span>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:;" click="rejectKycOnClick" data-id="">
                        <em class="icon ni ni-trash"></em>
                        <span>Rejeitar</span>
                      </a>
                    </li> -->
                  </ul>
                </div>
              </div> */ ?>
            </li>
          </ul>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <!-- .nk-tb-list -->
    <Pagination />
  </div>
</div>

<div class="modal" tabindex="-1" id="modalWithdrawalAccountPix">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pix</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Nome: <b><span class="span_wd_pix_username">Name</span></b><br />
        Chave pix: <b><span class="span_wd_pix_key">0</span></b><br />
        Valor: <b><span class="span_wd_pix_total" style="color:#02f99e">0</span></b>
        <div class="form-group">
          <label>Resposta:</label>
          <textarea class="form-control inp_wd_pix_reason"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary inp_wd_reject" data-bs-dismiss="modal" click="rejectOnClick" data-id="">Rejeitar</button>
        <button type="button" class="btn btn-primary inp_wd_approve" data-bs-dismiss="modal" click="approveOnClick" data-id="">Já paguei</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" id="modalWithdrawalAccountBank">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transferência bancária</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Nome: <b><span class="span_wd_bank_username">Name</span></b><br />
        Banco: <b><span class="span_wd_bank_name">027 - Bank name</span></b><br />
        Tipo: <b><span class="span_wd_bank_acc_type">Current account</span></b><br />
        Conta: <b><span class="span_wd_bank_account">00000000</span>-<span class="span_wd_bank_digit">0</span></b><br />
        Agencia: <b><span class="span_wd_bank_agency">0000</span></b><br />
        Valor: <b><span class="span_wd_bank_total" style="color:#02f99e">0</span></b>
        <div class="form-group">
          <label>Resposta:</label>
          <textarea class="form-control inp_wd_bank_reason"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary inp_wd_reject" data-bs-dismiss="modal" click="rejectOnClick" data-id="">Rejeitar</button>
        <button type="button" class="btn btn-primary inp_wd_approve" data-bs-dismiss="modal" click="approveOnClick" data-id="">Já paguei</button>
      </div>
    </div>
  </div>
</div>

</content>