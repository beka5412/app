App.Browser.Public.Unauthorized = class Unauthorized extends Page {
    context = 'public';
    title = 'Não autorizado';

    view() {
        let link = new Link;
        link.to(`${siteUrl()}/login`);
    }
};