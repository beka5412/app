App.User.Customer.Show = class Show extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Customer.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/customer/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};