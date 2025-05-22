App.User.App.UTMify.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.UTMify.Index';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/app/utmify/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    onSubmitAppUTMify() {
        let form = document.querySelector('.frm_edit_app_utmify');
        let apikey = form.querySelector('.inp_utmify_apikey');

        let data = {
            apikey: apikey?.value || ''
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/user/app/utmify/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/apps');
            }
        });
    }
};