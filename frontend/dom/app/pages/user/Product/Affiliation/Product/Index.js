App.User.Product.Affiliation.Product.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Affiliation.Product.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/affiliation/products/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_product_edit', 'affiliation'));
    }
};