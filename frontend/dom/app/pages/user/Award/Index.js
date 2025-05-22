App.User.Award.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Award.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/award/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};