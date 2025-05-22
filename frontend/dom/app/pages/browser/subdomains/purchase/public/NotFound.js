App.Browser.Subdomains.Purchase.Public.NotFound = class NotFound extends Page {
    subdomain = 'purchase';
    context = 'public';
    title = 'Não encontrado';

    view(loaded) {
        return super.find(`browser/subdomains/${this.subdomain}/${this.context}/${this.constructor.name}`, loaded);
    }
};