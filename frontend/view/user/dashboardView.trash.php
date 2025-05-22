<?php
use Backend\Enums\Withdrawal\EWithdrawalStatus;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Withdrawal\EWithdrawalTransferType;
?>

<content ready="ready">
<div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
          <div class="nk-block-between">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Bem-vindo, <?php echo $user->name ?? $admin->name ?? ''; ?></h3>
              <div class="nk-block-des text-soft">
              </div>
            </div>            
          </div>
        </div>
       
        <?php if (env('MAINTENANCE') == 'true'): ?>
        <div class="alert alert-warning">A plataforma está passando por manutenção.</div>
        <?php endif ?>

        <div class="nk-block">
          <div class="row g-gs">
            <div class="col-xl-4">
              <div class="card h-100">
                <div class="card-body" style=" padding: 18px !important; ">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="card-title">
                        <h4 class="title mb-1" style=" font-size: 18px !important; ">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Vendas Hoje</font>
                          </font>
                        </h4>
                      </div>
                      <div class="">
                        <div class="amount textsub text-primary">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo __('R$') ?> <?php echo currency($total_approved); ?></font>
                          </font>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4">
              <div class="card h-100">
                <div class="card-body" style=" padding: 18px !important; ">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="card-title">
                        <h4 class="title mb-1"  style=" font-size: 18px !important; ">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Saldo Disponivel</font>
                          </font>
                        </h4>
                      </div>
                      <div class="">
                        <div class="amount textsub text-success">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo __('R$') ?> <?php echo currency($balance->available ?? 0); ?></font>
                          </font>
                        </div>
                       
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4">
              <div class="card h-100">
                <div class="card-body" style=" padding: 18px !important; ">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="card-title">
                        <h4 class="title mb-1"  style=" font-size: 18px !important; ">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Conversão de vendas</font>
                          </font>
                        </h4>
                      </div>
                      <div class="">
                        <div class="amount textsub text-warning">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?php echo $count_orders ? currency($count_approved_sales / $count_orders * 100) : 0 ?>%</font>
                          </font>
                        </div>
                       
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <script type="application/json" json-id="salesOverview">
            {
              "last_30_days": <?php echo json_encode($sales_last_30_days); ?>
            }
            </script>
            <div class="col-xxl-12">
              <div class="card card-bordered h-100">
                <div class="card-inner">
                  <div class="card-title-group align-start gx-3 mb-3">
                    <div class="card-title">
                      <h6 class="title">GRÁFICO DE FATURAMENTO</h6>
                    </div>
                  
                  </div>
                  <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                    <div class="nk-sale-data">
                      Vendas<span class="amount"><?php echo __('R$') ?> <?php echo currency($total_approved); ?></span>
                    </div>
                    <div class="nk-sale-data">
                      <span class="amount sm"><small>Pedidos</small> <?php echo $count_approved_sales; ?> 
                      </span>
                    </div>
                  </div>
                  <div class="nk-sales-ck large pt-4">
                    <div class="chartjs-size-monitor">
                      <div class="chartjs-size-monitor-expand">
                        <div class=""></div>
                      </div>
                      <div class="chartjs-size-monitor-shrink">
                        <div class=""></div>
                      </div>
                    </div>
                    <canvas class="chartjs-render-monitor" id="salesOverview" width="2084" height="352" style="display: block; height: 176px; width: 1042px;"></canvas>
                    <canvas class="sales-overview-chart chartjs-render-monitor" id="salesOverview" width="762" height="176" style="display: block; height: 176px; width: 762px;"></canvas>
                  </div>
                </div>
              </div>
            </div>           
          </div>
        </div>
</div>

</content>

