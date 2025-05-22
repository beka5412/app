const router = new Routes();

/*
|------------------------------------------------------------------
| Rotas para alteracao de conteudo da pagina dinamicamente
| isso sera renderizado apos o carregamento da pagina
| 
| Toda rota que exibe uma pagina, mesmo que nao seja single page
| deve ser criada tanto no web.php quanto neste arquivo.
|------------------------------------------------------------------
*/


/**
 * Main site
 */
(url => {
    router.use(url, '/', function() {
        alert('welcome');
    });

    /**
     * Public
     */
    router.use(url, '/login', new App.Public.Login);
    router.use(url, '/register', new App.Public.Register);
    router.use(url, '/admin/login', new App.Public.AdminLogin);

    /**
     * User
     */
    router.use(url, '/logout', new App.User.Logout);
    router.use(url, '/dashboard', new App.User.Dashboard);
    router.use(url, '/profile/address', new App.User.Address.Edit);
    router.use(url, '/profile', new App.User.Profile.Edit);
    router.use(url, '/products', new App.User.Product.Index);
    router.use(url, '/product/{id}/edit', new App.User.Product.Edit);
    router.use(url, '/product/new', new App.User.Product.New);
    router.use(url, '/marketplace', new App.User.MarketPlace.Index);
    router.use(url, '/marketplace/{id}/view', new App.User.MarketPlace.Show);
    router.use(url, '/sales', new App.User.Sale.Index);
    router.use(url, '/subscriptions', new App.User.Subscription.Index);
    router.use(url, '/abandoned-carts', new App.User.AbandonedCart.Index);
    router.use(url, '/abandoned-cart/{id}/show', new App.User.AbandonedCart.Show);
    router.use(url, '/affiliates', new App.User.Affiliate.Index);
    router.use(url, '/balance', new App.User.Balance.Index);
    router.use(url, '/discount-coupon', new App.User.DiscountCoupon.Index);
    router.use(url, '/orderbumps', new App.User.OrderBump.Index);
    router.use(url, '/orderbump/new', new App.User.OrderBump.New);
    router.use(url, '/orderbump/{id}/edit', new App.User.OrderBump.Edit);
    router.use(url, '/upsells', new App.User.Upsell.Index);
    router.use(url, '/upsell/new', new App.User.Upsell.New);
    router.use(url, '/upsell/{id}/edit', new App.User.Upsell.Edit);
    router.use(url, '/social-proof', new App.User.SocialProof.Index);
    router.use(url, '/product-groups', new App.User.ProductGroup.Index);
    router.use(url, '/product-group/{id}/edit', new App.User.ProductGroup.Edit);
    router.use(url, '/support', new App.User.Support.Index);
    router.use(url, '/reports', new App.User.Report.Index);
    router.use(url, '/settings', new App.User.Setting.Index);
    router.use(url, '/apps', new App.User.App.Index);
    router.use(url, '/app/utmify', new App.User.App.UTMify.Index);
    router.use(url, '/app/memberkit', new App.User.App.Memberkit.Index);
    router.use(url, '/app/memberkit/{id}/edit', new App.User.App.Memberkit.Edit);
    router.use(url, '/app/memberkit/new', new App.User.App.Memberkit.New);
    router.use(url, '/app/astronmembers', new App.User.App.AstronMembers.Index);
    router.use(url, '/app/astronmembers/{id}/edit', new App.User.App.AstronMembers.Edit);
    router.use(url, '/app/astronmembers/new', new App.User.App.AstronMembers.New);
    router.use(url, '/app/sellflux', new App.User.App.Sellflux.Index);
    router.use(url, '/app/sellflux/{id}/edit', new App.User.App.Sellflux.Edit);
    router.use(url, '/app/sellflux/new', new App.User.App.Sellflux.New);
    router.use(url, '/app/cademi', new App.User.App.Cademi.Index);
    router.use(url, '/app/cademi/{id}/edit', new App.User.App.Cademi.Edit);
    router.use(url, '/app/cademi/new', new App.User.App.Cademi.New);
    router.use(url, '/rocketzap', new App.User.RocketZap.Index);
    router.use(url, '/rocketmember', new App.User.RocketMember.Index);
    router.use(url, '/customers', new App.User.Customer.Index);
    router.use(url, '/customer/{id}/edit', new App.User.Customer.Edit);
    router.use(url, '/customer/new', new App.User.Customer.New);
    router.use(url, '/customer/{id}/show', new App.User.Customer.Show);
    router.use(url, '/coupons', new App.User.Coupon.Index);
    router.use(url, '/coupon/{id}/edit', new App.User.Coupon.Edit);
    router.use(url, '/coupon/new', new App.User.Coupon.New);
    router.use(url, '/sale/{id}/show', new App.User.Sale.Show);
    router.use(url, '/product/{id}/checkouts', new App.User.Product.Checkout.Index);
    router.use(url, '/product/{id}/checkout/new', new App.User.Product.Checkout.New);
    router.use(url, '/product/{product_id}/checkout/{checkout_id}/edit', new App.User.Product.Checkout.Edit);
    router.use(url, '/marketing', new App.User.Marketing.Index);
    router.use(url, '/product/{id}/pixels', new App.User.Product.Pixel.Index);
    router.use(url, '/product/{id}/pixel/new', new App.User.Product.Pixel.New);
    router.use(url, '/product/{product_id}/pixel/{id}/edit', new App.User.Product.Pixel.Edit);
    router.use(url, '/product/{id}/settings', new App.User.Product.Setting.Index);
    router.use(url, '/product/{id}/links', new App.User.Product.Link.Index);
    router.use(url, '/product/{id}/upsell', new App.User.Product.Upsell.Index);
    // router.use(url, '/product/{id}/tools', new App.User.Product.Tool.Index);
    router.use(url, '/product/{id}/plans', new App.User.Product.Plan.Index);
    router.use(url, '/product/{id}/plan/new', new App.User.Product.Plan.New);
    router.use(url, '/product/{product_id}/plan/{id}/edit', new App.User.Product.Plan.Edit);
    router.use(url, '/product/{id}/affiliation', new App.User.Product.Affiliation.Index);
    router.use(url, '/aff/products', new App.User.Product.Affiliation.Product.Index);
    router.use(url, '/aff/product/{id}', new App.User.Product.Affiliation.Product.Show);
    router.use(url, '/aff/product/{id}/links', new App.User.Product.Affiliation.Product.Link.Index);
    router.use(url, '/aff/product/{id}/materials', new App.User.Product.Affiliation.Product.Material.Index);
    router.use(url, '/aff/product/{id}/support', new App.User.Product.Affiliation.Product.Support.Index);
    router.use(url, '/awards', new App.User.Award.Index);
    router.use(url, '/kyc', new App.User.Kyc.Index);
    router.use(url, '/kyc/new', new App.User.Kyc.New);
    router.use(url, '/chats', new App.User.Chat.Index);
    router.use(url, '/chat/{id}/edit', new App.User.Chat.Edit);
    router.use(url, '/chat/new', new App.User.Chat.New);
    router.use(url, '/popups', new App.User.Popup.Index);
    router.use(url, '/popup/new', new App.User.Popup.New);
    router.use(url, '/popup/{id}/edit', new App.User.Popup.Edit);
    router.use(url, '/refunds', new App.User.Refund.Index);
    router.use(url, '/domains', new App.User.Domain.Index);
    router.use(url, '/recurrence', new App.User.Recurrence.Index);
    /**
     * Admin
     */
    router.use(url, '/admin/dashboard', new App.Admin.Dashboard.Index);
    router.use(url, '/admin/withdrawals', new App.Admin.Withdrawal.Index);
    router.use(url, '/admin/kyc', new App.Admin.Kyc.Index);
    router.use(url, '/admin/kyc/{id}/edit', new App.Admin.Kyc.Edit);
    router.use(url, '/admin/catalogs', new App.Admin.Catalog.Index);
    router.use(url, '/admin/catalog/{id}/edit', new App.Admin.Catalog.Edit);
    router.use(url, '/admin/customers', new App.Admin.Customer.Index);
    router.use(url, '/admin/customer/{id}/show', new App.Admin.Customer.Show);
    router.use(url, '/admin/products', new App.Admin.Product.Index);
    router.use(url, '/admin/product/{id}/show', new App.Admin.Product.Show);
    router.use(url, '/admin/product/requests', new App.Admin.Product.Request.Index);
    router.use(url, '/admin/orders', new App.Admin.Order.Index);
    router.use(url, '/admin/orders/{status}', new App.Admin.Order.Status);
    router.use(url, '/admin/order/{id}/show', new App.Admin.Order.Show);
    router.use(url, '/admin/balance', new App.Admin.Balance.Index);
    router.use(url, '/admin/marketing', new App.Admin.Marketing.Index);
    router.use(url, '/admin/support', new App.Admin.Support.Index);
    router.use(url, '/admin/support/{id}/show', new App.Admin.Support.Show);
    router.use(url, '/admin/awards', new App.Admin.Award.Index);
})(getSubdomainSerializedWithoutProtocol('app'));

