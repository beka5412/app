<?php use Backend\Enums\Upsell\{EUpsellStatus, EUpsellRedirectType}; ?>

<title> Editar Upsell </title>
<content>
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Editar Upsell</h3>
      </div>
      <div class="nk-block-head-content">
        <div class="toggle-wrap nk-block-tools-toggle">
          <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-outline-light d-md-none">
            <em class="icon ni ni-help"></em>
          </a>
          <a href="<?php echo site_url(); ?>/upsells" to="<?php echo site_url(); ?>/upsells"
            class="btn btn-outline-light d-none d-md-inline-flex">
            <!-- <em class="icon ni ni-back"></em> -->
            <span>Voltar</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Inicio Pagina de edicao order bump -->
  <div class="nk-block frm_edit_upsell">
    <div class="row g-gs">
      <div class="col-12 d-flex">
        <div class="col-md-12">
          <div class="card card-bordered card-preview">
            <div class="card-inner">
              <div class="preview-block">
                <div class="row gy-4">

                  <!-- <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Ativo</label>
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input inp_stock_control" name="reg-public"
                            <?php // if ($orderbump->status == EOrderbumpStatus::PUBLISHED->value): ?> checked="" <?php // endif; ?>
                            id="upsellEnabled">
                          <label class="custom-control-label" for="upsellEnabled"></label>
                        </div>
                      </div>
                    </div>
                  </div> -->

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Status</label>
                      <div class="form-control-wrap">
                        <select class="form-control inp_status">
                          <option <?php if ($upsell->status == EUpsellStatus::PUBLISHED->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellStatus::PUBLISHED->value; ?>">Publicado</option>
                          <option <?php if ($upsell->status == EUpsellStatus::DISABLED->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellStatus::DISABLED->value; ?>">Desativado</option>
                          <option <?php if ($upsell->status == EUpsellStatus::DRAFT->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellStatus::DRAFT->value; ?>">Rascunho</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Nome</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_name" value="<?php echo $upsell->name; ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Produto do upsell</label>
                      <div class="form-control-wrap">
                        <!-- TODO: listar apenas alguns produtos, poder pesquisar pelo nome -->
                        <select class="form-control inp_product" change="setPriceVariations">
                          <?php $first_product = null; $product_selected = null; foreach ($products as $product_): if (empty($first_product)) $first_product = $product_; ?>
                            <option 
                              <?php if ($product_->id == ($upsell->product_id ?? false)): $product_selected = $product_; ?> selected="" <?php endif; ?>
                              value="<?php echo $product_->id; ?>" data='<?php echo json_encode([
                                "product" => $product_,
                                "links" => $product_->product_links ?? "[]"
                              ]); ?>'><?php echo $product_->name; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Variação do preço</label>
                      <div class="form-control-wrap">
                        <select class="form-control sel_price_var">
                          <option value="0">R$ <?php echo currency(!empty($product_selected) ? $product_selected->price_promo ?: $product_selected->price
                            : (!empty($first_product) ? $first_product->price_promo ?: $first_product->price : 0)); ?> - Principal</option>
                          <?php $links = $product->product_links ?? []; foreach ($links as $link): ?>
                            <option <?php if (($link->id ?? null) === ($upsell->product_link_id ?? false)): ?> selected="" <?php endif; ?> value="<?php echo $link->id; ?>">R$ <?php echo currency($link->amount); ?> - <?php echo $link->slug; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Redirecionamento ao aceitar</label>
                      <div class="form-control-wrap">
                        <select class="form-control sel_accept_redirect" app-toggle="change">
                          <option <?php if ($upsell->accept_redirect == EUpsellRedirectType::PURCHASES->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellRedirectType::PURCHASES->value; ?>">Minhas compras</option>
                          <option <?php if ($upsell->accept_redirect == EUpsellRedirectType::EXTERNAL->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellRedirectType::EXTERNAL->value; ?>" app-target=".div_accept_page">Página externa</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12 div_accept_page <?php echo $upsell->accept_redirect == EUpsellRedirectType::EXTERNAL->value ? '' : 'hide'; ?>">
                    <div class="form-group">
                      <label class="form-label">Link da página ao aceitar</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_accept_page" value="<?php echo $upsell->accept_page ?? ''; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Redirecionamento ao recusar</label>
                      <div class="form-control-wrap">
                        <select class="form-control sel_refuse_redirect" app-toggle="change">
                          <option <?php if ($upsell->refuse_redirect == EUpsellRedirectType::PURCHASES->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellRedirectType::PURCHASES->value; ?>">Minhas compras</option>
                          <option <?php if ($upsell->refuse_redirect == EUpsellRedirectType::EXTERNAL->value): ?> selected="" <?php endif; ?> value="<?php echo EUpsellRedirectType::EXTERNAL->value; ?>" app-target=".div_refuse_page">Página externa</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12 div_refuse_page <?php echo $upsell->refuse_redirect == EUpsellRedirectType::EXTERNAL->value ? '' : 'hide'; ?>">
                    <div class="form-group">
                      <label class="form-label">Link da página ao recusar</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_refuse_page" value="<?php echo $upsell->refuse_page ?? ''; ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Texto aceitar</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_accept_text" value="<?php echo $upsell->accept_text ?? 'Sim, eu aceito esta oferta!'; ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Texto recusar</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_refuse_text" value="<?php echo $upsell->refuse_text ?? 'Não, eu gostaria de recusar esta oferta'; ?>">
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="c-right ps-1 mt-1 d-flex justify-content-end">
        <button click="upsellOnSubmit" class="btn btn-primary">Salvar</button>
      </div>
    </div>
  </div>
</content>