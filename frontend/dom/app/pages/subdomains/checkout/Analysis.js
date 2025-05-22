App.Subdomains.Checkout.Analysis = class Analysis extends Page {
    context = 'public';
    title = 'Analysis';

    view(loaded) {
        return super.find(`subdomains/checkout/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    onSubmit() {
        alert('enviar2');
    }
};