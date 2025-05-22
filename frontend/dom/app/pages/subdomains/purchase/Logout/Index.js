App.Subdomains.Purchase.Logout.Index = class Index extends Page {
    context = 'public';
    title = 'Logout';
    className = 'App.Subdomains.Purchase.Logout.Index';

    view() {
        let link = new Link;
        link.to(`${getSubdomainSerialized('purchase')}/login`);
        fetch('/logout');
    }
};