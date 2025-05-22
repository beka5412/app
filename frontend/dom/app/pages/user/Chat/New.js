App.User.Chat.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo chat';
    className = 'App.User.Chat.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/chat/new`, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'}
        }).then(response => response.json()).then(body => {
            let link = new Link;
            link.to(`${siteUrl()}/chat/${body.id}/edit`);
        });
    }
};