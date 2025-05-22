App.User.AbandonedCart.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.AbandonedCart.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/abandoned-cart/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};