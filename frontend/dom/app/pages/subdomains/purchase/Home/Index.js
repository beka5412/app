App.Subdomains.Purchase.Home.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Home';
    className = 'App.Subdomains.Purchase.Home.Index';

    view(loaded) {
        return super.find(`subdomains/purchase/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};