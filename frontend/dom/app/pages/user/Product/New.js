App.User.Product.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo produto';
    className = 'App.User.Product.New';

    view(loaded, linkInstance) {
        // return super.find(`user/product/${this.constructor.name}`, loaded);

        // requisicao criar novo e redirect para editar produto
        console.log('novo');

        fetch(`${siteUrl()}/ajax/actions/user/product/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            //let link = new Link;
            //link.to(`${siteUrl()}/product/${body.id}/edit`);
            window.location.href = `${siteUrl()}/product/${body.id}/edit`
        });
    }
};