App.Admin.Order.Show = class Show extends Page {
    context = 'dashboard';
    title = 'Admin Order';
    className = 'App.Admin.Order.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/order/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};