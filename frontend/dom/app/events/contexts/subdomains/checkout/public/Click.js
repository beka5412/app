Events.Contexts.Subdomains.Checkout.Public.Click = class Click extends EventClick {
    context = 'public';
    domain = getSubdomainSerialized('checkout');

    onSubmit() {
        console.log('clickei checkout ctx public ' + global.version);
    }
};