App.Admin.Kyc.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Kyc';
    className = 'App.Admin.Kyc.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/kyc/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};