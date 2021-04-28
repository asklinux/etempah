<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
// Errors
$lang['auth_incorrect_password'] = 'Maaf, kata laluan yang dimasukkan adalah salah.';
$lang['auth_incorrect_login'] = 'Maaf, ID pengguna yang dimasukkan adalah salah.';
$lang['auth_incorrect_email_or_username'] = 'Maaf, emel yang dimasukkan tidak wujud. Sila masukkan semula.';
$lang['auth_email_in_use'] = 'Maaf, emel yang dimasukkan telah digunakan. Sila masukkan emel yang lain.';
$lang['auth_username_in_use'] = 'Maaf, ID pengguna telah digunakan. Sila pilih ID pengguna lain.';
$lang['auth_current_email'] = 'Ini adalah emel yang sedang digunakan.';
$lang['auth_incorrect_captcha'] = 'Maaf, kod pengesahan yang dimasukkan adalah salah.';
$lang['auth_captcha_expired'] = 'Maaf, kod pengesahan anda telah tamat tempoh. Sila cuba sekali lagi.';

// Notifications
$lang['auth_message_logged_out'] = 'Anda telah keluar dari sistem.';
$lang['auth_message_registration_disabled'] = 'Pendaftaran ditutup. Sila hubungi admin untuk pertanyaan lanjut.';
$lang['auth_message_registration_completed_1'] = 'Anda telah berjaya mendaftar. Sila periksa emel untuk aktifkan akaun.';
$lang['auth_message_registration_completed_2'] = 'Anda telah berjaya mendaftar.';
$lang['auth_message_activation_email_sent'] = 'Emel pengaktifan akaun telah dihantar ke %s. Sila ikuti arahan di dalam emel tersebut untuk aktifkan akaun.';
$lang['auth_message_activation_completed'] = 'Akaun anda telah berjaya diaktifkan. Sila gunakan ID Pengguna dan Kata Laluan yang telah diberi.';
$lang['auth_message_activation_failed'] = 'Maaf, kod pengaktifan yang anda masukkan tidak sah atau telah tamat tempoh.';
$lang['auth_message_password_changed'] = 'Kata laluan anda telah berjaya disimpan.';
$lang['auth_message_new_password_sent'] = 'Emel penukaran kata laluan telah dihantar kepada anda.';
$lang['auth_message_new_password_activated'] = 'Anda telah berjaya menetapkan semula kata laluan.';
$lang['auth_message_new_password_failed'] = 'Maaf, kod pengaktifan anda tidak sah atau telah tamat tempoh. Sila periksa emel anda dan ikuti arahannya.';
$lang['auth_message_new_email_sent'] = 'Emel pengesahan telah dihantar ke %s. Sila ikuti arahan di dalam emel tersebut untuk penukaran emel baru.';
$lang['auth_message_new_email_activated'] = 'Anda telah berjaya menukar emel.';
$lang['auth_message_new_email_failed'] = 'Kod pengaktifan anda tidak sah atau telah tamat tempoh. Sila periksa emel anda dan ikuti arahannya.';
$lang['auth_message_banned'] = 'Maaf, anda tidak lagi dibenarkan mengakses sistem. Sila hubungi admin untuk pertanyaan lanjut.';
$lang['auth_message_unregistered'] = 'Akaun anda telah dihapuskan.';
$lang['auth_message_bulk_registration_completed'] = 'Akaun MyBooking anda telah berjaya diwujudkan. Sila periksa emel untuk maklumat akaun.';

// Email subjects
$lang['auth_subject_welcome'] = 'Selamat datang ke %s!';
$lang['auth_subject_activate'] = 'Selamat datang ke %s!';
$lang['auth_subject_forgot_password'] = 'Terlupa kata laluan?';
$lang['auth_subject_reset_password'] = 'Kata laluan baru anda di %s';
$lang['auth_subject_change_email'] = 'Emel baru anda di %s';
$lang['auth_subject_newbooking'] = '%s - Tempahan baru ' . date('d/m/Y H:i:s a') . " ";
$lang['auth_subject_approvebooking'] = '%s - Status Tempahan ' . date('d/m/Y H:i:s a') . " ";
$lang['auth_subject_rejectbooking'] = '%s - Status Tempahan ' . date('d/m/Y H:i:s a') . " ";
$lang['auth_subject_cancelbooking'] = '%s - Status Tempahan ' . date('d/m/Y H:i:s a') . " ";


/* End of file tank_auth_lang.php */
/* Location: ./application/language/malay/tank_auth_lang.php */
?>