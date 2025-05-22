App.User.Coupon.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar cliente';
    className = 'App.User.Coupon.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/coupon/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    couponOnSubmit() {
        let form = document.querySelector('.frm_edit_coupon');
        let code = form.querySelector('.inp_code');
        let discount = form.querySelector('.inp_discount');
        let description = form.querySelector('.inp_description');
        let status = form.querySelector('.inp_status');
        let type = form.querySelector('.inp_type');
        
        let data = {
            code: code?.value || '',
            discount: discount?.value || '',
            description: description?.value || '',
            status: status?.value || '',
            type: type?.value || '',
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/coupon/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/coupons');          
            }
        });
    }
};