App.Subdomains.Checkout.Test2 = class Index extends Page {
    context = 'form';
    title = 'Test2';

    view(loaded) {
        console.log('CHECKOUT');
        // return super.find(`user/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    onSubmit() {
        alert('enviar2');
    }
};