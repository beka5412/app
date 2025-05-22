App.Subdomains.Purchase.Login.Index = class Index extends Page {
    context = 'public';
    title = 'Login';
    className = 'App.Subdomains.Purchase.Login.Index';

    view(loaded) {
        return super.find(`subdomains/purchase/login/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    async loginOnSubmit() {
        let url = "<?php echo get_subdomain_serialized('purchase'); ?>/ajax/actions/subdomains/purchase/auth";
        let form = document.querySelector('.frm_customer_login');
        let login = form.querySelector('[name=login]').value;
        let password = form.querySelector('[name=password]').value;
        let error = form.querySelector('.div_purcharse_login_error');
        error.style.display = 'none';

        let data = { login, password };
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };
        
        let response = await fetch(url, options);
        let body = await response.json();

        if (body.status == "success") {
            let link = new Link;
            document.location = `${getSubdomainTranslated('purchase')}/dashboard`;
        }

        else {
            error.style.display = 'block';
        }
    }

    eyeOnClick(element) {
        iconEyeOn.classList.add('d-none');
        iconEyeOn.classList.remove('d-block');
        
        iconEyeOff.classList.add('d-block');
        iconEyeOff.classList.remove('d-none');

        inputPassword.type = 'text';
    }

    eyeOffOnClick(element) {
        iconEyeOn.classList.remove('d-none');
        iconEyeOn.classList.add('d-block');
        
        iconEyeOff.classList.remove('d-block');
        iconEyeOff.classList.add('d-none');
        
        inputPassword.type = 'password';
    }
};