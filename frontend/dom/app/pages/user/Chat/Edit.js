App.User.Chat.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar chat';
    className = 'App.User.Chat.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/chat/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    chatOnSubmit() {
        let form = document.querySelector('.frm_edit_chat');
        let name = form.querySelector('.inp_name');
        
        let data = {
            name: name?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/chat/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/chats');          
            }
        });
    }
};