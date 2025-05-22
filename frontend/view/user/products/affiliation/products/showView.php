<?php use Backend\Enums\Product\EProductAffPaymentType; ?>
<content>
  <AffiliationMenu />
  <div>
    <div class="nk-block">
      <div class="card card-bordered">
        <div class="card-inner">
          <div class="row">
            <div class="col-lg-4">
              <img class="card-img-top" src="<?php echo $product->image ?? 'https://checkout.bluedrops.com.br/uploads/1/22/12/1669997520.jpg'; ?>" alt="">
            </div>
            <div class="col-lg-8">
              <h5 class="text-muted pb-1"><?php echo $product->category->name ?? 'Sem categoria'; ?></h5>
              <h2 class="product-title"><?php echo $product->name; ?></h2>
              <div class="product-rating">
                <ul class="rating">
                  <li>
                    <em class="icon ni ni-star-fill"></em>
                  </li>
                  <li>
                    <em class="icon ni ni-star-fill"></em>
                  </li>
                  <li>
                    <em class="icon ni ni-star-fill"></em>
                  </li>
                  <li>
                    <em class="icon ni ni-star-fill"></em>
                  </li>
                  <li>
                    <em class="icon ni ni-star-half"></em>
                  </li>
                </ul>
                <div class="amount">(2 Reviews)</div>
              </div>
              <div class="product-info mt-1 me-xxl-5">
                <div class="d-flex price-and-commission">
                  <div class="col-lg-4 py-3">
                    <h5 class="">Preço máximo </h5>
                    <p class="text-3 mr-3 mb-0 text-green">R$ <?php echo number_format((Double) $product->price, 2, ',', '.'); ?></p>
                  </div>
                  <div class="col-lg-8 text-break py-3 align-items-end p-0">
                    <h5 class="">Comissão de até </h5>
                    <p class="text-3 mr-3 mb-0 text-green">R$ <?php echo number_format((Double) $product->price, 2, ',', '.'); ?></p>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 col-md-6 col-xs-12">
                <div>Página de venda: <b><a href="<?php echo $product->landing_page; ?>" target="_blank"><?php echo $product->landing_page; ?></a></b></div>
                <div>Tipo de comissionamento: <b><span>Último Clique</span></b></div>
                <div>Comissão dos afiliados: <b><span style="color: #14d8a2">
                  <?php if ($product->affiliate_payment_type == EProductAffPaymentType::PRICE->value): ?>R$<?php endif; ?>
                  <?php echo currency($product->affiliate_amount); ?><?php if ($product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value): ?>%<?php endif; ?>
                </span></b></div>
              </div>
              <div>
                <button class="mt-1 w-100 btn-afilie mt-4 btn_promote_affiliation <?php echo $affiliated ? 'd-none' : ''; ?>" click="promoteOnClick">Promover esse produto</button>
                <button class="mt-1 w-100 btn-afilie btn-demote mt-4 btn_demote_affiliation <?php echo $affiliated ? '' : 'd-none'; ?>" click="demoteOnClick">Desfazer afiliação</button>
              </div>
            </div>
          </div>
        </div>
        <hr class="hr border-light">
        <div class="card card-bordered card-preview">
          <div class="card-inner">
            <div class="row g-gs">
              <div class="col-md-4">
                <ul class="nav link-list-menu border border-light round m-0" role="tablist">
                  <li>
                    <a class="active" data-bs-toggle="tab" href="#tabItem17" aria-selected="true" role="tab">
                    <em class="icon ni ni-user"></em>
                    <span>Regras de Afiliação</span>
                    </a>
                  </li>
                  <li>
                    <a data-bs-toggle="tab" href="#tabItem18" aria-selected="false" tabindex="-1" role="tab">
                    <em class="icon ni ni-lock-alt"></em>
                    <span>Sobre o Produto</span>
                    </a>
                  </li>
                  <li>
                    <a data-bs-toggle="tab" href="#tabItem19" aria-selected="false" tabindex="-1" role="tab">
                    <em class="icon ni ni-bell"></em>
                    <span>Sobre o Produtor</span>
                    </a>
                  </li>
                  <li>
                    <a data-bs-toggle="tab" href="#tabItem20" aria-selected="false" tabindex="-1" role="tab">
                    <em class="icon ni ni-link"></em>
                    <span>Materiais de Divulgação</span>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="col-md-8">
                <div class="tab-content">
                  <div class="tab-pane active" id="tabItem17" role="tabpanel">
                    <p><?php echo $product->description; ?></p>
                  </div>
                  <div class="tab-pane" id="tabItem18" role="tabpanel">
                    <p><?php echo $product->description; ?></p>
                  </div>
                  <div class="tab-pane" id="tabItem19" role="tabpanel">
                    <div class="cart-resume">
                      <h5 class="card-title">Dados do cliente</h5>
                      <p>
                        <strong>Nome: </strong> <?php echo $product->author; ?>
                      </p>
                      <p>
                        <strong>E-mail: </strong> <?php echo $product->support_email; ?>
                      </p>
                      <p>
                        <strong>Garantia do Produto: </strong> <?php echo $product->warranty_time; ?>
                      </p>
                      <p>
                        <strong>Tipo de Produto: </strong> <?php echo $product->type; ?>
                      </p>
                      <strong>conquistas: </strong> 
                    </div>
                  </div>
                  <div class="tab-pane" id="tabItem20" role="tabpanel">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</content>