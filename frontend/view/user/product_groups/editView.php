<title>Edit Product Group</title>

<content>
<div class="nk-block">
    <div class="row g-gs">
      <div class="col-12 d-flex">
        <div class="col-md-12">
          <div class="card card-bordered card-preview">
            <div class="card-inner">
              <div class="preview-block">
                <div class="row gy-4">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Nome</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_name" value="Checklist Milionário">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Categoria Do Produto</label>
                      <div class="form-control-wrap">
                        <div class="form-group">
                          <div class="form-control-wrap">
                            <select class="form-select inp_category">
                              <option value="
                                        1"> Saúde e Esportes </option>
                              <option value="
                                        2"> Finanças e Investimentos </option>
                              <option value="
                                        3"> Relacionamentos </option>
                              <option value="
                                        4"> Negócios e Carreira </option>
                              <option value="
                                        5"> Espiritualidade </option>
                              <option value="
                                        6"> Sexualidade </option>
                              <option value="
                                        7"> Entretenimento </option>
                              <option value="
                                        8"> Culinária e Gastronomia </option>
                              <option value="
                                        9"> Idiomas </option>
                              <option value="
                                        10"> Direito </option>
                              <option value="
                                        11"> Apps &amp; Software </option>
                              <option value="
                                        12"> Literatura </option>
                              <option value="
                                        13"> Casa e Construção </option>
                              <option value="
                                        14"> Desenvolvimento Pessoal </option>
                              <option value="
                                        15"> Moda e Beleza </option>
                              <option value="
                                        16"> Animais e Plantas </option>
                              <option value="
                                        17"> Educacional </option>
                              <option value="
                                        18"> Hobbies </option>
                              <option value="
                                        19"> Internet </option>
                              <option value="
                                        20"> Ecologia e Meio Ambiente </option>
                              <option value="
                                        21"> Música e Artes </option>
                              <option value="
                                        22"> Tecnologia da Informação </option>
                              <option selected="" value="
                                        23"> Empreendedorismo Digital </option>
                              <option value="
                                        24"> Outros </option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="form-label" for="default-textarea">Descrição do Produto</label>
                      <div class="form-control-wrap">
                        <textarea class="form-control no-resize inp_description" id="default-textarea"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <label class="form-label" for="productImage">Imagem do Produto (Tamanho recomendado: 300x250 pixels)</label>
                    <input type="hidden" class="inp_image" value="/upload/63f6feef9b98e.png">
                    <input type="file" id="productImage" class="d-none" change="productUploadImage">
                    <div class="ez-dropzone">
                      <div class="dz-message" data-dz-message="">
                        <span class="dz-message-text img_product_image">
                          <img src="/upload/63f6feef9b98e.png">
                        </span>
                        <span class="dz-message-or">&nbsp;</span>
                        <button type="button" class="btn btn-primary" onclick="productImage.click()">SELECT</button>
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
                  <h4>Produtos</h4>
                  <div class="col-lg-12">
                    <div class="form-group">
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDefault">Add Produtos</button>
                    </div>
                  </div>
                  <div class="nk-block">
                    <div class="card card-bordered">
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
                            <div class="nk-tb-item tr">
                              <div class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                  <input type="checkbox" class="custom-control-input" id="pid1">
                                  <label class="custom-control-label" for="pid1"></label>
                                </div>
                              </div>
                              <div class="nk-tb-col">
                                <span class="tb-product">
                                  <img src="/upload/63e12e1c683af.png" alt="" class="thumb">
                                  <a to="https://rocketpays.app/product/56/edit" href="https://rocketpays.app/product/56/edit">
                                    <span class="title">Curso Viver de Multinivel</span>
                                  </a>
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-inner"></div>
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
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="form-label" for="default-01">O produto é grátis?</label>
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input inp_is_free" name="reg-public" id="switch-is-free" !toggle=".div_product_price, .div_product_price_promo" load="toggleStatement(element, true)">
                          <label class="custom-control-label" for="switch-is-free"></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4 div_product_price d-block">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Valor do produto</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_price" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)" value="97">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4 div_product_price_promo d-block">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Valor promocional</label>
                      <div class="form-control-wrap">
                        <input type="text" class="form-control inp_price_promo" id="default-01" keyup="$inputCurrency" keydown="$onlyNumbers" blur="$inputCurrency" load="element.value = currency(element.value)" value="47" placeholder="">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row gy-4 mt-4">
                  <div class="col-sm-4 div_product_stock_qty d-none">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Quantidade em estoque?</label>
                      <div class="form-control-wrap">
                        <input class="form-control inp_stock_qty" value="">
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
                      <label class="form-label" for="default-01">Página de vendas</label>
                      <div class="form-control-wrap">
                        <div class="input-group">
                          <input type="text" value="https://formulaviverdodigital.com.br/" class="form-control inp_landing_page" id="basic-url">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label" for="default-01">E-mail de suporte</label>
                      <div class="form-control-wrap">
                        <input type="text" class="form-control inp_support_email" id="default-01" value="contato@formulaviverdodigital.com.br" placeholder="">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label" for="default-01">Nome de exibição do produtor</label>
                      <div class="form-control-wrap">
                        <input type="text" class="form-control inp_author" id="default-01" value="Higor Valetta" placeholder="">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Tempo de garantia</label>
                      <div class="form-control-wrap">
                        <select class="form-select inp_warranty_time">
                          <option selected="" value="7">7 dias</option>
                          <option value="14">14 dias</option>
                          <option value="21">21 dias</option>
                          <option value="30">30 dias</option>
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
        <button click="productOnSubmit" class="btn btn-primary">Salvar</button>
      </div>
    </div>
</div>
</content>