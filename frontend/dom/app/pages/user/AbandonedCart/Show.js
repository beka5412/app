App.User.AbandonedCart.Show = class Show extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.AbandonedCart.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/abandoned-cart/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};