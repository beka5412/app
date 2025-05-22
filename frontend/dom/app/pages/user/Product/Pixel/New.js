App.User.Product.Pixel.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo Pixel';
    className = 'App.User.Product.Pixel.New';

    view(loaded, linkInstance) {
        let product_id = params().id;
        fetch(`${siteUrl()}/ajax/actions/user/product/${product_id}/pixel/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            window.location.href = `${siteUrl()}/product/${product_id}/pixel/${body.id}/edit`;
        });
    }
};