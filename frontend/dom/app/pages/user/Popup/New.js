App.User.Popup.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo popup';
    className = 'App.User.Popup.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/popup/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/popup/${body.id}/edit`);
        });
    }
};