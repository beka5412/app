App.Browser.Subdomains.Purchase.Public.Unauthorized = class Unauthorized extends Page {
    subdomain = 'purchase';
    context = 'public';
    title = 'Não autorizado';

    view() {
        let link = new Link;
        link.to(`${getSubdomainSerialized(this.subdomain)}/login`);
    }
};