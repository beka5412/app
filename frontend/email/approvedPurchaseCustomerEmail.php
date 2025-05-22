<?php
$autologin_url = env("PROTOCOL") . "://purchase." . env("HOST") . "/login/token/$customer->one_time_access_token";
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
  xmlns:o="urn:schemas-microsoft-com:office:office">

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
      -ms-interpolation-mode: bicubic;
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
                  <a href="#"><img style="height: 70px" src="https://app.migraz.com/images/logo-dark.png"
                      alt="logo"></a>
                </td>
              </tr>
            </tbody>
          </table>
          <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
            <tbody>
              <tr>
                <td style="padding: 30px 30px 20px">
                  <p style="margin-bottom: 10px; font-size: 18px;">Olá
                    <?php echo $customer->name; ?>, tudo certo?
                  </p>
                  <p style="margin-bottom: 10px;">Parabéns, sua compra foi aprovada! Seu vendedor já foi notificado e
                    caso precise entrará em contato com mais informações. Confira os detalhes do seu pedido:
                  </p>
                  <?php foreach ($products as $product): ?>
                    <span class="user-card" style="display:flex;margin-top:40px;margin-bottom:40px">
                      <span class="product-image">
                        <img src="https://rocketpays.app/<?php echo $product->image; ?>"
                          style="width: 48px; border-radius: 4px; margin-right: 1rem; max-width: 100vw;">
                      </span>
                      <span style="font-family:helvetica, sans-serif;font-size:16px">
                        <strong style="display:block">
                          <?php echo $product->name; ?>
                        </strong>
                        <span>R$
                          <?php echo currency($order?->total); ?>
                        </span>
                      </span>
                    </span>
                  <?php endforeach; ?>

                  <p style="margin-bottom:20px; display:block">Agora você pode acessar este produto e aproveitar todo o
                    conteúdo que acabou de adquirir.</p>
                  <table border="0" cellspacing="0" cellpadding="0" align="center" style="width:auto">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:0 0 24px 0">
                          <table border="0" cellspacing="0" cellpadding="0" style="width:auto">
                            <tbody>
                              <tr>
                                <td align="center" style="border-radius:4px;background-color:#5981E3">
                                  <a href="<?php echo $autologin_url; ?>"
                                    style="background-color:#6576ff;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">Acessar
                                    Minhas Compras</a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <!-- <table cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody>
                        <tr>
                            <td valign="top" bgcolor="#edfbff" style="padding:40px 20px 40px 20px;border:0px solid #d4e0e4">                                                    
                            <p style="margin-bottom: 10px;">Segue o acesso a sua área restrita da Rocketpays, atráves dela você poderá visualizar todas as suas compras realizadas atráves da RocketPays.</p>
                            <p style="margin-bottom: 10px;">Login: <?php echo $customer->email; ?></p>
                            <br>
                            <a href="<?php echo $autologin_url; ?>" style="background-color:#6576ff;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">
                              Acessar Minhas Compras
                            </a>
                            <p class="ie-text-fallback mt-2" style="color:#6d6f71;margin:undefined 0 24px 0;font-size:12px;text-align:left;line-height:1.44;display:block">Caso você não tenha uma senha de uma conta da RocketPays ou se tiver esquecido, é só gerar uma nova neste link: <a href="https://purchase.rocketpays.app/password/forgot"> https://purchase.rocketpays.app/password/forgot </a>
                            </p>
                            </td>
                        </tr>
                        <tr>
                            <td height="24"></td>
                        </tr>
                        </tbody>
                  </table> -->
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                      <tr>
                        <td align="center" height="24px" valign="top" width="100%"
                          background="https://s3.amazonaws.com/static.hotmart.com/emails-hotmart/img/note__border--bottom--yellow.png"
                          bgcolor="#fffbcc"
                          style="background:#fffbcc url(assets://images/note__border--top--yellow.png) bottom repeat-x;padding:0 0 0 0;height:27px;min-height:27px">
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:0 24px 0 24px;" bgcolor="#fffbcc">
                          <p>
                            <strong>Informações da compra</strong>
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong>Nome do produtor:</strong>
                            <?php echo $product->author; ?>
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong>Email:</strong>
                            <?php echo $product->support_email; ?>
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong>Garantia:</strong>
                            <?php echo $product->warranty_time; ?> Dias
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong>Código da transação:</strong>
                            <?php echo $order?->transaction_id ?? ''; ?>
                          </p>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" height="24px" valign="top" width="100%"
                          background="assets://images/note__border--bottom--yellow.png" bgcolor="#fffbcc"
                          style="background:#fffbcc url(assets://images/note__border--bottom--yellow.png) top repeat-x;padding:0 0 0 0;height:27px;min-height:27px">
                        </td>
                      </tr>
                      <tr>
                        <td height="24"></td>
                      </tr>
                    </tbody>
                  </table>
                  <p style="margin-bottom: 10px;">Atenciosamente,</p>
                  <p style="margin-bottom: 10px;">RocketPays</p>
                  <p style="margin-bottom: 15px;">Este é um e-mail automático e não deve ser respondido, caso precise de
                    ajuda, <a style="color: #6576ff; text-decoration:none;" href="mailto:info@yourwebsite.com">Clique
                      Aqui</a> para entrar contato com a Central de Atendimento RocketPays. </p>
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