<?php

require_once('class.phpmailer.php');

switch ($_POST['fn']) {
    case 'permissioncheck':
	permissioncheck();
	break;
    case 'smtpcheck':
	smtpcheck();
	break;
    case 'mysqlcheck':
	mysqlcheck();
	break;
    default:
	echo json_encode(array('result' => false, 'error' => 'No function of that kind existed.'));
	break;
}

function permissioncheck() {
    $check['checkPhp'] = (version_compare(PHP_VERSION, '5.1.6') >= 0) ? 1 : 0;
    $check['checkMysql'] = (strnatcmp(mysqli_get_client_version(), '40100') >= 0) ? 1 : 0;
    $check['checkAssets'] = (is_writable(dirname(dirname(__FILE__)) . '/assets')) ? 1 : 0;
    $check['checkInstallation'] = (is_writable(dirname(dirname(__FILE__)) . '/installation')) ? 1 : 0;
    $check['checkConfig'] = (is_writable(dirname(dirname(__FILE__)) . '/application/config/config.php')) ? 1 : 0;
    $check['checkSetting'] = (is_writable(dirname(dirname(__FILE__)) . '/application/config/setting.php')) ? 1 : 0;
    $check['checkImg'] = (is_writable(dirname(dirname(__FILE__)) . '/assets/img/')) ? 1 : 0;

    echo json_encode($check);
    exit;
}

function smtpcheck() {

//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
    $smtp_host = ($_POST['smtp_host']) ? $_POST['smtp_host'] : '';
    $smtp_user = ($_POST['smtp_user']) ? $_POST['smtp_user'] : '';
    $smtp_pass = ($_POST['smtp_pass']) ? $_POST['smtp_pass'] : '';
    $smtp_port = ($_POST['smtp_port']) ? $_POST['smtp_port'] : '';

    if (empty($smtp_host) || empty($smtp_port) || (empty($smtp_user) && !empty($smtp_pass))):
	echo json_encode(array('result' => false, 'error' => 'empty'));
	exit;
    endif;

    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSMTP(); // telling the class to use SMTP
//    $body = file_get_contents('contents.html');
//    $body = eregi_replace("[\]", '', $body);
//    $mail->Host = $smtp_host; // SMTP server
    $mail->SMTPDebug = 0;       // enables SMTP debug information (for testing)
    // 1 = errors and messages
    // 2 = messages only
    $mail->Host = $smtp_host; // sets the SMTP server
    $mail->From = 'tankinging@oscc.org.my';
    $mail->FromName = 'MyBooking OSCC MAMPU';
    $mail->AddAddress("smtpmybooking@gmail.com");
//    $mail->AddAddress("sheikh@oscc.org.my");

    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = (!empty($smtp_user) && !empty($smtp_pass)) ? true : false;    // enable SMTP authentication
    $mail->Username = (empty($smtp_user)) ? '' : $smtp_user; // SMTP account username
    $mail->Password = (empty($smtp_pass)) ? '' : $smtp_pass; // SMTP account password
    $mail->Port = $smtp_port;      // set the SMTP port for the GMAIL server

    $mail->Subject = "Test email";
    $mail->Body = "Server Address:" . $_SERVER['SERVER_ADDR'] . ", ServerName: " . $_SERVER['SERVER_NAME'];
    $mail->WordWrap = 50;



    if ($mail->Send()) {
	echo json_encode(array('result' => true));
	exit;
    } else {
	$error = $mail->ErrorInfo;
	echo json_encode(array('result' => false, 'error' => $error));
	exit;
    }



//    try {
////	$mail->Host = "mail.yourdomain.com"; // SMTP server
//	$mail->SMTPDebug = 2;		     // enables SMTP debug information (for testing)
//	$mail->SMTPAuth = (!empty($smtp_user) && !empty($smtp_pass)) ? true : false;		  // enable SMTP authentication
//	$mail->Host = $smtp_host; // sets the SMTP server
//	$mail->Port = $smtp_port;		    // set the SMTP port for the GMAIL server
//	$mail->Username = "yourname@yourdomain"; // SMTP account username
//	$mail->Password = "yourpassword";	// SMTP account password
//	$mail->AddReplyTo('name@yourdomain.com', 'First Last');
//	$mail->AddAddress('whoto@otherdomain.com', 'John Doe');
//	$mail->SetFrom('name@yourdomain.com', 'First Last');
////	$mail->AddReplyTo('name@yourdomain.com', 'First Last');
//	$mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
//	$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
//	$mail->MsgHTML(file_get_contents('contents.html'));
////	$mail->AddAttachment('images/phpmailer.gif');      // attachment
////	$mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
//	$mail->Send();
//	echo "Message Sent OK<p></p>\n";
//    } catch (phpmailerException $e) {
//	echo $e->errorMessage(); //Pretty error messages from PHPMailer
//    } catch (Exception $e) {
//	echo $e->getMessage(); //Boring error messages from anything else!
//    }
//    try {
//	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
//	$mail->IsSMTP(); // telling the class to use SMTP
////    $body = file_get_contents('contents.html');
////    $body = eregi_replace("[\]", '', $body);
////    $mail->Host = $smtp_host; // SMTP server
//	$mail->SMTPDebug = 2;       // enables SMTP debug information (for testing)
//	// 1 = errors and messages
//	// 2 = messages only
//	$mail->Host = $smtp_host; // sets the SMTP server
//	$mail->From = 'tankinging@oscc.org.my';
//	$mail->FromName = 'MyBooking OSCC MAMPU';
//	$mail->AddAddress("smtpmybooking@gmail.com");
////    $mail->AddAddress("sheikh@oscc.org.my");
//
//	$mail->SMTPSecure = 'ssl';
//	$mail->SMTPAuth = (!empty($smtp_user) && !empty($smtp_pass)) ? true : false;    // enable SMTP authentication
//	$mail->Username = (empty($smtp_user)) ? '' : $smtp_user; // SMTP account username
//	$mail->Password = (empty($smtp_pass)) ? '' : $smtp_pass; // SMTP account password
//	$mail->Port = $smtp_port;      // set the SMTP port for the GMAIL server
//
//	$mail->Subject = "Test email";
//	$mail->Body = "Server Address:" . $_SERVER['SERVER_ADDR'] . ", ServerName: " . $_SERVER['SERVER_NAME'] . "Boleh lah sheikh, siapa ckp tak boleh";
//	$mail->WordWrap = 50;
//	
//	echo json_encode(array('result' => true));exit;
//    } catch (phpmailerException $e) {
//	echo json_encode(array('result' => false, 'error' => $e->errorMessage()));exit;
//    } catch (Exception $e) {
//	echo json_encode(array('result' => false, 'error' => $e->getMessage() ));exit;
//    }
}

function mysqlcheck() {

    $database = $_POST['database'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hostname = $_POST['hostname'];

    $resource = mysql_connect($hostname, $username, $password);
    if ($resource) {
	if (mysql_select_db($database, $resource)) {
	    echo 'Success';
	    exit;
	} else {
	    $sql = 'CREATE DATABASE ' . $database;

	    if (mysql_query($sql, $resource)):
		echo 'Success';
		exit;
	    else:
		echo 'Database creation failed';
		exit;
	    endif;
	}
    }
    else {
	echo 'Connection Failed';
	exit;
    }
}

?>