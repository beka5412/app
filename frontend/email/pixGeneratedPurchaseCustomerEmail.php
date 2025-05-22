<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    
    <style>
      
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
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">
	<center style="width: 100%; background-color: #f5f6fa;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
               <td style="padding:40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <a href="https://rocketpays.app/"><img style="height: 70px" src="https://app.migraz.com/images/logo-dark.png" alt="logo"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="padding: 0px 30px 15px 30px;">
                                    <h2 style="font-size: 18px; color: #6576ff; font-weight: 600; margin: 0;">Olá <?php echo $customer->name; ?>, tudo certo?</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0px 30px 15px 30px;">
                                <?php foreach ($products as $product): ?>
                                    <p style="margin-bottom: 10px; font-size: 18px;"></p>
                                    <p>Garanta seu acesso ao produto<?php echo $product->name; ?>! Lembre-se de pagar o Pix antes que ele expire.</p>
                                    <p>É só copiar e colar o código abaixo no aplicativo do seu banco. Se preferir, você também pode pagar com o QR code usando a câmera do seu celular.</p>
                                    <?php endforeach; ?>
                                    </td>

                            </tr>
                            <tr>
                                <td style="padding: 0px 30px 15px 30px;">
                                        <div class="example-alert  mt-2">
                                            <div style="font-size: 12px; color: #06889b; background-color: #e1f8fb; border-color: #b5edf5; position: relative; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 4px; padding: 1rem 3.25rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-1-circle" viewBox="0 0 16 16">
                                                <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8Zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383h1.312Z"/>
                                                </svg>
                                            Abra o aplicativo do seu banco e acesse a área Pix
                                            </div>
                                            <div style="font-size: 12px;color: #06889b;background-color: #e1f8fb;border-color: #b5edf5;position: relative;margin-bottom: 1rem;border: 1px solid transparent;border-radius: 4px;padding: 1rem 3.25rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-2-circle" viewBox="0 0 16 16">
                                                <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8Zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM6.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306Z"/>
                                                </svg>
                                            Selecione a opção pagar com código Pix Copia e Cola e cole o código no espaço indicado no aplicativo
                                            </div>
                                            <div style="font-size: 12px;color: #06889b;background-color: #e1f8fb;border-color: #b5edf5;position: relative;margin-bottom: 1rem;border: 1px solid transparent;border-radius: 4px;padding: 1rem 3.25rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-3-circle" viewBox="0 0 16 16">
                                                    <path d="M7.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318Z"/>
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8Z"/>
                                                    </svg>                                           
                                                     Após o pagamento, você receberá por email as informações de acesso à sua compra
                                            </div>
                                        </div>                                    
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0px 30px 15px 30px;">
                                   <div style="padding: 20px;background: #f0faf5; font-weight: 500; border-radius: 4px;">
                                    <p style="margin-bottom: 5px;">Código Copia e Cola Pix:</p>
                                    <p style="margin-bottom: 10px;"><?php echo !empty($order) ? $order?->meta('payment_pix_code') ?? '' : ''; ?></p>
                                    <p style="margin-bottom: 5px;">Escaneie o QrCode:</p>
                                    <p style="margin-bottom: 25px;">
                                        <img src="<?php $image_base64 = !empty($order) ? $order?->meta('payment_pix_image') ?? '' : ''; echo site_url() . "/image/base64?data=".urlencode($image_base64); ?>" style="width: 258px; height: 258px">
                                    </p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0px 30px 15px 30px;">
                                    <div style="padding: 20px; background: #d0e1ffa1; font-weight: 500; border-radius: 4px;">
                                        <span style="font-family:helvetica, sans-serif;font-size: 18px;border-bottom: solid 1px #ececec;display:block;margin-bottom: 6px;">Detalhes da compra</span>
                                            <?php foreach ($products as $product): ?>
                                                    <span class="user-card" style=" display: flex; margin-top: 20px;">
                                                        <span class="product-image">
                                                            <img src="https://rocketpays.app/<?php echo $product->image; ?>" style="width: 48px;border-radius: 4px;margin-right: 1rem;">
                                                        </span>
                                                        <span class="user-info">
                                                            <span style=" font-size:14px; font-weight: 700; color: #364a63; display: block !important; "><?php echo $product->name; ?> </span>
                                                            <span style="font-family:helvetica, sans-serif;font-size:14px;display: block;">R$ <?php echo number_format($product->price, 2, ',', '.'); ?></span>
                                                        </span>
                                                    </span>
                                            <?php endforeach; ?>
                                        <span style="font-family:helvetica, sans-serif;font-size:14px;margin-top: 10px;"><strong>Nome: </strong> <?php echo $product->author; ?></span>
                                        <span style="font-family:helvetica, sans-serif;font-size:14px;display: block;"><strong>E-mail: </strong><?php echo $product->support_email; ?></span>
                                        <span style="font-family:helvetica, sans-serif;font-size:14px;display: block;"><strong>Data do pedido: </strong><?php echo $order?->created_at ?? ''; ?></span>
                                        <span style="font-family:helvetica, sans-serif;font-size:14px;display: block;"><strong>Número do Pedido: </strong><?php echo $order?->id ?? ''; ?></span>
                                        <span style="font-family:helvetica, sans-serif;font-size:14px;display: block;"><strong>Garantia: </strong><?php echo $product->warranty_time; ?> Dias</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 40px">
                                    <p>Atenciosamente,</p>
                                    <p>RocketPays</p>
                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">Caso necessite de informações ou precise de ajuda no uso deste produto, por favor entre em contato com o vendedor responsável, <?php echo $product->author; ?>, através do email <?php echo $product->support_email; ?></p>
                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb; margin-top: 10px;">Este é um e-mail automático da RocketPays e não deve ser respondido, Somos A RocketPAys a plataforma de venda e divulgação de produtos digitais utilizada pelo vendedor</p>
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