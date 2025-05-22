App.User.DiscountCoupon.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.DiscountCoupon.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/discount-coupon/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
};