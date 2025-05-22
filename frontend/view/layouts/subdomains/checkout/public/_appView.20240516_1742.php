<html>

<head>
    <title>Checkout</title>

    <meta name="viewport" content="initial-scale=1, width=device-width">
    <meta property="og:image" content="https://checkout.rocketpays.app">
    <meta property="og:image:type" content="image/png">
    <meta property="og:title" content="Checkout" />
    <link rel="shortcut icon" type="image/x-icon" href="" id="favicon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/dash/css/dashlite.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="/dash/css/theme.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="/static/css/custom.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
    <!-- <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

    <?php echo $css; ?>
    
    <?php if (isset($pixels)) foreach ($pixels as $pixel):
    if ($pixel->platform == "facebook") 
    {
        $metatag = $pixel->metatag ?: '';
        $aux = explode(">", $metatag)[0];
        if (str_contains($metatag, "<meta")) echo "$aux>\n";
    }
    endforeach; ?>

    <script type="application/json" json-id="checkout_meta">
    {
        "pixels": <?php echo isset($pixels) ? json_encode($pixels ?? '[]') : '[]'; ?>
    }
    </script>

    <script>
        (function() {
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            
            let tagJSON = id => JSON.parse(document.querySelector(`[json-id="${id}"]`)?.textContent || '');

            let checkoutMeta = tagJSON('checkout_meta');
            checkoutMeta?.pixels?.forEach(item => {
                let pixel = item.content;
                // console.log(pixel);

                if (pixel > 0 && !isNaN(pixel)) {
                    fbq('init', String(pixel));
                    fbq('track', 'PageView');

                    <?php /* if (!empty($initial_sku) && route_is(["/$initial_sku", '/ajax/pages/subdomains/checkout/Index'])): ?>
                        fbq('track', 'InitiateCheckout');
                    <?php endif; */ ?>

                    <?php /* if (route_is(['/thanks', '/ajax/pages/subdomains/checkout/Thanks'])): 
                        // logica para executar apenas 1x esse envio ao facebook
                    ?>
                        fbq('track', 'Purchase', {
                            content_ids: ['partner_event_id'],
                            eventref: 'fb_oea', // or set to empty string
                            currency: 'BRL',  // your currency string value goes here
                            num_items: 1, // your number of tickets purchased value goes here
                            value: 10, // your total transaction value goes here
                        });
                    <?php endif; */ ?>

                    <?php /* if (route_is([
                        '/{sku}', '/ajax/pages/subdomains/checkout/Index',
                        '/thanks', '/ajax/pages/subdomains/checkout/Thanks',
                        '/billet', '/ajax/pages/subdomains/checkout/Billet',
                        '/pix', '/ajax/pages/subdomains/checkout/Pix',
                        '/pix/paid', '/ajax/pages/subdomains/checkout/PixPaid',
                    ])): ?>
                        fbq('track', 'InitiateCheckout');
                    <?php endif; 

                    // se estiver no checkout

                    // se estiver na pagina de obrigado
                    // fbq('track', 'Purchase', {
                    //     content_ids: ['partner_event_id'],
                    //     eventref: 'fb_oea', // or set to empty string
                    //     currency: 'BRL',  // your currency string value goes here
                    //     num_items: 1, // your number of tickets purchased value goes here
                    //     value: total, // your total transaction value goes here
                    // });
                    */ ?>
                    
                }
            });
        }());
    </script>
</head>

<body class="nk-body bg-lighter npc-general has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            <Content />
        </div>
    </div>
    <script src="/dash/js/bundle.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/dash/js/scripts.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="/dash/js/charts/gd-default.js?ver=<?php echo uniqid(); ?>"></script>
    
</body>

</html>