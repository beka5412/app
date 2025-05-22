App.User.Product.Checkout.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo Checkout';
    className = 'App.User.Product.Checkout.New';

    view(loaded, linkInstance) {
        let product_id = params().id;
        fetch(`${siteUrl()}/ajax/actions/user/product/${product_id}/checkout/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            window.location.href = `${siteUrl()}/product/${product_id}/checkout/${body.id}/edit`
        });
    }
};