App.Browser.Subdomains.Purchase.Dashboard.Unauthorized = class Unauthorized extends Page {
    subdomain = 'purchase';
    context = 'dashboard';
    title = 'Não autorizado';

    view() {
        let link = new Link;
        link.to(`${getSubdomainSerialized(this.subdomain)}/login`);
    }
};