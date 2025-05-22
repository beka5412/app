<title>Chats</title>
<content>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
          <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Chats
          </div>
          <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
                <em class="icon ni ni-help"></em>
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
                <em class="icon ni ni-help"></em>
                    <span>Ajuda</span>
                </a>
                <a  href="javascript:void(0);" to="<?php echo site_url(); ?>/chat/new" class="toggle btn btn-icon btn-primary d-md-none">
                    <em class="icon ni ni-plus"></em>
                </a>
                <a href="javascript:void(0);" to="<?php echo site_url(); ?>/chat/new" class="toggle btn btn-primary d-none d-md-inline-flex">
                    <em class="icon ni ni-plus"></em>
                    <span>Cadastrar Chat</span>
                </a>
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
          <span>Oferta</span>
        </div>
        <div class="nk-tb-col tb-col-md">
          <span>Produto</span>
        </div>
        <div class="nk-tb-col tb-col-md">
          <span>Status</span>
        </div>
        <div class="nk-tb-col nk-tb-col-tools">
        </div>
      </div>
      <?php foreach ($chats as $chat): ?>
      <div class="nk-tb-item tr">
        <div class="nk-tb-col nk-tb-col-check">
          <div class="custom-control custom-control-sm custom-checkbox notext">
            <input type="checkbox" class="custom-control-input" id="pid1">
            <label class="custom-control-label" for="pid1"></label>
          </div>
        </div>
        <div class="nk-tb-col tb-col-sm">
          <a href="<?php echo site_url(); ?>/chat/<?php echo $chat->id; ?>/edit" to="<?php echo site_url(); ?>/chat/<?php echo $chat->id; ?>/edit" class="tb-product">
            <span class="title"> <?php echo $chat->name; ?></span>
          </a>
        </div>
        <div class="nk-tb-col">
          <span class="tb-lead">R$ 0</span>
        </div>
        <div class="nk-tb-col tb-col-md">
          <span class="tb-lead"><?php echo $chat->name ?? ''; ?></span>
        </div>
        <div class="nk-tb-col tb-col-md">
          <span class="badge badge-sm badge-dot has-bg bg-success d-sm-inline-flex">Ativo</span>
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
                      <a href="<?php echo site_url(); ?>/chat/<?php echo $chat->id; ?>/edit" 
                         to="<?php echo site_url(); ?>/chat/<?php echo $chat->id; ?>/edit">
                        <em class="icon ni ni-edit"></em>
                        <span>Editar</span>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:;" click="chatDestroy" data-id="<?php echo $chat->id; ?>">
                        <em class="icon ni ni-trash"></em>
                        <span>Deletar</span>
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

     <!-- bloco help -->
     <div class="nk-block mt-2">
    <div class="card card-bordered">
      <div class="card-inner">
        <div class="nk-help">
          <div class="nk-help-img">
            <img src="https://img.freepik.com/free-vector/live-collaboration-concept-illustration_114360-663.jpg?w=2000&amp;t=st=1677382991~exp=1677383591~hmac=2d2ba41bf15223d515dbffb005e772233c845c002dfd939dad03603707fef08e" <="" div="">
          </div>
          <div class="nk-help-text">
            <h5>Você está precisando de ajuda?</h5>
            <p class="text-soft">Preparamos um tutorial para lhe auxiliar e tirar duvidas em como gerenciar seus OrderBump, click no botão ao lado para ver</p>
          </div>
          <div class="nk-help-action">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp" class="btn btn-lg btn-outline-primary">Ver tutorial</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal tutorial -->
  <div class="modal" tabindex="-1" id="modalHelp" aria-modal="true" role="dialog">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <a href="#" class="close" data-bs-dismiss="modal">
                  <em class="icon ni ni-cross"></em>
              </a>
              <div class="modal-body modal-body text-center">
                  <div class="nk-modal">
                      <div class="header-section-help" style="align-items: center;">
                          <h4 class="mb-1" style="font-weight: 600; text-transform: uppercase;">OrderBump</h4>
                      </div>
                      <div class="help-description" style="overflow-y: auto;">
                          <span id="description">
                          <p>Aprenca como gerenciar seus OrderBump e converter muito mais...</p>
                          <p>
                              <br>
                          </p>
                          <iframe width="460" height="315" src="https://www.youtube.com/embed/NUemSEdQbGc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>                  <p>
                              <br>
                          </p>
                          </span>
                      </div>
                      <div class="nk-modal-action">
                          <a href="#" class="btn btn-lg btn-mw btn-primary" data-bs-dismiss="modal"> Aprenda mais sobre este recurso </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

</content>
