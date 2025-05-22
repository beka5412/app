App.Admin.Customer.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.Admin.Customer.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/customer/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};
