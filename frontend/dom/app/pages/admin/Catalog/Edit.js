App.Admin.Catalog.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Admin Catalog';
    className = 'App.Admin.Catalog.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/catalog/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};