App.User.Domain.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Domain.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/domain/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    test() {
        let form = document.querySelector('.frm_add_domain');
        let input_domain, domain = (input_domain = form.querySelector('.inp_domain')).value || '';
        
        let data = {
            domain
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/user/domain/add`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                window.location.href = `${siteUrl() + '/domains'}`;
            }
            else
                toastError('Erro ao tentar adicionar o domínio');
        });
    }
};