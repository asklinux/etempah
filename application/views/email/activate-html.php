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
Terima kasih kerana menyertai <?php echo $site_name; ?>. Berikut adalah maklumat akaun anda. Sila simpan maklumat berikut dengan selamat.
<br />
<br />
<?php if (strlen($username) > 0) { ?>ID Pengguna:  <strong><?php echo $username; ?> </strong><br /><?php } ?>
Alamat emel:  <strong><?php echo $email; ?> </strong><br />
<?php if (isset($password)) { ?>Kata laluan: <strong><?php echo $password; ?> </strong><br /><?php  } ?>
<br /><br />
Sila klik pautan dibawah untuk mengaktifkan akaun anda:<br />
<nobr><a href="<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
<br />
Anda haruslah mengaktifkan akaun dalam tempoh <?php echo $activation_period; ?> jam, jika tidak akaun anda akan dibatalkan dan anda perlu menghubungi pentadbir utama untuk mendaftar sekali lagi.<br />
<br />
<br />
Terima kasih!<br />
<?php echo $site_name; ?>
</td>
</tr>
</table>
</div>
</body>
</html>