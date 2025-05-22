App.Admin.Kyc.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar kyc';
    className = 'App.Admin.Kyc.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/kyc/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    adminKycOnSubmit() {
        let form = document.querySelector('.frm_edit_admin_kyc');
        let status = form.querySelector('.sel_status');
        
        let data = {
            status: status?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/admin/kyc/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/admin/kyc');          
            }
        });
    }

    inputPhoneMask() {
        let element = $('.frm_edit_admin_kyc .inp_phone');
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

    end() {
        let self = this;
        global.onloadRoutines.push({
            name: 'kycPhone', callback: function() {
                self.inputPhoneMask();
            }
        });
    }
};