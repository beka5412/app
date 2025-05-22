App.User.App.Memberkit.Edit = class Edit extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.Memberkit.Edit';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/app/memberkit/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    onSubmitMemberkit() {
        let form = document.querySelector('.frm_edit_app_memberkit');
        let enabled = form.querySelector('.inp_memberkit_enabled');
        let apikey = form.querySelector('.inp_memberkit_apikey');
        let classroom = form.querySelector('.inp_memberkit_classroom');
        let product_id = form.querySelector('.inp_memberkit_product');

        let data = {
            enabled: enabled?.checked ? 1 : 0,
            apikey: apikey?.value || '',
            classroom: classroom?.value || '',
            product_id: product_id?.value || ''
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/app/memberkit/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/app/memberkit');
            }
        });
    }
};