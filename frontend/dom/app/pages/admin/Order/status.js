App.Admin.Order.Status = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Order';
    className = 'App.Admin.Order.Status';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/order/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};