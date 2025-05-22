App.User.Product.Link.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Link.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/links/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_product_edit', 'links'));
    }

    copyLink(element, instance, methodName, ev) {
        copy(element.getAttribute('data-link'));
        toast('Copiado!');
    }
};