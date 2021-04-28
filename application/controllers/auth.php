<?php

if (session_id() == '') {
    session_start();
}

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->lang->load('tank_auth');
        $this->load->model('tank_auth/users');
        $this->load->model('User_model');
        $this->load->helper('language');
        $this->load->helper('download');
        $this->load->model('Email_queue_model');

        $catch_request = $this->uri->segment(2);

        if ($catch_request == 'activate') {
            $this->activate();
        }
    }

    function check_is_logged_in() {
        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    function index() {
        if ($message = $this->session->flashdata('message')) {
            $data['message'] = $message;
            $data['loadpage'] = 'frontpage';

            $this->load->view('include/master-auth', $data);
        } else {
            redirect('/auth/login/');
        }
    }

    function users() {
        if (!$this->tank_auth->is_admin()):
            redirect('/auth/login/');
        endif;

        $this->check_is_logged_in();

        //$data['users'] = $this->User_model->getAllRecords('user_level NOT IN(1,3)');
        $data['users'] = $this->User_model->getAllRecords('user_level IN(0,1,2,3)');
        $data['user_levels'] = $this->User_model->user_levels;
        $data['user_status'] = $this->User_model->user_status;
        $data['loadpage'] = 'auth/list';
        $data['pagetitle'] = 'Senarai Pengguna';
        $data['breadcrumb'] = array(
            'Pengguna' => 'auth/users',
        );

        $this->load->view('include/master-auth', $data);
    }

    function download() {
        $data = file_get_contents(FCPATH . 'assets/template/Fail Templat Excel eTempah.xls');
        $name = 'Fail Templat Excel eTempah.xls';

        force_download($name, $data);

        $this->check_is_logged_in();
        require_once(APPPATH . "/libraries/FileUploadHandler.php");
        $this->load->library("PHPExcel/PHPExcel");
        $this->lang->load("user");

        $data = array();
        $data['errors'] = array();
        $data['user_levels'] = $this->User_model->user_levels;
        $data['user_status'] = $this->User_model->user_status;
        $data['loadpage'] = 'auth/import_form';
        $data['pagetitle'] = 'Tambah Pengguna Secara Pukal';
        $data['breadcrumb'] = array(
            'Pengguna' => 'auth/users',
            'Import' => 'auth/import/',
        );


        $this->session->set_userdata('error_message', implode("<br/>\n", $data["errors"]));

        $this->load->view('include/master-auth', $data);
    }

    function import() {
        if (!$this->tank_auth->is_admin()):
            redirect('/auth/login/');
        endif;

        $this->check_is_logged_in();
        require_once(APPPATH . "/libraries/FileUploadHandler.php");
        $this->load->library("PHPExcel/PHPExcel");
        $this->lang->load("user");

        $data = array();
        $data['errors'] = array();
        $data['user_levels'] = $this->User_model->user_levels;
        $data['user_status'] = $this->User_model->user_status;
        $data['loadpage'] = 'auth/import_form';
        $data['pagetitle'] = 'Tambah Pengguna Secara Pukal';
        $data['breadcrumb'] = array(
            'Pengguna' => 'auth/users',
            'Import' => 'auth/import/',
        );


        if (!empty($_POST["submit"])) {
            try {
                //$email_activation = $this->config->item('email_activation', 'tank_auth');
                $email_activation = false;
                $uploader = new FileUploadHandler("file_upload", "xls,xlsx");
                $file_path = $uploader->getFilePath();
                if (empty($file_path))
                    throw new Exception("fail yang tidak sah");
                $format = $uploader->getFileExtension() == "xls" ? "Excel5" : "Excel2007";
                //echo "format: " . $format;
                $reader = PHPExcel_IOFactory::createReader($format);
                $excel = $reader->load($file_path);

                //$excel->setActiveSheetIndex(0);
                $sheet = $excel->getActiveSheet();

                $rowno = 2;
                $col = 65;
                $errors = array();
                $rowcount = 0;
                $list_import = array();
                //Check file first
                while ($sheet->getCell(chr($col) . $rowno)->getValue() != "") {
                    $rowcount++;
                    //$this->tank_auth
                    $newrecord = array(
                        "username" => trim($sheet->getCell(chr($col) . $rowno)->getValue()),
                        "password" => trim($sheet->getCell(chr($col + 1) . $rowno)->getValue()),
                        "email" => trim($sheet->getCell(chr($col + 2) . $rowno)->getValue()),
                        "fullname" => trim($sheet->getCell(chr($col + 3) . $rowno)->getValue()),
                        "position" => trim($sheet->getCell(chr($col + 4) . $rowno)->getValue() . ""),
                        "level" => trim($sheet->getCell(chr($col + 5) . $rowno)->getValue() . ""),
                        "department" => trim($sheet->getCell(chr($col + 6) . $rowno)->getValue(), ""),
                        "telephone" => trim($sheet->getCell(chr($col + 7) . $rowno)->getValue() . ""),
                        "telephone_ext" => trim($sheet->getCell(chr($col + 8) . $rowno)->getValue() . ""),
                        "mobile" => trim($sheet->getCell(chr($col + 9) . $rowno)->getValue() . "")
                    );

                    if (!$this->tank_auth->is_username_available($newrecord["username"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", username: " . $newrecord["username"] . " " . lang("already exists");
                    }
                    if (empty($newrecord["password"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", " . lang("invalid password");
                    }
                    if (empty($newrecord["email"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", email: " . lang("invalid email");
                    }
                    if (!$this->tank_auth->is_email_available($newrecord["email"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", email: " . $newrecord["email"] . " " . lang("already exists");
                    }
                    if (empty($newrecord["fullname"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", " . lang("invalid full name");
                    }
                    if (empty($newrecord["position"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", " . lang("invalid position");
                    }
                    if (empty($newrecord["department"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", " . lang("invalid department");
                    }
                    if (empty($newrecord["telephone"])) {
                        $errors[] = lang("Record number") . ": " . $rowcount . ", " . lang("invalid telephone");
                    }

                    $list_import[] = $newrecord;
                    $rowno++;
                }

                if (!empty($errors)) {
                    $data["errors"] = $errors;
                } else {

                    if ($rowcount <= 0) {
                        throw new Exception(lang("No rows"));
                    }

                    foreach ($list_import as $row) {
                        $user_data = $this->tank_auth->create_user(
                                $row["username"], $row["email"], $row["password"], $email_activation, 0);
                        if (!is_null($user_data)) {
                            $db_data_profile = array(
                                'fk_user_id' => $this->db->insert_id(),
                                'full_name' => $row["fullname"],
                                'position_name' => $row["position"],
                                'position_level' => $row["level"],
                                'department_name' => $row["department"],
                                'contact_office' => $row["telephone"],
                                'contact_office_ext' => $row["telephone_ext"],
                                'contact_mobile' => $row["mobile"]
                            );
                            $this->User_model->setRecordForUserProfiles($db_data_profile);

                            $db_data_profile['site_name'] = $this->config->item('website_name', 'tank_auth');
                            $db_data_profile['pass'] = $row["password"];

                            if ($email_activation) {  // send "activate" email
                                $user_data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;
                                $this->_send_email('activate', $user_data['email'], $user_data);
                            } else {
                                $user_data = array_merge($user_data, $db_data_profile);

                                if ($this->config->item('email_account_details', 'tank_auth')) { // send "welcome" email
                                    //$this->_send_email('welcome', $user_data['email'], $user_data);
                                    // Add to email queue for later process
                                    $booking_notification_type = "bulkimport";
                                    $site_name = $this->config->item('website_name', 'tank_auth');
                                    $email['subject'] = sprintf($this->lang->line('auth_message_bulk_registration_completed'), $site_name);
                                    $email['message'] = $this->Email_queue_model->setMessageContent($booking_notification_type, $user_data);
                                }
                            }
                            unset($user_data['password']); // Clear password (just for any case)

                            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Pengguna-pengguna baru telah berjaya didaftarkan.'));
                        } else {
                            $errors = $this->tank_auth->get_error_message();
                            foreach ($errors as $k => $v):
                                $data['errors'][$k] = $this->lang->line($v);
                            endforeach;
                        }
                    }//each list

                    redirect('auth/users');
                }
            } catch (Exception $e) {
                $data["errors"][] = $e->getMessage();
            }
        }

        $this->session->set_userdata('error_message', implode("<br/>\n", $data["errors"]));

        $this->load->view('include/master-auth', $data);
    }

    /**
     * Login user on the site
     *
     * @return void
     */
    function login($status_id = 0) {
        if ($this->tank_auth->is_logged_in()) {  // logged in
            redirect('frontpage');
        } elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            redirect('/auth/send_again/');
        } else {
            $data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
                    $this->config->item('use_username', 'tank_auth'));
            $data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

            $this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('remember', 'Remember me', 'integer');

            // Get login for counting attempts to login
//            if ($this->config->item('login_count_attempts', 'tank_auth') AND
//                    ($login = $this->input->post('login'))) {
//                $login = $this->security->xss_clean($login);
//            } else {
//                $login = '';
//            }
//
//            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
//            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
//                if ($data['use_recaptcha'])
//                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
//                else
//                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
//            }

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if ($this->tank_auth->login($this->form_validation->set_value('login'), $this->form_validation->set_value('password'), $this->form_validation->set_value('remember'), $data['login_by_username'], $data['login_by_email'])) { // success
                    if ($this->tank_auth->get_user_fullname() == '') {
                        $this->session->set_userdata(array('warning_message' => '<i class="icon-exclamation-sign"></i> Sila lengkapkan profil pengguna anda.'));
                        redirect('auth/profile/' . $this->tank_auth->get_user_id());
                    } else {
                        redirect('');
                    }
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    if (isset($errors['banned'])) { // banned user
                        $this->_show_message($this->lang->line('auth_message_banned') . ' ' . $errors['banned']);
                    } elseif (isset($errors['not_activated'])) {    // not activated user
                        redirect('/auth/send_again/');
                    } else {      // fail
                        foreach ($errors as $k => $v):
                            $data['errors'][$k] = $this->lang->line($v);
                        endforeach;

                        $error_message = array(
                            'message' => implode('<br>', $data['errors']),
                        );
                        $this->session->set_userdata('login-popup', $error_message);
                    }
                }
            }

//            $data['show_captcha'] = FALSE;
//            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
//                $data['show_captcha'] = TRUE;
//                if ($data['use_recaptcha']) {
//                    $data['recaptcha_html'] = $this->_create_recaptcha();
//                } else {
//                    $data['captcha_html'] = $this->_create_captcha();
//                }
//            }

            $this->load->model('Equipment_model');
            $data['equipments'] = $this->Equipment_model->getAllRecords($this->Equipment_model->order_by['form-list']);
            $data['status_id'] = $status_id;
            $data['loadpage'] = "auth/login_form";

            $this->load->view('include/master-nonauth', $data);
        }
    }

    /**
     * Logout user
     *
     * @return void
     */
    function logout() {
        $this->tank_auth->logout();

        //$this->_show_message($this->lang->line('auth_message_logged_out'));
        redirect('/auth/login/');
    }

    /**
     * Register user on the site
     *
     * @return void
     */
    function register() {
        if (!$this->tank_auth->is_admin()):
            redirect('/auth/login/');
        endif;

        $this->check_is_logged_in();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('');
        } elseif ($this->tank_auth->is_logged_in(FALSE)) {
            redirect('/auth/send_again/');
        } else {
            $use_username = $this->config->item('use_username', 'tank_auth');

            if ($use_username) {
                //$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[' . $this->config->item('username_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('username_max_length', 'tank_auth') . ']|alpha_dash');
            }

            $this->form_validation->set_rules('email', 'Emel', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('user_level', 'Tahap Pengguna', 'required');

            $data['errors'] = array();
            $email_activation = $this->config->item('email_activation', 'tank_auth');

            if ($this->form_validation->run()) {
                $length = 8;
                $pass_range = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
                $random_pass = substr(str_shuffle($pass_range), 0, $length);

                $extract_username = explode('@', $this->form_validation->set_value('email'));
                $username = $extract_username[0];

                if (!is_null($data = $this->tank_auth->create_user(
                                $use_username ? $username : '', $this->form_validation->set_value('email'), $random_pass, $email_activation, $this->form_validation->set_value('user_level')
                                )
                )) {
                    $db_data_profile['fk_user_id'] = $this->db->insert_id();
                    $this->User_model->setRecordForUserProfiles($db_data_profile);

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    $data['pass'] = $random_pass;

                    if ($email_activation) {  // send "activate" email
                        $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                        $this->_send_email('activate', $data['email'], $data);

                        unset($data['password']); // Clear password (just for any case)
                        //$this->_show_message($this->lang->line('auth_message_registration_completed_1'));
                    } else {
                        if ($this->config->item('email_account_details', 'tank_auth')) { // send "welcome" email
                            $this->_send_email('welcome', $data['email'], $data);
                        }
                        unset($data['password']); // Clear password (just for any case)
                        //$this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
                    }

                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Pengguna baru telah berjaya didaftarkan.'));
                    redirect('auth/users');
                } else {
                    $errors = $this->tank_auth->get_error_message();

                    foreach ($errors as $k => $v):
                        $data['errors'][$k] = $this->lang->line($v);
                        $error_list[] = $this->lang->line($v);
                    endforeach;

                    $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> ' . $data['errors']['email']));
                }

                if (!empty($data['errors'])):
                    $data['errors'] = array();
                    $data['errors'] = implode('\n<br>', $error_list);
                endif;
            }

            $data['user_levels'] = $this->User_model->user_levels;
            $data['use_username'] = $use_username;
            $data['loadpage'] = 'auth/register_form';
            $data['agency_details'] = 'auth/agency_detail';
            $data['pagetitle'] = empty($id) ? 'Tambah Pengguna' : 'Profil Pengguna';
            $data['breadcrumb'] = array(
                'Pengguna' => 'auth/users',
                'Profil' => 'auth/register/',
            );

            $this->load->view('include/master-auth', $data);
        }
    }

    /**
     * User profile on the site
     *
     * @return void
     */
    function profile($id = 0) {
        if (!empty($id) && !$this->tank_auth->is_admin() && ($id != $this->tank_auth->get_user_id())):
            redirect('/auth/login/');
        endif;

        $this->check_is_logged_in();

        if (!$this->tank_auth->is_logged_in()) {  // logged in
            redirect('auth/login');
        } elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            redirect('/auth/send_again/');
        } elseif (!$this->config->item('allow_registration', 'tank_auth')) { // registration is off
            $this->_show_message($this->lang->line('auth_message_registration_disabled'));
        } elseif (empty($id)) {
            redirect('auth/users');
        } else {
            $this->form_validation->set_rules('password2', 'Sahkan Kata Laluan Baru', 'trim|xss_clean');
            $this->form_validation->set_rules('password', 'Kata Laluan Baru', 'trim|xss_clean|matches[password2]');
            $this->form_validation->set_rules('full_name', 'Nama Penuh', 'required');
            $this->form_validation->set_rules('department_name', 'Bahagian', 'required');
            $this->form_validation->set_rules('position_name', 'Jawatan', 'required');
            $this->form_validation->set_rules('position_level', 'Gred', 'required');

            $proceed = true;
            if (!$this->form_validation->run()) {
                $proceed = false;
            }

            if ($this->input->post() && !empty($id) && $proceed) {
                $userid = ($this->tank_auth->get_user_id() == $id) ? $id : $this->input->post('id');
                $db_data_profile['full_name'] = $this->input->post('full_name');
                $db_data_profile['department_name'] = $this->input->post('department_name');
                $db_data_profile['position_name'] = $this->input->post('position_name');
                $db_data_profile['position_level'] = $this->input->post('position_level');
                $db_data_profile['contact_office'] = $this->input->post('contact_office');
                $db_data_profile['contact_office_ext'] = $this->input->post('contact_office_ext');
                $db_data_profile['contact_mobile'] = $this->input->post('contact_mobile');
                $db_data_user['activated'] = $this->input->post('activated');
                $db_data_user['password'] = $this->input->post('password') ? $this->form_validation->set_value('password') : '';
                $db_data_user['old_password'] = $this->input->post('old_password') ? $this->input->post('old_password') : '';

                if ($this->User_model->setRecordForUserProfiles($db_data_profile, $userid)) {

                    if ($this->tank_auth->get_user_id() == $id) {
                        $this->session->set_userdata(array(
                            'fullname' => $this->input->post('full_name'),
                        ));
                    }

                    if ($db_data_user['password'] && $db_data_user['old_password']) {
                        $change_password = $this->tank_auth->change_password($db_data_user['old_password'], $db_data_user['password']);
                        if (!$change_password) {
                            $this->session->set_userdata(array('error_message' => '<i class="icon-info-sign"></i> Password lama yang dimasukkan adalah salah. Sila cuba lagi.'));
                            redirect("auth/profile/$userid");
                        }
                    }

                    // Update user status
                    //$this->User_model->setRecord(array('activated' => $db_data_user['activated']), $userid);

                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));

                    redirect("auth/profile/$userid");
                }
            }

            $data['user_profile'] = $this->User_model->getRecordById($id);
            $data['user_levels'] = $this->User_model->user_levels;
            $data['loadpage'] = 'auth/profile';
            $data['pagetitle'] = empty($id) ? 'Profil Pengguna Baru' : 'Profil Pengguna';
            $data['breadcrumb'] = array(
                'Pengguna' => 'auth/users',
                'Profil' => 'auth/profile/' . $id,
            );

            $this->load->view('include/master-auth', $data);
        }
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    function send_again() {
        if (!$this->tank_auth->is_logged_in(FALSE)) {       // not logged in or activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->change_email(
                                $this->form_validation->set_value('email')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                    $this->_send_email('activate', $data['email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/send_again_form', $data);
        }
    }

    /**
     * Activate user account.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function activate() {

        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        // clear current session
        $this->tank_auth->logout();

        // Activate user
        if ($this->tank_auth->activate_user($user_id, $new_email_key)) {
            $error_message = array(
                'message' => $this->lang->line('auth_message_activation_completed'),
            );

            $_SESSION['show_activated'] = true;

            redirect("auth/profile/" . $user_id);

            //$this->_show_message($this->lang->line('auth_message_activation_completed') . ' ' . anchor('/auth/login/', 'Login'));
            //redirect("auth/profile/$user_id");
        } else {
            //$this->_show_message($this->lang->line('auth_message_activation_failed'));

            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Akaun anda telah diaktifkan.'));
            redirect("");
        }
    }

    /**
     * Generate reset code (to change password) and send it to user
     *
     * @return void
     */
    function forgot_password() {
        if ($this->tank_auth->is_logged_in()) {  // logged in
            redirect('');
        } elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            redirect('/auth/send_again/');
        } else {
            $this->form_validation->set_rules('login', 'Email', 'trim|required|xss_clean');
            $data['errors'] = array();
            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->forgot_password(
                                $this->form_validation->set_value('login')))) {

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with password activation link
                    $this->_send_email('forgot_password', $data['email'], $data);
                    //$this->_show_message($this->lang->line('auth_message_new_password_sent'));

                    $data['loadpage'] = "auth/forgot_password_success";
                    $data['loadpage'] = isset($data['loadpage']) ? $data['loadpage'] : "auth/forgot_password_form";
                    $this->load->view('include/master-nonauth-empty', $data);
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }

            $data['loadpage'] = "auth/forgot_password_form";
            $this->load->view('include/master-nonauth-empty', $data);
        }
    }

    /**
     * Replace user password (forgotten) with a new one (set by user).
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function reset_password() {
        // clear current session
        //$this->tank_auth->logout();

        $user_id = $this->uri->segment(3);
        $new_pass_key = $this->uri->segment(4);

        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
        $this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

        $data['errors'] = array();

        if ($this->form_validation->run()) { // validation ok
            if (!is_null($data = $this->tank_auth->reset_password(
                            $user_id, $new_pass_key, $this->form_validation->set_value('new_password')))) { // success
                $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                // Send email with new password
                $this->_send_email('reset_password', $data['email'], $data);

                redirect('auth/login');
                //$this->_show_message($this->lang->line('auth_message_new_password_activated') . ' ' . anchor('/auth/login/', 'Login'));
            } else {       // fail
                $this->_show_message($this->lang->line('auth_message_new_password_failed'));
            }
        } else {
            // Try to activate user by password key (if not activated yet)
            if ($this->config->item('email_activation', 'tank_auth')) {
                $this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
            }

            if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
                $this->_show_message($this->lang->line('auth_message_new_password_failed'));
            }
        }

        $data['loadpage'] = "auth/reset_password_form";
        $this->load->view('include/master-nonauth-empty', $data);
    }

    /**
     * Change user password
     *
     * @return void
     */
    function change_password() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if ($this->tank_auth->change_password(
                                $this->form_validation->set_value('old_password'), $this->form_validation->set_value('new_password'))) { // success
                    $this->_show_message($this->lang->line('auth_message_password_changed'));
                } else {       // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/change_password_form', $data);
        }
    }

    /**
     * Change user email
     *
     * @return void
     */
    function change_email() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->set_new_email(
                                $this->form_validation->set_value('email'), $this->form_validation->set_value('password')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with new email address and its activation link
                    $this->_send_email('change_email', $data['new_email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/change_email_form', $data);
        }
    }

    /**
     * Replace user email with a new one.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function reset_email() {
        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        // Reset email
        if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) { // success
            $this->tank_auth->logout();
            $this->_show_message($this->lang->line('auth_message_new_email_activated') . ' ' . anchor('/auth/login/', 'Login'));
        } else {  // fail
            $this->_show_message($this->lang->line('auth_message_new_email_failed'));
        }
    }

    function deactivate($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        if ($this->User_model->setRecord(NULL, $id, true)) {
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Rekod telah berjaya dihapuskan.'));
        }

        redirect('auth/users');
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @return void
     */
    function unregister() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if ($this->tank_auth->delete_user(
                                $this->form_validation->set_value('password'))) {  // success
                    $this->_show_message($this->lang->line('auth_message_unregistered'));
                } else {       // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/unregister_form', $data);
        }
    }

    /**
     * Show info message
     *
     * @param	string
     * @return	void
     */
    function _show_message($message) {
        $this->session->set_flashdata('message', $message);
        redirect('/auth/');
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param	string
     * @param	string
     * @param	array
     * @return	void
     */
    function _send_email($type, $email, &$data) {
        $this->load->library('email');
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_' . $type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/' . $type . '-html', $data, TRUE));
        $this->email->set_alt_message($this->load->view('email/' . $type . '-txt', $data, TRUE));
        $this->email->send();
    }

    /**
     * Create CAPTCHA image to verify user as a human
     *
     * @return	string
     */
    function _create_captcha() {
        $this->load->helper('captcha');

        $cap = create_captcha(array(
            'img_path' => './' . $this->config->item('captcha_path', 'tank_auth'),
            'img_url' => base_url() . $this->config->item('captcha_path', 'tank_auth'),
            'font_path' => './' . $this->config->item('captcha_fonts_path', 'tank_auth'),
            'font_size' => $this->config->item('captcha_font_size', 'tank_auth'),
            'img_width' => $this->config->item('captcha_width', 'tank_auth'),
            'img_height' => $this->config->item('captcha_height', 'tank_auth'),
            'show_grid' => $this->config->item('captcha_grid', 'tank_auth'),
            'expiration' => $this->config->item('captcha_expire', 'tank_auth'),
                ));

        // Save captcha params in session
        $this->session->set_flashdata(array(
            'captcha_word' => $cap['word'],
            'captcha_time' => $cap['time'],
        ));

        return $cap['image'];
    }

    /**
     * Callback function. Check if CAPTCHA test is passed.
     *
     * @param	string
     * @return	bool
     */
    function _check_captcha($code) {
        $time = $this->session->flashdata('captcha_time');
        $word = $this->session->flashdata('captcha_word');

        list($usec, $sec) = explode(" ", microtime());
        $now = ((float) $usec + (float) $sec);

        if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
            return FALSE;
        } elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
                $code != $word) OR
                strtolower($code) != strtolower($word)) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Create reCAPTCHA JS and non-JS HTML to verify user as a human
     *
     * @return	string
     */
    function _create_recaptcha() {
        $this->load->helper('recaptcha');

        // Add custom theme so we can get only image
        $options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

        // Get reCAPTCHA JS and non-JS HTML
        $html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

        return $options . $html;
    }

    /**
     * Callback function. Check if reCAPTCHA test is passed.
     *
     * @return	bool
     */
    function _check_recaptcha() {
        $this->load->helper('recaptcha');

        $resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'), $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

        if (!$resp->is_valid) {
            $this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */