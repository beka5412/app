App.User.Chat.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Chat.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/chat/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    chatDestroy(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let id = srcElement.getAttribute('data-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/chat/${id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError('Chat não encontrado.');
        });
    }
};