<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logs extends CI_Controller {

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
        
        $this->load->model('User_model');
        $this->load->model('Log_model');
    }

    function index($user_id = 0, $page = 0) {
        $per_page = 20;
        $params1 = array(
            'user_id' => $user_id,
            'pagination' => array(
                'page' => (!empty($page) ? $page : 0),
                'per_page' => $per_page,
            ),
        );

        // Get user list for dropdown
        $users = $this->User_model->getAllRecords();
        
        if (!empty($user_id)){
            $logs = $this->Log_model->getRecordByUserId($params1);
        }
        else {
            $logs = $this->Log_model->getAllRecords($params1);
        }
        
        $selected_user = !empty($user_id) ? $user_id : 0;
        
        $config = array();
        $config['base_url'] = site_url() . '/logs/index/' . $selected_user . '/';
        $config['total_rows'] = $this->Log_model->getCountLogs($user_id);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $pagination = $this->pagination->create_links();
        
        $data['pagination'] = $pagination;
        $data['select_user'] = $selected_user;
        $data['users'] = $users;
        $data['logs'] = $logs;
        $data['loadpage'] = 'logs/list';
        $data['pagetitle'] = 'Senarai Log Aktiviti';
        $data['breadcrumb'] = array(
            'Logs' => 'logs',
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($data) {
        if ($this->input->post()) {
            
        } else {
            
        }
    }

}

/* End of file logs.php */
/* Location: ./application/controllers/transports.php */