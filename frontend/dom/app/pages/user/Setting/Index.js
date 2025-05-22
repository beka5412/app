App.User.Setting.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Setting.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/setting/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
    
    addDomain() {
        let form = document.querySelector('.frm_add_domain');
        let input_domain, domain = (input_domain = form.querySelector('.inp_domain')).value || '';
        let data = { domain };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };

        fetch(`/ajax/actions/user/domain/add`, options).then(response => response.json()).then(body => {
            toast(body.message);
            if (body?.status == 'success') {
                $('.modal_add_domain').modal('hide');
                let link = new Link;
                link.to(siteUrl() + '/settings');          
            }
        });
    }

    domainKeyUp(element, instance, methodName, ev) {
        console.log(element.value);
        let domainElement = document.querySelector('.span_domain_output');
        domainElement.innerHTML = "pay." + element.value;
    }
};