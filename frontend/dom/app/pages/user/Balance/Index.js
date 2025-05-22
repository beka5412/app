App.User.Balance.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Balance.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/balance/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    bankAccountOnSubmit() {
        let form = document.querySelector('.frm_edit_bank_account');
        let name = form.querySelector('.inp_bankacc_name');
        let doc = form.querySelector('.inp_bankacc_doc');
        let bank = form.querySelector('.inp_bankacc_bank');
        let type = form.querySelector('.inp_bankacc_type');
        let agency = form.querySelector('.inp_bankacc_agency');
        let account = form.querySelector('.inp_bankacc_account');
        let digit = form.querySelector('.inp_bankacc_digit');
        let pix_type = form.querySelector('.inp_bankacc_pix_type');
        let pix = form.querySelector('.inp_bankacc_pix');
        
        let data = {
            name: name?.value || '',
            doc: doc?.value || '',
            bank: bank?.value || '',
            type: type?.value || '',
            agency: agency?.value || '',
            account: account?.value || '',
            digit: digit?.value || '',
            pix_type: pix_type?.value || '',
            pix: pix?.value || ''
        };

        try {
            if (!data.name) throw new EmptyNameException;
            if (!data.doc) throw new EmptyDocException;
            if (!data.bank) throw new EmptyBankException;
            if (!data.type) throw new EmptyTypeException;
            if (!data.agency) throw new EmptyAgencyException;
            if (!data.account) throw new EmptyAccountException;
            // if (!data.digit) throw new EmptyDigitException;

            let options = {
                headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
                method: 'PATCH',
                body: JSON.stringify(data)
            };

            let id = params().id;
            
            fetch(`/ajax/actions/user/bank-account/${id}/edit`, options).then(response => response.json()).then(body => {
                toast(body.message);  
                if (body?.status == 'success') {
                    // let link = new Link;
                    // link.to(siteUrl() + '/balance');  
                    // console.log('sucesso'); 
                    updateTagJSON('config', {...tagJSON('config'), bank_account: body.data });
                    $('[click=withdrawRequestOnClick]').click();
                }
            });
        }

        catch (ex) {
            let message = '';

            if (ex instanceof EmptyNameException) {
                message = 'O nome não pode estar em branco.';
            }
            
            if (ex instanceof EmptyDocException) {
                message = 'O documento não pode estar em branco.';
            }
            
            if (ex instanceof EmptyBankException) {
                message = 'O banco não pode estar em branco.';
            }
            
            if (ex instanceof EmptyTypeException) {
                message = 'O tipo de conta não pode estar em branco.';
            }
            
            if (ex instanceof EmptyAgencyException) {
                message = 'A agência não pode estar em branco.';
            }
            
            if (ex instanceof EmptyAccountException) {
                message = 'A conta não pode estar em branco.';
            }

            if (message) toastError(message);
        }
    }

    withdrawRequestOnClick(element) {
        const { minimum_withdrawal, bank_account, kyc_confirmed } = tagJSON('config');
        console.log(tagJSON('config'));

        let form = document.querySelector('.frm_request_withdrawal');
        let amount = form.querySelector('.inp_withdrawal_amount');
        let transfer_type = form.querySelector('.inp_w_transfer_type');

        let btnText = element.innerText;

        
        let withdrawalFee = Number(amount?.hasAttribute('data-withdrawal-fee') ? amount?.getAttribute('data-withdrawal-fee') : '0');

        let data = {
            amount: Number(amount?.hasAttribute('data-value') ? amount?.getAttribute('data-value') : '0'),
            transfer_type: transfer_type.value
        };

        let calc = data.amount - withdrawalFee;

        if (calc <= 0 || data.amount < minimum_withdrawal) return Swal.fire(`O valor mínimo para saque é ${minimum_withdrawal} reais`, '', 'error');

        if (!kyc_confirmed) {
            (new Link).to(siteUrl() + '/kyc');
            return;
        }

        if (!bank_account?.pix) {
            $('#modalWithdrawalRegisterPix').modal('show');
            return;
        }

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        Swal
            .fire({
                title: `Deseja realmente sacar ${currencySymbol(data.amount - withdrawalFee)}?`,
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            })
            .then(result => {
                if (result.isConfirmed) {
                    element.innerText = 'Aguarde...';              
                    // fetch(`/ajax/actions/user/withdrawal/${id}/store`, options).then(response => response.json()).then(body => {
                    fetch(`/ajax/actions/user/withdrawal/${id}/iugu`, options).then(response => response.json()).then(body => {
                        if (body?.status == 'success') {
                            Swal.fire('Saque solicitado com sucesso.', '', 'success');
                            document.location.reload();        
                        }
                        else toast(body.message);
                    })
                    .finally(err => {
                        element.innerText = btnText;
                    });
                }
            })
    }

    calcWithdrawal(element, instance, methodName, ev) {
        instance.$inputCurrencyAlways(...arguments);
        let form = document.querySelector('.frm_request_withdrawal');
        let result = form.querySelector('.span_result_withdrawal');
        let amount = form.querySelector('.inp_withdrawal_amount');

        let amount_v = Number(currencyToNumber(amount.value));
        
        result.innerHTML = amount_v > global.settings.withdrawalFee ? currency(amount_v - global.settings.withdrawalFee) : amount_v;
    }
};