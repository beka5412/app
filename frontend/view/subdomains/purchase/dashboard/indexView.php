<?php use Backend\Enums\Order\EOrderStatusDetail; ?>
<?php use Backend\Enums\RefundRequest\ERefundRequestStatus; ?>
<title>Minhas Compras</title>
<style>
  .nk-header {
    background: #4e089c !important;
    border-color: #ffffff;
  }
</style>
<content>
<div class="row">
  <div class="nk-content-inner">
    <div class="nk-content-body">
      <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><?= lang($locale, 'My purchases') ?></h3>
          </div>
        </div>
      </div>
      <div class="nk-block">
        <div class="row g-gs">
          <?php foreach ($purchases as $purchase): ?>
          <div class="col-xxl-3 col-lg-3 col-sm-6" click="showPurchaseInfo" data-purchase='<?php echo json_encode($purchase); ?>'>
            <div class="card card-bordered product-card">
              <div class="product-thumb">
                <a class="toggle tipinfo" data-target="demoML" href="javascript:void(0);" aria-label="<?php $purchase?->product?->name; ?>"
                  data-bs-original-title="<?= $purchase?->product?->name; ?>">
                  <img class="card-img-top" src="<?php echo $purchase?->product?->image ?? ''; ?>"
                    alt="<?php echo $purchase?->product?->name ?? ''; ?>">
                </a>
              </div>
              <div class="card-inner text-center">
                <h5 class="product-title">
                  <a href="javascript:void(0);">
                    <?php echo $purchase?->product?->name ?? ''; ?>
                  </a>
                </h5>
                <ul class="product-tags">
                  <li>
                    <a href="javascript:void(0);">
                      <?php if ($purchase?->product?->delivery == Backend\Enums\Product\EProductDelivery::DOWNLOAD->value): ?>
                        <?= lang($locale, 'File to download') ?>
                      <?php elseif ($purchase?->product?->delivery == Backend\Enums\Product\EProductDelivery::EXTERNAL->value): ?>
                        <?= lang($locale, 'External link') ?>
                      <?php elseif ($purchase?->product?->delivery == Backend\Enums\Product\EProductDelivery::MEMBERKIT->value): ?>
                        <?= lang($locale, 'Members area') ?>
                      <?php endif ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<a data-target="demoML" class="toggle tipinfo d-none" id="demoMLToogler"></a>

