<title>Prova Social</title>

<content>
  <div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">Prova Social</h3>
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
                <li>
                  <div class="drodown">
                    <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white"
                      data-bs-toggle="dropdown" aria-expanded="false">Status</a>
                    <div class="dropdown-menu dropdown-menu-end" style="">
                      <ul class="link-list-opt no-bdr">
                        <li>
                          <a href="#">
                            <span>New Items</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <span>Featured</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <span>Out of Stock</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </li>
                <li class="nk-block-tools-opt">
                  <a href="https://checkout.bluedrops.com.br/user/physical-products/create"
                    class="toggle btn btn-icon btn-primary d-md-none">
                    <em class="icon ni ni-plus"></em>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="nk-block">
      <div class="nk-tb-list is-separate mb-3">
        <div class="nk-tb-item nk-tb-head">
          <div class="nk-tb-col nk-tb-col-check">
            <div class="custom-control custom-control-sm custom-checkbox notext">
              <input type="checkbox" class="custom-control-input" id="pid">
              <label class="custom-control-label" for="pid"></label>
            </div>
          </div>
          <div class="nk-tb-col tb-col-sm">
            <span>Nome</span>
          </div>
          <div class="nk-tb-col">
            <span>Preço Unidade</span>
          </div>
          <div class="nk-tb-col tb-col-md">
            <span>Links</span>
          </div>
          <div class="nk-tb-col tb-col-md">
            <em class="tb-asterisk icon ni ni-star-round"></em>
          </div>
          <div class="nk-tb-col nk-tb-col-tools">
            <ul class="nk-tb-actions gx-1 my-n1">
              <li class="me-n1">
                <div class="dropdown">
                  <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                    <em class="icon ni ni-more-h"></em>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                    <ul class="link-list-opt no-bdr">
                      <li>
                        <a href="#">
                          <em class="icon ni ni-edit"></em>
                          <span>Edit Selected</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <em class="icon ni ni-trash"></em>
                          <span>Remove Selected</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <em class="icon ni ni-bar-c"></em>
                          <span>Update Stock</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <em class="icon ni ni-invest"></em>
                          <span>Update Price</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <?php foreach ($products as $product): ?>
        <div class="nk-tb-item">
          <div class="nk-tb-col nk-tb-col-check">
            <div class="custom-control custom-control-sm custom-checkbox notext">
              <input type="checkbox" class="custom-control-input" id="pid1">
              <label class="custom-control-label" for="pid1"></label>
            </div>
          </div>
          <div class="nk-tb-col tb-col-sm">
            <a class="tb-product" to="<?php echo site_url(); ?>/product/<?php echo $product->id; ?>/edit">
              <img src="https://checkout.bluedrops.com.br/uploads/1/22/12/1669997520.jpg" alt="" class="thumb">
              <span class="title">
                <?php echo $product->name; ?>
              </span>
            </a>
          </div>
          <div class="nk-tb-col">
            <span class="tb-lead">R$
              <?php echo number_format($product->price, 2, ',', '.'); ?>
            </span>
          </div>
          <div class="nk-tb-col tb-col-md">
            <a href="#" data-toggle="modal" data-target="#modalLinks_3">
              <em class="icon ni ni-eye"></em>
              <span>Links do checkout</span>
            </a>
          </div>
          <div class="nk-tb-col tb-col-md">
            <div class="asterisk tb-asterisk">
              <a href="#">
                <em class="asterisk-off icon ni ni-star"></em>
                <em class="asterisk-on icon ni ni-star-fill"></em>
              </a>
            </div>
          </div>
          <div class="nk-tb-col nk-tb-col-tools">
            <ul class="nk-tb-actions gx-1 my-n1">
              <li class="me-n1">
                <div class="dropdown">
                  <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                    <em class="icon ni ni-more-h"></em>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                    <ul class="link-list-opt no-bdr">
                      <li>
                        <a href="#" data-toggle="modal" data-target="#modalLinks_3">
                          <em class="icon ni ni-eye"></em>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </div>
    <input type="hidden" id="get-products-url" value="https://checkout.bluedrops.com.br/user/get-physical-products">
  </div>

</content>