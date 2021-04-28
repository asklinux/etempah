<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Tetapan katalaluan baru di <?php echo $site_name; ?></title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Tetapan katalaluan baru</h2>
Anda terlupa katalaluan? Tiada masalah.<br />
Untuk penetapan katalaluan yang baru, sila klik pada pautan di bawah:<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>" style="color: #3366cc;">Tetapkan katalaluan baru</a></b></big><br />
<br />
Pautan tidak berfungsi? Salin pautan berikut ke pelayar web anda:<br />
<nobr><a href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?></a></nobr><br />
<br />
<br />
Anda menerima emel ini melalui permohonan di <a href="<?php echo site_url(''); ?>" style="color: #3366cc;"><?php echo $site_name; ?></a>. 
Ini adalah sebahagian daripada prosedur untuk menetapkan katalaluan baru anda di dalam sistem. Jika BUKAN ANDA yang telah memohon untuk menukar katalaluan ini, sila abaikan emel ini dan gunakan katalaluan anda seperti biasa.
<br />
<br />
<br />
Terima kasih,<br />
The <?php echo $site_name; ?> Team
</td>
</tr>
</table>
</div>
</body>
</html>