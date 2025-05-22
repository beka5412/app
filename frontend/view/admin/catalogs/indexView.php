<title>Catálogos</title>
<content>
  <div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title"><?php echo $title; ?></h3>
        </div>
        <div class="nk-block-head-content">
          <div class="toggle-wrap nk-block-tools-toggle">
            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
              <em class="icon ni ni-more-v"></em>
            </a>
            <div class="toggle-expand-content" data-content="pageMenu">
              <ul class="nk-block-tools g-3">
                <li>
                  <div class="form-control-wrap">
                    <div class="form-icon form-icon-right">
                      <em class="icon ni ni-search"></em>
                    </div>
                    <input type="text" class="form-control" id="default-04" placeholder="Quick search by id">
                  </div>
                </li>

                <li class="nk-block-tools-opt">
                  <a href="#" data-target="addCatalog" class="toggle btn btn-icon btn-primary d-md-none">
                    <em class="icon ni ni-plus"></em>
                  </a>
                  <a href="#" data-target="addCatalog" class="toggle btn btn-primary d-none d-md-inline-flex">
                    <em class="icon ni ni-plus"></em>
                    <span>Add Produto</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="nk-block">
      <div class="row g-gs">
        <?php foreach ($catalogs as $key => $catalog) : ?>

          <div class="col-xxl-3 col-lg-4 col-sm-6">
            <div class="card card-bordered product-card">
              <div class="product-thumb">
                <a href="#">
                  <img class="card-img-top" src="<?php echo $catalog->image; ?>" alt="">
                </a>
              </div>
              <div class="card-inner text-center">
                <h2>#<?php echo $catalog->sku; ?> - <?php echo $catalog->name; ?></h2>
                <div class="product-price text-primary h5">
                  R$ <?php echo $catalog->price; ?>
                </div>
                <button data-target="editCatalog" onclick='update(<?= json_encode($catalog); ?>)' class="toggle btn btn-primary d-none d-md-inline-flex">
                  <em class="icon ni ni-plus"></em>
                  <span>Editar Produto</span>
                </button>
                <a href="" class="btn btn-dim btn-danger mt-2"><em class="icon ni ni-trash"></em><span>Deletar</span></a>
              </div>
            </div>
          </div>

        <?php endforeach; ?>

      </div>
      <Pagination />
    </div>
    <div class="nk-add-product toggle-slide toggle-slide-right toggle-screen-any" data-content="addCatalog" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar="init">
      <div class="simplebar-wrapper" style="margin: -24px;">
        <div class="simplebar-height-auto-observer-wrapper">
          <div class="simplebar-height-auto-observer"></div>
        </div>
        <div class="simplebar-mask">
          <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
            <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
              <div class="simplebar-content" style="padding: 24px;">
                <div class="nk-block-head">
                  <div class="nk-block-head-content">
                    <h5 class="nk-block-title">Novo produto</h5>
                    <div class="nk-block-des">
                    </div>
                  </div>
                </div>
                <div class="nk-block">
                  <div class="row g-3">
                    <div class="col-12">
                      <div class="form-group">
                        <label class="form-label" for="catalog_title">Nome do Produto</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="catalog_title">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="catalog_price">Preço</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="catalog_price">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <label class="form-label" for="category">Categoria</label>
                        <div class="form-control-wrap">
                          <select class="form-control" name="catalog_category" id="catalog_category">
                            <?php foreach ($categories as $category) : ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <label class="form-label" for="catalog_description">Descrição</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="catalog_description">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="upload-zone small bg-lighter my-2 dropzone dz-clickable">
                        <div class="dz-message">
                          <input type="file" name="inp_image" id="inp_image" accept="image/png, image/jpeg" class="dz-message-text">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary" click="newCatalog">
                        <em class="icon ni ni-plus"></em>
                        <span>Salvar Produto</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="simplebar-placeholder" style="width: auto; height: 700px;"></div>
      </div>
      <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
      </div>
      <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
        <div class="simplebar-scrollbar" style="height: 430px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
      </div>
    </div>
    <div class="nk-add-product toggle-slide toggle-slide-right toggle-screen-any" data-content="editCatalog" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar="init">
      <div class="simplebar-wrapper" style="margin: -24px;">
        <div class="simplebar-height-auto-observer-wrapper">
          <div class="simplebar-height-auto-observer"></div>
        </div>
        <div class="simplebar-mask">
          <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
            <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
              <div class="simplebar-content" style="padding: 24px;">
                <div class="nk-block-head">
                  <div class="nk-block-head-content">
                    <h5 class="nk-block-title">Editar produto</h5>
                    <div class="nk-block-des">
                    </div>
                  </div>
                </div>
                <div class="nk-block">
                  <div class="row g-3">
                    <div class="col-12">
                      <input type="hidden" name="id_edit" id="id_edit" value="">
                      <div class="form-group">
                        <label class="form-label" for="title_edit">Nome do Produto</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="title_edit">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="price_edit">Preço</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="price_edit">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <label class="form-label" for="category_edit">Categoria</label>
                        <div class="form-control-wrap">
                          <select class="form-control" name="category_edit" id="category_edit">
                            <?php foreach ($categories as $category) : ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <label class="form-label" for="description_edit">Descrição</label>
                        <div class="form-control-wrap">
                          <input type="text" class="form-control" id="description_edit">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="upload-zone small bg-lighter my-2 dropzone dz-clickable">
                        <div class="dz-message">
                          <img id="image_atual" src="" alt="">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="upload-zone small bg-lighter my-2 dropzone dz-clickable">
                        <div class="dz-message">
                          <label class="form-label" for="image_edit">Alterar Imagem</label>
                          <input type="file" name="image_edit" id="image_edit" accept="image/png, image/jpeg" class="dz-message-text">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary" click="editCatalog">
                        <em class="icon ni ni-plus"></em>
                        <span>Salvar Produto</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="simplebar-placeholder" style="width: auto; height: 700px;"></div>
      </div>
      <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
      </div>
      <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
        <div class="simplebar-scrollbar" style="height: 430px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
      </div>
    </div>
  </div>
  <script>
    function update(catalog) {

     // alert(catalog.name)
      document.getElementById('id_edit').value = catalog.id;
      document.getElementById('title_edit').value = catalog.name;
      document.getElementById('price_edit').value = catalog.price;
      document.getElementById('image_atual').src = catalog.image;
      document.getElementById('description_edit').value = catalog.description;
      document.getElementById('category_edit').value = catalog.category_id;
    }
  </script>
</content>