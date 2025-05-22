App.Subdomains.Checkout.Billet = class Billet extends Page {
    context = 'public';
    title = 'Boleto';

    view(loaded) {
        return super.find(`subdomains/checkout/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    billetCopyCode(element, instance, methodName, ev) {
        copy(element.getAttribute('data-code'));
        toast('Código copiado!');
    }

    onSubmit() {
        alert('Controller Billet');
    }
};