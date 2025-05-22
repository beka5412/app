App.User.Kyc.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar kyc';
    className = 'App.User.Kyc.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/kyc/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};