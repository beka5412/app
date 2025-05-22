<?php use Backend\Enums\Orderbump\EOrderbumpStatus; ?>
<title> Editar Orderbump </title>

<content>
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <h3 class="nk-block-title page-title">Editar Orderbump</h3>
    </div>
  </div>
    <!-- Inicio Pagina de edicao order bump -->
    <div class="nk-block frm_edit_orderbump">
      <div class="row g-gs">
        <div class="col-12 d-flex">
          <div class="col-md-12">
            <div class="card card-bordered card-preview">
              <div class="card-inner">
                <div class="preview-block">
                  <div class="row gy-4">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Ativo</label>
                        <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input inp_stock_control" name="reg-public"
                              <?php if ($orderbump->status == EOrderbumpStatus::PUBLISHED->value): ?> checked="" <?php endif; ?>
                              id="orderbumpEnabled">
                            <label class="custom-control-label" for="orderbumpEnabled"></label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label class="form-label">Nome</label>
                        <div class="form-control-wrap">
                          <input class="form-control inp_orderbump_name" value="<?php echo $orderbump->name; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Oferecer o produto...</label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_orderbump_product">
                                <?php foreach ($products as $product): ?>
                                  <option <?php if ($product->id == $orderbump->product_id): ?> selected="" <?php endif; ?> value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Preço promocional (Unitario)</label>
                        <div class="form-control-wrap">
                          <input class="form-control inp_orderbump_price" keyup="$inputCurrency" keydown="$onlyNumbers"
                            blur="$inputCurrency" load="element.value = currency(element.value)" value="<?php echo $orderbump->price; ?>">
                        </div>
                      </div>
                    </div>
                   
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 d-flex">
          <div class="col-md-12">
            <div class="card card-bordered card-preview">
              <div class="card-inner">
                <div class="preview-block">
                  <div class="row gy-4">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">Texto do botão (CTA) * </label>
                        <div class="form-control-wrap">
                          <input class="form-control inp_orderbump_text_button" value="<?php echo $orderbump->text_button; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Título /* (opcional) */ </label>
                        <div class="form-control-wrap">
                          <input class="form-control inp_orderbump_title" value="<?php echo $orderbump->title; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label class="form-label" for="default-textarea">Descrição do
                          Produto</label>
                        <div class="form-control-wrap">
                          <textarea class="form-control no-resize inp_orderbump_description"
                            id="default-textarea"><?php echo $orderbump->description; ?></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr class="preview-hr">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 d-flex">
          <div class="col-md-12">
            <div class="card card-bordered card-preview">
              <div class="card-inner">
                <div class="preview-block">
                  <div class="row gy-4">
                  <h4>Regras</h4>
                  <hr class="divider-gl">
                      <div class="col-lg-12">
                          <div class="form-group">
                            <label class="form-label" for="default-01">Checkout (Selecione em qual chechout vai mostrar o Orderbump)</label>
                            <div class="form-control-wrap">
                                  <select class="form-select inp_orderbump_product_as_checkout">
                                    <?php foreach ($products as $product): ?>
                                      <option <?php if ($product->id == $orderbump->product_as_checkout_id): ?> selected="" <?php endif; ?> value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                    <?php endforeach; ?>
                                  </select>
                            </div>
                          </div>
                        </div>
                      </div>
                  <hr class="preview-hr">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="c-right ps-1 mt-1 d-flex justify-content-end">
          <button click="orderbumpOnSubmit" class="btn btn-primary">Salvar</button>
        </div>
      </div>
    </div>

  <!-- Modal Trigger Code -->
<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" id="modalDefault">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
        <em class="icon ni ni-cross"></em>
      </a>
        <div class="modal-header">
          <h5 class="modal-title">Adicionar produtos</h5>
        </div>
        <div class="modal-body">
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
                    <span>Nome</span>
                  </div>
                  <div class="nk-tb-col">
                    <span></span>
                  </div>
                </div>
                <?php foreach ($products as $product): ?>
                <div class="nk-tb-item tr">
                  <div class="nk-tb-col nk-tb-col-check">
                    <div class="custom-control custom-control-sm custom-checkbox notext">
                      <input type="checkbox" class="custom-control-input" id="pid1">
                      <label class="custom-control-label" for="pid1"></label>
                    </div>
                  </div>
                  <div class="nk-tb-col">
                    <span class="tb-product">
                      <img src="<?php echo $product->image; ?>" alt="" class="thumb">
                      <a href="#">
                        <span class="title"><?php echo $product->name; ?></span>
                      </a>
                    </span>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
              <a href="#" class="btn btn-outline-light">Cancelar</a>
              <a href="#" class="btn btn-primary">Adicionar</a>      
        </div>
    </div>
  </div>
</div>
</content>