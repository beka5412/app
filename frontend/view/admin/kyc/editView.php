<?php

use Backend\Enums\Kyc\{EKycStatus, EKycType}; ?>
<title> Editar Kyc </title>
<content>
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <h3 class="nk-block-title page-title">Editar Kyc</h3>
    </div>
  </div>
  <!-- Inicio Pagina de edicao order bump -->
  <div class="nk-block frm_edit_admin_kyc">
    <div class="row g-gs">
      <div class="col-12 d-flex">
        <div class="col-md-12">
          <div class="card card-bordered card-preview">
            <div class="card-inner">
              <div class="preview-block">
                <div class="row gy-4">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <div class="form-group">
                        <label class="form-label" for="default-01">Ação</label>
                        <select class="form-control sel_status">
                          <option <?php if ($kyc->status == EKycStatus::PENDING->value) : ?> selected="" <?php endif; ?> value="<?php echo EKycStatus::PENDING->value; ?>">Pendente</option>
                          <option <?php if ($kyc->status == EKycStatus::CONFIRMED->value) : ?> selected="" <?php endif; ?> value="<?php echo EKycStatus::CONFIRMED->value; ?>">Confirmar</option>
                          <option <?php if ($kyc->status == EKycStatus::REJECTED->value) : ?> selected="" <?php endif; ?> value="<?php echo EKycStatus::REJECTED->value; ?>">Rejeitar</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <?php if ($is_cpf) : ?>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">Nome</label>
                        <div class="form-control-wrap">
                          <input disabled="" class="form-control inp_name" value="<?php echo $kyc->name; ?>">
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Tipo de documento</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control" value="<?php
                                                                        if ($kyc->type == EKycType::ID->value) echo 'Identidade / CPF'; // RG ou CPF | ID
                                                                        else if ($kyc->type == EKycType::PASSPORT->value) echo 'Passaporte'; // Passaporte | Passport
                                                                        else if ($kyc->type == EKycType::DRIVING_LICENSE->value) echo 'CNH'; // Carteira de motorista | Driving license
                                                                        else if ($kyc->type == EKycType::COMPANY->value) echo 'CNPJ'; // CNPJ | EIN
                                                                        ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Nº do documento</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control" value="<?php echo $kyc->doc; ?>">
                      </div>
                    </div>
                  </div>
                  <?php #if ($kyc->type == EKycType::COMPANY->value): 
                  ?>
                  <?php if ($is_cnpj) : ?>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">Nome fantasia</label>
                        <div class="form-control-wrap">
                          <input disabled="" class="form-control" value="<?php echo $kyc->fantasy_name; ?>">
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">Nome do responsável</label>
                        <div class="form-control-wrap">
                          <input disabled="" class="form-control" value="<?php echo $kyc->responsible_name; ?>">
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="form-label">CPF do responsável</label>
                        <div class="form-control-wrap">
                          <input disabled="" class="form-control" value="<?php echo $kyc->responsible_doc; ?>">
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Data de nascimento / criação no caso de empresa</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_birthdate" value="<?php echo $kyc->birthdate ? date('d/m/Y', strtotime($kyc->birthdate)) : ''; ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Telefone</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_phone" value="<?php echo $kyc->phone; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="form-label">Frente do documento</label>
                          <div class="form-control-wrap">
                            <img src="<?php echo site_url(); ?>/admin/kyc/<?php echo $kyc->id; ?>/front.png" />
                          </div>
                        </div>
                      </div>

                      <?php if (!in_array($kyc->type, [EKycType::PASSPORT->value, EKycType::COMPANY->value])) : ?>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label class="form-label">Verso do documento</label>
                            <div class="form-control-wrap">
                              <img src="<?php echo site_url(); ?>/admin/kyc/<?php echo $kyc->id; ?>/back.png" />
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="form-label">Selfie com o documento</label>
                          <div class="form-control-wrap">
                            <img src="<?php echo site_url(); ?>/admin/kyc/<?php echo $kyc->id; ?>/front.png" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">CEP</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_zipcode" value="<?php echo $kyc->zipcode; ?>">
                      </div>
                    </div>
                  </div>


                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Rua</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_street" value="<?php echo $kyc->street; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Endereço</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_address_no" value="<?php echo $kyc->address_no; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Bairro</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_neighborhood" value="<?php echo $kyc->neighborhood; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Cidade</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_city" value="<?php echo $kyc->city; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Estado</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_state" value="<?php echo $kyc->state; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">Nacionalidade</label>
                      <div class="form-control-wrap">
                        <input disabled="" class="form-control inp_nationality" value="<?php echo $kyc->nationality; ?>">
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
        <button click="adminKycOnSubmit" class="btn btn-primary">Salvar</button>
      </div>
    </div>
  </div>
</content>