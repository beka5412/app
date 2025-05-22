/* <?php use \Backend\Enums\Withdrawal\EWithdrawalTransferType; ?> */
App.Admin.Withdrawal.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Withdrawal';
    className = 'App.Admin.Withdrawal.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/withdrawal/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    dashboardOnSubmit() {
        alert('TESTE 123');
    }

    elementStatus(status, base) {
        let element = null;
        if (status == 'approved') element = base.querySelector('.div_wd_status_approved');
        if (status == 'pending') element = base.querySelector('.div_wd_status_pending');
        if (status == 'canceled') element = base.querySelector('.div_wd_status_canceled');
        return element;
    }

    hideAllStatus(base) {
        let approved = this.elementStatus('approved', base);
        let pending = this.elementStatus('pending', base);
        let canceled = this.elementStatus('canceled', base);
        approved.style.display = 'none';
        pending.style.display = 'none';
        canceled.style.display = 'none';
    }

    seeWithdrawalRequest(element, instance, methodName, ev) {
        let id = element.getAttribute('data-id');
        let withdrawal = JSON.parse(element.getAttribute('data-withdrawal') || '{}');

        let modalPix = document.querySelector('#modalWithdrawalAccountPix');
        let modalBank = document.querySelector('#modalWithdrawalAccountBank');

        let btnRejectPix = modalPix.querySelector('.inp_wd_reject');
        let btnApprovePix = modalPix.querySelector('.inp_wd_approve');

        let btnRejectBank = modalBank.querySelector('.inp_wd_reject');
        let btnApproveBank = modalBank.querySelector('.inp_wd_approve');

        let pix_key = document.querySelector('.span_wd_pix_key');
        let pix_total = document.querySelector('.span_wd_pix_total');
        let bank_name = document.querySelector('.span_wd_bank_name');
        let bank_acc_type = document.querySelector('.span_wd_bank_acc_type');
        let bank_account = document.querySelector('.span_wd_bank_account');
        let bank_digit = document.querySelector('.span_wd_bank_digit');
        let bank_agency = document.querySelector('.span_wd_bank_agency');
        let bank_total = document.querySelector('.span_wd_bank_total');
        let pix_reason = document.querySelector('.inp_wd_pix_reason');
        let bank_reason = document.querySelector('.inp_wd_bank_reason');
        let pix_user_name = document.querySelector('.span_wd_pix_username');
        let bank_user_name = document.querySelector('.span_wd_bank_username');

        pix_user_name.innerHTML = withdrawal?.user?.name || '';
        bank_user_name.innerHTML = withdrawal?.user?.name || '';
        

        btnRejectPix.setAttribute('data-id', id);
        btnApprovePix.setAttribute('data-id', id);
        btnRejectBank.setAttribute('data-id', id);
        btnApproveBank.setAttribute('data-id', id);
        
        btnRejectPix.setAttribute('data-reason', pix_reason.value);
        btnApprovePix.setAttribute('data-reason', pix_reason.value);
        btnRejectBank.setAttribute('data-reason', bank_reason.value);
        btnApproveBank.setAttribute('data-reason', bank_reason.value);

        this.transferType = withdrawal?.transfer_type;

        pix_reason.value = withdrawal?.reason || '';
        bank_reason.value = withdrawal?.reason || '';

        if (withdrawal?.answered) {
            pix_reason.setAttribute('disabled', true);
            bank_reason.setAttribute('disabled', true);
        }

        else {
            pix_reason.removeAttribute('disabled');
            bank_reason.removeAttribute('disabled');
        }

        let w_bank_name = withdrawal?.user?.bank_account?.bank?.name || '00';
        let w_bank_code = withdrawal?.user?.bank_account?.bank?.code || 'Não encontrado';

        bank_name.innerHTML = `${w_bank_code} - ${w_bank_name}`;
        let acc_type = withdrawal?.user?.bank_account?.type || '';

        if (acc_type == 'current')
            bank_acc_type.innerHTML = 'Conta corrente';

        else if (acc_type == 'savings')
            bank_acc_type.innerHTML = 'Conta poupança';

        bank_account.innerHTML = withdrawal?.user?.bank_account?.account || '';
        bank_digit.innerHTML = withdrawal?.user?.bank_account?.digit || '';
        bank_agency.innerHTML = withdrawal?.user?.bank_account?.agency || '';
        let total = Number(withdrawal?.amount || 0);
        let w_fee = Number("<?php echo doubleval(get_setting('withdrawal_fee')); ?>");
        let final_total = total > w_fee ? total - w_fee : total;
        bank_total.innerHTML = currencySymbol(final_total);
        pix_key.innerHTML = withdrawal?.user?.bank_account?.pix || '';
        pix_total.innerHTML = currencySymbol(final_total);
    }

    getReason()
    {
        let pix_reason = document.querySelector('.inp_wd_pix_reason');
        let bank_reason = document.querySelector('.inp_wd_bank_reason');
        let reason = '';

        if (this.transferType == '<?php echo EWithdrawalTransferType::PIX->value; ?>')
            reason = pix_reason.value;

        else if (this.transferType == '<?php echo EWithdrawalTransferType::BANK->value; ?>')
            reason = bank_reason.value;

        return reason;
    }

    approveOnClick(element, instance, methodName, ev) {
        let self = this;
        let id = element.getAttribute('data-id');
        // let base = $(element).parents('.tr')[0];
        
        let data = {
            status: '<?php echo \Backend\Enums\Withdrawal\EWithdrawalStatus::APPROVED->value; ?>',
            reason: this.getReason()
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        // this.hideAllStatus(base);

        fetch(`/ajax/actions/admin/withdrawal/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            
            if (body?.status == "success") {
                // self.elementStatus('approved', base).style.display = 'block';
                let link = new Link;
                link.to(`${siteUrl()}/admin/withdrawals`);
            }
        });
    }

    rejectOnClick(element, instance, methodName, ev) {
        let self = this;
        let id = element.getAttribute('data-id');
        // let base = $(element).parents('.tr')[0];
        
        let data = {
            status: '<?php echo \Backend\Enums\Withdrawal\EWithdrawalStatus::CANCELED->value; ?>',
            reason: this.getReason()
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        // this.hideAllStatus(base);

        fetch(`/ajax/actions/admin/withdrawal/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            
            if (body?.status == "success") {
                // self.elementStatus('canceled', base).style.display = 'block';
                let link = new Link;
                link.to(`${siteUrl()}/admin/withdrawals`);
            }
        });
    }
};