App.Admin.Support.Show = class Show extends Page {
    context = 'dashboard';
    title = 'Admin Support';
    className = 'App.Admin.Support.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/support/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};