App.Admin.Balance.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Balance';
    className = 'App.Admin.Balance.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/balance/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};