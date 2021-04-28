<?php

if (!defined('BASEPATH'))
    die();

class Frontpage extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('security');
        $this->load->library('tank_auth');
        $this->lang->load('tank_auth');

        if (!$this->tank_auth->is_logged_in()) {
            redirect('/auth/login/');
        }

        $this->load->model('Booking_model');
    }

    public function index($status_id = 0) {
        $order_by = '';
        $data['status_id'] = $status_id;

        $this->load->model('Equipment_model');
        $data['equipments'] = $this->Equipment_model->getAllRecords($this->Equipment_model->order_by['form-list']);
        $data['loadpage'] = 'frontpage';
        $data['pagetitle'] = 'Laman Utama';
        $data['breadcrumb'] = array();

        $this->load->view('include/master-auth', $data);
    }

}

/* End of file frontpage.php */
/* Location: ./application/controllers/frontpage.php */
