TITLE=<?= lang($locale, 'Purchase approved') . "\n" ?>
SUBJECT=<?= '[platform] - '.lang($locale, 'you bought').' [product_name]' ?>

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
              font-family: "Roboto", sans-serif !important;
            }
        </style>
    <![endif]-->

  <!--[if !mso]>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <![endif]-->

  <!-- Web Font / @font-face : END -->

  <!-- CSS Reset : BEGIN -->


  <style>
    html,
    body {
      margin: 0 auto !important;
      padding: 0 !important;
      height: 100% !important;
      width: 100% !important;
      font-family: "Roboto", sans-serif !important;
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
                  <a href="#"><img style="height: 70px" src="https://app.migraz.com/images/logo-dark.png" alt="logo"></a>
                </td>
              </tr>
            </tbody>
          </table>
          <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
            <tbody>
              <tr>
                <td style="padding: 30px 30px 20px">
                  <p style="margin-bottom: 10px; font-size: 18px;">
                  <?= lang($locale, 'Hello') ?> [username], <?= lang($locale, 'how are you?') ?>
                  </p>
                  <p style="margin-bottom: 10px;">
                    <?= lang($locale, 'Congratulations, your purchase has been approved! Your seller has already been notified and will contact you with more information if necessary. Check your order details:') ?>
                  </p>
                  <span class="user-card" style="display:flex;margin-top:40px;margin-bottom:40px">
                    <span class="product-image">
                      <img src="[image]" style="width: 48px; border-radius: 4px; margin-right: 1rem; max-width: 100vw;">
                    </span>
                    <span style="font-family:helvetica, sans-serif;font-size:16px">
                      <strong style="display:block">
                        [product_name]
                      </strong>
                      <span>[symbol] [total]
                      </span>
                    </span>
                  </span>

                  <p style="margin-bottom:20px; display:block"><?= lang($locale, 'You can now access this product and enjoy all the content you just purchased.') ?></p>
                  <table border="0" cellspacing="0" cellpadding="0" align="center" style="width:auto">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:0 0 24px 0">
                          <table border="0" cellspacing="0" cellpadding="0" style="width:auto">
                            <tbody>
                              <tr>
                                <td>
                                  Email: <b>[email]</b><br />
                                  <?= lang($locale, 'Password') ?>: <b>[password]</b><br /><br />
                                </td>
                              </tr>
                              <tr>
                                <td align="center" style="border-radius:4px;background-color:#5981E3">
                                  <a href="[login_url]" style="background-color:#6576ff;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">
                                    <?= lang($locale, 'Access my purchases') ?>
                                  </a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                      <tr>
                        <td align="center" height="24px" valign="top" width="100%" background="https://s3.amazonaws.com/static.hotmart.com/emails-hotmart/img/note__border--bottom--yellow.png" bgcolor="#fffbcc" style="background:#fffbcc url(assets://images/note__border--top--yellow.png) bottom repeat-x;padding:0 0 0 0;height:27px;min-height:27px">
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:0 24px 0 24px;" bgcolor="#fffbcc">
                          <p>
                            <strong><?= lang($locale, 'Purchase information') ?></strong>
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong><?= lang($locale, 'Producer name') ?>:</strong>
                            [product_author]
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong>Email:</strong>
                            [product_support_email]
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong><?= lang($locale, 'Days') ?>:</strong>
                            [product_warranty] Dias
                          </p>
                          <p style="color:#5d5d4b;font-size:14px;line-height:undefined;margin:0 0 12px 0">
                            <strong><?= lang($locale, 'Transaction code') ?>:</strong>
                            [transaction_id]
                          </p>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" height="24px" valign="top" width="100%" background="assets://images/note__border--bottom--yellow.png" bgcolor="#fffbcc" style="background:#fffbcc url(assets://images/note__border--bottom--yellow.png) top repeat-x;padding:0 0 0 0;height:27px;min-height:27px">
                        </td>
                      </tr>
                      <tr>
                        <td height="24"></td>
                      </tr>
                    </tbody>
                  </table>
                  <p style="margin-bottom: 10px;"><?= lang($locale, 'Regards') ?>,</p>
                  <p style="margin-bottom: 10px;"><?= env('APP_NAME') ?></p>
                  <p style="margin-bottom: 15px;">
                    <?= lang($locale, 'This is an automated email and should not be responded to.') ?>
                    <?= lang($locale, 'Support channel') ?> <?= env('SMTP_FROM') ?>.
                  </p>
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