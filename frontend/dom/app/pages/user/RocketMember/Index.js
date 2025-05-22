App.User.RocketMember.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.RocketMember.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/rocketmember/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};