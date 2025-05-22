App.Admin.Marketing.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Marketing';
    className = 'App.Admin.Marketing.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/marketing/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};