App.User.Sale.Show = class Show extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Sale.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/sale/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};