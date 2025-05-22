App.Browser.Form.NotFound = class NotFound extends Page {
    context = 'form';
    title = 'Não encontrado';

    view(loaded) {
        return super.find(`browser/${this.context}/${this.constructor.name}`, loaded);
    }
};