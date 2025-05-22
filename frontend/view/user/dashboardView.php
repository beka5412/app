<content ready="ready">
  <link rel="stylesheet" href="/static/css/dashboard.css">

  <script type="application/json" json-id="dashboardInfo">
    <?= json_encode([
      "sales_last_week" => $sales_last_week,
      "sales_last_month" => $sales_last_month,
      'sales_last_12_months' => $sales_last_12_months,
      'last_7_days' => $last_7_days,
      'last_30_days' => $last_30_days,
      'last_12_months' => $last_12_months,
    ]); ?>
  </script>

  <!-- <div>
    <div id="footerdashboard1">
      
    <div>
        <div id="notification">
          <div class="contentnotification">
            <img alt="alert" id="alert-circle" src="/images/dashboard/alert-circle.svg">
            <div class="tas">
              <p class="status">
                Status da conta
              </p>
              <p class="status2">
                Em breve você terá acesso a um informe instantâneo da saúde do seu negócio.
              </p>
            </div>
          </div>
        </div>
       
        
       <div id="notification" class="mt-2">
          <div class="contentnotification">
            <em class="icon ni ni-home" style="font-size: 22px; color: #99186e;"></em>
            <div class="tas">
              <p class="status">
                Atualize seu endereço
              </p>
              <p class="status2">
                Para receber sua premiação, precisamos que seu endereço esteja atualizado.                
              </p>
              <div class="d-flex justify-content-end">
                <a href="<?= site_url().'/profile/address' ?>" to="<?= site_url().'/profile/address' ?>" class="btn btn-primary notification-small-button d-inline-block">
                  Atualizar
                </a>
              </div>
            </div>
          </div>
        </div> 
        
      </div> 
    </div>
    <div id="footerdashboard">
      <div id="main3401">
        <div class="main3400">
          <div class="main3395">
            <div id="main3396">
              <div id="tas4">
                <div class="metric-item">
                  <div class="metric-header">
                    <img alt="icon" src="/images/dashboard/Featured icon.svg" class="featured-icon">
                    <button id="eye-button1" class="eye-button">
                      <img alt="icon" src="/images/dashboard/eye.svg" class="eye">
                    </button>
                  </div>
                  <div class="metric-content">
                    <p class="vendashojecss">Vendas hoje</p>
                    <p id="vendashoje" style="opacity: 0" class="metric-number-aprovadas">R$ <?= currency($sales_today) ?></p>
                  </div>
                </div>
                <div class="metric-item">
                  <div class="metric-header">
                    <img alt="icon" src="/images/dashboard/Featured icon2.png" class="featured-icon">
                    <button id="eye-button2" class="eye-button">
                      <img alt="icon" src="/images/dashboard/eye.svg" class="eye">
                    </button>
                  </div>
                  <div class="metric-content">
                    <p class="vendashojecss">Saldo disponível</p>
                    <p id="saldodisponivel" style="opacity: 0" class="metric-number-saldodisponivel">R$ <?= currency($balance->available) ?></p>
                  </div>
                </div>
                <div class="metric-item">
                  <div class="metric-header">
                    <img alt="icon" src="/images/dashboard/Featured icon3.svg" class="featured-icon">
                  </div>
                  <div class="metric-content">
                    <p class="vendashojecss">Conversão de vendas</p>
                    <p class="metric-number-conversao"><?= currency($conversion_today) ?>%</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> -
      
      
      </div>
   
    </div>
  </div>
  </body> -->
  <div class="container-fluid">
    <div class="nk-content-inner">
      <div class="nk-content-body">
        <div class="nk-block">
          <div>
            <div id="vendasgerais" class="mt-4">
              <h1><span class="text">Seja bem-vindo, <?= explode(" ", $user->name ?? $admin->name ?? '')[0]; ?></span></h1>
              <p>Únicos em cada venda, poderosos em cada negócio.</p>
              <br>
            </div>
          </div>
            <?php if (env('MAINTENANCE') == 'true'): ?>
              <div id="notification" class="mt-2">
                <div class="contentnotification">
                  <em class="icon ni ni-setting-alt" style="font-size: 22px; color: #997818;"></em>
                  <div class="tas">
                    <p class="status">
                      Manutenção
                    </p>
                    <p class="status2">
                      A plataforma está passando por manutenção e pode apresentar algumas instabilidades.
                    </p>
                  </div>
                </div>
              </div>
            <?php endif ?>
        </div>

        <div class="nk-block">
          <div class="row g-gs">
            <div class="col-xxl-3 col-sm-6">
              <div class="card">
                <div class="nk-ecwg nk-ecwg6">
                  <div class="card-inner">
                    <div class="card-title-group">
                      <div class="card-title">
                        <h6 class="title">Total em vendas</h6>
                      </div>
                    </div>
                    <div class="data">
                      <div class="data-group">
                        <div class="amount my-1 text-info">R$ <?= currency($total_approved) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
              <div class="card">
                <div class="nk-ecwg nk-ecwg6">
                  <div class="card-inner">
                    <div class="card-title-group">
                      <div class="card-title">
                        <h6 class="title">Vendas Hoje</h6>
                      </div>
                    </div>
                    <div class="data">
                      <div class="data-group">
                        <div class="amount  my-1 text-info">R$ <?= currency($sales_today) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
              <div class="card">
                <div class="nk-ecwg nk-ecwg6">
                  <div class="card-inner">
                    <div class="card-title-group">
                      <div class="card-title ">
                        <h6 class="title">Saldo disponível</h6>
                      </div>
                    </div>
                    <div class="data">
                      <div class="data-group">
                        <div class="amount my-1 text-success">R$ <?php currency($balance->available ?? 0.00) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
              <div class="card">
                <div class="nk-ecwg nk-ecwg6">
                  <div class="card-inner">
                    <div class="card-title-group">
                      <div class="card-title">
                        <h6 class="title">Conversão de vendas</h6>
                      </div>
                    </div>
                    <div class="data">
                      <div class="data-group">
                        <div class="amount"><?php echo $count_orders ? currency($count_approved_sales / $count_orders * 100) : 0 ?>%</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="nk-block">
          <div class="main3400">
            <div class="main3395">
              <div class="relatorio">
                <div class="float-filters">
                  <div class="float-filters-wrapper">
                    <div class="p-4">
                      <div class="form-group">
                        <label for="" class="form-label">Produto</label>
                        <select name="" id="dashboardSelectProduct" class="form-control">
                          <?php foreach ($products as $product): ?>
                          <option value="<?= $product->id ?>"><?= $product->name ?></option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="" class="form-label">Período</label>
                        <input type="text" id="date-picker" class="form-select form-control float-filters-data-picker" placeholder="Selecionar intervalo">
                      </div>

                      <button class="btn btn-primary d-block w-100" click="dashboardFilterChartOnClick">Filtrar</button>
                    </div>
                  </div>
                </div>
                <div
                  class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                  <div class="controls">
                    <div class="button-group btns_period_chart">
                      <button class="botoesescolha" id="yearly">12 meses</button>
                      <button class="botoesescolha" id="monthly">30 dias</button>
                      <button class="botoesescolha" id="weekly">7 Dias</button>
                      <button class="botoesescolha" id="btnFilters">Filtros</button>
                      <!-- <button class="botoesescolhafinal" id="custom"><span>+</span> Personalizado</button> -->
                    </div>
                    <!-- <input type="text" id="date-picker" placeholder="Selecionar intervalo de datas"> -->
                  </div>
                </div>
                <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</content>
