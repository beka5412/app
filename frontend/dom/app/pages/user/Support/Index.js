App.User.Support.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Support.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/support/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};