<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Search extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }

        $this->load->model('Booking_model');
        $this->load->model('Booking_transport_model');
        $this->load->model('Transport_model');
        $this->load->model('User_model');
        $this->load->helper('date');
    }

    function index($page = 0) {
        if ($this->input->post() && ($this->input->post('search_text') != '')) {
            $per_page = 8;
            
            $search_text = $this->input->post('search_text');
            $search_filter = $this->input->post('search_filter');
            
            $params1 = array(
                'search_text' =>$search_text,
                'search_filter' =>$search_filter,
                'sorting_type' => 'form-list',
                'pagination' => array(
                    'page' => $page,
                    'per_page' => $per_page,
                ),
            );
            
            $search_result = $this->Booking_model->getAllRecordsForRoomByName($params1);
        }

        $data['result_rooms'] = !empty($search_result) ? $search_result : array();
        $data['loadpage'] = 'search/search';
        $data['pagetitle'] = 'Carian Maklumat';
        $data['breadcrumb'] = array(
            'Carian Maklumat' => 'search',
        );

        $this->load->view('include/master-auth', $data);
    }

}

/* End of file search.php */
/* Location: ./application/controllers/search.php */