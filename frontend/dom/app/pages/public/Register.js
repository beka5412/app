App.Public.Register = class Register extends Page {
    context = 'form';
    title = 'Cadastrar';

    view(loaded) {
        return super.find(`public/${this.constructor.name}`, loaded);
    }

    clearError() {
        let errorElement = document.querySelector('.login-error');
        errorElement.style.display = 'none';
    }

    error(message) {
        let errorElement = document.querySelector('.login-error');

        errorElement.style.display = 'block';
        errorElement.innerHTML = message;
    }

    onSubmit() {
        this.clearError();

        let inpName = document.getElementById('inpName');
        let inpEmail = document.getElementById('inpEmail');
        let inpPassword = document.getElementById('inpPassword');
        let inpPasswordConfirm = document.getElementById('inpPasswordConfirm');
        let inpDoc = document.getElementById('inpDoc');
        let inpBirthdate = document.getElementById('inpBirthdate');
        let inpPhone = document.getElementById('inpPhone');

        let url = '/ajax/register';
        let name = inpName?.value || '';
        let email = inpEmail?.value || '';
        let password = inpPassword?.value || '';
        let passwordConfirm = inpPasswordConfirm?.value || '';
        let doc = inpDoc?.value || '';
        let birthdate = inpBirthdate?.value || '';
        let phone = inpPhone?.value || '';

        // sanitize
        phone = phone.replace(/\D/g, '');
        birthdate = birthdate.split('/').reverse().join('-');
        doc = doc.replace(/\D/g, '');
             
        try {
            if (!name) {
                inpName.focus();
                throw new EmptyNameException('Informe seu nome.');
            }

            if (!email) {
                inpEmail.focus();
                throw new EmptyEmailException('Informe seu e-mail.');                
            }

            if (!password) {
                inpPassword.focus();
                throw new EmpyPasswordException('Crie uma senha.');                
            }

            if (!passwordConfirm) {
                inpPasswordConfirm.focus();
                throw new EmpyPasswordConfirmException('Confirme sua senha.');                
            }

            if (password != passwordConfirm) {
                throw new PasswordNotMatchException('As senhas não coincidem.');                
            }

            if (!doc) {
                inpDoc.focus();
                throw new EmptyDocException('Informe o número do seu documento de identificação.');                
            }

            if (!birthdate) {
                inpBirthdate.focus();
                throw new EmptyBirthdateException('Informe sua data de nascimento.');                
            }

            if (!phone) {
                inpPhone.focus();
                throw new EmptyPhoneException('Informe seu número de telefone.');                
            }
        }

        catch (exception) {
            return this.error(exception.message);
        }
        
        let options = {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                name,
                email,
                password,
                passwordConfirm,
                doc,
                birthdate,
                phone
            })
        };

        fetch(url, options)
            .then(response => response.json())
            .then(body => {
                console.log(body)
                if (body.status === 'success') {
                    document.location = body.redirect;
                }
                else this.error(body.message || "Erro ao criar conta.");
            })
        ;
    }

    end() {
        let self = this;
        
        global.onloadRoutines.push({
            name: "registerMasks", callback: function() {
                App.mask.doc('#inpDoc');
                App.mask.phone('#inpPhone');
                App.mask.birthdate('#inpBirthdate');
            }
        });
    }
};