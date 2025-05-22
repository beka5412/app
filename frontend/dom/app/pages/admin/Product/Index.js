App.Admin.Product.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Product';
    className = 'App.Admin.Product.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/product/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};