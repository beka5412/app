App.User.Kyc.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo kyc';
    className = 'App.User.Kyc.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/kyc/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/kyc/${body.id}/edit`);
        });
    }
};