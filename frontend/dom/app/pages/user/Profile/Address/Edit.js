App.User.Address.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Edit address';
    className = 'App.User.Address.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/address/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};
