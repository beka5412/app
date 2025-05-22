<?php use Backend\Enums\Pixel\EPixelPlatform; ?>
<title>Editar pixel</title>

<content>
  <div class="nk-content-body">

    <div class="nk-block-head">
      <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">
            <?php echo $product->sku; ?>
          </h3>
        </div>
        <div class="nk-block-head-content">
          <a  href="<?php echo site_url() . "/product/" . $product->id . "/pixels"; ?>"
            class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
            <em class="icon ni ni-arrow-left"></em>
            <span>Voltar</span>
          </a>
          <a  href="<?php echo site_url() . "/product/" . $product->id . "/pixels"; ?>"
            class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
            <em class="icon ni ni-arrow-left"></em>
          </a>
        </div>
      </div>
    </div>

        <div class="nk-block frm_edit_pixel">
          <div class="nk-tb-list is-separate mb-3">

            <div class="card card-bordered card-preview">
              <div class="card-inner">

                <div class="row gy-4">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Nome</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_pixel_name" value="<?php echo $pixel->name; ?>" />
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Plataforma</label>
                      <div class="form-control-wrap">
                        <select class="form-control inp_pixel_platform">
                          <option <?php if ($pixel->platform == EPixelPlatform::FACEBOOK->value): ?> selected="" <?php endif; ?> value="<?php echo EPixelPlatform::FACEBOOK->value; ?>">Facebook</option>
                          <option <?php if ($pixel->platform == EPixelPlatform::INSTAGRAM->value): ?> selected="" <?php endif; ?> value="<?php echo EPixelPlatform::INSTAGRAM->value; ?>">Google Ads</option>
                            <option <?php if ($pixel->platform == EPixelPlatform::INSTAGRAM->value): ?> selected="" <?php endif; ?> value="<?php echo EPixelPlatform::INSTAGRAM->value; ?>">Google Analytics</option>
                          <option <?php if ($pixel->platform == EPixelPlatform::TIKTOK->value): ?> selected="" <?php endif; ?> value="<?php echo EPixelPlatform::TIKTOK->value; ?>">TikTok</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Pixel ID</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_pixel_content" value="<?php echo $pixel->content; ?>" />
                      </div>
                    </div>
                  </div>
                  <div class="form-control-wrap">
                  <label class="form-label">Domínio (Gerenciar meus domínios <a href="<?php echo site_url(); ?>/settings">clique aqui</a>)</label>
                    <select class="form-select inp_domain">
                      <?php foreach ($domains as $domain): ?>
                      <option <?php if ($domain->id == $pixel->domain_id): ?> selected="" <?php endif; ?> value="<?php echo $domain->id; ?>"><?php echo $domain->domain; ?></option>
                      <?php endforeach; ?>
                    </select>
                   
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Token da API de Conversão (opcional)</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_pixel_access_token" value="<?php echo $pixel->access_token; ?>" />
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label">Colar meta tag &lt;meta name="" content=""&gt; para verificar o domínio</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_pixel_metatag" value='<?php 
                          $s = str_replace("<", "&lt;", $pixel->metatag ?? ''); 
                          $s = str_replace(">", "&gt;", $s); 
                          echo $s;
                        ?>' />
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            
            <div class="c-right ps-1 mt-1 d-flex justify-content-end">
              <button click="pixelOnSubmit" class="btn btn-primary">Salvar</button>
            </div>

          </div>
        </div>
      
  </div>
</content>