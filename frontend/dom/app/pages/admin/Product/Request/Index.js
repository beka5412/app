App.Admin.Product.Request.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin - Product Request';
    className = 'App.Admin.Product.Request.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/product/requests/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    adminProductRequestApprove(srcElement) {
        const id = srcElement.getAttribute('data-id');

        let data = { id };

        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/admin/product/request/${id}/approve`, options).then(response => response.json()).then(body => {
            toast(body.message);
            row.remove();
        });
    }

    adminProductRequestReject(srcElement) {
        const id = srcElement.getAttribute('data-id');

        let data = { id };
        
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/admin/product/request/${id}/reject`, options).then(response => response.json()).then(body => {
            toast(body.message);
            row.remove();
        });
    }
};
