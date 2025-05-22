<?php use Backend\Enums\Kyc\{EKycType, EKycStatus};
$full_name = $kyc?->name ?? '';
$aux_name = explode(' ', $full_name);
$first_name = $aux_name[0] ?? ''; 
$last_name = substr($full_name, strlen($first_name), strlen($full_name));
$edit_enabled = empty($kyc) || $kyc?->status == EKycStatus::REJECTED->value;
$is_cpf = strlen($kyc?->doc ?? '') <= 14;
$is_cnpj = strlen($kyc?->doc ?? '') > 14;
?>

<title>Kyc</title>

<content>
<!-- tabela vazia -->
<?php if ($kyc?->status == EKycStatus::CONFIRMED->value): ?>
<div class="nk-block nk-block-middle wide-md mx-auto">
  <div class="nk-block-content nk-error-ld text-center">
    <center><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 114 113.9" style=" width: 220px; "> <path d="M87.84,110.34l-48.31-7.86a3.55,3.55,0,0,1-3.1-4L48.63,29a3.66,3.66,0,0,1,4.29-2.8L101.24,34a3.56,3.56,0,0,1,3.09,4l-12.2,69.52A3.66,3.66,0,0,1,87.84,110.34Z" transform="translate(-4 -2.1)" fill="#c4cefe"></path> <path d="M33.73,105.39,78.66,98.1a3.41,3.41,0,0,0,2.84-3.94L69.4,25.05a3.5,3.5,0,0,0-4-2.82L20.44,29.51a3.41,3.41,0,0,0-2.84,3.94l12.1,69.11A3.52,3.52,0,0,0,33.73,105.39Z" transform="translate(-4 -2.1)" fill="#c4cefe"></path> <rect x="22" y="17.9" width="66" height="88" rx="3" ry="3" fill="#6576ff"></rect> <rect x="31" y="85.9" width="48" height="10" rx="1.5" ry="1.5" fill="#fff"></rect> <rect x="31" y="27.9" width="48" height="5" rx="1" ry="1" fill="#e3e7fe"></rect> <rect x="31" y="37.9" width="23" height="3" rx="1" ry="1" fill="#c4cefe"></rect> <rect x="59" y="37.9" width="20" height="3" rx="1" ry="1" fill="#c4cefe"></rect> <rect x="31" y="45.9" width="23" height="3" rx="1" ry="1" fill="#c4cefe"></rect> <rect x="59" y="45.9" width="20" height="3" rx="1" ry="1" fill="#c4cefe"></rect> <rect x="31" y="52.9" width="48" height="3" rx="1" ry="1" fill="#e3e7fe"></rect> <rect x="31" y="60.9" width="23" height="3" rx="1" ry="1" fill="#c4cefe"></rect> <path d="M98.5,116a.5.5,0,0,1-.5-.5V114H96.5a.5.5,0,0,1,0-1H98v-1.5a.5.5,0,0,1,1,0V113h1.5a.5.5,0,0,1,0,1H99v1.5A.5.5,0,0,1,98.5,116Z" transform="translate(-4 -2.1)" fill="#9cabff"></path> <path d="M16.5,85a.5.5,0,0,1-.5-.5V83H14.5a.5.5,0,0,1,0-1H16V80.5a.5.5,0,0,1,1,0V82h1.5a.5.5,0,0,1,0,1H17v1.5A.5.5,0,0,1,16.5,85Z" transform="translate(-4 -2.1)" fill="#9cabff"></path> <path d="M7,13a3,3,0,1,1,3-3A3,3,0,0,1,7,13ZM7,8a2,2,0,1,0,2,2A2,2,0,0,0,7,8Z" transform="translate(-4 -2.1)" fill="#9cabff"></path> <path d="M113.5,71a4.5,4.5,0,1,1,4.5-4.5A4.51,4.51,0,0,1,113.5,71Zm0-8a3.5,3.5,0,1,0,3.5,3.5A3.5,3.5,0,0,0,113.5,63Z" transform="translate(-4 -2.1)" fill="#9cabff"></path> <path d="M107.66,7.05A5.66,5.66,0,0,0,103.57,3,47.45,47.45,0,0,0,85.48,3h0A5.66,5.66,0,0,0,81.4,7.06a47.51,47.51,0,0,0,0,18.1,5.67,5.67,0,0,0,4.08,4.07,47.57,47.57,0,0,0,9,.87,47.78,47.78,0,0,0,9.06-.87,5.66,5.66,0,0,0,4.08-4.09A47.45,47.45,0,0,0,107.66,7.05Z" transform="translate(-4 -2.1)" fill="#2ec98a"></path> <path d="M100.66,12.81l-1.35,1.47c-1.9,2.06-3.88,4.21-5.77,6.3a1.29,1.29,0,0,1-1,.42h0a1.27,1.27,0,0,1-1-.42c-1.09-1.2-2.19-2.39-3.28-3.56a1.29,1.29,0,0,1,1.88-1.76c.78.84,1.57,1.68,2.35,2.54,1.6-1.76,3.25-3.55,4.83-5.27l1.35-1.46a1.29,1.29,0,0,1,1.9,1.74Z" transform="translate(-4 -2.1)" fill="#fff"></path> </svg>                    </center>
    <div class="wide-xs mx-auto">
      <h3 class="nk-error-title mt-2">Você já verificou sua identidade</h3>
      <p class="nk-error-text">A sua conta encontra-se verificada de acordo com o processo de identificação.</p>
      <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light  d-md-none">
        <em class="icon ni ni-help"></em>
      </a>
      <a href="#" data-bs-toggle="modal" data-bs-target="#modalHelp"  class="btn btn-outline-light d-none d-md-inline-flex">
        <em class="icon ni ni-help"></em>
        <span>Aprenda mais sobre esse recurso</span>
      </a>
    </div>
  </div>
