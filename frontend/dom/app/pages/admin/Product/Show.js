App.Admin.Product.Show = class Show extends Page {
    context = 'dashboard';
    title = 'Admin Product';
    className = 'App.Admin.Product.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/product/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};