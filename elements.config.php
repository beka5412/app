<?php

$subdomain_index = env('SUBDOMAIN_INDEX');
$index = $subdomain_index ? $subdomain_index : ".";
$subdomain_checkout = translate_subdomain_name('checkout');
$subdomain_purchase = translate_subdomain_name('purchase');

return
    [
        $index =>
        [
            'dashboard' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/{key}/_footerView.php'],
                '<Sidebar />' => ['path' => 'frontend/view/layouts/{key}/_sidebarView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/{key}/_eAppView.php'],
                '<Pagination />' => ['path' => 'frontend/view/layouts/{key}/_paginationView.php'],
                '<ProductMenu />' => ['path' => 'frontend/view/user/products/layouts/_menuView.php'],
                '<ProductCheckoutMenu />' => ['path' => 'frontend/view/user/products/checkouts/layouts/_menuView.php'],
                '<ProductCheckoutTestimonials />' => ['path' => 'frontend/view/user/products/checkouts/components/Testimonials/index.php'],
                '<AffiliationMenu />' => ['path' => 'frontend/view/user/products/affiliation/products/layouts/_menuView.php'],
                '<Loading>' => ['path' => 'frontend/view/layouts/dashboard/_loadingView.php'],
                '<React.Loading />' => ['path' => 'frontend/view/layouts/dashboard/_loadingView.react.php'],
            ],

            'form' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/{key}/_footerView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/{key}/_eAppView.php'],
            ],

            'public' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/{key}/_footerView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/{key}/_eAppView.php'],
            ]
        ],

        $subdomain_checkout =>
        [
            'dashboard' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_footerView.php'],
                '<Sidebar />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_sidebarView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_eAppView.php'],
            ],

            'form' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_footerView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_eAppView.php'],
            ],

            'public' =>
            [
                '<Header />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_headerView.php'],
                '<Footer />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_footerView.php'],
                '<App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_sAppView.php'],
                '</App>' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_eAppView.php'],
            ]
        ],

        $subdomain_purchase =>
        [
            'dashboard' =>
            [
                // '<Sidebar />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_sidebarView.php'],
                // '<Pagination />' => ['path' => 'frontend/view/layouts/subdomains/{subdomain}/{key}/_paginationView.php'],
                '<Sidebar />' => ['path' => 'frontend/view/layouts/subdomains/purchase/{key}/_sidebarView.php'],
                '<Pagination />' => ['path' => 'frontend/view/layouts/subdomains/purchase/{key}/_paginationView.php'],
            ]
        ]
    ];
