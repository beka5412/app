<title>Editar cliente <?php echo $customer->name; ?></title>

<content>
    <div class="nk-content-body frm_edit_customer">

        <div class="nk-block-head">
            <div class="nk-block-between g-3">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">
                        <?php echo $customer->id; ?>
                    </h3>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tabItem5">
                    <em class="icon ni ni-box"></em>
                    <span>Geral</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabItem6">
                    <em class="icon ni ni-setting"></em>
                    <span>Configurações</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabItem7">
                    <em class="icon ni ni-cart"></em>
                    <span>Checkout</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabItem8">
                    <em class="icon ni ni-hot"></em>
                    <span>Order bump</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabItem9">
                    <em class="icon ni ni-user-add"></em>
                    <span>Afiliado</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabItem10">
                    <em class="icon ni ni-link-alt"></em>
                    <span>Links</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tabItem5">

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-12 d-flex">
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Nome</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control inp_name"
                                                                value="<?php echo $customer->name; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">E-mail</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control inp_email"
                                                                value="<?php echo $customer->email; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Categoria Do
                                                            Produto</label>
                                                        <div class="form-control-wrap">
                                                            <div class="form-group">
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select">
                                                                        <option value="999">
                                                                            Selecione uma categoria
                                                                        </option>
                                                                        <option value="0">
                                                                            Saúde e Esportes
                                                                        </option>
                                                                        <option value="1">
                                                                            Finanças e Investimentos
                                                                        </option>
                                                                        <option value="2">
                                                                            Relacionamentos
                                                                        </option>
                                                                        <option value="3">
                                                                            Negócios e Carreira
                                                                        </option>
                                                                        <option value="4">
                                                                            Espiritualidade
                                                                        </option>
                                                                        <option value="5">
                                                                            Sexualidade
                                                                        </option>
                                                                        <option value="6">
                                                                            Entretenimento
                                                                        </option>
                                                                        <option value="7">
                                                                            Culinária e Gastronomia
                                                                        </option>
                                                                        <option value="8">
                                                                            Idiomas
                                                                        </option>
                                                                        <option value="9">
                                                                            Direito
                                                                        </option>
                                                                        <option value="10">
                                                                            Apps &amp; Software
                                                                        </option>
                                                                        <option value="11">
                                                                            Literatura
                                                                        </option>
                                                                        <option value="12">
                                                                            Casa e Construção
                                                                        </option>
                                                                        <option value="13">
                                                                            Desenvolvimento Pessoal
                                                                        </option>
                                                                        <option value="14">
                                                                            Moda e Beleza
                                                                        </option>
                                                                        <option value="15">
                                                                            Animais e Plantas
                                                                        </option>
                                                                        <option value="16">
                                                                            Educacional
                                                                        </option>
                                                                        <option value="17">
                                                                            Hobbies
                                                                        </option>
                                                                        <option value="18">
                                                                            Internet
                                                                        </option>
                                                                        <option value="19">
                                                                            Ecologia e Meio Ambiente
                                                                        </option>
                                                                        <option value="20">
                                                                            Música e Artes
                                                                        </option>
                                                                        <option value="21">
                                                                            Tecnologia da Informação
                                                                        </option>
                                                                        <option value="22">
                                                                            Empreendedorismo Digital
                                                                        </option>
                                                                        <option value="23">
                                                                            Outros
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group"><label class="form-label"
                                                            for="default-textarea">Descrição do Produto</label>
                                                        <div class="form-control-wrap"><textarea
                                                                class="form-control no-resize"
                                                                id="default-textarea">Large text area content</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <label class="form-label" for="default-textarea">Imagem do Produto
                                                        (Tamanho recomendado: 300x250 pixels)</label>
                                                    <div class="upload-zone">
                                                        <div class="dz-message" data-dz-message> <span
                                                                class="dz-message-text">Drag and drop file</span> <span
                                                                class="dz-message-or">or</span> <button
                                                                class="btn btn-primary">SELECT</button> </div>
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
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">O produto é
                                                            grátis?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="reg-public" id="site-off">
                                                                <label class="custom-control-label"
                                                                    for="site-off">Não</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Valor do
                                                            Produto</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control inp_price"
                                                                value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Valor
                                                            Promocional</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
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
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Página de
                                                            vendas</label>
                                                        <div class="form-control-wrap">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"
                                                                        id="basic-addon3">https://</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="basic-url">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">E-mail de
                                                            suporte</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Nome de exibição do
                                                            produtor</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Tempo de garantia</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select">
                                                                <option value="999">
                                                                    7 Dias
                                                                </option>
                                                                <option value="0">
                                                                    14 Dias
                                                                </option>
                                                                <option value="1">
                                                                    21 Dias
                                                                </option>
                                                                <option value="2">
                                                                    30 Dias
                                                                </option>
                                                            </select>
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
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Tipo do produto</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select">
                                                                <option value="999">Digital</option>
                                                                <option value="0">físico</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Tipo de Pagamento</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select">
                                                                <option value="999">Único</option>
                                                                <option value="0">Recorrente</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Entrega do conteúdo</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select">
                                                                <option value="999">Rocketmember</option>
                                                                <option value="0">Área de membros Externa</option>
                                                                <option value="1">Somente venda</option>
                                                                <option value="2">Arquivo Donwload</option>
                                                            </select>
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
                            <button click="customerOnSubmit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane" id="tabItem6">

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-12 d-flex">
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Método de
                                                            pagamento</label>
                                                        <div class="form-group grids">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="customCheck1">
                                                                <label class="custom-control-label"
                                                                    for="customCheck1">Pix</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="customCheck2">
                                                                <label class="custom-control-label"
                                                                    for="customCheck2">Cartão de crédito</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="customCheck2">
                                                                <label class="custom-control-label"
                                                                    for="customCheck2">Boleto</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Desconto no
                                                            Pix?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="pix" id="pix-off">
                                                                <label class="custom-control-label"
                                                                    for="pix-off">Não</label>
                                                            </div>
                                                        </div>
                                                        <label class="form-label" for="default-01">Valor do
                                                            Desconto</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Desconto no
                                                            Cartão?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="pix" id="card-off">
                                                                <label class="custom-control-label"
                                                                    for="card-off">Não</label>
                                                            </div>
                                                        </div>
                                                        <label class="form-label" for="default-01">Valor do
                                                            Desconto</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Desconto no
                                                            Boleto?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="pix" id="boleto-off">
                                                                <label class="custom-control-label"
                                                                    for="boleto-off">Não</label>
                                                            </div>
                                                        </div>
                                                        <label class="form-label" for="default-01">Valor do
                                                            Desconto</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Parcelamento</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select">
                                                                <option value="1">Apenas a vista</option>
                                                                <option value="2">Até 2x</option>
                                                                <option value="3">Até 3x</option>
                                                                <option value="4">Até 4x</option>
                                                                <option value="5">Até 5x</option>
                                                                <option value="6">Até 6x</option>
                                                                <option value="7">Até 7x</option>
                                                                <option value="8">Até 8x</option>
                                                                <option value="9">Até 9x</option>
                                                                <option value="10">Até 10x</option>
                                                                <option value="11">Até 11x</option>
                                                                <option value="12">Até 12x</option>
                                                            </select>
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
                            <div class="col-md-3 d-sm-none">
                                <p>Produto
                                    A aprovação do produto é instantânea, ou seja, você pode cadastrar e já começar a
                                    vender.
                                    A imagem do produto será exibida na área de membros e no seu programa de afiliados.
                                </p>
                            </div>
                            <div class="col-md-9">
                                <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Página de obrigado
                                                            Pix?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="pix" id="page-pix-off">
                                                                <label class="custom-control-label"
                                                                    for="page-pix-off">Não</label>
                                                            </div>
                                                        </div>
                                                        <label class="form-label" for="default-01">Url da página</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Página de obrigado
                                                            Cartão?</label>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    name="pix" id="page-card-off">
                                                                <label class="custom-control-label"
                                                                    for="page-card-off">Não</label>
                                                            </div>
                                                        </div>
                                                        <label class="form-label" for="default-01">Url da página</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="default-01"
                                                                placeholder="Input placeholder">
                                                        </div>
                                                    </div>
                                                </div>
                                                \
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tabItem7">
                <p>contnet 7 </p>
            </div>
            <div class="tab-pane" id="tabItem8">
                <p>contnet 8</p>
            </div>
            <div class="tab-pane" id="tabItem9">
                <p>contnet 9</p>
            </div>
            <div class="tab-pane" id="tabItem10">
                <p>contnet 10</p>
            </div>
        </div>
    </div>
</content>