<html>

<head>
    <title>
        <?php echo $title; ?>
    </title>
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/dash/css/dashlite.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="/dash/css/theme.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="/static/css/custom.css?v=<?php echo uniqid(); ?>">
    <?php echo $css; ?>
</head>

<body class="nk-body bg-dark npc-general has-sidebar is-dark">
    <div class="nk-app-root">
        <div class="nk-main">
            <Content />
        </div>
    </div>
    <script src="/dash/js/bundle.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="/dash/js/scripts.js?ver=<?php echo uniqid(); ?>"></script>
    <script src="/dash/js/charts/gd-default.js?ver=<?php echo uniqid(); ?>"></script>
</body>

</html>