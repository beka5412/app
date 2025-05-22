App.User.OrderBump.New = class New extends Page {
    context = 'dashboard';
    title = 'Novo Orderbump';
    className = 'App.User.OrderBump.New';

    view(loaded, linkInstance) {
        fetch(`${siteUrl()}/ajax/actions/user/orderbump/new`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Client-Name': 'Action'
            }
        }).then(response => response.json()).then(body => {
            window.location.href = `${siteUrl()}/orderbump/${body.id}/edit`;
        }).catch(error => {
            console.error('Fetch error:', error);
        });
    }
};