<div class="nk-demo-panel nk-demo-panel-2x toggle-slide toggle-slide-right bg-dark toggle-screen-any div_purchase_info"
  data-content="demoML" data-toggle-overlay="true" data-toggle-body="true" data-toggle-screen="any">
  <div class="nk-demo-head">
    <h6 class="mb-0"><?= lang($locale, 'Product') ?> #{% purchase.product.id %}</h6>
    <span class="nk-demo-close toggle btn btn-icon btn-trigger revarse mr-n2 active" data-target="demoML">
      <em class="icon ni ni-cross"></em>
    </span>
  </div>
  <div class="nk-demo-item" data-simplebar="init">
    <div class="container info-header-purchase">
      <div class="custom-control custom-control-sm custom-checkbox custom-control-pro mb-4">
        <span class="user-card">
          <span class="product-image">
            <img
              src="{% purchase.product.image %}"
              alt="">
              
          </span>
          <span class="user-info">
            <h5 class="product-title">{% purchase.product.name %}</h5>
            <span>
              <b><?= lang($locale, 'Producer') ?>:</b>
              {% purchase.product.author %}
            </span>
            <ul class="rating text-danger mt-1">
              <li><em class="icon ni ni-heart-fill"></em></li>
              <li><em class="icon ni ni-heart-fill"></em></li>
              <li><em class="icon ni ni-heart-fill"></em></li>
              <li><em class="icon ni ni-heart-fill"></em></li>
              <li><em class="icon ni ni-heart-fill"></em></li>
            </ul>
          </span>
        </span>
      </div>
    </div>
    <span class="mt-2">
      <?= lang($locale, 'When accessing the content, you will be redirected to an external area of the producer itself. If you have any questions, speak to') ?> 
      {% purchase.product.support_email %}.</span>
    <div class="info-header mt-2 flex flex-wrap" style="gap: 5px">
      {%
        switch (purchase.product.delivery) {
          case '<?php echo Backend\Enums\Product\EProductDelivery::DOWNLOAD->value; ?>':
            `<a href="${ purchase.product.attachment_file || '' }" class="btn btn-primary"><span>Baixar</span><em class="icon ni ni-external-alt"></em></a>`
            break;
            
          case '<?php echo Backend\Enums\Product\EProductDelivery::EXTERNAL->value; ?>':
            `<a href="${ purchase.product.attachment_url || '' }" target="_blank" class="btn btn-primary"><span>Acessar o conteúdo</span><em class="icon ni ni-external-alt"></em></a>`
            break;
              
          case '<?php echo Backend\Enums\Product\EProductDelivery::MEMBERKIT->value; ?>':
            /* `<a href="javascript:void(0);" target="_blank" class="btn btn-primary"><span>Acessar</span><em class="icon ni ni-external-alt"></em></a>`
            */
            break;
            
          /* case '<?php echo Backend\Enums\Product\EProductDelivery::NOTHING->value; ?>':
          break; */
        }
      %}

      <!-- <a href="javascript:void(0);" class="btn btn-dim btn-info"><span><?= lang($locale, 'Help center') ?></span><em
          class="icon ni ni-help"></em></a> -->

      <span style="display: {% expiredWarranty(purchase.created_at, purchase.product.warranty_time) ? 'none' : 'inline-block' %}">
      <a href="javascript:;" class="btn btn-outline-warning btn_cancel_refund_purchase" data-id="{% purchase.id %}" click="cancelRefundPurchase"
        style="padding: 10px; display: {% purchase?.refund_request?.status == '<?php echo ERefundRequestStatus::PENDING->value; ?>' ? 'block' : 'none' %}">
        <span><?= lang($locale, 'Cancel refund request') ?></span>
        <em class="icon ni ni-wallet-out"></em
      ></a>

      <a href="javascript:;" class="btn btn-outline-warning btn_refund_purchase" data-id="{% purchase.id %}" click="refundPurchasePre"
      style="padding: 10px; display: {% purchase?.refund_request?.status == '<?php echo ERefundRequestStatus::PENDING->value; ?>'
        || purchase?.refund_request?.status == '<?php echo ERefundRequestStatus::CONFIRMED->value; ?>'
        ? 'none' : 'block' %}">
        <span><?= lang($locale, 'Request refund') ?></span>
        <em class="icon ni ni-wallet-out"></em
      ></a>
     
    </div>
    <hr>
    <div>
      <h5 class="card-title"><?= lang($locale, 'Producer details') ?></h5>
      <p>
        <strong><?= lang($locale, 'Name') ?>: </strong>  {% purchase.product.author %}
      </p>
      <p>
        <strong><?= lang($locale, 'Email') ?>: </strong>  {% purchase.product.support_email %}
      </p>
      <p>
        <strong><?= lang($locale, 'Warranty') ?>: </strong>  {% purchase.product.warranty_time %} Dias
      </p>
      <p>
        <strong><?= lang($locale, 'Purchase date') ?>: </strong>
         {% purchase.order.created_at.substr(0x00, 0x0A).split('-').reverse().join('/') %} 
         às {% purchase.order.created_at.substr(0x0B, 0x05) %}h
      </p>
      <p>
        <strong><?= lang($locale, 'Pay day') ?>: </strong> 
         {% purchase.created_at.substr(0x00, 0x0A).split('-').reverse().join('/') %} 
         às {% purchase.created_at.substr(0x0B, 0x05) %}h
      </p>
      <!-- <p>
        <strong><?= lang($locale, 'Total') ?>: </strong> {% currencySymbol(purchase.order.total) %}
      </p> -->
      <p>
        <strong><?= lang($locale, 'Payment method') ?>: </strong> 
        {%
          switch(purchase.order.meta.info_payment_method) {
            case 'credit_card':
              `<?= lang($locale, 'Credit card') ?>`;
              break;

            case 'billet':
              `<?= lang($locale, 'Billet') ?>`;
              break;

            case 'pix':
              `Pix`;
              break;

            default:
              `<?= lang($locale, 'Credit card') ?>`;
              break;
          }
        %}
      </p>
      <!-- p>
        <strong><?= lang($locale, 'Payment status') ?>: </strong>
        <span style="margin-left: 5px">
        {% 
          /*
          switch(purchase.order.status_details) {            
            case '<?php echo Backend\Enums\Order\EOrderStatusDetail::APPROVED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Aprovado</span>`;
              break;

            case '<?php echo EOrderStatusDetail::PENDING->value; ?>':
                `<span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Pendente</span>`;
              break;

            case '<?php echo EOrderStatusDetail::BILLET_PRINTED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Boleto impresso</span>`;
            break;

            case '<?php echo EOrderStatusDetail::PIX_GENERATED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Pix gerado</span>`;
            break;

            case '<?php echo EOrderStatusDetail::CAPTURED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Valor reservado no cartão</span>`;
            break;

            case '<?php echo EOrderStatusDetail::IN_ANALYSIS->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Em análise</span>`;
            break;

            case '<?php echo EOrderStatusDetail::REFUNDED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Reembolsado</span>`;
            break;

            case '<?php echo EOrderStatusDetail::CHARGEDBACK->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Chargeback</span>`;
            break;

            case '<?php echo EOrderStatusDetail::DISPUTED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Contestado / fraude</span>`;
            break;

            case '<?php echo EOrderStatusDetail::PRE_AUTHORIZED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-info d-none d-sm-inline-flex">Pré-autorizado</span>`;
            break;

            case '<?php echo EOrderStatusDetail::REJECTED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Rejeitado</span>`;
            break;

            case '<?php echo EOrderStatusDetail::CANCELED->value; ?>':
              `<span class="badge badge-sm badge-dot has-bg bg-danger d-none d-sm-inline-flex">Cancelado</span>`;
            break;
          }
          */
        %}
        </span>
          
      </ -->
      <p>
        <strong><?= lang($locale, 'Transaction id') ?>: </strong>
        {% purchase.order.uuid %}
      </p>
    </div>

  </div>
</div>

<div class="modal modalPurchaseConfirmRefund" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar reembolso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Informe o motivo para do cancelamento:</label>
          <textarea class="inp_purchase_reason form-control mt-1"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn_purchase_confirm_refund" click="refundPurchase">Save changes</button>
      </div>
    </div>
  </div>
</div>

</content>