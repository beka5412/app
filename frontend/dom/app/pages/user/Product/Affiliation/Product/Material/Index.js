App.User.Product.Affiliation.Product.Material.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Affiliation.Product.Material.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/affiliation/product/{id}/materials/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_aff_product_show', 'materials'));
    }
};