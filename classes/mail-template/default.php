<!--HTML email, template by http://tedgoas.github.io/Cerberus/-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{subject}}</title> <!-- The title tag shows in email notifications, like Android 4.4. -->
    <!--[if mso]>

    <style>
        * {
            font-family: sans-serif !important;
        }
    </style>
    <![endif]-->

    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href=\'https://fonts.googleapis.com/css?family=Roboto:400,700\' rel=\'stylesheet\' type=\'text/css\'> -->
    <!--<![endif]-->
    <style>

        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        div[style*="margin: 16px 0"] {
            margin:0 !important;
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
        img {
            -ms-interpolation-mode:bicubic;
        }
        .mobile-link--footer a,
        a[x-apple-data-detectors] {
            color:inherit !important;
            text-decoration: underline !important;
        }
    </style>

    <!-- Progressive Enhancements -->
    <style>

        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

    </style>

</head>
<body width="100%" bgcolor="#ededed" style="margin: 0;">
<center style="width: 100%; background: #ededed;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
        {{subject}}
    </div>

    <div style="max-width: 600px; margin: auto;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
            <tr>
                <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;">
            <tr>
                <td style="padding: 20px 0; text-align: center">
                    <img src="{{image}}" alt="Logo" border="0" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px;width:100%;max-width:200px;">
                </td>
            </tr>
        </table>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;">
            <tr>
                <td bgcolor="#ffffff">
                    <table role="presentation" cellspacing="0" align="center" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 40px; mso-height-rule: exactly; line-height: 20px; color: #555555;text-align: center;">
                                Iemand heeft een formulier ingevuld op de website.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Email Body : BEGIN -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;">
                <tr>
                    <td bgcolor="#ffffff">
                        <table role="presentation" cellspacing="0" align="center" cellpadding="0" border="0" width="100%">
                            {{message}}
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="40" style="font-size: 0; line-height: 0;">
                        &nbsp;
                    </td>
                </tr>
            </table>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">
                <tr>
                    <td style="padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #888888;">
                        Deze e-mail is uitsluitend bestemd voor de geadresseerde(n). Het bericht kan vertrouwelijke informatie bevatten welke niet voor derden is bedoeld. Meld het ons als deze e-mail bij de verkeerde persoon is terechtgekomen.
                        <br>
                        IP Adres van verstuurder: {{userIP}}
                        <br><br>
                    </td>
                </tr>
            </table>
            </td>
            </tr>
            </table>
    </div>
</center>
</body>
</html>

