App.User.Subscription.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Subscription.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/subscription/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    cancelCustomerSubscriptionOnClick(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH'
        };

        let row = $(srcElement).parents('.tr')[0];
        let id = row.getAttribute('data-subscription-id');

        fetch(`/ajax/actions/user/subscription/${id}/cancel`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                let link = new Link;
                link.to(siteUrl() + '/subscriptions'); 
            }
            else
                toastError('Assinatura não encontrado.');
        });
    }
};