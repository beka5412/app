Events.Contexts.Subdomains.Checkout.Form.Click = class Click extends EventClick {
    context = 'form';
    domain = getSubdomainSerialized('checkout');

    onSubmit() {
        console.log('clickei checkout ctx form ' + global.version);
    }
};