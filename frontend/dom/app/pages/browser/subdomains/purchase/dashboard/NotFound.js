App.Browser.Subdomains.Purchase.Dashboard.NotFound = class NotFound extends Page {
    subdomain = 'purchase';
    context = 'dashboard';
    title = 'Não encontrado';

    view(loaded) {
        return super.find(`browser/subdomains/${this.subdomain}/${this.context}/${this.constructor.name}`, loaded);
    }
};