<title> Editar Chat </title>
<content>
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <h3 class="nk-block-title page-title">Editar Chat</h3>
    </div>
  </div>
  <!-- Inicio Pagina de edicao order bump -->
  <div class="nk-block frm_edit_chat">
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
                            <?php // if ($orderbump->status == EOrderbumpStatus::PUBLISHED->value): ?> checked="" <?php // endif; ?>
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
                        <input class="form-control inp_name" value="<?php echo $chat->name; ?>">
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
        <button click="chatOnSubmit" class="btn btn-primary">Salvar</button>
      </div>
    </div>
  </div>
</content>