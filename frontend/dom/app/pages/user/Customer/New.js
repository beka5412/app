App.User.Customer.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo cliente';
    className = 'App.User.Customer.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/customer/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/customer/${body.id}/edit`);
        });
    }
};