</div>
<?php else: ?>

<div class="container-xl wide-lg">
  <div class="nk-content-inner">
    <div class="nk-content-body">
      <div class="kyc-app wide-sm m-auto">
        <div class="nk-block-head nk-block-head-lg wide-xs mx-auto">
          <div class="nk-block-head-content text-center">
            <h2 class="nk-block-title fw-normal">
              <font style="vertical-align: inherit;">
                <font style="vertical-align: inherit;">Verificação de identidade</font>
              </font>
            </h2>
            <div class="nk-block-des">
              <p>
                <font style="vertical-align: inherit;">
                  <font style="vertical-align: inherit;">Para cumprir o regulamento, cada participante terá que passar por verificação de identidade (KYC/AML) para evitar causas de fraude.</font>
                </font>
              </p>
            </div>
          </div>
        </div>
        
        <?php if ($kyc?->status == EKycStatus::PENDING->value): ?>
            <div class="wide-xs mx-auto text-center mb-4">
              <p class="nk-error-text" style="background: #a480152b; border-radius: 4px; padding: 4px 5px; color: #FFC107;">Seus dados estão em análise.</p>
            </div>
        <?php endif ?>
        
        <div class="nk-block frm_edit_kyc">
          <div class="card card-bordered">
            <div class="nk-kycfm">
              <div class="nk-kycfm-head">
                <div class="nk-kycfm-count">01</div>
                <div class="nk-kycfm-title">
                  <h5 class="title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Detalhes pessoais</font>
                    </font>
                  </h5>
                  <p class="sub-title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Suas informações pessoais simples necessárias para identificação.</font>
                    </font>
                  </p>
                </div>
              </div>
              <div class="nk-kycfm-content">
                <div class="nk-kycfm-note">
                  <em class="icon ni ni-info-fill" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dica à direita" data-bs-original-title="Tooltip on right"></em>
                  <p>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Por favor digite com cuidado e preencha o formulário com seus dados pessoais. </font>
                      <font style="vertical-align: inherit;">Você não pode editar esses detalhes depois de enviar o formulário.</font>
                    </font>
                  </p>
                </div>
                <div class="row g-4">
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Endereço de e-mail </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_email" value="<?php echo $user->email; ?>" disabled />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Número de telefone </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_phone" value="<?php echo $kyc?->phone; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Data de Nascimento / Criação </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg date-picker-alt inp_birthdate" 
                        value="<?php echo $kyc?->birthdate ? date('m/d/Y', strtotime($kyc?->birthdate)) : ''; ?>" 
                        <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nº do documento (CPF/CNPJ)</font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_doc"
                        keyup="cpfCnpjOnKeyup" load="setTimeout(() => { element.value = '<?php echo $kyc?->doc; ?>'; }, 1)"
                        value="" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 div_first_name" style="<?php if ($is_cpf): ?> display: block <?php elseif($is_cnpj): ?> display: none <?php endif; ?>">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nome </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_first_name" value="<?php echo $first_name; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 div_last_name" style="<?php if ($is_cpf): ?> display: block <?php elseif($is_cnpj): ?> display: none <?php endif; ?>">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Sobrenome </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_last_name" value="<?php echo $last_name; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 div_responsible_name" style="<?php if ($is_cpf): ?> display: none <?php elseif($is_cnpj): ?> display: block <?php endif; ?>">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nome do responsável </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_responsible_name" value="<?php echo $kyc?->responsible_name; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 div_responsible_doc"
                    style="<?php if ($is_cpf): ?> display: none <?php elseif($is_cnpj): ?> display: block <?php endif; ?>">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">CPF do responsável </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_responsible_doc" 
                        value="<?php echo $kyc?->responsible_doc; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 div_fantasy_name"
                    style="<?php if ($is_cpf): ?> display: none <?php elseif($is_cnpj): ?> display: block <?php endif; ?>">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nome fantasia </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_fantasy_name"
                        value="<?php echo $kyc?->fantasy_name; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="nk-kycfm-head">
                <div class="nk-kycfm-count">02</div>
                <div class="nk-kycfm-title">
                  <h5 class="title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Seu endereço</font>
                    </font>
                  </h5>
                  <p class="sub-title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Suas informações pessoais simples necessárias para identificação</font>
                    </font>
                  </p>
                </div>
              </div>
              <div class="nk-kycfm-content">
                <div class="nk-kycfm-note">
                  <em class="icon ni ni-info-fill" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dica à direita" data-bs-original-title="Tooltip on right"></em>
                  <p>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Você não pode editar esses detalhes depois de enviar o formulário.</font>
                    </font>
                  </p>
                </div>
                <div class="row g-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">CEP </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_zipcode" value="<?php echo $kyc?->zipcode; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Rua </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_street" value="<?php echo $kyc?->street; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nº</font>
                          </font>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_address_no" value="<?php echo $kyc?->address_no; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Bairro </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_neighborhood" value="<?php echo $kyc?->neighborhood; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Cidade </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_city" value="<?php echo $kyc?->city; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Estado </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_state" value="<?php echo $kyc?->state; ?>" 
                        <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?> placeholder="São Paulo">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="form-label-group">
                        <label class="form-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">Nacionalidade </font>
                          </font>
                          <span class="text-danger">
                            <font style="vertical-align: inherit;">
                              <font style="vertical-align: inherit;">*</font>
                            </font>
                          </span>
                        </label>
                      </div>
                      <div class="form-control-group">
                        <input type="text" class="form-control form-control-lg inp_nationality" value="<?php echo $kyc?->nationality ?: 'Brasileira'; ?>" <?php if (!$edit_enabled): ?> disabled="" <?php endif; ?>>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="nk-kycfm-head">
                    <div class="nk-kycfm-count">03</div>
                    <div class="nk-kycfm-title">
                      <h5 class="title">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Conta bancária</font>
                        </font>
                      </h5>
                      <p class="sub-title">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Conta para receber os pagamentos.</font>
                        </font>
                      </p>
                    </div>
                  </div>
                  <div class="nk-kycfm-content">
                    <div class="row g-4">
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="form-label-group">
                            <label class="form-label">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Banco </font>
                              </font>
                              <span class="text-danger">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">*</font>
                                </font>
                              </span>
                            </label>
                          </div>
                          <div class="form-control-group">
                            <select class="form-select inp_bankacc_bank">
                              <?php foreach (iugu_banks() as $bank) : ?>
                                <option value="<?php echo $bank->id; ?>"><?php echo $bank->name; ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="form-label-group">
                            <label class="form-label">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Tipo de conta </font>
                              </font>
                              <span class="text-danger">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">*</font>
                                </font>
                              </span>
                            </label>
                          </div>
                          <div class="form-control-group">
                            <select class="form-select inp_bankacc_type">
                              <option value="current">Conta Corrente</option>
                              <option value="savings">Conta Poupança</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="form-label-group">
                            <label class="form-label">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Agência </font>
                              </font>
                              <span class="text-danger">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">*</font>
                                </font>
                              </span>
                            </label>
                          </div>
                          <div class="form-control-group">
                            <input type="text" class="form-control inp_bankacc_agency" id="comp-email" value="" placeholder="0000-0">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="form-label-group">
                            <label class="form-label">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Conta </font>
                              </font>
                              <span class="text-danger">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">*</font>
                                </font>
                              </span>
                            </label>
                          </div>
                          <div class="form-control-group">
                          <input type="text" class="form-control inp_bankacc_account" id="comp-copyright" value="" placeholder="000000-0">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              <div class="nk-kycfm-head">
                <div class="nk-kycfm-count">04</div>
                <div class="nk-kycfm-title">
                  <h5 class="title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Carregamento de documento</font>
                    </font>
                  </h5>
                  <p class="sub-title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Para verificar sua identidade, faça o upload de qualquer um dos seus documentos.</font>
                    </font>
                  </p>
                </div>
              </div>
              <div class="nk-kycfm-content">
                <div class="nk-kycfm-note">
                  <em class="icon ni ni-info-fill" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dica à direita" data-bs-original-title="Tooltip on right"></em>
                  <p>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Para concluir, faça o upload de qualquer um dos seguintes documentos pessoais.</font>
                    </font>
                  </p>
                </div>
                <ul class="nk-kycfm-control-list g-3 ul_select_doc_type">
                  <li class="nk-kycfm-control-item" onclick="$('.div_doc_back').hide();$('.div_doc_front').show()">
                    <input class="nk-kycfm-control" <?php if ($kyc?->type == EKycType::PASSPORT->value): ?> checked="" <?php endif; ?> type="radio" value="passport" name="id-proof" id="passport" data-title="Passport">
                    <label class="nk-kycfm-label" for="passport">
                      <span class="nk-kycfm-label-icon">
                        <span class="label-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 71.9904 75.9285">
                            <path d="M27.14,23.73A15.55,15.55,0,1,0,42.57,39.4v-.12A15.5,15.5,0,0,0,27.14,23.73Zm11.42,9.72H33a25.55,25.55,0,0,0-2.21-6.53A12.89,12.89,0,0,1,38.56,33.45ZM31,39.28a26.9929,26.9929,0,0,1-.2,3.24H23.49a26.0021,26.0021,0,0,1,0-6.48H30.8A29.3354,29.3354,0,0,1,31,39.28ZM26.77,26.36h.75a21.7394,21.7394,0,0,1,2.85,7.09H23.91A21.7583,21.7583,0,0,1,26.77,26.36Zm-3.28.56a25.1381,25.1381,0,0,0-2.2,6.53H15.72a12.88,12.88,0,0,1,7.78-6.53ZM14.28,39.28A13.2013,13.2013,0,0,1,14.74,36H20.9a29.25,29.25,0,0,0,0,6.48H14.74A13.1271,13.1271,0,0,1,14.28,39.28Zm1.44,5.83h5.57a25.9082,25.9082,0,0,0,2.2,6.53A12.89,12.89,0,0,1,15.72,45.11ZM27.51,52.2h-.74a21.7372,21.7372,0,0,1-2.85-7.09h6.44A21.52,21.52,0,0,1,27.51,52.2Zm3.28-.56A25.1413,25.1413,0,0,0,33,45.11h5.57a12.84,12.84,0,0,1-7.77,6.53Zm2.59-9.12a28.4606,28.4606,0,0,0,0-6.48h6.15a11.7,11.7,0,0,1,0,6.48ZM14.29,62.6H40v2.6H14.28V62.61ZM56.57,49l1.33-5,2.48.67-1.33,5Zm-6,22.52L55.24,54l2.48.67L53.06,72.14Zm21.6-61.24-29.8-8a5.13,5.13,0,0,0-6.29,3.61h0L33.39,16H6.57A2.58,2.58,0,0,0,4,18.55V70.38A2.57,2.57,0,0,0,6.52,73L6.57,73h29.7l17.95,4.85a5.12,5.12,0,0,0,6.28-3.6v-.06L75.8,16.61a5.18,5.18,0,0,0-3.6066-6.3763L72.18,10.23ZM6.57,70.38V18.55H45.14a2.57,2.57,0,0,1,2.57,2.57V67.79a2.57,2.57,0,0,1-2.55,2.59H6.57ZM73.34,15.91,58,73.48a2.59,2.59,0,0,1-2.48,1.93,2.5192,2.5192,0,0,1-.67-.09l-9-2.42a5.15,5.15,0,0,0,4.37-5.11V47.24l1.32.36,1.33-5-2.49-.67-.16.62V21.94l2.62.71,3.05,10,2.13.57L57.88,24l3.76,1,1.65,3,1.42.39-.25-4.09,2.24-3.42-1.41-.39L62.4,22.15l-3.76-1,4.76-7.92-2.13-.57-7.6,7.14-4-1.08A5.1,5.1,0,0,0,45.14,16H36.05l2.51-9.45a2.57,2.57,0,0,1,3.12-1.84h0l29.81,8.05a2.56,2.56,0,0,1,1.56,1.21A2.65,2.65,0,0,1,73.34,15.91ZM56.57,39.59l.66-2.5,2.44.65L59,40.24Zm4.88,1.31.66-2.51,2.44.66-.65,2.5Zm-9.76-2.61.66-2.51,2.45.66-.66,2.5Z" transform="translate(-3.9995 -2.101)" fill="#6476ff"></path>
                          </svg>
                        </span>
                      </span>
                      <span class="nk-kycfm-label-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Passaporte</font>
                        </font>
                      </span>
                    </label>
                  </li>
                  <li class="nk-kycfm-control-item" onclick="$('.div_doc_back').show();$('.div_doc_front').show()">
                    <input class="nk-kycfm-control" <?php if ($kyc?->type == EKycType::ID->value || !$kyc?->type): ?> checked="" <?php endif; ?> type="radio" value="id" name="id-proof" id="national-id" data-title="National ID">
                    <label class="nk-kycfm-label" for="national-id">
                      <span class="nk-kycfm-label-icon">
                        <span class="label-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 76 63">
                            <path d="M76,18.79,56.53,9.56a6.19,6.19,0,0,0-5.19,0l-19.5,9.23a3.35,3.35,0,0,0-1.9,2.55H8.33A6.26,6.26,0,0,0,2,27.51v38.3A6.27,6.27,0,0,0,8.33,72H71.67A6.27,6.27,0,0,0,78,65.81v-44A3.37,3.37,0,0,0,76,18.79Zm-.56,47a3.77,3.77,0,0,1-3.8,3.71H8.33a3.77,3.77,0,0,1-3.8-3.71V27.51a3.75,3.75,0,0,1,3.7993-3.7H29.87v9.34A34.49,34.49,0,0,0,44,60.41l7.51,5.8a4.11,4.11,0,0,0,4.94,0l7.51-5.8A36.5307,36.5307,0,0,0,75.47,45.62V65.81Zm0-32.66a32.09,32.09,0,0,1-13.1,25.34l-7.51,5.8a1.5,1.5,0,0,1-1.8,0l-7.51-5.8A32.05,32.05,0,0,1,32.4,33.15V21.83A.91.91,0,0,1,33,21l19.5-9.23a3.51,3.51,0,0,1,3,0L74.92,21a.91.91,0,0,1,.55.82V33.15ZM53.87,21.43a12.47,12.47,0,0,0-12.6,12.31,12.62,12.62,0,0,0,25.23,0,12.46,12.46,0,0,0-12.6178-12.3l-.0122,0Zm0,22.14A9.83,9.83,0,1,1,64,33.78a10,10,0,0,1-10.1,9.79Zm3.31-13.22-5.32,5.19-1.18-1.15a1.29,1.29,0,0,0-1.79,0,1.2,1.2,0,0,0-.013,1.697l.013.013h0l2.08,2a1.27,1.27,0,0,0,1.79,0L59,32.09a1.22,1.22,0,0,0,0-1.72h0a1.29,1.29,0,0,0-1.8,0ZM29.87,57.16h-20a1.24,1.24,0,1,0,0,2.47h20a1.24,1.24,0,0,0,0-2.47ZM19.73,62.1H9.89a1.24,1.24,0,0,0,0,2.48h9.84a1.24,1.24,0,0,0,0-2.48Zm8.66-14.28h0L24,45.71a.36.36,0,0,1-.22-.34V44.16a1.878,1.878,0,0,1,.18-.24,10.9991,10.9991,0,0,0,1.35-2.48,2.53,2.53,0,0,0,1.23-2.16V37.51a2.61,2.61,0,0,0-.46-1.43V34a4.69,4.69,0,0,0-1.15-3.43,6.68,6.68,0,0,0-5.19-1.85,6.67,6.67,0,0,0-5.18,1.85A4.61,4.61,0,0,0,13.4,34v2a2.46,2.46,0,0,0-.46,1.43v1.78a2.49,2.49,0,0,0,.78,1.81,10.148,10.148,0,0,0,1.52,3v1.2a.36.36,0,0,1-.21.33l-4.1,2.15A4.79,4.79,0,0,0,8.33,52v1.43a1.26,1.26,0,0,0,.37.88,1.33,1.33,0,0,0,.9.36H29.87a1.31,1.31,0,0,0,.9-.36,1.26,1.26,0,0,0,.37-.88V52.11A4.76,4.76,0,0,0,28.39,47.82Zm.21,4.4H10.87V52a2.27,2.27,0,0,1,1.25-2l4.12-2.16a2.85,2.85,0,0,0,1.54-2.5V43.72a1.3,1.3,0,0,0-.3-.8,7.39,7.39,0,0,1-1.4-2.8,1.53,1.53,0,0,0-.6-.83V37.46a1.22,1.22,0,0,0,.43-.92v-2.7a2.17,2.17,0,0,1,.53-1.58,4.38,4.38,0,0,1,3.28-1,4.43,4.43,0,0,1,3.26,1,2.22,2.22,0,0,1,.55,1.6.8552.8552,0,0,0,0,.16v2.56a1.36,1.36,0,0,0,.46,1l-.08,1.86a1.23,1.23,0,0,0-.84.8,8.5819,8.5819,0,0,1-1.19,2.31c-.1.14-.22.28-.33.41a1.22,1.22,0,0,0-.33.82v1.66A2.86,2.86,0,0,0,22.86,48l4.41,2a2.28,2.28,0,0,1,1.33,2.07v.15Z" transform="translate(-2 -8.9898)" fill="#6476ff"></path>
                          </svg>
                        </span>
                      </span>
                      <span class="nk-kycfm-label-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">identidade nacional</font>
                        </font>
                      </span>
                    </label>
                  </li>
                  <li class="nk-kycfm-control-item" onclick="$('.div_doc_back').show();$('.div_doc_front').show()">
                    <input class="nk-kycfm-control" <?php if ($kyc?->type == EKycType::DRIVING_LICENSE->value): ?> checked="" <?php endif; ?> type="radio" value="driving_license" name="id-proof" id="driver-licence" data-title="Driving License">
                    <label class="nk-kycfm-label" for="driver-licence">
                      <span class="nk-kycfm-label-icon">
                        <span class="label-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 76 76">
                            <path d="M70.5,2H9.9A7.9167,7.9167,0,0,0,2,9.9V51.5A7.49,7.49,0,0,0,9.5,59H31.6a1.538,1.538,0,0,0,1.5-1.5A1.4727,1.4727,0,0,0,31.6,56H9.7A4.6946,4.6946,0,0,1,5,51.3V15H75V46.9a1.5,1.5,0,1,0,3,0V10.1C78,5.6,74.7,2,70.5,2ZM75,11H5V9.5A4.6115,4.6115,0,0,1,9.8,5H70.3a4.6115,4.6115,0,0,1,4.8,4.5V11ZM64.3,29.5a4.1408,4.1408,0,0,1-1.5,2.9.9593.9593,0,0,0-.3,1L63,35a.9879.9879,0,0,0,.7.7l3.9,1a2.0749,2.0749,0,0,1,1,.6.972.972,0,0,0,1.4-.1h0a.9663.9663,0,0,0-.1-1.4h0a5.7028,5.7028,0,0,0-2.2-1.1l-3-.8-.1-.5a7.08,7.08,0,0,0,1.6-3.1,1.8059,1.8059,0,0,0,1-1.4l.2-1.7a1.8411,1.8411,0,0,0-.8-1.8l.1-1.1.2-.2a2.5846,2.5846,0,0,0,.1-3.4,4.3847,4.3847,0,0,0-3.8-1.8,7.2965,7.2965,0,0,0-3.5.9c-4.1.1-4.6,2.4-4.6,4,0,.3.1.9.1,1.5-.1.1-.3.2-.4.3a1.9638,1.9638,0,0,0-.5,1.5l.2,1.7a2.0944,2.0944,0,0,0,1.1,1.5,6.1046,6.1046,0,0,0,1.5,3l-.1.6-3,.8A5.4636,5.4636,0,0,0,49.9,40a.9448.9448,0,0,0,1,1H65a1,1,0,0,0,0-2H52.1a3.1116,3.1116,0,0,1,2.5-2.3l3.6-.9a.9879.9879,0,0,0,.7-.7l.4-1.7a.8065.8065,0,0,0-.3-.9,4.6858,4.6858,0,0,1-1.4-2.9.8949.8949,0,0,0-1-.8l-.3-1.6a.9448.9448,0,0,0,1-1v-.1a19.0913,19.0913,0,0,1-.2-2c0-1,0-2,2.9-2a1.4213,1.4213,0,0,0,.6-.2,4.1045,4.1045,0,0,1,2.6-.7,2.1984,2.1984,0,0,1,2.1.9c.4.6.2.8.1.9l-.4.2a.9078.9078,0,0,0-.3.7L64.6,26a.7787.7787,0,0,0,.7.9h.2l-.1,1.6A1.0278,1.0278,0,0,0,64.3,29.5ZM34.1,27a1.538,1.538,0,0,0,1.5-1.5A1.4727,1.4727,0,0,0,34.1,24h-6a1.5,1.5,0,0,0,0,3ZM12.8,37h21a1.5,1.5,0,0,0,0-3h-21a1.538,1.538,0,0,0-1.5,1.5A1.4727,1.4727,0,0,0,12.8,37Zm-.4-10h9a1.5,1.5,0,0,0,0-3h-9a1.5,1.5,0,1,0,0,3ZM74.9,55a2.0059,2.0059,0,0,0-2-2h-.2a7.0756,7.0756,0,0,0-3.1,1c-1.4-3-3.8-5.6-5.4-6.4-1.1-.6-4.9-1.2-8.3-1.2s-7.1.6-8.2,1.2c-1.7.8-4,3.4-5.4,6.4a6.6831,6.6831,0,0,0-3.1-1,2.2959,2.2959,0,0,0-1.4.4A2.0876,2.0876,0,0,0,37,55a5.5585,5.5585,0,0,0,2,4c.2.1.3.2.5.3a16.4687,16.4687,0,0,0-1,5.8c0,2.1.2,5.8,1.5,7.7v2.4a2.9483,2.9483,0,0,0,2.8,2.9h3.4A2.8616,2.8616,0,0,0,49,75.3h0v-1a27.5212,27.5212,0,0,0,7,1,27.5212,27.5212,0,0,0,7-1v1a2.7754,2.7754,0,0,0,2.7,2.8H69a2.8183,2.8183,0,0,0,2.9-2.8h0V72.9c1.2-1.9,1.4-5.5,1.4-7.7a16.0869,16.0869,0,0,0-1-5.8.8643.8643,0,0,0,.6-.3A5.7634,5.7634,0,0,0,74.9,55ZM49.3,50.1a22.2387,22.2387,0,0,1,6.8-.8,30.84,30.84,0,0,1,6.8.8c1.1.5,3.6,3.4,4.6,6.5-2.7.4-9.1,1.2-11.5,1.2s-8.7-.9-11.4-1.2C45.7,53.5,48.2,50.7,49.3,50.1Zm-8.1,6.6c-.1-.2-.3-.3-.4-.5a2.1859,2.1859,0,0,1,.5.3c0,.1,0,.1-.1.2ZM46.1,75H43V74h3v1.1Zm23,0H66V74h3v1.1Zm.4-5H66.9a6.7381,6.7381,0,0,0-2.6.9h0a32.0084,32.0084,0,0,1-8.2,1.4,42.62,42.62,0,0,1-7.6-1.5,6.1538,6.1538,0,0,0-1.9-.2l-4,.4a19.5493,19.5493,0,0,1-.8-5.9,6.15,6.15,0,0,1,.1-1.4c1.9.1,4.2.7,4.2,1.4a1.52,1.52,0,0,0,1.4,1.5h0c.8,0,1.5-1.4,1.5-1.4v-.7c0-3.4-4.7-4-6.5-4.1.2-.5.4-1,.6-1.4h0c.4.1,9.8,1.4,13,1.4S68.7,59.1,69,59h0c.2.5.4.9.6,1.4-1.8.1-6.4.7-6.4,4.1a1.4036,1.4036,0,0,0,2.8,0v-.1c0-.7,2.2-1.3,4.2-1.4a6.602,6.602,0,0,1,.1,1.4A17.2549,17.2549,0,0,1,69.5,70Zm1.6-13.3c0-.1-.1-.1-.1-.2l.5-.3A2.1813,2.1813,0,0,1,71.1,56.7ZM59.2,64h-6a1.5,1.5,0,0,0,0,3h6a1.5,1.5,0,0,0,0-3Z" transform="translate(-2 -2)" fill="#6476ff"></path>
                          </svg>
                        </span>
                      </span>
                      <span class="nk-kycfm-label-text">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">Carteira de motorista</font>
                        </font>
                      </span>
                    </label>
                  </li>
                </ul>
                <h6 class="title">
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">Para evitar atrasos ao verificar a conta, verifique abaixo:</font>
                  </font>
                </h6>
                <ul class="list list-sm list-checked">
                  <li>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">A credencial escolhida não deve estar vencida.</font>
                    </font>
                  </li>
                  <li>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">O documento deve estar em boas condições e claramente visível.</font>
                    </font>
                  </li>
                  <li>
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Certifique-se de que não haja reflexos de luz no cartão.</font>
                    </font>
                  </li>
                </ul>
                
                <?php if ($kyc?->doc_front || $kyc?->doc_back): ?>
                <div class="row mt-5">
                <?php if ($kyc?->doc_front): ?>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label w-100 text-center">Frente do documento</label>
                      <div class="form-control-wrap">
                        <img src="<?php echo site_url(); ?>/kyc/<?php echo $kyc?->id; ?>/front.png" class="px-5" />
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>

                  <?php if ($kyc?->doc_back): ?>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label w-100 text-center">Verso do documento</label>
                      <div class="form-control-wrap">
                        <img src="<?php echo site_url(); ?>/kyc/<?php echo $kyc?->id; ?>/back.png" class="px-5" />
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>

                  <?php if ($kyc?->front_selfie): ?>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label w-100 text-center">Selfie com documento</label>
                      <div class="form-control-wrap">
                        <img src="<?php echo site_url(); ?>/kyc/<?php echo $kyc?->id; ?>/back.png" class="px-5" />
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($edit_enabled): ?>
                <div class="nk-kycfm-upload mt-5 div_doc_front">
                  <h6 class="title nk-kycfm-upload-title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Carregue a frente do documento</font>
                    </font>
                  </h6>
                  <div class="row align-items-center">
                    <div class="col-sm-8">
                      <div class="nk-kycfm-upload-box">
                        <div class="upload-zone dropzone dz-clickable">
                          <div class="dz-message" data-dz-message="">
                            <div style="display: table; margin: 0 auto;">
                              <img class="img_doc_front" style="display:none;margin-bottom:20px" />
                              <div class="div_dropzone_doc_front">
                              <span class="dz-message-text">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">Arraste e solte o arquivo</font>
                                </font>
                              </span>
                              <span class="dz-message-or">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">ou</font>
                                </font>
                              </span>
                              </div>
                            </div>
                            <input type="hidden" class="inp_doc_front" value="<?php echo $kyc?->doc_front; ?>" />
                            <input type="file" class="file_doc_front" style="display: none" change="uploadDocFrontOnChange" />
                            <button class="btn btn-primary" click="uploadDocFrontOnClick">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">SELECIONE</font>
                              </font>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                      <div class="mx-md-4">
                        <img src="/images/id-front.svg" alt="Identificação frontal">
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
                <?php if ($edit_enabled): ?>
                <div class="nk-kycfm-upload div_doc_back">
                  <h6 class="title nk-kycfm-upload-title">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Carregue o verso do documento</font>
                    </font>
                  </h6>
                  <div class="row align-items-center">
                    <div class="col-sm-8">
                      <div class="nk-kycfm-upload-box">
                        <div class="upload-zone dropzone dz-clickable">
                          <div class="dz-message" data-dz-message="">
                            <div style="display: table; margin: 0 auto;">
                              <img class="img_doc_back" style="display:none;margin-bottom:20px" />
                              <div class="div_dropzone_doc_back">
                              <span class="dz-message-text">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">Arraste e solte o arquivo</font>
                                </font>
                              </span>
                              <span class="dz-message-or">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">ou</font>
                                </font>
                              </span>
                              </div>
                            </div>
                            <input type="hidden" class="inp_doc_back" value="<?php echo $kyc?->doc_back; ?>" />
                            <input type="file" class="file_doc_back" style="display: none" change="uploadDocBackOnChange" />
                            <button class="btn btn-primary" click="uploadDocBackOnClick">
                              <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">SELECIONE</font>
                              </font>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                      <div class="mx-md-4">
                        <img src="/images/id-back.svg" alt="Identificação de volta">
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
                <?php if ($edit_enabled): ?>
                  <div class="nk-kycfm-upload div_front_selfie">
                    <h6 class="title nk-kycfm-upload-title">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Carregue o selfie com o documento</font>
                      </font>
                    </h6>
                    <div class="row align-items-center">
                      <div class="col-sm-8">
                        <div class="nk-kycfm-upload-box">
                          <div class="upload-zone dropzone dz-clickable">
                            <div class="dz-message" data-dz-message="">
                              <div style="display: table; margin: 0 auto;">
                                <img class="img_front_selfie" style="display:none;margin-bottom:20px" />
                                <div class="div_dropzone_front_selfie">
                                <span class="dz-message-text">
                                  <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Arraste e solte o arquivo</font>
                                  </font>
                                </span>
                                <span class="dz-message-or">
                                  <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">ou</font>
                                  </font>
                                </span>
                                </div>
                              </div>
                              <input type="hidden" class="inp_front_selfie" value="<?php echo $kyc?->front_selfie; ?>" />
                              <input type="file" class="file_front_selfie" style="display: none" change="uploadFrontSelfieOnChange" />
                              <button class="btn btn-primary" click="uploadFrontSelfieOnClick">
                                <font style="vertical-align: inherit;">
                                  <font style="vertical-align: inherit;">SELECIONE</font>
                                </font>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                          <img src="/images/id-back.svg" alt="Identificação de volta">
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="nk-kycfm-footer">
                <?php if ($edit_enabled): ?>
                <div class="form-group">
                  <div class="custom-control custom-control-xs custom-checkbox">
                    <input type="checkbox" class="custom-control-input chx_accept_terms" id="tc-agree">
                    <label class="custom-control-label" for="tc-agree">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Eu li os </font>
                      </font>
                      <a href="#">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">termos de condição</font>
                        </font>
                      </a>
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;"> e </font>
                      </font>
                      <a href="#">
                        <font style="vertical-align: inherit;">
                          <font style="vertical-align: inherit;">a política de privacidade</font>
                        </font>
                      </a>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <div class="custom-control custom-control-xs custom-checkbox">
                    <input type="checkbox" class="custom-control-input chx_the_information_is_correct" id="info-assure">
                    <label class="custom-control-label" for="info-assure">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Todas as informações pessoais que inseri estão corretas.</font>
                      </font>
                    </label>
                  </div>
                </div>
                <div class="nk-kycfm-action pt-2">
                  <button type="submit" class="btn btn-lg btn-primary" click="kycOnSubmit">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;">Enviar Documentos</font>
                    </font>
                  </button>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

</content>