App.User.App.Sellflux.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo';
    className = 'App.User.App.Sellflux.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/app/sellflux/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/app/sellflux/${body.id}/edit`);
        });
    }
};
