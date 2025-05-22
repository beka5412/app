<title>Domínios</title>

<content>

      <div class="nk-content-body">
        <div class="nk-block-head">
          <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
              <h3 class="nk-block-title page-title">Meus Dominios</h3>
              <div class="nk-block-des text-soft">
                <p>Adicione e verifique os domínios que você usa no gerenciador de anúncios do Facebook.</p>
              </div>
            </div>
            <div class="nk-block-head-content">
              <ul class="nk-block-tools g-3">
                <li>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDefault"><em class="icon ni ni-plus"></em><span> Add Dominio</span></button>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="nk-block">
          <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
              <div class="card-inner">
                <div class="card-title-group">
                  <div class="card-title">
                    <h5 class="title">Meus Dominios</h5>
                  </div>
                  <div class="card-tools me-n1">
                    <ul class="btn-toolbar">
                      <li>
                        <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search">
                          <em class="icon ni ni-search"></em>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="card-search search-wrap" data-search="search">
                    <div class="search-content">
                      <a href="#" class="search-back btn btn-icon toggle-search" data-target="search">
                        <em class="icon ni ni-arrow-left"></em>
                      </a>
                      <input type="text" class="form-control form-control-sm border-transparent form-focus-none" placeholder="Quick search by order id">
                      <button class="search-submit btn btn-icon">
                        <em class="icon ni ni-search"></em>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-inner p-0">
                <table class="table table-orders">
                  <thead class="tb-odr-head">
                    <tr class="tb-odr-item">
                      <th class="tb-odr-amount w-70">
                        <span class="tb-odr-total">Dominio</span>
                        <span class="tb-odr-status d-none d-md-inline-block">Status</span>
                      </th>
                      <th class="tb-odr-action">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody class="tb-odr-body">
                    <tr class="tb-odr-item">
                      <td class="tb-odr-amount">
                        <span class="tb-odr-total">
                          <span class="amount">Suitlab.com.br</span>
                        </span>
                        <span class="tb-odr-status">
                          <span class="badge badge-dot bg-success">Verificado</span>
                        </span>
                      </td>
                      <td class="tb-odr-action">
                        <div class="tb-odr-btns d-none d-sm-inline">
                            <a href="/demo1/invoice-details.html" class="btn btn-dim btn-sm btn-primary">Verificar Dominio</a>
                            <a href="/demo1/invoice-print.html" target="_blank" class="btn btn-icon btn-white btn-dim btn-sm btn-primary">
                            <em class="icon ni ni-trash-empty"></em>
                            </a>
                        </div>
                        <a href="/demo1/invoice-details.html" class="btn btn-pd-auto d-sm-none">
                            <em class="icon ni ni-chevron-right"></em>
                        </a>
                    </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-inner">
                
              </div>
            </div>
          </div>
        </div>
      </div>

    <!-- Modal Content Code -->
    <div class="modal fade frm_add_domain" tabindex="-1" id="modalDefault">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
            <em class="icon ni ni-cross"></em>
          </a>
          <div class="modal-header">
            <h5 class="modal-title">Adicionar domínio</h5>
          </div>
          <div class="modal-body">
                <div class="example-alert mt-2">
                    <div class="alert alert-light">1. Informe o domínio que você utiliza no Gerenciador de Negócios do Facebook.</div>
                </div>
                <div class="col-12 mt-2">
                    <div class="form-group d-flex">
                        <input class="form-control me-1 inp_domain" placeholder="meudominio.com" value="" /> <!-- nao precisa informar o subdominio -->
                    </div>
                </div>
                <small class="mt-1">O domínio precisa estar validado no gerenciador de negócios. Em caso de dúvidas verifique a nossa Central de Ajuda.</small>
                <div class="example-alert mt-2">
                    <div class="alert alert-light">2. Visite o painel de configurações de DNS do seu domínio </div>
                </div>
                <div class="example-alert mt-2">
                    <div class="alert alert-light">3. Crie um registro CNAME com o nome checkout.seudominio.com apontando o valor para rocketpays.app</div>
                </div>
                <div class="example-alert mt-2">
                    <div class="alert alert-light">4. Salve o DNS</div>
                </div>
                <div class="example-alert mt-2">
                    <div class="alert alert-light">5. Em caso de dúvida veja nosso tutorial <a href="#" class="alert-link">click aqui</a></div>
                </div>
          </div>
          <div class="modal-footer bg-light">
            <button data-bs-dismiss="modal" class="close btn btn-primary"> Cancelar </button>
            <button type="button" click="addDomain" class="btn btn-primary"> Adicionar </button>
          </div>
        </div>
      </div>
    </div>
</content>