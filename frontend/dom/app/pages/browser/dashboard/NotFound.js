App.Browser.Dashboard.NotFound = class NotFound extends Page {
    context = 'dashboard';
    title = 'Não encontrado';

    view(loaded) {
        return super.find(`browser/${this.context}/${this.constructor.name}`, loaded);
    }
};