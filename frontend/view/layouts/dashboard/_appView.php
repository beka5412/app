<?php $user = user(); ?>
<html>

<head>
    <title> <?php echo $title; ?> </title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/dashlite.css?v=<?php echo uniqid(); ?>">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901,1,2&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="/static/css/custom.css?v=<?php echo uniqid(); ?>">
</head>

<body class="nk-body bg-lighter npc-general has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main bg-blur-main">
            <Sidebar />
            <div class="nk-wrap">
                <Header />
                <div class="nk-content ">


                  <?php if (!route_starts_with('/admin')) : ?>
                    <?php if (empty($user?->kyc)) : ?>
                    <div class="rp-notice alert alert-fill alert-warning alert-icon"><em class="icon ni ni-alert-circle"></em>Você ainda não verificou seus documentos. 
                    <a href="<?php echo site_url(); ?>/kyc">Clique aqui</a>
                    para completar o seu cadastro.</div>
                    <?php endif; ?>
                    
                    <?php if ($user?->kyc?->status == \Backend\Enums\Kyc\EKycStatus::PENDING->value) : ?>
                    <div class="rp-notice alert alert-fill alert-warning alert-icon"><em class="icon ni ni-alert-circle"></em>Seus documentos estão em verificação. Dentro de pouco tempo 
                    retornaremos com umas resposta.</div>
                    <?php endif; ?>

                    <?php if ($user?->kyc?->status == \Backend\Enums\Kyc\EKycStatus::REJECTED->value) : ?>
                    <div class="rp-notice alert alert-fill alert-danger alert-icon"><em class="icon ni ni-alert-circle"></em>Seus documentos não foram aceitos. 
                    <a href="<?php echo site_url(); ?>/kyc">Clique aqui</a>
                    para tentar outra vez.</div>
                    <?php endif; ?>
                <?php endif; ?> 

                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <Content />
                        </div>
                    </div>
                </div>

                <Footer />

            </div>
        </div>
    </div>

    <script src="/dash/js/bundle.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/dash/js/scripts.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="/dash/js/charts/gd-default.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="/dash/js/charts/gd-analytics.js?ver=<?php echo uniqid(); ?>"></script>

    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script type="text/javascript" src="https://unpkg.com/babel-standalone@6/babel.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js"
        integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.1.0/dist/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
</body>

</html>