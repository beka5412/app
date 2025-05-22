App.User.Upsell.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo upsell';
    className = 'App.User.Upsell.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/upsell/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/upsell/${body.id}/edit`);
        });
    }
};