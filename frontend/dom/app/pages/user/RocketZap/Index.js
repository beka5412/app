App.User.RocketZap.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.RocketZap.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/rocketzap/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};