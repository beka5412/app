App.User.Product.Plan.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar Plan';
    className = 'App.User.Product.Plan.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/plan/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    planOnSubmit() {
        let form = document.querySelector('.frm_edit_plan');
        let name = form.querySelector('.inp_plan_name');
        let price = form.querySelector('.inp_plan_price');
        let slug = form.querySelector('.inp_plan_slug');
        let recurrence_period = form.querySelector('.inp_recurrence_period');
        
        let data = {
            name: name?.value || '',
            price: price?.value || '',
            slug: slug?.value || '',
            recurrence_period: recurrence_period?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;
        let product_id = params().product_id;
        fetch(`/ajax/actions/user/product/${product_id}/plan/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + `/product/${product_id}/plans`);          
            }
        });
    }
};