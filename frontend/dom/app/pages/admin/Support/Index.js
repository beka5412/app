App.Admin.Support.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Support';
    className = 'App.Admin.Support.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/support/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};