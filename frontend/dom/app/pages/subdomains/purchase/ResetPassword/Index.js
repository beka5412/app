App.Subdomains.Purchase.ResetPassword.Index = class Index extends Page {
    context = 'public';
    title = 'Reset Password';
    className = 'App.Subdomains.Purchase.ResetPassword.Index';

    view(loaded) {
        return super.find(`subdomains/purchase/reset-password/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    resetPasswordOnSubmit(element) {
        let url = "<?php echo get_subdomain_serialized('purchase'); ?>/ajax/actions/subdomains/purchase/reset-password";

        let form = document.querySelector('.frm_customer_reset_password');
        let email = form.querySelector('[name=email]').value;
        
        let btnText = element.innerText;
        element.innerText = __('Wait') + '...';
        element.setAttribute('disabled', true);

        let data = { email };
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };
        
        fetch(url, options).then(response => response.json())
            .then(({ status, message }) => Swal.fire(`${__(status)}!`, message, status))
            .finally(onfinally => {
                element.innerText = btnText;
                element.removeAttribute('disabled');
            });
    }
};