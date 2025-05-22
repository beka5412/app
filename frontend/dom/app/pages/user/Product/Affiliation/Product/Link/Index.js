App.User.Product.Affiliation.Product.Link.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Affiliation.Product.Link.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/affiliation/product/{id}/links/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_aff_product_show', 'links'));
    }
};