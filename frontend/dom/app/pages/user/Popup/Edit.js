App.User.Popup.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar popup';
    className = 'App.User.Popup.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/popup/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    popupOnSubmit() {
        let form = document.querySelector('.frm_edit_popup');
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

        fetch(`/ajax/actions/user/popup/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/popups');          
            }
        });
    }
};