App.User.Refund.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Refund.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/refund/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    confirmRefundCustomerOnClick(srcElement) {
        const options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH'
        };

        const id = srcElement.getAttribute('data-id');
        const row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/refund/${id}/confirm`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                let link = new Link;
                link.to(`${siteUrl()}/refunds`);
            }
            else
                toastError(__('Refund error.'));
        });
    }

    cancelRefundCustomerOnClick(srcElement) {
        const options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH'
        };

        const id = srcElement.getAttribute('data-id');
        const row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/refund/${id}/cancel`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                let link = new Link;
                link.to(`${siteUrl()}/refunds`);
            }
            else
                toastError(__('Refund error.'));
        });
    }
};