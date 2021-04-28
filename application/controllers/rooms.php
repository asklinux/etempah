<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rooms extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }
        if ($this->tank_auth->is_admin() || $this->tank_auth->is_subadmin()){
        }
        else {
            redirect('auth/login');
        }
        
        $this->load->model('Room_model');
        $this->load->model('User_model');
    }

    function index() {
        $order_by = '';
        $data['rooms'] = $this->Room_model->getAllRecords();
        $data['loadpage'] = 'rooms/list';
        $data['pagetitle'] = 'Senarai Bilik';
        $data['breadcrumb'] = array(
            'Bilik' => 'rooms',
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($id = 0) {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Nama Bilik', 'required');
            $this->form_validation->set_rules('location', 'Lokasi', 'required');
	    $this->form_validation->set_rules('building', 'Blok / Bangunan', 'trim');
	    $this->form_validation->set_rules('floor', 'Aras / Tingkat', 'trim');
	    $this->form_validation->set_rules('facilities', 'Fasiliti / Kemudahan', 'trim');
	    $this->form_validation->set_rules('description', 'Penerangan', 'trim');
            //$this->form_validation->set_rules('building', 'Bangunan', 'required');
            //$this->form_validation->set_rules('floor', 'Aras', 'required');
            $this->form_validation->set_rules('capacity', 'Kapasiti', 'required|integer|trim');

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $room_owner_id = $this->input->post('fk_user_id');
                $db_data['fk_user_id'] = !empty($room_owner_id) ? $room_owner_id : 0;
                $db_data['name'] = $this->input->post('name');
                $db_data['description'] = $this->input->post('description');
                $db_data['location'] = $this->input->post('location');
                $db_data['building'] = $this->input->post('building');
                $db_data['floor'] = $this->input->post('floor');
                $db_data['capacity'] = $this->input->post('capacity');
                $db_data['facilities'] = $this->input->post('facilities');
                $db_data['status'] = $this->input->post('status');
                $db_data['status_description'] = empty($db_data['status']) ? $this->input->post('status_description') : '';

                if ($this->Room_model->setRecord($db_data, $id)) {
                    if ($id) {
                        $this->Log_model->setRecord($id, 124);
                    } else {
                        $this->Log_model->setRecord($this->db->insert_id(), 120);
                    }

                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                    redirect('rooms');
                }
            }
        }

        if (!empty($id)) {
            $data['room'] = $this->Room_model->getRecordById($id);
        }

        $data['sub_admins'] = $this->User_model->getAllRecords('user_level = 2');
        $data['room_status'] = $this->Room_model->status;
        $data['user_list'] = $this->User_model->getAllRecords();
        $data['loadpage'] = 'rooms/create';
        $data['pagetitle'] = empty($id) ? 'Tambah Bilik' : 'Kemaskini Bilik';
        $data['breadcrumb'] = array(
            'Bilik' => 'rooms',
            'Tambah Bilik' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function delete($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        if ($this->Room_model->setRecord(NULL, $id, true)) {
            $this->Log_model->setRecord($id, 122);
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Rekod telah berjaya dihapuskan.'));
        }

        redirect('rooms');
    }

}

/* End of file rooms.php */
/* Location: ./application/controllers/rooms.php */