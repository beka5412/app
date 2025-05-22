
<html>
<head>
    <title> <?php echo $title; ?> </title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">    
    <link rel="stylesheet" href="/dash/css/dashlite.css?v=<?php echo uniqid(); ?>">
    <link rel="stylesheet" href="/static/css/custom.css?v=<?php echo uniqid(); ?>">
    <?php echo $css; ?>
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
<script src="/dash/js/charts/gd-analytics.js?ver=<?php echo uniqid(); ?>"></script>
</body>
</html>