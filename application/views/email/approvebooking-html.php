<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Akaun Baru :: <?php echo $site_name; ?></title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%"></td>
                    <td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
                        <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Selamat datang ke <?php echo $site_name; ?>!</h2>

                        <p>Assalamualaikum Dan Salam Sejahtera</p>

                        <p><strong><span style='text-decoration: underline;'>PEMBERITAHUAN KELULUSAN TEMPAHAN</span></strong></p>

                        <p>Tempahan anda di dalam <?php echo $site_name; ?> telah diluluskan.</p>

                        <p>Untuk melihat maklumat tempahan tersebut, 
                            <a href="<?php echo site_url('/booking_rooms/view/'.$booking_id.'/'); ?>" style="color: #3366cc;">sila klik di sini</a>.
                        </p>

                        Terima kasih!<br />
                        <?php echo $site_name; ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
