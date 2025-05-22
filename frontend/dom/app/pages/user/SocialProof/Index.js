App.User.SocialProof.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.SocialProof.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/social-proof/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};