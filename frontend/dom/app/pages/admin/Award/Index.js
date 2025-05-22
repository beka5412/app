App.Admin.Award.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.Admin.Award.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/award/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    adminAwardSentOnClick(srcElement) {
        const id = srcElement.getAttribute('data-id');

        let data = { id };

        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/admin/award/request/${id}/sent`, options).then(response => response.json()).then(body => {
            toast(body.message);
            row.remove();
        });
    }

    adminAwardCanceledOnClick(srcElement) {
        const id = srcElement.getAttribute('data-id');

        let data = { id };

        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/admin/award/request/${id}/canceled`, options).then(response => response.json()).then(body => {
            toast(body.message);
            row.remove();
        });
    }
};
