App.User.ProductGroup.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.ProductGroup.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product-group/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};