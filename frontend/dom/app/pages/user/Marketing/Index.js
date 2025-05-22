App.User.Marketing.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Marketing.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/marketing/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};