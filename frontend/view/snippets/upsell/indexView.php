<?php 
$site_url = site_url(); 
$product_id = $_GET['id'] ?? '';
?>

<link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
<link href="<?php echo $site_url; ?>/snippets/upsell.min.css" rel="stylesheet">

<div class="upsell-wrapper">
    <button id="btnUpsell" class="button">
        Comprar
    </button>
    <div style="text-align: center">
        <div class="error-payment" id="elementErrorPayment"></div>
    </div>
</div>

<script src="<?php echo $site_url; ?>/snippets/upsell.min.js"></script>
