<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <!-- Web Font / @font-face : BEGIN -->
    <!--[if mso]>
        <style>
            * {
                font-family: 'Roboto', sans-serif !important;
            }
        </style>
    <![endif]-->

    <!--[if !mso]>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset : BEGIN -->
    
    
    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            font-family: 'Roboto', sans-serif !important;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 24px;
            color: #8094ae;
            font-weight: 400;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }
        a {
            text-decoration: none;
        }
        img {
            -ms-interpolation-mode:bicubic;
        }
        .product-image {
            border-radius: 50%;
            height: 72px;
            width: 72px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            background: #798bff;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.06em;
            flex-shrink: 0;
            position: relative;
            margin-right: 20px;
        }
        .cart-resume {
            padding: 20px;
            background: #f0faf5;
            font-weight: 500;
            border-radius: 4px;
        }
        .user-info .lead-text, .user-info .sub-text {
            display: flex;
            align-items: center;
        }
        .lead-text {
            font-size: 0.875rem;
            font-weight: 700;
            color: #364a63;
            display: block;
        }
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">
	<center style="width: 100%; background-color: #f5f6fa;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <a href="#"><img style="height: 70px" src="https://app.migraz.com/images/logo-dark.png" alt="logo"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="padding: 30px 30px 15px 30px;">
                                    <b>Reembolsado</b>
                                    <h2 style="font-size: 18px; color: #6576ff; font-weight: 600; margin: 0;">Sua compra foi reembolsada com sucesso!</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 30px 20px">
                                    <p style="margin-bottom: 10px; font-size: 18px;">Olá <?php echo $customer->name; ?></p>
                                    <p>Confira os detalhes do seu pedido que foi reembolsado:
                                    <?php foreach ($products as $product): ?>
                                        <span class="user-card" style=" display: flex; margin-top: 20px;">
                                            <span class="product-image">
                                                <img src="https://rocketpays.app/<?php echo $product->image; ?>" style="width: 48px;border-radius: 4px;margin-right: 1rem;">
                                            </span>
                                            <span class="user-info">
                                                <span class="lead-text" style=" font-size: 0.875rem; font-weight: 700; color: #364a63; display: block !important; "><?php echo $product->name; ?> </span>
                                                <span class="lead-text product-price text-primary">R$ <?php echo number_format($product->price, 2, ',', '.'); ?></span>
                                            </span>
                                        </span>
                                        <?php endforeach; ?>
                                    </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 40px" style="padding: 20px; background: #f0faf5; font-weight: 500; border-radius: 4px;">
                                    <div class="cart-resume">
                                        <h5 class="card-title">Dados do Produtor</h5>
                                        <p>
                                            <strong>Nome: </strong> <?php echo $product->author; ?>
                                        </p>
                                        <p>
                                            <strong>E-mail: </strong> <?php echo $product->support_email; ?>
                                        </p>
                                        <p>
                                            <strong>Garantia: </strong> <?php echo $product->warranty_time; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 40px">
                                    <p>Atenciosamente,</p>
                                    <p>RocketPays</p>
                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">Este é um e-mail automático e não deve ser respondido, caso precise de ajuda, <a href="https://ajuda.rocketleads.com.br">clique aqui</a> para entrar contato com a Central de Atendimento RocketPays.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="font-size: 13px;">Copyright © 2023 RocketPays. All rights reserved. <br> By <a style="color: #6576ff; text-decoration:none;" href="https://rocketleads.com.br">RocketLeads</a>.</p>
                                    <ul style="margin: 10px -4px 0;padding: 0;">
                                        <li style="display: inline-block; list-style: none; padding: 4px;"><a style="display: inline-block; height: 30px; width:30px;border-radius: 50%; background-color: #ffffff" href="https://www.facebook.com/rocketleads.com.br"><img style="width: 30px" src="https://rocketpays.app/images/email/brand-b.png" alt="brand"></a></li>
                                        <li style="display: inline-block; list-style: none; padding: 4px;"><a style="display: inline-block; height: 30px; width:30px;border-radius: 50%; background-color: #ffffff" href="https://twitter.com/rocketleadsoficial"><img style="width: 30px" src="https://rocketpays.app/images/email/brand-e.png" alt="brand"></a></li>
                                        <li style="display: inline-block; list-style: none; padding: 4px;"><a style="display: inline-block; height: 30px; width:30px;border-radius: 50%; background-color: #ffffff" href="https://www.youtube.com/@rocketleadsoficial"><img style="width: 30px" src="https://rocketpays.app/images/email/brand-d.png" alt="brand"></a></li>
                                        <li style="display: inline-block; list-style: none; padding: 4px;"><a style="display: inline-block; height: 30px; width:30px;border-radius: 50%; background-color: #ffffff" href="#"><img style="width: 30px" src="https://rocketpays.app/images/email/brand-c.png" alt="brand"></a></li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
               </td>
            </tr>
        </table>
    </center>
</body>
</html>