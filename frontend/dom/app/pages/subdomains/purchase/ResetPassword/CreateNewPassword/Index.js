App.Subdomains.Purchase.ResetPassword.CreateNewPassword.Index = class Index extends Page {
    context = 'public';
    title = 'Reset Password';
    className = 'App.Subdomains.Purchase.ResetPassword.CreateNewPassword.Index';

    savePasswordSubmit(element) {
        let url = "<?php echo get_subdomain_serialized('purchase'); ?>/ajax/actions/subdomains/purchase/reset-password/save";

        let form = document.querySelector('.frm_customer_create_new_password');
        let token = form.querySelector('[name=token]').value;
        let password = form.querySelector('[name=password]').value;
        let confirm_password = form.querySelector('[name=confirm_password]').value;
        
        let btnText = element.innerText;

        if (!cmp_both_valid(password, '==', confirm_password))
            return Swal.fire('Erro!', __('Passwords do not match.'), 'error');

            
        element.innerText = __('Wait') + '...';
        element.setAttribute('disabled', true);

        let data = { token, password, confirm_password };
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };
        
        fetch(url, options).then(response => response.json())
        .then(({ status, message }) => {
            Swal.fire(`${__(status)}!`, message, status).then(result => {
                if (result && status == 'success') {
                    // let link = new Link;
                    // link.to(`${getSubdomainSerialized('purchase')}/dashboard`);
                    document.location = `${getSubdomainSerialized('purchase')}/dashboard`;
                }
            })
        })
        .finally(onfinally => {
            element.innerText = btnText;
            element.removeAttribute('disabled');
        });
    }
};