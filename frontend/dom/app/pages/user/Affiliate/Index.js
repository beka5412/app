App.User.Affiliate.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Affiliate.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/affiliate/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};