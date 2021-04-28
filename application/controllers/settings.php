<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }
        if ($this->tank_auth->is_admin()){
        }
        else {
            redirect('auth/login');
        }
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->config('tank_auth', TRUE);

        $this->load->model('User_model');
        $this->load->helper('file');
        $this->load->library('email');
        require_once(APPPATH . "/libraries/FileUploadHandler.php");
        require_once(APPPATH . '/libraries/phpass-0.1/PasswordHash.php');

        $errors = array();

        $this->load->config('setting', true);
        $this->load->config('email', true);
    }

    function index($page = 0) {
        if ($this->input->post()) {

            $this->form_validation->set_rules('agency_name', 'Nama Agensi', 'trim|required');
            $this->form_validation->set_rules('agency_address', 'Alamat Agensi', 'trim');
            $this->form_validation->set_rules('smtp_host', 'Perumah SMTP', 'trim');
            $this->form_validation->set_rules('smtp_port', 'Port SMTP', 'trim');
            $this->form_validation->set_rules('smtp_user', 'ID Pengguna SMTP', 'trim');
            $this->form_validation->set_rules('smtp_pass', 'Kata Laluan SMTP', 'trim');

            if ($this->form_validation->run()) {
                $pathSetting = FCPATH . APPPATH . 'config/setting.php';
                $pathEmail = FCPATH . APPPATH . 'config/email.php';
                if (is_writable($pathSetting) && is_writable($pathEmail)) {
                    $strSetting = file_get_contents($pathSetting, TRUE);
                    $strEmail = file_get_contents($pathEmail, TRUE);
                    if ($strSetting && $strEmail) {
                        foreach ($this->config->item('setting') as $kunci => $nilai) {
                            if ($kunci != 'logo_name'):
                                $old = '["' . $kunci . '"]="' . $nilai . '"';
                                $new = '["' . $kunci . '"]="' . $this->input->post($kunci) . '"';
                                $strSetting = str_replace("$old", "$new", $strSetting);
                            endif;
                        }
                        file_put_contents($pathSetting, $strSetting);

                        $exclude_list = array('mailtype', 'charset', 'newline', 'protocol', 'smtp_timeout');
                        foreach ($this->config->item('email') as $key => $value) {
                            if (!in_array($key, $exclude_list)) {
                                $old = '["' . $key . '"] = "' . $value . '"';
                                $new = '["' . $key . '"] = "' . $this->input->post($key) . '"';
                                $strEmail = str_replace("$old", "$new", $strEmail);
                            }
                        }
                        file_put_contents($pathEmail, $strEmail);

                        $upload_result = true;
                        if (!empty($_FILES['new_logo']['name'])):
                            $upload_result = $this->upload_file();
                        endif;

                        if (is_bool($upload_result)) {
                            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                        } else {
                            $this->session->set_userdata('error_message', $upload_result['error']);
                        }

                        if ($this->input->post('driver_email') != $this->input->post('driver_id')) {
                            $this->register_driver();
                        }
                        redirect('settings', 'refresh');
                    } else {
                        $this->session->set_userdata('error_message', 'Tiada data didalam fail');
                    }
                } else {
                    $this->session->set_userdata('error_message', 'Fail <i>settings.php</i> tidak berjaya diubah, maklumat tidak dapat disimpan.');
                    redirect('settings', 'refresh');
                }// tak boleh baca file
            } else {
                $this->session->set_userdata('error_message', validation_errors());
                redirect('settings', 'refresh');
            }
        }

        $data['loadpage'] = 'settings/settings';
        $data['pagetitle'] = 'Tetapan Utama';
        $data['breadcrumb'] = array(
            'Tetapan Utama' => 'settings',
        );
        $data['admin'] = $this->User_model->getAllRecords('user_level = 1');
        $data['headdriver'] = $this->User_model->getRecordHeadDriver();
        $data['notAdmin'] = $this->User_model->getAllRecords('user_level = 0');
        //$data['all'] = $this->User_model->getAllRecords();

        $user_list = $this->User_model->getAllRecords('activated = 1 AND user_level = 0');
        $user_list_tmp = '';

        foreach ($user_list as $user):
            $user_list_tmp[] = '"' . htmlentities($user->email, ENT_QUOTES) . '"';
        endforeach;

        if (!empty($user_list_tmp)):
            $data['user_list'] = implode(',', $user_list_tmp);
        endif;

        $data['loadpage'] = 'settings/settings';
        $data['pagetitle'] = 'Tetapan Utama';
        $data['breadcrumb'] = array(
            'Tetapan Utama' => 'settings',
        );
        $data['admin'] = $this->User_model->getAllRecords('user_level = 1');
        $data['headdriver'] = $this->User_model->getRecordHeadDriver();
        $data['notAdmin'] = $this->User_model->getAllRecords('user_level = 0');
        //$data['all'] = $this->User_model->getAllRecords();
//		$this->session->set_userdata('error_message', $upload_result['error']);
        $this->load->view('include/master-auth', $data);
    }

    function upload_file() {
        $config['upload_path'] = FCPATH . 'assets/img/';
        $config['file_name'] = 'agency-logo';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = '14000';
        $config['max_width'] = '2000';
        $config['max_height'] = '2000';

        $this->load->library('upload', $config);

        $upload_result = true;
        if (!empty($_FILES['new_logo']['name'])) {
            $results = $this->upload->do_upload('new_logo');

            if ($results) {
                /*
                 * Perfecting the algorithm for image/jpeg and image/png extension. Do take note it has around 6 types of extensions. If this fails, revert to the commented code.
                 */
                $old = '';
                if (glob(FCPATH . 'assets/img/agency-logo.*')) {
                    foreach (glob(FCPATH . 'assets/img/agency-logo.*') as $key => $value) {
                        $current = $value;
                        if ($old) {
                            if (filemtime($old) > filemtime($current)) {
                                (unlink($current)) ? '' : $this->session->set_userdata('error_message', 'Fail ' . $current . ' tidak dapat dihapuskan');
                            } else {
                                if (unlink($old)) {
                                    $old = $current;
                                } else {
                                    $this->session->set_userdata('error_message', 'Fail ' . $old . ' tidak dapat dihapuskan');
                                }
                                $old = $current;
                            }
                        } else {
                            $old = $current;
                        }
                    }
                }

//		$config['image_library'] = 'gd2';
//		$config['source_image'] = $old;
////		$config['create_thumb'] = TRUE;
//		$config['width'] = 1;
//		$config['height'] = 40;
//		$config['maintain_ratio'] = TRUE;
//		$config['master_dim'] = 'height';
//
//		$this->load->library('image_lib', $config);
//
//		$resize = $this->image_lib->resize();
//		if (!$resize) {
//		    
//		}
//		echo $this->image_lib->display_errors(); exit;

                $old = array_pop(explode('/', $old));
//		if ($old != $this->config->item('logo_name', 'setting')) {
                $pathSetting = FCPATH . APPPATH . 'config/setting.php';
                $strSetting = file_get_contents($pathSetting, TRUE);
                $oldStr = '["logo_name"]="' . $this->config->item('logo_name', 'setting') . '"';
                $newStr = '["logo_name"]="' . $old . '"';
                $strSetting = str_replace("$oldStr", "$newStr", $strSetting);

                file_put_contents($pathSetting, $strSetting);
//		}

                /*
                 *  The old coding that only cater for 2 types of extensions, which are .jpg and .png. If there are any other extensions being used as logo, e.g .x-png,
                 * this code will fail to delete the files.
                 */
//		if (file_exists(FCPATH . 'assets/img/agency-logo.jpg') && file_exists(FCPATH . 'assets/img/agency-logo.png')) {
//		    $png = filemtime(FCPATH . 'assets/img/agency-logo.png');
//		    $jpeg = filemtime(FCPATH . 'assets/img/agency-logo.jpg');
//		    ($png > $jpeg) ? unlink(FCPATH . 'assets/img/agency-logo.jpg') : unlink(FCPATH . 'assets/img/agency-logo.png');
//		}

                return true;
            } else {
                $upload_result = array('error' => $this->upload->display_errors());
                return $upload_result;
            }
        }
    }

    function check_user($str) {
        if ($this->input->post('admin_check') != $str) {
            $query = $this->User_model->getUniqueness($str);
            if ($query->num_rows() != 0) {
                $this->session->set_userdata('error_message', 'Emel yang dimasukkan wujud, pentadbir baru gagal disimpan.');

                return FALSE;
            } else {
                return TRUE;
            }
        } else {

            return TRUE;
        }
    }

    function register_admin() {
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

            $this->form_validation->set_rules('admin_email', 'Emel Pentadbir', 'trim|required|xss_clean|valid_email');
//			$this->form_validation->set_rules('user_level', 'Tahap Pengguna', 'required');

            $data['errors'] = array();
            $email_activation = $this->config->item('email_activation', 'tank_auth');

            if ($this->form_validation->run()) {
                $length = 8;
                $pass_range = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
                $random_pass = substr(str_shuffle($pass_range), 0, $length);

                $hasher = new PasswordHash($this->config->item('phpass_hash_strength', 'tank_auth'), $this->config->item('phpass_hash_portable', 'tank_auth'));
                $hashed_password = $hasher->HashPassword($random_pass);

                $db_data_admin['email'] = $this->form_validation->set_value('admin_email');
                $db_data_admin['password'] = $random_pass;
                $db_data_admin['full_name'] = $this->input->post('admin_name');

                if ($this->User_model->setRecord($db_data_admin, $this->input->post(admin_id))) {
                    $db_data_admin['password'] = $random_pass;
                    if ($email_activation) {
                        $db_data_admin['new_email_key'] = md5(rand() . microtime());
                    }
                    if ($email_activation) {  // send "activate" email
                        $db_data_admin['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

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
                }

//		if (!is_null($data = $this->tank_auth->create_user(
//				$use_username ? $username : '', $this->form_validation->set_value('admin_email'), $random_pass, $email_activation, 1
//				)
//			)) {
//		    $db_data_profile['fk_user_id'] = $this->db->insert_id();
//		    $this->User_model->setRecordForUserProfiles($db_data_profile);
//
//		    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
//		    $data['pass'] = $random_pass;
//
//		    if ($email_activation) {  // send "activate" email
//			$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;
//
//			$this->_send_email('activate', $data['email'], $data);
//
//			unset($data['password']); // Clear password (just for any case)
//			//$this->_show_message($this->lang->line('auth_message_registration_completed_1'));
//		    } else {
//			if ($this->config->item('email_account_details', 'tank_auth')) { // send "welcome" email
//			    $this->_send_email('welcome', $data['email'], $data);
//			}
//			unset($data['password']); // Clear password (just for any case)
//			//$this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
//		    }
//
//		    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Pengguna baru telah berjaya didaftarkan.'));
//		    redirect('auth/users');
//		} else {
//		    $errors = $this->tank_auth->get_error_message();
//
//		    foreach ($errors as $k => $v):
//			$data['errors'][$k] = $this->lang->line($v);
//			$error_list[] = $this->lang->line($v);
//		    endforeach;
//		}

                if (!empty($data['errors'])):
                    $data['errors'] = array();
                    $data['errors'] = implode('\n<br>', $error_list);
                endif;
            }

            return;
        }
    }

    function register_driver() {
        $oldDriver = $this->input->post('driver_id');
        $newDriverEmail = $this->input->post('driver_email');
        $driver_level = array(
            'user_level' => '3'
        );
        $user_level = array(
            'user_level' => '0'
        );

        if ($newDriverEmail) {
            $userData = $this->User_model->getRecordByEmail($newDriverEmail);
            if ($userData->id != $oldDriver):   
                $this->User_model->setRecord($driver_level, $userData->id);

                if ($oldDriver) {
                    $this->User_model->setRecord($user_level, $oldDriver);
                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                }
                $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
            endif;
        }
        else {
            $this->User_model->setRecord($user_level, $oldDriver);
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
        }
    }

    function check_is_logged_in() {
        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }
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

}

/* End of file search.php */
	/* Location: ./application/controllers/search.php */

	
