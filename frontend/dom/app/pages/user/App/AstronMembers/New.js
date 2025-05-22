App.User.App.AstronMembers.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo';
    className = 'App.User.App.AstronMembers.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/app/astronmembers/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/app/astronmembers/${body.id}/edit`);
        });
    }
};
