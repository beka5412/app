App.User.App.Sellflux.Edit = class Edit extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.Sellflux.Edit';
    uri = 'app/sellflux';
    
    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/${this?.uri || ''}/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    onSubmitSellflux() {
        const form = document.querySelector('.frm_edit_app_sellflux');
        const enabled = form.querySelector('.inp_sellflux_enabled');
        const link = form.querySelector('.inp_sellflux_link');
        const product_id = form.querySelector('.inp_sellflux_product_id');

        let data = {
            enabled: enabled?.checked ? 1 : 0,
            link: link?.value || '',
            product_id: product_id?.value || ''
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/${this?.uri || ''}/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + `/${this?.uri || ''}`);
            }
        });
    }
};