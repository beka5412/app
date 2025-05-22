App.User.Kyc.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Kyc.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/kyc/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    kycOnSubmit() {
        let form = document.querySelector('.frm_edit_kyc');
        let doc = form.querySelector('.inp_doc');
        let doc_back = form.querySelector('.inp_doc_back');
        let doc_front = form.querySelector('.inp_doc_front');
        let doc_type_select = form.querySelector('.ul_select_doc_type');
        let doc_type = [ ...doc_type_select.querySelectorAll('input[type=radio]') ].find(item => item.checked);
        let first_name = form.querySelector('.inp_first_name');
        let last_name = form.querySelector('.inp_last_name');
        let email = form.querySelector('.inp_email');
        let birthdate = form.querySelector('.inp_birthdate');
        let phone = form.querySelector('.inp_phone');
        let street = form.querySelector('.inp_street');
        let address_no = form.querySelector('.inp_address_no');
        let city = form.querySelector('.inp_city');
        let state = form.querySelector('.inp_state');
        let nationality = form.querySelector('.inp_nationality');
        let zipcode = form.querySelector('.inp_zipcode');
        let neighborhood = form.querySelector('.inp_neighborhood');
        let responsible_name = form.querySelector('.inp_responsible_name');
        let responsible_doc = form.querySelector('.inp_responsible_doc');
        let fantasy_name = form.querySelector('.inp_fantasy_name');
        let chx_accept_terms = form.querySelector('.chx_accept_terms');
        let chx_the_information_is_correct = form.querySelector('.chx_the_information_is_correct');
        let bankacc_bank = form.querySelector('.inp_bankacc_bank');
        let bankacc_type = form.querySelector('.inp_bankacc_type');
        let bankacc_agency = form.querySelector('.inp_bankacc_agency');
        let bankacc_account = form.querySelector('.inp_bankacc_account');

        if (!chx_accept_terms?.checked) {
            toast('Aceite os termos de condições e políticas de privacidade.');
            return;
        }
        
        if (!chx_the_information_is_correct?.checked) {
            toast('Confirme se as informações estão corretas.');
            return;
        }

        let isCpf = doc?.value?.length <= 14;
        let isCNPJ = doc?.value?.length > 14;

        let data = {
            doc: doc.value || '',
            doc_front: doc_front?.value || '',
            doc_back: doc_back?.value || '',
            type: doc_type?.value || '',
            first_name: isCpf ? first_name.value || '' : '',
            last_name: isCpf ? last_name.value || '' : '',
            email: email.value || '',
            birthdate: birthdate.value || '',
            phone: phone.value || '',
            street: street.value || '',
            address_no: address_no.value || '',
            city: city.value || '',
            state: state.value || '',
            nationality: nationality.value || '',
            zipcode: zipcode.value || '',
            neighborhood: neighborhood.value || '',
            responsible_name: isCNPJ ? responsible_name.value || '' : '',
            responsible_doc: isCNPJ ? responsible_doc.value || '' : '',
            fantasy_name: isCNPJ ? fantasy_name.value || '' : '',
            bankaccount_bank: bankacc_bank.value || '',
            bankaccount_type: bankacc_type.value || '',
            bankaccount_agency: bankacc_agency.value || '',
            bankaccount_account: bankacc_account.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/kyc/update`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/kyc');          
            }
        });
    }
    
    uploadDocBackOnClick() {
        $('.file_doc_back').click();
    }
    
    uploadDocFrontOnClick() {
        $('.file_doc_front').click();
    }

    uploadFrontSelfieOnClick() {
        $('.file_front_selfie').click();
    }

    uploadDocBackOnChange() {
        let form = document.querySelector('.frm_edit_kyc');
        let doc_back = form.querySelector('.inp_doc_back');

        let formData = new FormData();
        formData.append('image', document.querySelector('.file_doc_back')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/kyc/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let image = body?.data?.image;
                if (image) {
                    doc_back.value = image;
                    let img = form.querySelector('.img_doc_back');
                    if (img) {
                        img.src = `<?php echo site_url(); ?>/kyc/images/${image}`;
                        img.style.display = 'block';
                    }
                    let dropzoneText = form.querySelector('.div_dropzone_doc_back');
                    if (dropzoneText) dropzoneText.style.display = 'none';
                }
            }
            else toastError(body.message);
        });
    }

    uploadDocFrontOnChange() {
        let form = document.querySelector('.frm_edit_kyc');
        let doc_front = form.querySelector('.inp_doc_front');

        let formData = new FormData();
        formData.append('image', document.querySelector('.file_doc_front')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/kyc/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let image = body?.data?.image;
                if (image) {
                    doc_front.value = image;
                    let img = form.querySelector('.img_doc_front');
                    if (img) {
                        img.src = `<?php echo site_url(); ?>/kyc/images/${image}`;
                        img.style.display = 'block';
                    }
                    let dropzoneText = form.querySelector('.div_dropzone_doc_front');
                    if (dropzoneText) dropzoneText.style.display = 'none';
                }
            }
            else toastError(body.message);
        });
    }

    uploadFrontSelfieOnChange() {
        let form = document.querySelector('.frm_edit_kyc');
        let front_selfie = form.querySelector('.inp_front_selfie');

        let formData = new FormData();
        formData.append('image', document.querySelector('.file_front_selfie')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/kyc/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let image = body?.data?.image;
                if (image) {
                    front_selfie.value = image;
                    let img = form.querySelector('.img_front_selfie');
                    if (img) {
                        img.src = `<?php echo site_url(); ?>/kyc/images/${image}`;
                        img.style.display = 'block';
                    }
                    let dropzoneText = form.querySelector('.div_dropzone_front_selfie');
                    if (dropzoneText) dropzoneText.style.display = 'none';
                }
            }
            else toastError(body.message);
        });
    }

    inputPhoneMask() {
        let element = $('.frm_edit_kyc .inp_phone');
        var options = {
            placeholder: "(__) ____-____",
            onKeyPress: function(cep, e, field, options_) {
                let masks = ["(00) 0000-00009", "(00) 0 0000-0000"],
                digits = cep.replace(/[^0-9]/g, "").length,
                mask = digits <= 10 ? masks[0] : masks[1];
        
                element.mask(mask, options_);
            }
        };
        
        if ($(document.body)?.mask) element.mask("(00) 0000-0000", options);
    }

    inputCpfCnpjMask(element) {
        let options = {
            placeholder: "___.___.___-__",
            onKeyPress: function(value, e, field, options_) {
                let masks = ["000.000.000-009", "00.000.000/0000-00"],
                digits = value.replace(/\D/g, "").length,
                mask = digits <= 11 ? masks[0] : masks[1];

                element.mask(mask, options_);
            }
        };

        if ($(document.body)?.mask) element.mask("000.000.000-00", options);
    }

    inputZipcodeMask() {
        let selector = '.frm_edit_kyc .inp_zipcode';
        $(selector).exists() && $(selector)?.mask('00000-000');
    }

    end() {
        let self = this;
        global.onloadRoutines.push({
            name: 'kycFields', callback: function() {
                self.inputPhoneMask();
                self.inputCpfCnpjMask($('.frm_edit_kyc .inp_doc'));
                self.inputCpfCnpjMask($('.frm_edit_kyc .inp_responsible_doc'));
                self.inputZipcodeMask();
                NioApp.Picker.dob('.date-picker-alt');
            }
        });
        
        /**
         * Zipcode input in keyup event
         */
        $('body').on('keyup', '.frm_edit_kyc .inp_zipcode', function(ev) {
            let form = document.querySelector('.frm_edit_kyc');
            let value = this.value;
            if (this.value.length == 9) {
                $.get(`https://viacep.com.br/ws/${value}/json`).done(response => {
                    if (!(response?.uf)) return;
                    $('[checkout-input="state"]').val(response.uf);
                    if (/[0-9]/.test(ev.key)) $('[checkout-input="number"]').focus();
                    
                    let street = form.querySelector('.inp_street');
                    let address_no = form.querySelector('.inp_address_no');
                    let city = form.querySelector('.inp_city');
                    let state = form.querySelector('.inp_state');
                    let neighborhood = form.querySelector('.inp_neighborhood');

                    street.value = response.logradouro;
                    city.value = response.localidade;
                    state.value = response.uf;
                    neighborhood.value = response.bairro;

                    if (/[0-9]/.test(ev.key)) $(address_no).focus();
                });
            }
        });
    }

    cpfCnpjOnKeyup(element, instance, methodName, ev) {
        if (element?.value?.length <= 14) {
            $('.div_first_name').show();
            $('.div_last_name').show();

            $('.div_responsible_name').hide();
            $('.div_responsible_doc').hide();
            $('.div_fantasy_name').hide();
        }

        else {
            $('.div_first_name').hide();
            $('.div_last_name').hide();

            $('.div_responsible_name').show();
            $('.div_responsible_doc').show();
            $('.div_fantasy_name').show();
        }
    }
};