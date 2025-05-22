App.User.App.Memberkit.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo';
    className = 'App.User.App.Memberkit.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/app/memberkit/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            //let link = new Link;
            window.location.href = `${siteUrl()}/app/memberkit/${body.id}/edit`;
        });
    }
};