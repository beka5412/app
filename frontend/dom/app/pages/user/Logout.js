App.User.Logout = class Logout {
    context = 'dashboard';
    title = 'Logout';
    className = 'App.User.Logout';
    
    view() {
        let link = new Link;
        link.to(`${siteUrl()}/login`);
        fetch('/logout');
    }
};