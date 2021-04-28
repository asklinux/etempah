<?php
ini_set('display_errors', 'on');

define('INSTALLATION_BASEPATH', str_replace("\\", "/", 'system'));
require_once('../application/libraries/phpass-0.1/PasswordHash.php');
require_once('../application/config/email.php');
require_once('../application/config/database.php');
require_once('../application/config/setting.php');
require_once('../application/config/tank_auth.php');
require_once('../application/language/malay/tank_auth_lang.php');
require_once ('../application/config/config.php');

$pathSetting = dirname(dirname(__FILE__)) . '/application/config/setting.php';
$pathEmail = dirname(dirname(__FILE__)) . '/application/config/email.php';
$pathDatabase = dirname(dirname(__FILE__)) . '/application/config/database.php';

function hasher($config, $pass) {
    $hasher = new PasswordHash(
	    $config['phpass_hash_strength'], $config['phpass_hash_portable']);
    $hashed = $hasher->HashPassword($pass);
    return $hashed;
}

function fileContent($pathToFile) {
    $content = file_get_contents($pathToFile);
    return ($content) ? $content : '';
}

function writeDatabase($data, $link) {
    $qry = mysql_query($data);
    return ($qry) ? $qry : '';
}

function sendMail() {
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSMTP();
    $mail->SMTPDebug = 2;       // enables SMTP debug information (for testing)
    $mail->Host = $smtp_host; // sets the SMTP server
    $mail->From = 'smtpmybooking@gmail.com';
    $mail->FromName = 'MyBooking OSCC MAMPU';
    $mail->AddAddress("smtpmybooking@gmail.com");
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = (!empty($smtp_user) && !empty($smtp_pass)) ? true : false;    // enable SMTP authentication
    $mail->Username = (empty($smtp_user)) ? '' : $smtp_user; // SMTP account username
    $mail->Password = (empty($smtp_pass)) ? '' : $smtp_pass; // SMTP account password
    $mail->Port = $smtp_port;      // set the SMTP port for the GMAIL server
    $mail->Subject = $lang['auth_subject_welcome'];
    $mail->Body = $lang['auth_message_activation_completed'];
    $mail->WordWrap = 50;

    if (!$mail->Send()) {
	$error = $mail->ErrorInfo;
	echo json_encode(array('result' => false, 'error' => $error));
	exit;
    } else {
	echo json_encode(array('result' => true));
	exit;
    }
}

