App.Public.AdminLogin = class AdminLogin extends Page {
    context = 'form';
    title = 'Entrar no admin';

    view(loaded) {
        return super.find(`public/${this.constructor.name}`, loaded);
    }

    clearError() {
        let errorElement = document.querySelector('.login-error');
        errorElement.style.display = 'none';
        return;
    }

    error(message) {
        let errorElement = document.querySelector('.login-error');

        errorElement.style.display = 'block';
        errorElement.innerHTML = message;
    }

    onSubmit() {
        this.clearError();

        let [url, login, password] = [
            '/ajax/auth-admin', 
            document.getElementById('inpLogin')?.value,
            document.getElementById('inpPassword')?.value
        ];

        try {
            if (!login || !password) {
                throw new PasswordWrongException('Login ou senha em branco.');
            }
        }

        catch (exception)
        {
            return this.error(exception.message);
        }
        
        let options = {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ login, password })
        };

        fetch(url, options)
            .then(response => response.json())
            .then(body => {
                try {
                    if (body?.status == 'success') {
                        // let link = new Link;
                        // link.to(`${siteUrl()}/admin/dashboard`);
                        document.location = `${siteUrl()}/admin/dashboard`;
                    }

                    else throw new PasswordWrongException(body?.message || 'Erro ao fazer login.');
                }

                catch (exception)
                {
                    this.error(exception.message);
                }
            })
        ;
    }
};