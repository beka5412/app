App.Browser.Form.Unauthorized = class NotFound extends Page {
    context = 'form';
    title = 'Não autorizado';

    view() {
        console.log('NAO AUTORIZADO FORM');
        let link = new Link;
        link.to(`${siteUrl()}/login`);
    }
};