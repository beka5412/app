App.User.App.Cademi.Edit = class Edit extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.Cademi.Edit';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/app/cademi/${this?.constructor?.name || ''}${this?.queryString || ''}`, (div) => {
            setTimeout(() => {
                this.pageLoaded();
            }, 0);

            return loaded(div);
        });
    }

    editCademiOnReady() {
        this.pageLoaded();
    }

    pageLoaded() {
        const form = document.querySelector('.frm_edit_app_cademi');
        const product_id_view = form.querySelector('.inp_cademi_product_id_view');
        const product_id = form.querySelector('.inp_cademi_product_id');
        product_id_view.value = product_id.value;
    }

    onSubmitCademi() {
        const form = document.querySelector('.frm_edit_app_cademi');
        const enabled = form.querySelector('.inp_cademi_enabled');
        const subdomain = form.querySelector('.inp_cademi_subdomain');
        const token = form.querySelector('.inp_cademi_token');
        const product_id = form.querySelector('.inp_cademi_product_id');

        const data = {
            enabled: enabled?.checked ? 1 : 0,
            subdomain: subdomain?.value || '',
            token: token?.value || '',
            product_id: product_id?.value || ''
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/app/cademi/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/app/cademi');
            }
        });
    }

    appCademiProductOnChange() {
        const form = document.querySelector('.frm_edit_app_cademi');
        const product_id = form.querySelector('.inp_cademi_product_id');
        const product_id_view = form.querySelector('.inp_cademi_product_id_view');

        product_id_view.value = product_id.value;
    }
};