if ($_POST) {

    /*
     * Database credentials.
     */
    $hostname = $_POST['hostname']; // save to config/database.php
    $username = $_POST['username'];
    $password = $_POST['password'];
    $database = $_POST['database'];

    $mysqlCredentials = array($hostname, $username, $password, $database);

    /*
     * SMTP credentials.
     */
    $smtp_host = $_POST['smtp_host']; // save to smtp config/email.php
    $smtp_user = ($_POST['smtp_user']) ? $_POST['smtp_user'] : '';
    $smtp_pass = ($_POST['smtp_pass']) ? $_POST['smtp_pass'] : '';
    $smtp_port = $_POST['smtp_port'];

    /*
     * Creates link with the database.
     */
    $link = mysql_connect($hostname, $username, $password);
    mysql_select_db($database, $link);

    /*
     * Given the database credentials are correct, this code should work fine. This code will create all the tables required by mybooking.
     */
    $sqlQuery = fileContent(dirname(dirname(__FILE__)) . '/application/db/mybooking_db.sql');
    $sqlQueries = explode(';', $sqlQuery);

    foreach ($sqlQueries as $sqlQuery) {
	$sqlQuery = $sqlQuery . ';';
	$writeAdmin = writeDatabase($sqlQuery, $link);
    }

    /*
     * This code is to save new super admin of the system.
     */

    $adminHashedPassword = hasher($config, $_POST['admin_password']);

    $usernameAdmin = $_POST['admin_name'];
    $emailAdmin = $_POST['admin_email'];

    $queryAdmin = "INSERT INTO users (username, password, email, user_level, activated) VALUES ('$usernameAdmin', '$adminHashedPassword', '$emailAdmin','1', '1');";

    if (writeDatabase($queryAdmin, $link)):
	$id = mysql_insert_id($link);
	$nameAdmin = $_POST['admin_name'];
	$queryAdminProfile = "INSERT INTO user_profiles (fk_user_id, full_name) VALUES ('$id','$nameAdmin');";
	writeDatabase($queryAdminProfile, $link);
    else:
	die('Pemasangan tidak berjaya, pentadbir utama gagal disimpan.');
    endif;

    /*
     * And if the head driver was keyed-in, it will save that too.
     */
    if ($_POST['driver_email'] != '') {
	$driver_email = $_POST['driver_email'];
	$driver_name = ($_POST['head_driver']) ? $_POST['head_driver'] : '';

	$extract_username = explode('@', $_POST['driver_email']);
	$usernameDriver = $extract_username[0];

	$length = 8;
	$pass_range = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	$random_pass = substr(str_shuffle($pass_range), 0, $length);

	$driverHashedPassword = hasher($config, $random_pass); //set password for driver and email, maybe, no, wajib!!

	$queryDriver = "INSERT INTO users (username, password, email, user_level, activated) VALUES ('$usernameDriver', '$driverHashedPassword', '$driver_email', '3', '1');";
	if (writeDatabase($queryDriver, $link)):
	    $idDriver = mysql_insert_id($link);
	    $nameDriver = $_POST['head_driver'];
	    $queryDriverProfile = "INSERT INTO user_profiles (fk_user_id, full_name) VALUES ('$idDriver', '$nameDriver')";
	    writeDatabase($queryDriverProfile, $link);
	endif;
    }

    /*
     * This code below will save database credentials entered by user. It will be used everytime this webapp loads.
     * Note: Problem will occur if modification was done to the file since it uses normal search method instead of regular expression.
     */
    $configDatabase = fileContent($pathDatabase);
    foreach ($db['default'] as $key => $value) {
	if (in_array($key, array_keys($_POST))):
	    $old = "['" . $key . "'] = '" . $value . "'";
	    $new = "['" . $key . "'] = '" . $_POST[$key] . "'";
	    $configDatabase = str_replace("$old", "$new", $configDatabase);
	endif;
    }
    file_put_contents($pathDatabase, $configDatabase);

    /*
     * This code will save smtp credentials entered by user. It is important for sending emails.
     * Note: Problem will occur if modification was done to the file since it uses normal search method instead of regular expression.
     */
    $configEmail = fileContent($pathEmail);
    foreach ($config as $key => $value) {
	if (in_array($key, array_keys($_POST))):
	    $old = '["' . $key . '"] = "' . $value . '"';
	    $new = '["' . $key . '"] = "' . $_POST[$key] . '"';
	    $configEmail = str_replace("$old", "$new", $configEmail);
	endif;
    }
    file_put_contents($pathEmail, $configEmail);

    /*
     * This code will save agency's information entered by user.
     * Note: Problem will occur if modification was done to the file since it uses normal search method instead of regular expression.
     */
    $configSetting = fileContent($pathSetting);
    foreach ($config as $key => $value) :
	if (in_array($key, array_keys($_POST))):
	    $old = '["' . $key . '"]="' . $value . '"';
	    $new = '["' . $key . '"]="' . $_POST[$key] . '"';
	    $configSetting = str_replace("$old", "$new", $configSetting);
	endif;
    endforeach;
    file_put_contents($pathSetting, $configSetting);

    $pos_server = strpos($_SERVER['SCRIPT_NAME'], '/installation');

    if ($pos_server > 0) {
	$pos_server_name = substr($_SERVER['SCRIPT_NAME'], 0, $pos_server);
	header('Location:http://' . $_SERVER['SERVER_NAME'] . $pos_server_name . '/index.php');
	exit;
    } else {
	header('Location:http://' . $SERVER['SERVER_NAME'] . '/index.php');
	exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="-1">
        <title>Pemasangan MyBooking</title>
        <link type="text/css" rel="stylesheet" href="css/jquery.stepy.css" />
        <link type="text/css" rel="stylesheet" href="../assets/css/bootstrap.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.validate.js"></script>
        <script type="text/javascript" src="js/jquery.stepy.js"></script>
        <style type="text/css">
            body { background: url('img/background.gif'); font: normal 12px verdana; }
            div#content { align: center; background-color: #FFF; border: 1px solid #DEDEDE; box-shadow: 3px 3px 12px 3px rgba(100, 100, 100, 0.4); margin: 0 auto; padding: 15px; width: 1100px;  min-height: 510px;  border-radius: 4px;}
            legend { border: none; min-width: 50px; width:auto;}
            .fieldsmall {border-radius: 4px;}
            .btn { margin-bottom:5%;}
            #agency_address{ resize: none; }
            #license {  height :450px; border-radius: 4px; text-align: justify; overflow-y: scroll; padding: 20px; border: 1px solid #848484; background-color: #FAFAFA}
            fieldset.row-fluid { min-height: 450px;}
            label.alert.alert-error { clear: both; font: 10px verdana; margin: 3px 0 10px 0px;}
            div.well.box-shadow { padding: 0px 19px 10px 19px;}
            hr { margin : 0; }
            a:hover { color: #08c; text-decoration: none; }
            .topbanner { padding: 20px; }
            fieldset.fieldsmall.offset2 { padding: 20px 0 20px; }
            label.normal {  font: normal 10px verdana !important; }
            div.description { padding-top: 15px ; }
            fieldset.row-fluid { border: none; }
            fieldset.row-fluid legend { font: bold 20px verdana; }
            #agree { margin: 10px 0 3px 0px; }
            fieldset.fieldsmall.offset1.span10 { padding: 40px 0; } 
            div.custom-buttons {  border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 0px 30px 0px; margin: 10px 0; }
            #check-again { margin-top: 30px; margin-bottom: 0px;}
            #smtp_check{ margin-left: 7px;}
            .msg-check-error-smtp, .msg-check-success-smtp, .msg-check-success-permission{ margin: 0px 10% 0px; }
        </style>
        <script type="text/javascript">
	    $(document).ready(function() {
		$('input[name=db_check]').click(function() {
		    $.post("ajax.php", {fn: 'mysqlcheck', hostname: $('input[name=hostname]').val(), username: $('input[name=username]').val(), password: $('input[name=password]').val(), database: $('input[name=database]').val()},
		    function(data) {
			if (data == 'Success') {
			    $('input[name=db_success]').val('Success');
			    $('input[id=db_check]').removeClass('btn-danger btn-warning btn-info').addClass('btn-success').val('Berjaya!');
			}
			else if (data == 'Database not set') {
			    $('input[name=db_success]').val('Database not set');
			    $('input[id=db_check]').removeClass('btn-success btn-danger btn-info').addClass('btn-warning').val('Pangkalan data tiada!');
			}
			else if (data == 'Database creation failed') {
			    $('input[name=db_success]').val('Database creation failed');
			    $('input[id=db_check]').removeClass('btn-success btn-warning btn-info').addClass('btn-warning').val('Pangkalan data gagal dibina!');
			}
			else {
			    $('input[name=db_succes]').val('Connection Failed');
			    $('input[id=db_check]').removeClass('btn-success btn-warning btn-info').addClass('btn-danger').val('Sambungan ke pangkalan data tidak berjaya');
			}
		    });
		});
		$('input[id^=mysql]').keydown(function() {
		    $('input[id=db_check]').removeClass('btn-success btn-warning btn-danger').addClass('btn-info').val('Klik untuk periksa');
		});

	    });
        </script>
    </head>

    <body id="bodyid" >
        <div class="container">
            <div  class="topbanner">
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <img src="img/logo-jata-negara.png" width="55">
                    <img src="img/mybooking-logo.png" width="140"> Pemasangan
                </a>
                <span>(versi: 1.0)</span>
            </div>
        </div>
        <div id="content" class="container-fluid">
            <noscript>
            <style>
                #content form {display:none;}
            </style>
            <div class="noscript">
                <fieldset>
                    <legend id="noscript">Gagal</legend>
                    Pelayar Web Anda Tidak Mempunyai Sokongan Javascript. Sila Aktifkannya untuk Meneruskan Proses Pemasangan.
                </fieldset>
            </div>
            </noscript>
            <form id="custom" class="form-horizontal" action="index.php" method="POST">	
                <fieldset title="Langkah 1" class="row-fluid">
                    <legend>Perlesenan</legend>
                    <div class="error-container row-fluid">
                    </div>
                    <div class="row-fluid">
                        <div class="span10 offset1" id="license">
			    <?php echo file_get_contents('LICENSE.html', true); ?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <div class="span1">
                                <input type="checkbox" value="1" id="agree" name="agree"/>
                            </div>
                            <div class="span11" style="font-style: normal; margin:0;">
                                <label for="agree">Setuju dan Teruskan dengan Pemasangan</label>
				<?php // var_dump($this->config->item('setting'));          ?>
                            </div>			
                        </div>			
                    </div>
                </fieldset>
                <fieldset title="Langkah 2" class="row-fluid">
                    <legend>Pra-Pemasangan</legend>
                    <div class="error-container row-fluid">
                    </div>
                    <div class="row-fluid" style="min-height:450px">
                        <fieldset class="fieldsmall offset1 span10">
                            <div>
                                <div class="span10 offset1">
                                    <label>Berikut merupakan keperluan kepada sistem MyBooking :</label>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal">PHP Versi sama atau lebih dari 5.1.6 </label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (version_compare(PHP_VERSION, '5.1.6') >= 0) ? 'img/yes.png' : 'img/no.png'; ?>" name="checkPhp"/>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal">Sokongan MySQL</label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (strnatcmp(mysqli_get_client_version(), '40100') >= 0) ? 'img/yes.png' : 'img/no.png'; ?>" name='checkMysql'/>
                                </div>
                            </div>
                            <div>
                                <div class="span10 offset1">
                                    <label>Sistem ini juga memerlukan direktori-direktori dibawah boleh diubah:</label>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal"><?php echo (dirname(dirname(__FILE__)) . '/assets'); ?></label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo is_writable(dirname(dirname(__FILE__)) . '/assets') ? 'img/yes.png' : 'img/no.png'; ?>" name='checkAssets'/>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal"><?php echo (dirname(dirname(__FILE__)) . '/installation'); ?></label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/installation')) ? 'img/yes.png' : 'img/no.png'; ?>" name='checkInstallation'/>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal"><?php echo (dirname(dirname(__FILE__)) . '/application/config/config.php'); ?></label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/application/config/config.php')) ? 'img/yes.png' : 'img/no.png'; ?>" name='checkConfig'/>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal"><?php echo (dirname(dirname(__FILE__)) . '/application/config/setting.php'); ?></label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/application/config/setting.php')) ? 'img/yes.png' : 'img/no.png'; ?>" name='checkSetting'/>
                                </div>
                            </div>
                            <div>
                                <div class="span6 offset2">
                                    <label class="normal"><?php echo (dirname(dirname(__FILE__)) . '/assets/img/'); ?></label>
                                </div>
                                <div class="span3">
                                    <img src="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/assets/img/')) ? 'img/yes.png' : 'img/no.png'; ?>" name='checkImg'/>
                                </div>
                            </div>
                            <div class="control-group msg-check-success-permission">
                                <label class="control-label"></label><div id="ajax-placeholder-success-permission" class="alert margin-bottom0 alert-success"></div>
                            </div>
                            <div>
                                <div class='offset5'>
                                    <button type='button' id='check-again' class='btn btn-inverse'>Periksa Semula</button>
                                </div>
                            </div>
                            <input type="hidden" name="checkPhp" value="<?php echo (version_compare(PHP_VERSION, '5.1.6') >= 0) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkMysql" value="<?php echo (strnatcmp(mysqli_get_client_version(), '40100') >= 0) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkAssets" value="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/assets')) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkInstallation" value="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/installation')) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkConfig" value="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/application/config/config.php')) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkSetting" value="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/application/config/setting.php')) ? 'success' : ''; ?>"/>
                            <input type="hidden" name="checkImg" value="<?php echo (is_writable(dirname(dirname(__FILE__)) . '/assets/img/')) ? 'success' : ''; ?>"/>
                        </fieldset>
                    </div>
                </fieldset>
                <fieldset title="Langkah 3" class="row-fluid">	
                    <legend>Tetapan Sistem</legend>
                    <div class="error-container row-fluid">
                    </div>
                    <div class="row-fluid "> 
                        <div class="span10 offset1 box-shadow well">
                            <h4>Tetapan Pangkalan Data</h4>
                            <hr class="title-divider">
                            <div class="row-fluid">
                                <div class="span4 description">
                                    <p>Aplikasi MyBooking menyimpan segala datanya di dalam pangkalan data. Oleh kerana itu, proses pemasangan memerlukan maklumat berkenaan log masuk kedalam pengkalan data tersebut.</p>
                                    <p>Sekiranya anda memasang MyBooking pada pelayan web capaian jauh, anda perlu mendapatkan maklumat tersebut dari Hos anda.</p>
                                    <p>Berikut adalah penerangan mengenai tetapan ini: </p>
                                    <ul>
                                        <li><strong>Hos:</strong> Nama hos bagi pelayan ini.</li>
                                        <li><strong>Nama Pengguna MySQL:</strong> Nama pengguna bagi pangkalan data.</li>
                                        <li><strong>Kata Laluan MySQL:</strong> Kata laluan bagi pengguna pangkalan data.</li>
                                        <li><strong>Nama Pangkalan Data:</strong> Nama pangkalan data bagi MyBooking.</li>
                                    </ul>
                                    <p>Klik butang untuk memeriksa sambungan ke pangkalan data. Pastikan sambungan berjaya sebelum meneruskan proses pemasangan.</p>
                                </div> 
                                <div class="span8"> 
                                    <fieldset class="fieldsmall">
                                        <legend>Pangkalan Data</legend>
                                        <div class="row-fluid">
                                            <div class="control-group">
                                                <label class="control-label" for="hostname">Hos</label>
                                                <div class="controls">
                                                    <input type="text" id="mysqlhostname" name="hostname" value="localhost">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="username">Nama Pengguna MySQL</label>
                                                <div class="controls">
                                                    <input type="text" id="mysqlusername" name="username" placeholder="username">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="password">Kata Laluan MySQL</label>
                                                <div class="controls">
                                                    <input type="text" id="mysqlpassword" name="password"  placeholder="password">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="database">Nama Pangkalan Data</label>
                                                <div class="controls">
                                                    <input type="text" id="mysqldatabase" name="database" value="mybooking_db">
                                                </div>
                                            </div>
                                            <label id="success"></label>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <input type="button" class="btn btn-info" id="db_check" name="db_check" value="Klik untuk periksa"/>
                                                    <input type="hidden" name="db_success" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span10 offset1 box-shadow well"> 
                            <h4>Tetapan SMTP</h4>
                            <hr class="title-divider">
                            <div class="row-fluid"> 
                                <div class="span4 description">
                                    <p> Simple Mail Transfer Protocol atau lebih dikenali dengan SMTP adalah merupakan standard internet bagi transaksi emel melalui rangkaian IP. Standard ini digunapakai secara meluas didunia
                                        dan juga menjadi keperluan bagi sistem ini. </p>
                                    <p>Berikut merupakan penerangan bagi tetapan:</p>
                                    <ul>
                                        <li><strong>Perumah SMTP <i>(SMTP Host</i>):</strong> Hos bagi SMTP.</li>
                                        <li><strong>ID Pengguna SMTP <i>(Username</i>):</strong> ID pengguna bagi hos SMTP.</li>
                                        <li><strong>Kata Laluan SMTP <i>(Password)</i>:</strong> Kata laluan bagi hos SMTP.</li>
                                        <li><strong>Port SMTP <i>(Port)</i>:</strong> Port SMTP bagi host SMTP.</li>
                                    </ul>
                                </div> 
                                <div class="span8"> 
                                    <fieldset class="fieldsmall">
                                        <legend>SMTP</legend>
                                        <div class="control-group msg-check-success-smtp">
                                            <label class="control-label"></label><div id="ajax-placeholder-success-smtp" class="alert margin-bottom0 alert-success"></div>
                                            <div class="controls ">
                                            </div>
                                        </div>
                                        <div class="control-group msg-check-error-smtp">
                                            <label class="control-label"></label><div id="ajax-placeholder-error-smtp" class="alert margin-bottom0 alert-error"></div>
                                            <div class="controls">
                                            </div>
                                            <div>&nbsp;</div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="control-group">
                                                <label class="control-label" for="smtp_host">Perumah SMTP</label>
                                                <div class="controls">
                                                    <input type="text" name="smtp_host" id="smtp_host">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="smtp_user">ID Pengguna SMTP</label>
                                                <div class="controls">
                                                    <input type="text" name="smtp_user" id="smtp_user">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="smtp_pass">Kata Laluan SMTP</label>
                                                <div class="controls">
                                                    <input type="password" name="smtp_pass" id="smtp_pass">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="smtp_port">Port SMTP</label>
                                                <div class="controls">
                                                    <input type="text" name="smtp_port" id="smtp_port">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <button type='button' class='btn btn-info' name='smtp_check' id='smtp_check' data-loading-text="Pemuatan Data...">Klik untuk Periksa</button>
                                                    <input type='hidden' name='smtp_success' value='' />
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset title="Langkah 4" class="row-fluid">
                    <legend>Tetapan Utama</legend>
                    <div class="error-container row-fluid">
                    </div>
                    <div class="row-fluid">
                        <div class="span10 offset1 box-shadow well">
                            <h4>Maklumat Pentadbir Utama</h4>
                            <hr class="title-divider">
                            <div class="row-fluid">
                                <div class="span4 description">
                                    <p>Pentadbir utama merupakan pengguna yang mempunyai hak mutlak bagi sistem MyBooking. Tugas utamanya adalah untuk menyelenggara sistem dan juga bertindak sebagai 
                                        pentadbir bahagian dan/atau ketua pemandu sekiranya ketiadaan salah satu atau kedua-duanya. Hanya satu pentadbir akan dicipta dalam proses pemasangan ini. 
                                        Penambahan dan pengurangan dapat dilakukan pada tetapan utama.</p>
                                    <p>Berikut merupakan penerangan bagi maklumat ini:</p>
                                    <ul>
                                        <li><strong>Emel Pentadbir:</strong> Emel pentadbir utama.</li>
                                        <li><strong>ID Pentadbir:</strong> ID pentadbir utama (tidak boleh mempunyai jarak).</li>
                                        <li><strong>Kata Laluan:</strong> Kata laluan bagi akaun pentadbir utama.</li>
                                        <li><strong>Sahkan Kata Laluan:</strong> Masukkan sama seperti kata laluan untuk pengesahan.</li>
                                    </ul>
                                </div> 
                                <div class="span8"> 
                                    <fieldset class="fieldsmall">
                                        <legend>Pentadbir Utama</legend>
                                        <div class="control-group">
                                            <label class="control-label" for="admin_email">Emel Pentadbir*</label>
                                            <div class="controls">
                                                <input type="text" name="admin_email" id="admin_email">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="admin_name">Nama Pentadbir*</label>
                                            <div class="controls">
                                                <input type="text" name="admin_name" id="admin_name">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="admin_password">Kata Laluan*</label>
                                            <div class="controls">
                                                <input type="password" name="admin_password" id="admin_password">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="admin_retype_password">Sahkan Kata Laluan*</label>
                                            <div class="controls">
                                                <input type="password" name="admin_retype_password" id="admin_retype_password">
                                            </div>
                                        </div>
                                        <br/><br/>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/> 
                    <div class="row-fluid">
                        <div class="span10 offset1 box-shadow well">
                            <h4>Maklumat Ketua Pemandu</h4>
                            <hr class="title-divider">
                            <div class="row-fluid">
                                <div class="span4 description">
                                    <p>Aplikasi MyBooking turut menyediakan modul tempahan kenderaan selain daripada tempahan bilik, makanan dan peralatan. Sekiranya terdapat tempahan kenderaan, satu
                                        notifikasi berkaitan tempahan kenderaan akan dihantar kepada ketua pemandu bagi membolehkannya untuk menyediakan kenderaan yang bersesuaian. </p>
                                    <p>Berikut merupakan penerangan bagi maklumat ini.</p>
                                    <ul>
                                        <li><strong>Emel Ketua Pemandu:</strong> Emel ketua pemandu.</li>
                                        <li><strong>Nama Ketua Pemandu:</strong> Nama ketua pemandu.</li>
                                    </ul>
                                </div> 
                                <div class="span8"> 
                                    <fieldset class="fieldsmall">
                                        <legend>Ketua Pemandu</legend>
                                        <div class="control-group">
                                            <label class="control-label" for="driver_email">Emel Ketua Pemandu</label>
                                            <div class="controls">
                                                <input type="text" name="driver_email" id="driver_email">
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="head_driver">Nama Ketua Pemandu</label>
                                            <div class="controls">
                                                <input type="text" name="head_driver" id="head_driver">
                                            </div>
                                        </div>
                                        <br/><br/>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span10 offset1 box-shadow well"> 
                            <h4>Maklumat Agensi</h4>
                            <hr class="title-divider">
                            <div class="row-fluid">
                                <div class="span4 description">
                                    <p>MyBooking adalah satu sistem yang digunapakai oleh pelbagai agensi kerajaan. Oleh yang demikian, ianya akan menjadi satu perkara sukar untuk membezakannya antara agensi sekiranya antaramukanya sama.
                                        Dengan adanya maklumat agensi ini, pengguna tidak lagi keliru dengan antaramuka sama.</p>
                                    <p>Berikut merupakan penerangan bagi maklumat ini:</p>
                                    <ul>
                                        <li><strong>Nama Agensi:</strong> Nama agensi anda.</li>
                                        <li><strong>Alamat Agensi:</strong> Alamat agensi anda</li>
                                        <li><strong>Telefon:</strong> Nombor agensi untuk dihubungi.</li>
                                        <li><strong>Fax:</strong> Nombor fax agensi.</li>
                                    </ul>
                                </div> 
                                <div class="span8">
                                    <fieldset class="fieldsmall">
                                        <legend>Agensi</legend>
                                        <div class="control-group">
                                            <label class="control-label" for="agency_name">Nama Agensi*</label>
                                            <div class="controls">
                                                <input type="text" name="agency_name" id="agency_name">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="agency_address">Alamat Agensi</label>
                                            <div class="controls">
                                                <textarea name="agency_address" id="agency_address" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="agency_phone">Telefon</label>
                                            <div class="controls">
                                                <input type="text" name="agency_phone" id="agency_phone">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="agency_fax">Fax</label>
                                            <div class="controls">
                                                <input type="text" name="agency_fax" id="agency_fax">
                                            </div>
                                        </div>
                                        <br/><br/>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                </fieldset>
                <fieldset title="Langkah 5" class="row-fluid">
                    <legend>Pemasangan Berjaya</legend>
                    <div class="error-container row-fluid">
                    </div>
                    <div class="row-fluid">
                        <div  class="span10 offset1 box-shadow well">
                            <div  class="span10 offset1" style="min-height:450px; padding-top: 140px;">
                                <label>Tahniah! Proses pemasangan ini hampir selesai.</label><br/>
                                <p></p>
                                <p>Perlu diingatkan supaya menghapuskan direktori pemasangan. Sistem ini dikhuatiri tidak stabil sekiranya penghapusan direktori ini tidak dilakukan.</p>
                                <!--<input type="button" class="btn btn-primary" data-loading-text="Sedang Menghapus" value="Hapus Direktori Pemasangan"/>-->	
                            </div>
                        </div>
                    </div>
                </fieldset>
                <!--<input type="submit" class="finish btn btn-success" value="finish" />-->
                <button type="submit" class="finish btn btn-success" ><i class ='icon-ok-sign icon-white'></i> Tamat!</button>
            </form><br/>
        </div>
        <div id="container footer">
            <div id="copy" style="text-align: center;"><p>Hak Cipta © 2012 <img src="img/mybooking-logo.png" width="85"/> Produk OSCC, MAMPU</p></div>
        </div>
        <script type="text/javascript">
	    $(document).ready(function() {

		$('.msg-check-error-smtp').hide();

		$('.msg-check-success-permission').hide();

		$('.msg-check-success-smtp').hide();

		$('#default').stepy();

		$('#custom').stepy({
		    back: function(index) {
			$('label.alert.alert-error').remove();
		    },
		    nextLabel: 'Teruskan <i class="icon-forward"></i>',
		    backLabel: '<i class="icon-backward"></i> Kembali',
		    block: true,
		    errorImage: true,
		    titleClick: true,
		    validate: true
		});

		$('div#step').stepy({
		    finishButton: false
		});

		var dbErrorMessage = 'Sambungan ke pangkalan data masih belum berjaya.'

		$.validator.addMethod("check",
			function(value, element) {
			    if (value == 'Success') {
				return true;
			    }
			    else if (value == 'Database not set') {
				//			this.settings.messages.checkdb.checkdb = 'Pangkalan data tidak wujud';
				dbErrorMessage = 'Pangkalan data tidak wujud';
				return false;
			    }
			    return false;
			},
			dbErrorMessage
			);
		
		var spaceErrorMessage = 'Nama admin tidak boleh jarak.';
		
		$.validator.addMethod("noSpace", function(value, element) {
		    return value.indexOf(" ") < 0 && value != "";
		}, "No space please and don't leave it empty");

		// Optionaly
		$('#custom').validate({
		    errorPlacement: function(error, element) {
			$('#custom div.stepy-error').append(error);
		    }, rules: {
			agree: {required: true},
			checkPhp: {required: true},
			checkMysql: {required: true},
			checkAssets: {required: true},
			checkInstallation: {required: true},
			checkConfig: {required: true},
			checkSetting: {required: true},
			checkImg: {required: true},
			//			hostname: {required: true},
			//			username: {required: true},
			//			password: {required: true},
			//			database: {required: true},
			db_success: {check: true},
			admin_email: {email: true, required: true},
			admin_name: {required: true, noSpace: true},
			admin_password: {minlength: 4, required: true},
			admin_retype_password: {required: true, equalTo: '#admin_password'},
			agency_name: {required: true}

		    }, messages: {
			agree: {required: '<strong>Perhatian!</strong> Anda perlu bersetuju dengan polisi diatas untuk meneruskan proses pemasangan.'},
			checkPhp: {required: 'Versi PHP adalah kurang dari keperluan.'},
			checkMysql: {required: 'Versi MySQL tidak memenuhi syarat.'},
			checkAssets: {required: 'Fail/Direktori tidak boleh diubah.'},
			checkInstallation: {required: 'Fail/Direktori tidak boleh diubah.'},
			checkConfig: {required: 'Fail/Direktori tidak boleh diubah.'},
			checkSetting: {required: 'Fail/Direktori tidak boleh diubah.'},
			checkImg: {required: 'Fail/Direktori tidak boleh diubah.'},
			//			hostname: {required: 'Sila masukkan nama hos pangkalan data'},
			//			username: {required: 'Nama pengguna pangkalan data salah.'},
			//			password: {required: 'Kata laluan pangkalan data salah.'},
			//			database: {required: 'Nama pangkalan data salah.'},
			admin_email: {email: 'Format emel pentadbir salah.', required: 'Ruangan emel pentadbir perlu diisi.'},
			admin_name: {required: 'Ruangan nama pentadbir perlu diisi.', minlength: 'Nama pentadbir hendaklah sekurang-kurangnya 4 aksara.', noSpace: 'Nama admin tidak boleh jarak.'},
			admin_password: {minlength: 'Kata laluan hendaklah sekurang-kurangnya 8 aksara.', required: 'Masukkan kata laluan.'},
			admin_retype_password: {required: 'Sahkan kata laluan pentadbir utama.', equalTo: 'Pastikan kata laluan dan sahkan kata laluan sama.'},
			agency_name: {required: 'Sila masukkan nama agensi.'}
		    }

		});

		$('#callback').stepy({
		    back: function(index) {
			alert('Going to step ' + index + '...');
		    }, next: function(index) {
			if ((Math.random() * 10) < 5) {
			    alert('Invalid random value!');
			    return false;
			}

			alert('Going to step ' + index + '...');
		    }, select: function(index) {
			alert('Current step ' + index + '.');
		    }, finish: function(index) {
			alert('Finishing on step ' + index + '...');
		    }, titleClick: true
		});

		$('#target').stepy({
		    description: false,
		    legend: false,
		    titleClick: true,
		    titleTarget: '#title-target'
		});

		$('#check-again').click(function() {
		    $.ajax({
			dataType: 'json',
			url: 'ajax.php',
			type: 'post',
			data: {fn: 'permissioncheck'},
			success: function(data) {
			    $.each(data, function(key, value) {
				switch (value)
				{
				    case 0:
					$('img[name=' + key + ']').attr('src', 'img/no.png');
					$('input[name=' + key + ']').val('');
					break;
				    case 1:
					$('img[name=' + key + ']').attr('src', 'img/yes.png');
					$('input[name=' + key + ']').val('success');
					break;
				}
			    });
			    $('#ajax-placeholder-success-permission').html('<i class="icon-info-sign"></i> Pemeriksaan semula telah dilakukan, pastikan direktori diatas boleh diubah.');
			    $('.msg-check-success-permission').fadeIn().fadeOut(5000);

			}
		    });
		});

		$('#smtp_check').click(function() {
		    $.ajax({
			dataType: 'json',
			url: 'ajax.php',
			type: 'post',
			data: {fn: 'smtpcheck', smtp_host: $('input[name=smtp_host]').val(), smtp_user: $('input[name=smtp_user]').val(), smtp_pass: $('input[name=smtp_pass]').val(), smtp_port: $('input[name=smtp_port]').val()},
			success: function(data) {
			    if (data['result'] == true) {
				//				console.log('Sepatutnya keluar tentang success.');
				//				$('#ajax-placeholder-success-smtp').html('<i class="icon-info-sign"></i> Pemeriksaan semula telah dilakukan, pastikan direktori diatas boleh diubah.');
				//				$('.msg-check-success-smtp').fadeIn().fadeOut(5000);
				$('button[id=smtp_check]').removeClass('btn-danger btn-info').addClass('btn-success').html('Berjaya');
			    }
			    else {
				$('button[id=smtp_check]').removeClass('btn-success btn-info').addClass('btn-danger').html('Tidak Berjaya');
				//				console.log('Sepatutnya error mesej muncul.');
				//				$('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Maaf, pentadbir utama tidak dapat ditambah.');
				//				$('.msg-check-error').fadeIn().fadeOut(5000);
			    }
			}
		    });
		});
		$('input[name^=smtp]').keydown(function() {
		    $('button[id=smtp_check]').removeClass('btn-success btn-danger').addClass('btn-info').html('Klik untuk periksa');
		});
	    });
        </script>
    </body>
</html>