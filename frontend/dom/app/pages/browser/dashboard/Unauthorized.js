App.Browser.Dashboard.Unauthorized = class Unauthorized extends Page {
    context = 'dashboard';
    title = 'Não autorizado';

    view() {
        let link = new Link;
        link.to(`${siteUrl()}/login`);
    }
};