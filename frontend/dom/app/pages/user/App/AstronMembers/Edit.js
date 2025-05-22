App.User.App.AstronMembers.Edit = class Edit extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.AstronMembers.Edit';
    uri = 'app/astronmembers';
    
    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/${this?.uri || ''}/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    onSubmitAstronMembers() {
        const form = document.querySelector('.frm_edit_app_astronmembers');
        const enabled = form.querySelector('.inp_astronmembers_enabled');
        const username = form.querySelector('.inp_astronmembers_username');
        const password = form.querySelector('.inp_astronmembers_password');
        const clubid = form.querySelector('.inp_astronmembers_clubid');
        const product_id = form.querySelector('.inp_astronmembers_product_id');

        let data = {
            enabled: enabled?.checked ? 1 : 0,
            username: username?.value || '',
            password: password?.value || '',
            clubid: clubid?.value || '',
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