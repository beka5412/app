App.User.Product.Plan.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo Plan';
    className = 'App.User.Product.Plan.New';

    view() {
        let product_id = params().id;
        fetch(`${siteUrl()}/ajax/actions/user/product/${product_id}/plan/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/product/${product_id}/plan/${body.id}/edit`);
        });
    }
};