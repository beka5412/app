App.User.Report.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Report.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/report/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};