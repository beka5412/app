

(
    () => {
        /**
         * Contexts
         * Listen events in context
         */
        (() => {
            
            /**
             * Main Website
             */
            (() => {
                let dashboard = new Events.Contexts.Dashboard.Click;
                let form = new Events.Contexts.Form.Click;
                let public = new Events.Contexts.Public.Click;
                
                /**
                 * Dashboard
                 */       
                dashboard.on('onSubmit');

                /**
                 * Form
                 */
                form.on('onSubmit');
                dashboard.on('reset');

                /**
                 * Public
                 */
                public.on('onSubmit');
            })(); // end main website
            
            /**
             * Checkout
             */
            (() => {
                let dashboard = new Events.Contexts.Subdomains.Checkout.Dashboard.Click;
                let form = new Events.Contexts.Subdomains.Checkout.Form.Click;
                let public = new Events.Contexts.Subdomains.Checkout.Public.Click;
                
                /**
                 * Dashboard
                 */       
                dashboard.on('onSubmit');

                /**
                 * Form
                 */
                form.on('onSubmit');

                /**
                 * Public
                 */
                public.on('onSubmit');
            })(); // end checkout
        })();


        /**
         * Pages
         * Listen events in page
         */
        (() => {

            /**
             * Main Website
             */
            (() => {
                /**
                 * Public
                 */
                (() => {
                    let login = new App.Public.Login;
                    let register = new App.Public.Register;

                    /**
                     * Login
                     */       
                    login.onEnter('onSubmit');
                    login.onClick('onSubmit');

                    /**
                     * Register
                     */
                    register.onClick('onSubmit');
                    register.onReady('ready');
                    register.onEnded('end');
                })();

                /**
                 * User
                 * 
                 * Shared context
                 */
                (() => {
                    let dashboard = new App.User.Dashboard;
                    // let profile = new App.User.Profile;
                    let productEdit = new App.User.Product.Edit;
                    let products = new App.User.Product.Index;
                    let customers = new App.User.Customer.Index;
                    let customerEdit = new App.User.Customer.Edit;
                    let coupons = new App.User.Coupon.Index;
                    let couponEdit = new App.User.Coupon.Edit;
                    let orderbumps = new App.User.OrderBump.Index;
                    let orderbumpEdit = new App.User.OrderBump.Edit;
                    let checkouts = new App.User.Product.Checkout.Index;
                    let checkoutEdit = new App.User.Product.Checkout.Edit;
                    let balance = new App.User.Balance.Index;
                    let pixels = new App.User.Product.Pixel.Index;
                    let pixelEdit = new App.User.Product.Pixel.Edit;
                    let plans = new App.User.Product.Plan.Index;
                    let planEdit = new App.User.Product.Plan.Edit;
                    let links = new App.User.Product.Link.Index;
                    let upsell = new App.User.Product.Upsell.Index;
                    let affiliation = new App.User.Product.Affiliation.Index;
                    let affiliationShow = new App.User.Product.Affiliation.Product.Show;
                    let productSettings = new App.User.Product.Setting.Index;
                    let marketplace = new App.User.MarketPlace.Index;
                    let marketplaceShow = new App.User.MarketPlace.Show;
                    let chat = new App.User.Chat.Index;
                    let chatEdit = new App.User.Chat.Edit;
                    let popup = new App.User.Popup.Index;
                    let popupEdit = new App.User.Popup.Edit;
                    let kyc = new App.User.Kyc.Index;
                    let domains = new App.User.Domain.Index;
                    let settings = new App.User.Setting.Index;
                    let upsells = new App.User.Upsell.Index;
                    let upsellEdit = new App.User.Upsell.Edit;
                    let subscriptions = new App.User.Subscription.Index;
                    let apps = new App.User.App.Index;
                    let appUTMify = new App.User.App.UTMify.Index;
                    let appMemberkit = new App.User.App.Memberkit.Index;
                    let appMemberkitEdit = new App.User.App.Memberkit.Edit;
                    let appAstronMembers = new App.User.App.AstronMembers.Index;
                    let appAstronMembersEdit = new App.User.App.AstronMembers.Edit;
                    let appCademi = new App.User.App.Cademi.Index;
                    let appCademiEdit = new App.User.App.Cademi.Edit;
                    let appSellflux = new App.User.App.Sellflux.Index;
                    let appSellfluxEdit = new App.User.App.Sellflux.Edit;
                    let refund = new App.User.Refund.Index;
                    let profile = new App.User.Profile.Edit;
                    let sale = new App.User.Sale.Index;


                    /**
                     * Dashboard
                     */       
                    dashboard.onClick('dashboardOnSubmit');
                    dashboard.onReady('ready');
                    dashboard.onEnded('end');
                    dashboard.onClick('dashboardFilterChartOnClick');

                    /**
                     * Profile
                     */
                    profile.onClick('profileOnSubmit');
                    // profile.onKeyup('$inputCurrency');
                    // profile.onKeydown('$onlyNumbers');
                    profile.onClick('profileUploadPhotoOnClick');
                    profile.onChange('profileUploadPhotoOnChange');

                    /**
                     * Products
                     */
                    products.onClick('productDestroy');
                    products.onClick('newProduct');
                    products.onReady('productsReady');
                    products.onResize('productsOnResize');
                    productEdit.onClick('productOnSubmit');
                    productEdit.onChange('productUploadImage');
                    productEdit.onKeyup('$inputCurrency');
                    productEdit.onKeydown('$onlyNumbers');
                    productEdit.onBlur('$inputCurrency');
                    productEdit.onChange('productUploadAttachment');
                    productEdit.onClick('addVariation');
                    productEdit.onClick('delVariation');
                    productEdit.onEnded('end');
                    productEdit.onClick('productCopyUpsellSnippet');
                    productEdit.onClick('productRemoveAttachment');
                    productEdit.onClick('productDestroy');
                    // another action on the page ...
                    // another action on the page ...

                    /**
                     * Product > Checkouts
                     */
                    checkouts.onClick('checkoutDestroy');

                    /**
                     * Product > Checkouts > Edit
                     */
                    checkoutEdit.onClick('checkoutOnSubmit');
                    checkoutEdit.onChange('checkoutUploadTopBanner');
                    checkoutEdit.onChange('checkoutUploadSidebarBanner');
                    checkoutEdit.onChange('checkoutUploadTop2Banner');
                    checkoutEdit.onChange('checkoutUploadFooterBanner');
                    checkoutEdit.onChange('checkoutUploadLogoBanner');
                    checkoutEdit.onChange('checkoutUploadFaviconBanner');
                    checkoutEdit.onClick('checkoutRemoveTopBanner');
                    checkoutEdit.onClick('checkoutRemoveTop2Banner');
                    checkoutEdit.onClick('checkoutRemoveSidebarBanner');
                    checkoutEdit.onClick('checkoutRemoveFooterBanner');
                    checkoutEdit.onClick('checkoutRemoveLogoBanner');
                    checkoutEdit.onClick('checkoutRemoveFaviconBanner');
                    checkoutEdit.onClick('checkoutBackRedirectOnSubmit');
                    checkoutEdit.onReady('ready');
                    checkoutEdit.onEnded('end');

                    /**
                     * Product > Pixels
                     */
                    pixelEdit.onClick('pixelOnSubmit');
                    pixels.onClick('pixelDestroy');

                    /**
                     * Product > Plan
                     */
                    planEdit.onClick('planOnSubmit');
                    plans.onClick('planDestroy');

                    /**
                     * Product > Link
                     */
                    links.onClick('copyLink');

                    /**
                     * Product > Upsell
                     */
                    upsell.onClick('productUpsellOnSubmit');
                    upsell.onClick('productCopyUpsellSnippet');
                    upsell.onClick('productUpsellGenerateSnippet');

                    /**
                     * Product > Affiliation
                     */
                    affiliation.onClick('affOnSubmit');
                    affiliation.onChange('percentOrPriceOnChange');
                    affiliationShow.onClick('promoteOnClick');
                    affiliationShow.onClick('demoteOnClick');

                    /**
                     * Product > Settings
                     */
                    productSettings.onClick('productSettingsOnSubmit');

                    /**
                     * Customers
                     */
                    customerEdit.onClick('customerOnSubmit');
                    customers.onClick('destroyCustomer');

                    /**
                     * Coupons
                     */
                    couponEdit.onClick('couponOnSubmit');
                    coupons.onClick('destroyCoupon');
                    coupons.onReady('ready');
                    

                    /**
                     * OrderBump
                     */
                    orderbumpEdit.onClick('orderbumpOnSubmit');
                    orderbumps.onClick('orderbumpDestroy');

                    /**
                     * Balance
                     */
                    balance.onClick('bankAccountOnSubmit');
                    balance.onClick('withdrawRequestOnClick');
                    balance.onKeyup('$inputCurrency');
                    balance.onKeyup('$inputCurrencyAlways');
                    balance.onKeydown('$onlyNumbers');
                    balance.onBlur('$inputCurrency');
                    balance.onBlur('$inputCurrencyAlways');
                    balance.onKeyup('calcWithdrawal');

                    /**
                     * Marketplace
                     */
                    // marketplace.onEnded('end');
                    // marketplace.onLoad('load');
                    marketplaceShow.onClick('promoteOnClick');
                    marketplaceShow.onClick('demoteOnClick');

                    /**
                     * Marketing > Chat
                     */
                    chat.onClick('chatDestroy');
                    chatEdit.onClick('chatOnSubmit');

                    /**
                     * Marketing > Popup
                     */
                    popup.onClick('popupDestroy');
                    popupEdit.onClick('popupOnSubmit');

                    /**
                     * Kyc
                     */
                    kyc.onClick('kycOnSubmit');
                    kyc.onChange('uploadDocBackOnChange');
                    kyc.onChange('uploadDocFrontOnChange');
                    kyc.onChange('uploadFrontSelfieOnChange');
                    kyc.onClick('uploadDocFrontOnClick');
                    kyc.onClick('uploadDocBackOnClick');
                    kyc.onClick('uploadFrontSelfieOnClick');
                    kyc.onEnded('end');
                    kyc.onKeyup('cpfCnpjOnKeyup');
                    
                    /**
                     * Domain
                     */
                    domains.onClick('test');

                    /**
                     * Settings
                     */
                    settings.onClick('addDomain');
                    settings.onKeyup('domainKeyUp');

                    /**
                     * Upsell
                     */
                    upsellEdit.onClick('upsellOnSubmit');
                    upsells.onClick('destroyUpsell');
                    upsellEdit.onChange('setPriceVariations');
                    upsells.onClick('copyHTML');
                    upsells.onClick('copyHTMLModal');

                    /**
                     * Subscription
                     */
                    subscriptions.onClick('cancelCustomerSubscriptionOnClick');

                    /**
                     * Apps
                     */
                    apps.onChange('onChangeAppUTMify');

                    /**
                     * App > UTMify
                     */
                    appUTMify.onClick('onSubmitAppUTMify');

                    /**
                     * App > Memberkit
                     */
                    appMemberkit.onClick('destroyMemberkitIntegration');
                    appMemberkitEdit.onClick('onSubmitMemberkit');

                    /**
                     * App > AstronMembers
                     */
                    appAstronMembers.onClick('destroyAstronmembersIntegration');
                    appAstronMembersEdit.onClick('onSubmitAstronMembers');

                    /**
                     * App > Cademi
                     */
                    appCademi.onClick('destroyCademiIntegration');
                    appCademiEdit.onClick('onSubmitCademi');
                    appCademiEdit.onChange('appCademiProductOnChange');
                    appCademiEdit.onReady('editCademiOnReady');

                    /**
                     * App > Sellflux
                     */
                    appSellflux.onClick('destroySellfluxIntegration');
                    appSellfluxEdit.onClick('onSubmitSellflux');

                    /**
                     * Refund
                     */
                    refund.onClick('confirmRefundCustomerOnClick');
                    refund.onClick('cancelRefundCustomerOnClick');

                    /**
                     * Sales
                     */
                    sale.onClick('fetchSalesData');
                })();

                /**
                 * Admin
                 * 
                 * Shared context
                 */
                (() => {
                    let admin = new App.Public.AdminLogin;
                    let widthdrawals = new App.Admin.Withdrawal.Index;
                    let kyc = new App.Admin.Kyc.Index;
                    let kycEdit = new App.Admin.Kyc.Edit;
                    let catalogs = new App.Admin.Catalog.Index;
                    let productRequests = new App.Admin.Product.Request.Index;
                    let awardRequests = new App.Admin.Award.Index;
                    let adminDashboard = new App.Admin.Dashboard.Index;
                    let adminOrder = new App.Admin.Order.Index;
                    
                    /**
                     * Admin
                     */     
                    admin.onEnter('onSubmit');
                    admin.onClick('onSubmit');

                    adminDashboard.onReady('ready');
                    adminOrder.onClick('fetchOrderData');

                    catalogs.onClick('newCatalog')
                    catalogs.onClick('editCatalog')

                    /**
                     * Withdrawals
                     */
                    widthdrawals.onClick('approveOnClick');
                    widthdrawals.onClick('rejectOnClick');
                    widthdrawals.onClick('seeWithdrawalRequest');

                    /**
                     * Kyc > Edit
                     */
                    // kyc.onClick('approveKycOnClick');
                    // kyc.onClick('rejectKycOnClick');
                    kycEdit.onClick('adminKycOnSubmit');
                    kycEdit.onEnded('end');

                    /**
                     * Product requests
                     */
                    productRequests.onClick('adminProductRequestApprove');
                    productRequests.onClick('adminProductRequestReject');

                    /**
                     * Award requests
                     */
                    awardRequests.onClick('adminAwardSentOnClick');
                    awardRequests.onClick('adminAwardCanceledOnClick');

                    

                })();
            })(); // end main website
            
            /**
             * Checkout
             * 
             * Shared context
             */
            (() => {
                let checkoutIndex = new App.Subdomains.Checkout.Index;
                // let checkoutCheckout = new App.Subdomains.Checkout.Checkout;
                let pixGenerated = new App.Subdomains.Checkout.Pix;
                let billetGenerated = new App.Subdomains.Checkout.Billet;
                let thanks = new App.Subdomains.Checkout.Thanks;

                /**
                 * Checkout
                 */
                checkoutIndex.onClick('checkoutOnSubmit');
                checkoutIndex.onClick('pmCreditCardOnClick');
                checkoutIndex.onClick('pmPixOnClick');
                checkoutIndex.onClick('pmBilletOnClick');
                checkoutIndex.onClick('orderbumpCheckboxOnClick');
                checkoutIndex.onReady('ready');
                checkoutIndex.onStepperNext('validateOnNext');
                checkoutIndex.onChange('installmentsOnChange');
                checkoutIndex.onEnded('end');
                checkoutIndex.onClick('applyCoupon');
                checkoutIndex.onChange('emailOnChange');
                checkoutIndex.onBlur('emailOnBlur');
                checkoutIndex.onClick('btnVerifyEmailOnClick');
                checkoutIndex.onBlur('holdernameOnBlur');
                checkoutIndex.onChange('holdernameOnChange');
                checkoutIndex.onClick('addCreditCardOnClick');
                checkoutIndex.onClick('savedCreditCardOnClick');
                checkoutIndex.onClick('editAddressOnClick');
                checkoutIndex.onKeyup('checkoutOnKeyupName');
                checkoutIndex.onClick('checkoutNextStep');

                /**
                 * Thanks
                 */
                thanks.onReady('ready');
                // checkoutCheckout.onClick('onSubmit');
                
                /**
                 * Pix generated
                 */
                pixGenerated.onClick('pixCopyCode');

                /**
                 * Billet printed
                 */
                billetGenerated.onClick('billetCopyCode');
            })(); // end checkout
            
            /**
             * Purchase
             * 
             * Shared context
             */
            (() => {
                let login = new App.Subdomains.Purchase.Login.Index;
                let dashboard = new App.Subdomains.Purchase.Dashboard.Index;
                let subscriptions = new App.Subdomains.Purchase.Subscription.Index;
                let resetPassword = new App.Subdomains.Purchase.ResetPassword.Index;
                let createNewPassword = new App.Subdomains.Purchase.ResetPassword.CreateNewPassword.Index;

                /**
                 * Login
                 */
                login.onClick('loginOnSubmit');
                login.onEnter('loginOnSubmit');
                login.onClick('eyeOnClick');
                login.onClick('eyeOffOnClick');

                /**
                 * Dashboard
                 */
                dashboard.onClick('showPurchaseInfo');
                dashboard.onClick('refundPurchasePre');
                dashboard.onClick('refundPurchase');
                dashboard.onClick('cancelRefundPurchase');

                /**
                 * Subscriptions
                 */
                subscriptions.onClick('cancelSubscriptionOnClick');

                /**
                 * Reset Password
                 */
                resetPassword.onClick('resetPasswordOnSubmit');

                /**
                 * Reset Password > Create New Password
                 */
                createNewPassword.onClick('savePasswordSubmit');
                
            })(); // end checkout
        })(); // end pages
    }
)();