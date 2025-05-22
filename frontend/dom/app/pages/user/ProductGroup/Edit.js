App.User.ProductGroup.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Grupo de produtos';
    className = 'App.User.ProductGroup.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product-group/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    customerOnSubmit() {
        let form = document.querySelector('.frm_edit_customer');
        let name = form.querySelector('.inp_name');
        let email = form.querySelector('.inp_email');
        
        let data = {
            name: name?.value || '',
            email: email?.value || '',
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/customer/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/customers');          
            }
        });
    }
};