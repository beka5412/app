<?php

$subdomain_index = env('SUBDOMAIN_INDEX');
$index = $subdomain_index ? $subdomain_index : ".";
$subdomain_checkout = translate_subdomain_name('checkout');
$subdomain_purchase = translate_subdomain_name('purchase');

$middlewares = [
    'auth' => 'Backend\Middlewares\AuthMiddleware@login',
    'customer_auth' => 'Backend\Middlewares\CustomerAuthMiddleware@login',
    'admin_auth' => 'Backend\Middlewares\AdminAuthMiddleware@login',
    'test' => 'Backend\Middlewares\TestMiddleware@access',
];

$testMiddlewares = ['middlewares' => [$middlewares['test']]];
$userMiddlewares = ['middlewares' => [$middlewares['auth']]];
$customerMiddlewares = ['middlewares' => [$middlewares['customer_auth']]];
$adminMiddlewares = ['middlewares' => [$middlewares['admin_auth']]];

return [
    $index =>
        [
            /**
             * Test
             */
            ['GET', '/phpinfo', 'Backend\Controllers\Test@phpinfo', $testMiddlewares],
            ['GET', '/test123', 'Backend\Controllers\Test@test_123', $testMiddlewares],
            ['GET', '/test', 'Backend\Controllers\Test@panel', $testMiddlewares],
            ['GET', '/test/date', 'Backend\Controllers\Test@date', $testMiddlewares],
            ['GET', '/test/hash_make/{password}', 'Backend\Controllers\Test@hash_make', $testMiddlewares],
            // ['GET', '/script/customers-password-hash', 'Backend\Controllers\Script@customers_password_hash', $testMiddlewares],
            ['GET', '/test/button/upsell', 'Backend\Controllers\Test@button_upsell', $testMiddlewares],
            ['GET', '/test/js-redirect', 'Backend\Controllers\Test@js_redirect', $testMiddlewares],
            ['GET', '/test/html-redirect', 'Backend\Controllers\Test@html_redirect', $testMiddlewares],
            ['GET', '/test/ipag/get-cards', 'Backend\Controllers\Test@ipag_get_cards', $testMiddlewares],
            ['GET', '/test/ipag/get-consult-subscription/{id}', 'Backend\Controllers\Test@ipag_consult_subscription', $testMiddlewares],
            ['GET', '/test/email/push', 'Backend\Controllers\Test@push_email', $testMiddlewares],
            ['GET', '/test/email/send', 'Backend\Controllers\Test@send', $testMiddlewares],
            ['GET', '/test/email', 'Backend\Controllers\Test@email', $testMiddlewares],
            ['GET', '/test/upsell-1', 'Backend\Controllers\Test@upsell_1', $testMiddlewares],
            ['GET', '/test/upsell-2', 'Backend\Controllers\Test@upsell_2', $testMiddlewares],
            ['GET', '/test/upsell-x/{product_id}', 'Backend\Controllers\Test@upsell_x', $testMiddlewares],
            ['GET', '/test/transfer', 'Backend\Controllers\Test@transfer', $testMiddlewares],
            ['GET', '/test/queue-stripe-webhook', 'Backend\Controllers\Test@queue_stripe_webhook', $testMiddlewares],
            ['POST', '/test/webhook/stripe', 'Backend\Controllers\Test@webhook_stripe', $testMiddlewares],
            ['POST', '/test/utmify', 'Backend\Controllers\Test@utmify', $testMiddlewares],
            ['POST', '/test/utmify/{id}', 'Backend\Controllers\Test@utmify_entity', $testMiddlewares],
            ['GET', '/test/iugu/charge/queue/push/{order_id}/{customer_id}', 'Backend\Controllers\Test@iugu_charge__queue_push', $testMiddlewares],
            ['GET', '/test/iugu/charge/queue/send/{id}', 'Backend\Controllers\Test@iugu_charge__queue_send', $testMiddlewares],
            ['GET', '/test/iugu/charge/update-items/{user_id}', 'Backend\Controllers\Test@iugu_charge__update_items'],
            ['GET', '/test/iugu/charge/list-items/{user_id}/{token}', 'Backend\Controllers\Test@iugu_charge__list_items'],
            ['GET', '/test/seller-credit-queue', 'Backend\Controllers\Test@seller_credit_queue', $testMiddlewares],
            ['GET', '/test/seller-credit-risk', 'Backend\Controllers\Test@seller_credit_risk', $testMiddlewares],
            ['GET', '/test/seller-credit-remove-item-queue', 'Backend\Controllers\Test@seller_credit_remove_queue_item', $testMiddlewares],
            ['GET', '/test/seller-credit-update-items-queue', 'Backend\Controllers\Test@seller_credit_update_items_queue', $testMiddlewares],
            ['GET', '/test/onesignal', 'Backend\Controllers\Test@onesignal', $testMiddlewares],
            // ['GET', '/test/script/resend-webhooks-charges', 'Backend\Controllers\Test@resend_webhooks_charges', $testMiddlewares],
            ['GET', '/test/timeout', 'Backend\Controllers\Test@timeout', $testMiddlewares],
            ['GET', '/test/timeout2', 'Backend\Controllers\Test@timeout2', $testMiddlewares],
            ['GET', '/test/timeout3', 'Backend\Controllers\Test@timeout3', $testMiddlewares],
            ['GET', '/test/memberkit', 'Backend\Controllers\Test@memberkit', $testMiddlewares],
            ['GET', '/test/astronmembers-add-user', 'Backend\Controllers\Test@astronmembers_add_user', $testMiddlewares],
            ['GET', '/test/astronmembers-drop-user', 'Backend\Controllers\Test@astronmembers_drop_user', $testMiddlewares],
            ['GET', '/test/send-email-test', 'Backend\Controllers\Test@send_email_test', $testMiddlewares],
            ['GET', '/test/stripe-reversal', 'Backend\Controllers\Test@stripe_reversal', $testMiddlewares],
            ['GET', '/test/mount-email-html', 'Backend\Controllers\Test@mount_email_html', $testMiddlewares],
            ['GET', '/test/sellflux', 'Backend\Controllers\Test@sellflux', $testMiddlewares],
            ['GET', '/test/autologin/{email}/{pw}', 'Backend\Controllers\Test@autologin', $testMiddlewares],
            ['GET', '/test/future-release', 'Backend\Controllers\Test@future_release', $testMiddlewares],
            ['GET', '/test/cademi/register/{subdomain}/{products}/{token}', 'Backend\Controllers\Test@test_cademi', $testMiddlewares],

            /**
             * Scripts
             */
            ['GET', '/script/resend-all-purchases', 'Backend\Controllers\Script@resend_all_purchases'],

            /**
             * User
             */
            ['GET', '/dashboard', 'Backend\Controllers\User\DashboardController@index', $userMiddlewares],
            // ['GET', '/profile', 'Backend\Controllers\User\ProfileController@index', $userMiddlewares],
            ['GET', '/logout', 'Backend\Controllers\User\LogoutController@index'],
            ['GET', '/members-area', 'Backend\Controllers\User\DashboardController@members_area'],
            ['GET', '/products', 'Backend\Controllers\User\Product\IndexController@index', $userMiddlewares],
            ['GET', '/product/{id}/edit', 'Backend\Controllers\User\Product\EditController@index', $userMiddlewares],
            ['GET', '/marketplace', 'Backend\Controllers\User\MarketPlace\IndexController@index', $userMiddlewares],
            ['GET', '/marketplace/{product_id}/view', 'Backend\Controllers\User\MarketPlace\ShowController@index', $userMiddlewares],
            ['GET', '/sales', 'Backend\Controllers\User\Sale\IndexController@index', $userMiddlewares],
            ['GET', '/subscriptions', 'Backend\Controllers\User\Subscription\IndexController@index', $userMiddlewares],
            ['GET', '/abandoned-carts', 'Backend\Controllers\User\AbandonedCart\IndexController@index', $userMiddlewares],
            ['GET', '/abandoned-cart/{id}/show', 'Backend\Controllers\User\AbandonedCart\ShowController@index', $userMiddlewares],
            ['GET', '/affiliates', 'Backend\Controllers\User\Affiliate\IndexController@index', $userMiddlewares],
            ['GET', '/balance', 'Backend\Controllers\User\Balance\IndexController@index', $userMiddlewares],
            ['GET', '/orderbumps', 'Backend\Controllers\User\OrderBump\IndexController@index', $userMiddlewares],
            ['GET', '/orderbump/{id}/edit', 'Backend\Controllers\User\OrderBump\EditController@index', $userMiddlewares],
            ['GET', '/upsells', 'Backend\Controllers\User\Upsell\IndexController@index', $userMiddlewares],
            ['GET', '/upsell/{id}/edit', 'Backend\Controllers\User\Upsell\EditController@index', $userMiddlewares],
            ['GET', '/social-proof', 'Backend\Controllers\User\SocialProof\IndexController@index', $userMiddlewares],
            ['GET', '/product-groups', 'Backend\Controllers\User\ProductGroup\IndexController@index', $userMiddlewares],
            ['GET', '/product-group/{id}/edit', 'Backend\Controllers\User\ProductGroup\EditController@index', $userMiddlewares],
            ['GET', '/support', 'Backend\Controllers\User\Support\IndexController@index', $userMiddlewares],
            ['GET', '/reports', 'Backend\Controllers\User\Report\IndexController@index', $userMiddlewares],
            ['GET', '/reports/general', 'Backend\Controllers\User\Report\GeneralController@index', $userMiddlewares],
            ['GET', '/reports/salesbyday', 'Backend\Controllers\User\Report\SalesByDayController@index', $userMiddlewares],
            ['GET', '/reports/salesbyproduct', 'Backend\Controllers\User\Report\SalesByProductController@index', $userMiddlewares],
            ['GET', '/reports/chargebacks', 'Backend\Controllers\User\Report\ChargebacksController@index', $userMiddlewares],
            ['GET', '/settings', 'Backend\Controllers\User\Setting\IndexController@index', $userMiddlewares],
            ['GET', '/apps', 'Backend\Controllers\User\App\IndexController@index', $userMiddlewares],
            ['GET', '/app/utmify', 'Backend\Controllers\User\App\UTMify\EditController@index', $userMiddlewares],
            ['GET', '/app/memberkit', 'Backend\Controllers\User\App\Memberkit\IndexController@index', $userMiddlewares],
            ['GET', '/app/memberkit/{id}/edit', 'Backend\Controllers\User\App\Memberkit\EditController@index', $userMiddlewares],
            ['GET', '/app/astronmembers', 'Backend\Controllers\User\App\Astronmembers\IndexController@index', $userMiddlewares],
            ['GET', '/app/astronmembers/{id}/edit', 'Backend\Controllers\User\App\Astronmembers\EditController@index', $userMiddlewares],
            ['GET', '/app/sellflux', 'Backend\Controllers\User\App\Sellflux\IndexController@index', $userMiddlewares],
            ['GET', '/app/sellflux/{id}/edit', 'Backend\Controllers\User\App\Sellflux\EditController@index', $userMiddlewares],
            ['GET', '/app/cademi', 'Backend\Controllers\User\App\Cademi\IndexController@index', $userMiddlewares],
            ['GET', '/app/cademi/{id}/edit', 'Backend\Controllers\User\App\Cademi\EditController@index', $userMiddlewares],
            ['GET', '/rocketzap', 'Backend\Controllers\User\RocketZap\IndexController@index', $userMiddlewares],
            ['GET', '/rocketmember', 'Backend\Controllers\User\RocketMember\IndexController@index', $userMiddlewares],
            ['GET', '/customers', 'Backend\Controllers\User\Customer\IndexController@index', $userMiddlewares],
            ['GET', '/customer/{id}/edit', 'Backend\Controllers\User\Customer\EditController@index', $userMiddlewares],
            ['GET', '/customer/{id}/show', 'Backend\Controllers\User\Customer\ShowController@index', $userMiddlewares],
            ['GET', '/coupons', 'Backend\Controllers\User\Coupon\IndexController@index', $userMiddlewares],
            ['GET', '/coupon/{id}/edit', 'Backend\Controllers\User\Coupon\EditController@index', $userMiddlewares],
            ['GET', '/sale/{id}/show', 'Backend\Controllers\User\Sale\ShowController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/checkout/{checkout_id}/edit', 'Backend\Controllers\User\Product\Checkout\EditController@index', $userMiddlewares],
            ['GET', '/marketing', 'Backend\Controllers\User\Marketing\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/pixels', 'Backend\Controllers\User\Product\Pixel\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/pixel/{id}/edit', 'Backend\Controllers\User\Product\Pixel\EditController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/settings', 'Backend\Controllers\User\Product\Setting\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/checkouts', 'Backend\Controllers\User\Product\Checkout\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/links', 'Backend\Controllers\User\Product\Link\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/upsell', 'Backend\Controllers\User\Product\Upsell\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/tools', 'Backend\Controllers\User\Product\Tool\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/affiliation', 'Backend\Controllers\User\Product\Affiliation\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/plans', 'Backend\Controllers\User\Product\Plan\IndexController@index', $userMiddlewares],
            ['GET', '/product/{product_id}/plan/{id}/edit', 'Backend\Controllers\User\Product\Plan\EditController@index', $userMiddlewares],
            ['GET', '/aff/products', 'Backend\Controllers\User\Product\Affiliation\Product\IndexController@index', $userMiddlewares],
            ['GET', '/aff/product/{id}', 'Backend\Controllers\User\Product\Affiliation\Product\ShowController@index', $userMiddlewares],
            ['GET', '/aff/product/{id}/links', 'Backend\Controllers\User\Product\Affiliation\Product\Link\IndexController@index', $userMiddlewares],
            ['GET', '/aff/product/{id}/materials', 'Backend\Controllers\User\Product\Affiliation\Product\Material\IndexController@index', $userMiddlewares],
            ['GET', '/aff/product/{id}/support', 'Backend\Controllers\User\Product\Affiliation\Product\Support\IndexController@index', $userMiddlewares],
            ['GET', '/awards', 'Backend\Controllers\User\Award\IndexController@index', $userMiddlewares],
            ['GET', '/kyc', 'Backend\Controllers\User\Kyc\IndexController@index', $userMiddlewares],
            ['GET', '/kyc/{id}/front.png', 'Backend\Controllers\User\Kyc\EditController@front_png', $userMiddlewares],
            ['GET', '/kyc/{id}/back.png', 'Backend\Controllers\User\Kyc\EditController@back_png', $userMiddlewares],
            // ['GET', '/kyc/{id}/back.png', 'Backend\Controllers\User\Kyc\EditController@back_png', $userMiddlewares],
            ['GET', '/kyc/images/{name}', 'Backend\Controllers\User\Kyc\EditController@get_image', $userMiddlewares],
            ['GET', '/chats', 'Backend\Controllers\User\Chat\IndexController@index', $userMiddlewares],
            ['GET', '/chat/{id}/edit', 'Backend\Controllers\User\Chat\EditController@index', $userMiddlewares],
            ['GET', '/popups', 'Backend\Controllers\User\Popup\IndexController@index', $userMiddlewares],
            ['GET', '/popup/{id}/edit', 'Backend\Controllers\User\Popup\EditController@index', $userMiddlewares],
            ['GET', '/refunds', 'Backend\Controllers\User\Refund\IndexController@index', $userMiddlewares],
            ['GET', '/domains', 'Backend\Controllers\User\Domain\IndexController@index', $userMiddlewares],
            ['GET', '/profile/address', 'Backend\Controllers\User\Address\EditController@index', $userMiddlewares],
            ['GET', '/profile', 'Backend\Controllers\User\Profile\EditController@index', $userMiddlewares],
            ['GET', '/recurrence', 'Backend\Controllers\User\Recurrence\IndexController@index', $userMiddlewares],
            ['GET', '/recurrence/{id}/show', 'Backend\Controllers\User\Recurrence\ShowController@show', $userMiddlewares],

            // ajax
            // ['GET', '/ajax/pages/user/Profile', 'Backend\Controllers\User\ProfileController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/Dashboard', 'Backend\Controllers\User\DashboardController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/Index', 'Backend\Controllers\User\Product\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/Edit', 'Backend\Controllers\User\Product\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/address/Edit', 'Backend\Controllers\User\Address\EditController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{id}/edit', 'Backend\Controllers\User\Product\EditController@update', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{id}/edit-settings', 'Backend\Controllers\User\Product\EditController@update_settings', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/uploadImage', 'Backend\Controllers\User\Product\EditController@uploadImage', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/uploadAttachment', 'Backend\Controllers\User\Product\EditController@uploadAttachment', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/product/{id}/destroy', 'Backend\Controllers\User\Product\IndexController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/new', 'Backend\Controllers\User\Product\IndexController@new', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/checkout/{checkout_id}/backredirect/update', 'Backend\Controllers\User\Product\Checkout\BackRedirect\EditController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/marketplace/Index', 'Backend\Controllers\User\MarketPlace\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/full/user/marketplace/Index', 'Backend\Controllers\User\MarketPlace\IndexController@full', $userMiddlewares],
            ['POST', '/ajax/actions/user/marketplace/product/{id}/promote', 'Backend\Controllers\User\MarketPlace\ShowController@promote', $userMiddlewares],
            ['POST', '/ajax/actions/user/marketplace/product/{id}/demote', 'Backend\Controllers\User\MarketPlace\ShowController@demote', $userMiddlewares],
            ['GET', '/ajax/pages/user/sale/Index', 'Backend\Controllers\User\Sale\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/sale/Show', 'Backend\Controllers\User\Sale\ShowController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/subscription/Index', 'Backend\Controllers\User\Subscription\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/subscription/{id}/Show', 'Backend\Controllers\User\Subscription\ShowController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/subscription/{id}/cancel', 'Backend\Controllers\User\Subscription\IndexController@cancel', $userMiddlewares],
            ['GET', '/ajax/pages/user/abandoned-cart/Index', 'Backend\Controllers\User\AbandonedCart\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/abandoned-cart/Show', 'Backend\Controllers\User\AbandonedCart\ShowController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/affiliate/Index', 'Backend\Controllers\User\Affiliate\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/balance/Index', 'Backend\Controllers\User\Balance\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/discount-coupon/Index', 'Backend\Controllers\User\DiscountCoupon\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/orderbump/Index', 'Backend\Controllers\User\OrderBump\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/orderbump/new', 'Backend\Controllers\User\OrderBump\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/orderbump/Edit', 'Backend\Controllers\User\OrderBump\EditController@element', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/orderbump/{id}/destroy', 'Backend\Controllers\User\OrderBump\IndexController@destroy', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/orderbump/{id}/edit', 'Backend\Controllers\User\OrderBump\EditController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/upsell/Index', 'Backend\Controllers\User\Upsell\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/upsell/new', 'Backend\Controllers\User\Upsell\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/upsell/Edit', 'Backend\Controllers\User\Upsell\EditController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/upsell/{id}/edit', 'Backend\Controllers\User\Upsell\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/upsell/{id}/destroy', 'Backend\Controllers\User\Upsell\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/social-proof/Index', 'Backend\Controllers\User\SocialProof\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product-group/Index', 'Backend\Controllers\User\ProductGroup\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product-group/Edit', 'Backend\Controllers\User\ProductGroup\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/support/Index', 'Backend\Controllers\User\Support\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/report/Index', 'Backend\Controllers\User\Report\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/setting/Index', 'Backend\Controllers\User\Setting\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/Index', 'Backend\Controllers\User\App\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/utmify/Index', 'Backend\Controllers\User\App\UTMify\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/memberkit/Index', 'Backend\Controllers\User\App\Memberkit\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/memberkit/Edit', 'Backend\Controllers\User\App\Memberkit\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/cademi/Index', 'Backend\Controllers\User\App\Cademi\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/cademi/Edit', 'Backend\Controllers\User\App\Cademi\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/astronmembers/Index', 'Backend\Controllers\User\App\Astronmembers\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/astronmembers/Edit', 'Backend\Controllers\User\App\Astronmembers\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/sellflux/Index', 'Backend\Controllers\User\App\Sellflux\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/app/sellflux/Edit', 'Backend\Controllers\User\App\Sellflux\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/rocketzap/Index', 'Backend\Controllers\User\RocketZap\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/rocketmember/Index', 'Backend\Controllers\User\RocketMember\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/customer/Index', 'Backend\Controllers\User\Customer\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/customer/Edit', 'Backend\Controllers\User\Customer\EditController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/customer/Show', 'Backend\Controllers\User\Customer\ShowController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/customer/{id}/edit', 'Backend\Controllers\User\Customer\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/customer/{id}/destroy', 'Backend\Controllers\User\Customer\IndexController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/customer/new', 'Backend\Controllers\User\Customer\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/coupon/Index', 'Backend\Controllers\User\Coupon\IndexController@element', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/coupon/{id}/destroy', 'Backend\Controllers\User\Coupon\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/coupon/Edit', 'Backend\Controllers\User\Coupon\EditController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/coupon/{id}/edit', 'Backend\Controllers\User\Coupon\EditController@update', $userMiddlewares],
            ['POST', '/ajax/actions/user/coupon/new', 'Backend\Controllers\User\Coupon\IndexController@new', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/checkout/new', 'Backend\Controllers\User\Product\Checkout\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/{product_id}/checkout/{checkout_id}/Edit', 'Backend\Controllers\User\Product\Checkout\EditController@element', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/destroy', 'Backend\Controllers\User\Product\Checkout\IndexController@destroy', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/testimonial/{testimonial_id}/edit', 'Backend\Controllers\User\Product\Checkout\Testimonial\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/testimonial/{testimonial_id}/destroy', 'Backend\Controllers\User\Product\Checkout\Testimonial\EditController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/testimonial/new', 'Backend\Controllers\User\Product\Checkout\Testimonial\EditController@new', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/testimonial/{testimonial_id}/uploadImage', 'Backend\Controllers\User\Product\Checkout\Testimonial\EditController@uploadImage', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/bank-account/{id}/edit', 'Backend\Controllers\User\BankAccount\EditController@update', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/withdrawal/{id}/store', 'Backend\Controllers\User\Withdrawal\IndexController@store', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/withdrawal/{id}/iugu', 'Backend\Controllers\User\Withdrawal\IndexController@iugu', $userMiddlewares],
            ['GET', '/ajax/pages/user/marketing/Index', 'Backend\Controllers\User\Marketing\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/pixels/Index', 'Backend\Controllers\User\Product\Pixel\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/pixel/new', 'Backend\Controllers\User\Product\Pixel\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/pixel/Edit', 'Backend\Controllers\User\Product\Pixel\EditController@element', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/pixel/{id}/destroy', 'Backend\Controllers\User\Product\Pixel\IndexController@destroy', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/pixel/{pixel_id}/edit', 'Backend\Controllers\User\Product\Pixel\EditController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/links/Index', 'Backend\Controllers\User\Product\Link\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/upsell/Index', 'Backend\Controllers\User\Product\Upsell\IndexController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/upsell/edit', 'Backend\Controllers\User\Product\Upsell\EditController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/plans/Index', 'Backend\Controllers\User\Product\Plan\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{id}/plan/new', 'Backend\Controllers\User\Product\Plan\IndexController@new', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/plan/{plan_id}/edit', 'Backend\Controllers\User\Product\Plan\EditController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/plan/Edit', 'Backend\Controllers\User\Product\Plan\EditController@element', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/plan/{id}/destroy', 'Backend\Controllers\User\Product\Plan\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/Index', 'Backend\Controllers\User\Product\Affiliation\IndexController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/affiliation/edit', 'Backend\Controllers\User\Product\Affiliation\IndexController@update', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/products/Index', 'Backend\Controllers\User\Product\Affiliation\Product\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/product/{id}/Show', 'Backend\Controllers\User\Product\Affiliation\Product\ShowController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/product/{id}/links/Index', 'Backend\Controllers\User\Product\Affiliation\Product\Link\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/product/{id}/materials/Index', 'Backend\Controllers\User\Product\Affiliation\Product\Material\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/affiliation/product/{id}/support/Index', 'Backend\Controllers\User\Product\Affiliation\Product\Support\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/settings/Index', 'Backend\Controllers\User\Product\Setting\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/product/checkouts/Index', 'Backend\Controllers\User\Product\Checkout\IndexController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/product/{product_id}/checkout/{id}/edit', 'Backend\Controllers\User\Product\Checkout\EditController@update', $userMiddlewares],
            ['POST', '/ajax/actions/user/product/{product_id}/checkout/{checkout_id}/uploadImage', 'Backend\Controllers\User\Product\Checkout\EditController@uploadImage', $userMiddlewares],
            ['GET', '/ajax/pages/user/marketplace/{product_id}/Show', 'Backend\Controllers\User\MarketPlace\ShowController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/award/Index', 'Backend\Controllers\User\Award\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/kyc/Index', 'Backend\Controllers\User\Kyc\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/chat/Index', 'Backend\Controllers\User\Chat\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/chat/new', 'Backend\Controllers\User\Chat\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/chat/Edit', 'Backend\Controllers\User\Chat\EditController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/chat/{id}/edit', 'Backend\Controllers\User\Chat\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/chat/{id}/destroy', 'Backend\Controllers\User\Chat\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/popup/Index', 'Backend\Controllers\User\Popup\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/popup/new', 'Backend\Controllers\User\Popup\IndexController@new', $userMiddlewares],
            ['GET', '/ajax/pages/user/popup/Edit', 'Backend\Controllers\User\Popup\EditController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/popup/{id}/edit', 'Backend\Controllers\User\Popup\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/popup/{id}/destroy', 'Backend\Controllers\User\Popup\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/refund/Index', 'Backend\Controllers\User\Refund\IndexController@element', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/refund/{id}/confirm', 'Backend\Controllers\User\Refund\IndexController@confirm', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/refund/{id}/cancel', 'Backend\Controllers\User\Refund\IndexController@cancel', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/kyc/update', 'Backend\Controllers\User\Kyc\EditController@update', $userMiddlewares],
            ['POST', '/ajax/actions/user/kyc/uploadImage', 'Backend\Controllers\User\Kyc\EditController@uploadImage', $userMiddlewares],
            ['GET', '/ajax/pages/user/domain/Index', 'Backend\Controllers\User\Domain\IndexController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/domain/add', 'Backend\Controllers\User\Domain\IndexController@add', $userMiddlewares],
            ['GET', '/ajax/actions/user/upsell/template', 'Backend\Controllers\User\Upsell\IndexController@get_template', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/utmify/edit', 'Backend\Controllers\User\App\UTMify\EditController@update', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/utmify/change', 'Backend\Controllers\User\App\UTMify\EditController@change', $userMiddlewares],
            ['POST', '/ajax/actions/user/app/memberkit/new', 'Backend\Controllers\User\App\Memberkit\IndexController@new', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/memberkit/{id}/edit', 'Backend\Controllers\User\App\Memberkit\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/app/memberkit/{id}/destroy', 'Backend\Controllers\User\App\Memberkit\IndexController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/app/astronmembers/new', 'Backend\Controllers\User\App\Astronmembers\IndexController@new', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/astronmembers/{id}/edit', 'Backend\Controllers\User\App\Astronmembers\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/app/astronmembers/{id}/destroy', 'Backend\Controllers\User\App\Astronmembers\IndexController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/app/sellflux/new', 'Backend\Controllers\User\App\Sellflux\IndexController@new', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/sellflux/{id}/edit', 'Backend\Controllers\User\App\Sellflux\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/app/sellflux/{id}/destroy', 'Backend\Controllers\User\App\Sellflux\IndexController@destroy', $userMiddlewares],
            ['POST', '/ajax/actions/user/app/cademi/new', 'Backend\Controllers\User\App\Cademi\IndexController@new', $userMiddlewares],
            ['PATCH', '/ajax/actions/user/app/cademi/{id}/edit', 'Backend\Controllers\User\App\Cademi\EditController@update', $userMiddlewares],
            ['DELETE', '/ajax/actions/user/app/cademi/{id}/destroy', 'Backend\Controllers\User\App\Cademi\IndexController@destroy', $userMiddlewares],
            ['GET', '/ajax/pages/user/profile/Edit', 'Backend\Controllers\User\Profile\EditController@element', $userMiddlewares],
            ['POST', '/ajax/actions/user/profile/uploadImage', 'Backend\Controllers\User\Profile\EditController@uploadImage', $userMiddlewares],
            ['POST', '/ajax/actions/user/profile/edit', 'Backend\Controllers\User\Profile\EditController@editProfile', $userMiddlewares],
            ['GET', '/ajax/actions/user/dashboard/sales/filter', 'Backend\Controllers\User\DashboardController@sales_filter', $userMiddlewares],
            ['GET', '/ajax/pages/user/recurrence/Index', 'Backend\Controllers\User\Recurrence\IndexController@element', $userMiddlewares],
            ['GET', '/ajax/pages/user/recurrence/{id}/Show', 'Backend\Controllers\User\Recurrence\ShowController@element', $userMiddlewares],
            /**
             * Admin
             */
            ['GET', '/admin/dashboard', 'Backend\Controllers\Admin\Dashboard\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/withdrawals', 'Backend\Controllers\Admin\Withdrawal\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/kyc', 'Backend\Controllers\Admin\Kyc\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/kyc/{id}/edit', 'Backend\Controllers\Admin\Kyc\EditController@index', $adminMiddlewares],
            ['GET', '/admin/kyc/{id}/front.png', 'Backend\Controllers\Admin\Kyc\EditController@front_png', $adminMiddlewares],
            ['GET', '/admin/kyc/{id}/back.png', 'Backend\Controllers\Admin\Kyc\EditController@back_png', $adminMiddlewares],
            ['GET', '/admin/logout', 'Backend\Controllers\Admin\LogoutController@index', $adminMiddlewares],
            ['GET', '/admin/catalogs', 'Backend\Controllers\Admin\Catalog\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/catalog/{id}/edit', 'Backend\Controllers\Admin\Catalog\EditController@index', $adminMiddlewares],
            ['GET', '/admin/customers', 'Backend\Controllers\Admin\Customer\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/customer/{id}/show', 'Backend\Controllers\Admin\Customer\ShowController@index', $adminMiddlewares],
            ['GET', '/admin/products', 'Backend\Controllers\Admin\Product\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/product/requests', 'Backend\Controllers\Admin\Product\Requests\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/product/{id}/show', 'Backend\Controllers\Admin\Product\ShowController@index', $adminMiddlewares],
            ['GET', '/admin/orders', 'Backend\Controllers\Admin\Order\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/orders/{status}', 'Backend\Controllers\Admin\Order\IndexController@status', $adminMiddlewares],
            ['GET', '/admin/order/{id}/show', 'Backend\Controllers\Admin\Order\ShowController@index', $adminMiddlewares],
            ['GET', '/admin/balance', 'Backend\Controllers\Admin\Balance\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/marketing', 'Backend\Controllers\Admin\Marketing\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/supports', 'Backend\Controllers\Admin\Support\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/support/{id}/show', 'Backend\Controllers\Admin\Support\ShowController@index', $adminMiddlewares],
            ['GET', '/admin/awards', 'Backend\Controllers\Admin\Award\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/chargebacks', 'Backend\Controllers\Admin\Chargeback\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/settings', 'Backend\Controllers\Admin\Settings\IndexController@index', $adminMiddlewares],
            ['POST', '/admin/settings/update', 'Backend\Controllers\Admin\Settings\IndexController@update', $adminMiddlewares],
            ['GET', '/admin/reports', 'Backend\Controllers\Admin\Report\IndexController@index', $adminMiddlewares],
            ['GET', '/admin/reports/active-subscriptions', 'Backend\Controllers\Admin\Report\SubscriptionController@activeSubscriptions', $adminMiddlewares],
            ['GET', '/admin/reports/daily-sales', 'Backend\Controllers\Admin\Report\DailySalesController@index', $adminMiddlewares],
            ['GET', '/admin/reports/daily-sales-by-user', 'Backend\Controllers\Admin\Report\DailySalesByUserController@index', $adminMiddlewares],
            ['GET', '/admin/reports/recurrences', 'Backend\Controllers\Admin\Report\RecurrenceController@index', $adminMiddlewares],

            // ajax
            ['GET', '/ajax/pages/admin/dashboard/Index', 'Backend\Controllers\Admin\Dashboard\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/withdrawal/Index', 'Backend\Controllers\Admin\Withdrawal\IndexController@element', $adminMiddlewares],
            ['POST', '/ajax/actions/admin/catalogs/new', 'Backend\Controllers\Admin\Catalog\IndexController@new', $adminMiddlewares],
            ['POST', '/ajax/actions/admin/catalogs/update', 'Backend\Controllers\Admin\Catalog\IndexController@update', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/withdrawal/{id}/edit', 'Backend\Controllers\Admin\Withdrawal\EditController@update', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/kyc/Index', 'Backend\Controllers\Admin\Kyc\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/kyc/Edit', 'Backend\Controllers\Admin\Kyc\EditController@element', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/kyc/{id}/edit', 'Backend\Controllers\Admin\Kyc\EditController@update', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/catalog/Index', 'Backend\Controllers\Admin\Catalog\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/catalog/Edit', 'Backend\Controllers\Admin\Catalog\EditController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/customer/Index', 'Backend\Controllers\Admin\Customer\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/customer/Show', 'Backend\Controllers\Admin\Customer\ShowController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/product/Index', 'Backend\Controllers\Admin\Product\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/product/Show', 'Backend\Controllers\Admin\Product\ShowController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/award/Index', 'Backend\Controllers\Admin\Award\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/product/requests/Index', 'Backend\Controllers\Admin\Product\Requests\IndexController@element', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/product/request/{id}/approve', 'Backend\Controllers\Admin\Product\Requests\IndexController@approve', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/product/request/{id}/reject', 'Backend\Controllers\Admin\Product\Requests\IndexController@reject', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/award/request/{id}/sent', 'Backend\Controllers\Admin\Award\IndexController@sent', $adminMiddlewares],
            ['PATCH', '/ajax/actions/admin/award/request/{id}/canceled', 'Backend\Controllers\Admin\Award\IndexController@canceled', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/order/Index', 'Backend\Controllers\Admin\Order\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/order/find', 'Backend\Controllers\Admin\Order\IndexController@find', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/order/Show', 'Backend\Controllers\Admin\Order\ShowController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/balance/Index', 'Backend\Controllers\Admin\Balance\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/marketing/Index', 'Backend\Controllers\Admin\Marketing\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/support/Index', 'Backend\Controllers\Admin\Support\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/support/Show', 'Backend\Controllers\Admin\Support\ShowController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/chargeback/Index', 'Backend\Controllers\Admin\Chargeback\IndexController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/chargeback/Show', 'Backend\Controllers\Admin\Chargeback\ShowController@element', $adminMiddlewares],
            ['GET', '/ajax/pages/admin/settings/Index', 'Backend\Controllers\Admin\Settings\IndexController@element', $adminMiddlewares],

            /**
             * Public
             */
            ['GET', '/', 'Backend\Controllers\Public\HomeController@index'],
            ['GET', '/redirect/{url}', 'Backend\Controllers\Public\PublicController@redirect'],
            ['GET', '/login', 'Backend\Controllers\Public\LoginController@index'],
            ['GET', '/register', 'Backend\Controllers\Public\RegisterController@index'],
            ['GET', '/admin/login', 'Backend\Controllers\Public\AdminLoginController@index'],
            ['GET', '/ajax/pages/public/Login', 'Backend\Controllers\Public\LoginController@element'],
            ['GET', '/image/base64', 'Backend\Controllers\Public\PublicController@render_image_base64'],
            ['GET', '/rocketmember-test', 'Backend\Services\RocketMember\RocketMember@test'],
            ['POST', '/rocketmember-api', 'Backend\Services\RocketMember\RocketMember@api'],
            ['GET', '/conversion-api', 'Backend\Controllers\Public\HomeController@conversion_api'],
            ['GET', '/ajax/auth/customer', 'Backend\Controllers\Public\PublicController@auth_customer'],
            ['GET', '/terms', 'Backend\Controllers\Public\PublicController@terms'],

            // ajax
            ['POST', '/ajax/auth', 'Backend\Controllers\Public\LoginController@auth'],
            ['POST', '/ajax/auth-admin', 'Backend\Controllers\Public\AdminLoginController@auth'],
            ['POST', '/ajax/register', 'Backend\Controllers\Public\RegisterController@register'],

            // static
            ['GET', '/upsell.min.js', 'Backend\Controllers\Public\UpsellController@js'],



            /**
             * Api
             */
            ['POST', '/api/rocketpanel/checkout/update', 'Backend\Controllers\Api\RocketPanelController@checkout_update'],
            ['POST', '/api/rocketpanel/checkout/register', 'Backend\Controllers\Api\RocketPanelController@checkout_register'],
            ['POST', '/api/rocketpanel/register', 'Backend\Controllers\Api\RocketPanelController@register'],
            ['POST', '/api/rocketpanel/login', 'Backend\Controllers\Api\RocketPanelController@login'],
            ['GET', '/api/rocketpanel/auth', 'Backend\Controllers\Api\RocketPanelController@auth'],
            ['PATCH', '/api/rocketpanel/cancel', 'Backend\Controllers\Api\RocketPanelController@cancel'],
            ['PATCH', '/api/rocketpanel/activate', 'Backend\Controllers\Api\RocketPanelController@activate'],

            // Api para o app
            // ['GET', '/api/app/test', 'Backend\Controllers\Api\App\IndexController@test'],
            ['GET', '/api/app/test-onesignal', 'Backend\Controllers\Api\App\IndexController@test_onesignal'],
            ['POST', '/api/app/login', 'Backend\Controllers\Api\App\IndexController@login'],
            ['GET', '/api/app/user', 'Backend\Controllers\Api\App\IndexController@user'],
            ['PATCH', '/api/app/user', 'Backend\Controllers\Api\App\IndexController@update_user'],
            ['GET', '/api/app/user/products', 'Backend\Controllers\Api\App\IndexController@products'],
            ['GET', '/api/app/user/product/{id}', 'Backend\Controllers\Api\App\IndexController@product'],
            ['GET', '/api/app/user/orders', 'Backend\Controllers\Api\App\IndexController@orders'],
            ['GET', '/api/app/user/order/{id}', 'Backend\Controllers\Api\App\IndexController@order'],

            /**
             * Api RocketMember
             */
            ['GET', '/api/rocketmember/product/{id}', 'Backend\Controllers\Api\RocketMemberController@getProduct'],
            ['GET', '/api/rocketmember/customer/{id}', 'Backend\Controllers\Api\RocketMemberController@getCustomer'],
            ['GET', '/api/rocketmember/order/{id}', 'Backend\Controllers\Api\RocketMemberController@getOrder'],
            ['GET', '/api/rocketmember/user/{id}', 'Backend\Controllers\Api\RocketMemberController@getUser'],

            /**
             * Api de chargeback
             */
            ['POST', '/api/chargeback/master', 'Backend\Controllers\Api\ChargebackController@master'],
            ['POST', '/api/chargeback/visa', 'Backend\Controllers\Api\ChargebackController@visa'],

            /**
             * Cronjob
             */
            ['GET', '/api/cronjob/expire-user', 'Backend\Controllers\Api\RocketPanelController@cron_user_expired'],
            ['GET', '/api/cronjob/public-suffix-list/update', 'Backend\Controllers\Cronjobs\PublicSuffixListController@update'],
            ['GET', '/api/cronjob/upsell/token/delete', 'Backend\Controllers\Cronjobs\UpsellController@token_delete'],
            ['DELETE', '/api/cronjob/customer/password-reset-tokens/delete', 'Backend\Controllers\Cronjobs\Customer\ResetPasswordRequestController@wook'],
            ['GET', '/api/cronjob/abandoned-cart/alive', 'Backend\Controllers\Cronjobs\AbandonedController@alive'],
            ['GET', '/api/cronjob/currencies-rate/update', 'Backend\Controllers\Cronjobs\CurrencyController@update'],
            ['PATCH', '/api/cronjob/pay-seller', 'Backend\Controllers\Api\PaySellerController@main'],
            ['PATCH', '/api/cronjob/users/chargeback-percent/update', 'Backend\Controllers\Cronjobs\User\ChargebackPercentController@handle'],
            ['PATCH', '/api/cronjob/pay-seller/scheduled', 'Backend\Controllers\Queue\SellerCreditController@handle'],
            ['POST', '/api/cronjob/users/award/create-request', 'Backend\Controllers\Cronjobs\User\AwardController@handle'],


            /**
             * Queues
             */
            ['GET', '/api/cronjob/sendmail', 'Backend\Controllers\Queue\EmailController@handle'],
            ['GET', '/api/queue/stripe/webhook', 'Backend\Controllers\Queue\StripeWebhookController@handle'],
            ['GET', '/api/queue/utmify', 'Backend\Controllers\Queue\UtmifyAPIController@handle'],
            ['GET', '/api/queue/memberkit', 'Backend\Controllers\Queue\MemberkitController@handle'],
            ['GET', '/api/queue/astronmembers', 'Backend\Controllers\Queue\AstronmembersController@handle'],
            ['GET', '/api/queue/sellflux', 'Backend\Controllers\Queue\SellfluxController@handle'],
            ['GET', '/api/queue/iugu/charge', 'Backend\Controllers\Queue\IuguChargeController@handle'],

            /**
             * WebHooks
             */
            ['ANY', '/wook/pagarme', 'Backend\Controllers\Webhooks\PagarMe\PagarMeController@wook'],
            ['ANY', '/wook/getnet', 'Backend\Controllers\Webhooks\GetNet\GetNetController@wook'],
            ['ANY', '/wook/bancointer', 'Backend\Controllers\Webhooks\BancoInter\BancoInterController@pix'],
            ['ANY', '/wook/withdrawal/expiration', 'Backend\Controllers\Cronjobs\WithdrawalController@wook'],
            ['POST', '/api/stripe/webhook', 'Backend\Controllers\Webhooks\Stripe\StripeController@wook'],
            ['ANY', '/api/iugu/webhook', 'Backend\Controllers\Webhooks\Iugu\IuguController@wook'],
            ['ANY', '/wook/noxpay', 'Backend\Controllers\Webhooks\NoxPay\NoxController@wook'],

            /**
             * Snippets
             */
            ['GET', '/snippets/upsell.min.css', 'Backend\Controllers\Snippets\UpsellController@min_css'],
            ['GET', '/snippets/upsell.min.js', 'Backend\Controllers\Snippets\UpsellController@min_js'],
            ['GET', '/snippets/upsell', 'Backend\Controllers\Snippets\UpsellController@index'],
            ['POST', '/snippets/upsell/pay', 'Backend\Controllers\Snippets\UpsellController@pay'],
            ['GET', '/snippets/upsell/intent', 'Backend\Controllers\Snippets\UpsellController@intent'],
            ['GET', '/snippets/upsell/intent/id', 'Backend\Controllers\Snippets\UpsellController@get_intent_id'],
            ['POST', '/snippets/upsell/clear', 'Backend\Controllers\Snippets\UpsellController@clear'],

            /**
             * Not found
             */
            ['GET', '/ajax/pages/browser/dashboard/NotFound', 'Backend\Controllers\Browser\NotFoundController@element', $userMiddlewares],
            ['GET', '/ajax/pages/browser/form/NotFound', 'Backend\Controllers\Browser\NotFoundController@element'],
            ['GET', '/ajax/pages/browser/public/NotFound', 'Backend\Controllers\Browser\NotFoundController@element'],

        ],

    $subdomain_checkout =>
        [
            /**
             * Pages
             */
            ['GET', '/{sku}', 'Backend\Controllers\Subdomains\Checkout\IndexController@index', ['middlewares' => ['Backend\Middlewares\Subdomains\Checkout\RedirectMiddleware@boot']]],
            ['GET', '/{sku}/{variation}', 'Backend\Controllers\Subdomains\Checkout\IndexController@index', ['middlewares' => ['Backend\Middlewares\Subdomains\Checkout\RedirectMiddleware@boot']]],
            ['GET', '/checkout', 'Backend\Controllers\Subdomains\Checkout\CheckoutController@index'],
            ['GET', '/billet', 'Backend\Controllers\Subdomains\Checkout\BilletController@index'],
            ['GET', '/pix', 'Backend\Controllers\Subdomains\Checkout\PixController@index'],
            ['GET', '/pix/paid', 'Backend\Controllers\Subdomains\Checkout\PixPaidController@index'],
            ['GET', '/thanks', 'Backend\Controllers\Subdomains\Checkout\ThanksController@index'],
            ['GET', '/analysis', 'Backend\Controllers\Subdomains\Checkout\AnalysisController@index'],
            ['ANY', '/wook/ipag', 'Backend\Controllers\Webhooks\IPag\IPagController@wook'],
            ['GET', '/return_url', 'Backend\Controllers\Subdomains\Checkout\ReturnUrlController@index'],

            // ajax
            ['GET', '/ajax/pages/subdomains/checkout/Index', 'Backend\Controllers\Subdomains\Checkout\IndexController@element'],
            ['GET', '/ajax/pages/subdomains/checkout/Checkout', 'Backend\Controllers\Subdomains\Checkout\CheckoutController@element'],
            ['GET', '/ajax/pages/subdomains/checkout/Thanks', 'Backend\Controllers\Subdomains\Checkout\ThanksController@element'],
            ['GET', '/ajax/pages/subdomains/checkout/Billet', 'Backend\Controllers\Subdomains\Checkout\BilletController@element'],
            ['GET', '/ajax/pages/subdomains/checkout/Pix', 'Backend\Controllers\Subdomains\Checkout\PixController@element'],
            ['GET', '/ajax/pages/subdomains/checkout/PixPaid', 'Backend\Controllers\Subdomains\Checkout\PixPaidController@element'],
            ['POST', '/ajax/actions/subdomains/checkout/makePayment', 'Backend\Controllers\Subdomains\Checkout\MakePaymentController@main'],
            ['POST', '/ajax/actions/subdomains/checkout/pay', 'Backend\Controllers\Subdomains\Checkout\MakePaymentController@pay'],
            ['POST', '/ajax/actions/subdomains/checkout/payPix', 'Backend\Controllers\Subdomains\Checkout\MakePaymentController@payPix'],
            ['POST', '/ajax/actions/subdomains/checkout/updateOrder/{id}', 'Backend\Controllers\Subdomains\Checkout\IndexController@updateOrder'],
            ['POST', '/ajax/actions/subdomains/checkout/paymentIntent/{checkout_id}', 'Backend\Controllers\Subdomains\Checkout\IndexController@paymentIntent'],
            ['POST', '/ajax/actions/subdomains/checkout/abandonedCart', 'Backend\Controllers\Subdomains\Checkout\AbandonedCartController@main'],
            ['POST', '/ajax/actions/subdomains/checkout/abandonedCart/alive', 'Backend\Controllers\Subdomains\Checkout\AbandonedCartController@alive'],
            ['GET', '/ajax/actions/subdomains/checkout/watchPix', 'Backend\Controllers\Subdomains\Checkout\PixController@watch'],
            ['POST', '/ajax/actions/subdomains/checkout/applyCoupon', 'Backend\Controllers\Subdomains\Checkout\CouponController@main'],
            // ['POST', '/ajax/actions/subdomains/checkout/customer/auth/send-pin', 'Backend\Controllers\Subdomains\Checkout\CustomerController@send_pin'],
            ['GET', '/ajax/actions/subdomains/checkout/customer/credit-card/latest', 'Backend\Controllers\Subdomains\Checkout\CustomerController@latestCreditCard'],




            /**
             * Test
             */
            ['GET', '/test/site-url-base', 'Backend\Controllers\Test@site_url_base'],




            /**
             * Snippets
             */
            // ['POST', '/snippets/upsell/clear', 'Backend\Controllers\Snippets\UpsellController@clear'],
        ],

    $subdomain_purchase =>
        [
            /**
             * Display
             */

            // pages
            // ['GET', '/', 'Backend\Controllers\Subdomains\Purchase\Home\IndexController@index'],
            ['GET', '/', 'Backend\Controllers\Subdomains\Purchase\Dashboard\IndexController@index', $customerMiddlewares],
            ['GET', '/dashboard', 'Backend\Controllers\Subdomains\Purchase\Dashboard\IndexController@index', $customerMiddlewares],
            ['GET', '/subscriptions', 'Backend\Controllers\Subdomains\Purchase\Subscription\IndexController@index', $customerMiddlewares],
            ['GET', '/login', 'Backend\Controllers\Subdomains\Purchase\Login\IndexController@index'],
            ['GET', '/logout', 'Backend\Controllers\Subdomains\Purchase\Login\IndexController@logout'],
            ['GET', '/reset/password', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\IndexController@index'],
            ['GET', '/reset/password/new', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\CreateNewPassword\IndexController@index'],

            // actions
            ['GET', '/login/token/{hash}', 'Backend\Controllers\Subdomains\Purchase\Login\IndexController@token'],
            ['GET', '/reset/password/{token}', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\CreateNewPassword\IndexController@index'],


            /**
             * Ajax
             */

            // pages
            ['POST', '/ajax/actions/subdomains/purchase/auth', 'Backend\Controllers\Subdomains\Purchase\Login\IndexController@auth'],
            ['GET', '/ajax/pages/subdomains/purchase/dashboard/Index', 'Backend\Controllers\Subdomains\Purchase\Dashboard\IndexController@element', $customerMiddlewares],
            ['GET', '/ajax/pages/subdomains/purchase/subscriptions/Index', 'Backend\Controllers\Subdomains\Purchase\Subscription\IndexController@element', $customerMiddlewares],
            ['GET', '/ajax/pages/subdomains/purchase/login/Index', 'Backend\Controllers\Subdomains\Purchase\Login\IndexController@element'],
            ['GET', '/ajax/pages/subdomains/purchase/reset-password/Index', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\IndexController@element'],
            ['GET', '/ajax/pages/subdomains/purchase/reset-password/create-new-password/Index', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\CreateNewPassword\IndexController@element'],

            // actions
            ['POST', '/ajax/actions/subdomains/purchase/{id}/refund', 'Backend\Controllers\Subdomains\Purchase\Dashboard\IndexController@refund', $customerMiddlewares],
            ['PATCH', '/ajax/actions/subdomains/purchase/{id}/cancelRefund', 'Backend\Controllers\Subdomains\Purchase\Dashboard\IndexController@cancelRefund', $customerMiddlewares],
            ['PATCH', '/ajax/actions/subdomains/purchase/subscription/{id}/cancel', 'Backend\Controllers\Subdomains\Purchase\Subscription\IndexController@cancel', $customerMiddlewares],
            ['POST', '/ajax/actions/subdomains/purchase/reset-password', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\IndexController@submit'],
            ['PATCH', '/ajax/actions/subdomains/purchase/reset-password/save', 'Backend\Controllers\Subdomains\Purchase\ResetPassword\CreateNewPassword\IndexController@save'],
        ]
];
