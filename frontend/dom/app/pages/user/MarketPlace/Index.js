App.User.MarketPlace.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.MarketPlace.Index';

    view(loaded, link) {
        $('.slider-init').slick('destroy');
        return super.find(`${link?.full?'full/':''}user/marketplace/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    // end() {
    //     global.onloadRoutines.push({
    //         name: "marketplaceSlider", callback: function() {
    //             // NioApp.Slick('.slider-init');
    //             console.log('LOADED');
    //         }
    //     });
    // }
};