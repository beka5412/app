App.User.Coupon.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo cupom';
    className = 'App.User.Coupon.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/coupon/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/coupon/${body.id}/edit`);
        });
    }
};