App.Subdomains.Checkout.Checkout = class Checkout extends Page {
    context = 'public';
    title = 'Checkout';

    view(loaded) {
        return super.find(`subdomains/checkout/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    onSubmit() {
        alert('Controller Checkout');
    }
};