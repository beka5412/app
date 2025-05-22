App.Admin.Customer.Show = class Show extends Page {
    context = 'dashboard';
    title = 'Admin Customer';
    className = 'App.Admin.Customer.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/customer/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};