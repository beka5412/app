App.User.App.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/app/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    onChangeAppUTMify(element, instance, methodName, ev) {
        let data = {
            status: element.checked ? 1 : 0
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/user/app/utmify/change`, options).then(response => response.json()).then(body => {
            toast(body.message);
        });
    }
};