/**
 * Subdomain: checkout
 */


(url => {
    router.use(url, '/{sku}', new App.Subdomains.Checkout.Index);
    router.use(url, '/{sku}/{variation}', new App.Subdomains.Checkout.Index);
    router.use(url, '/checkout', new App.Subdomains.Checkout.Checkout);
    router.use(url, '/billet', new App.Subdomains.Checkout.Billet);
    router.use(url, '/pix', new App.Subdomains.Checkout.Pix);
    router.use(url, '/thanks', new App.Subdomains.Checkout.Thanks);
    router.use(url, '/analysis', new App.Subdomains.Checkout.Analysis);

})(getSubdomainSerializedWithoutProtocol('checkout'));

/**
 * Subdomain: purchase
 */
(url => {
    router.use(url, '/', new App.Subdomains.Purchase.Home.Index);
    router.use(url, '/dashboard', new App.Subdomains.Purchase.Dashboard.Index);
    router.use(url, '/subscriptions', new App.Subdomains.Purchase.Subscription.Index);
    router.use(url, '/login', new App.Subdomains.Purchase.Login.Index);
    router.use(url, '/logout', new App.Subdomains.Purchase.Logout.Index);
    router.use(url, '/reset/password', new App.Subdomains.Purchase.ResetPassword.Index);
    router.use(url, '/reset/password/{token}', new App.Subdomains.Purchase.ResetPassword.CreateNewPassword.Index);

})(getSubdomainSerializedWithoutProtocol('purchase'));