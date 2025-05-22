Events.Contexts.Subdomains.Checkout.Dashboard.Click = class Click extends EventClick {
    domain = getSubdomainSerialized('checkout');
    context = 'dashboard';

    onSubmit() {
        console.log('clickei checkout ctx dashboard ' + global.version);
    }
};