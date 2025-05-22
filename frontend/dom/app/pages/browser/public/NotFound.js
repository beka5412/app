App.Browser.Public.NotFound = class NotFound extends Page {
    context = 'public';
    title = 'Não encontrado';

    view(loaded) {
        return super.find(`browser/${this.context}/${this.constructor.name}`, loaded);
    }
};