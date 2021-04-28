<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Equipments extends CI_Controller {

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

        $this->load->model('Equipment_model');
    }

    function index() {
        $order_by = '';
        $data['equipments'] = $this->Equipment_model->getAllRecords($this->Equipment_model->order_by['form-list']);
        $data['loadpage'] = 'equipments/list';
        $data['pagetitle'] = 'Senarai Peralatan';
        $data['breadcrumb'] = array(
            'Peralatan' => 'equipments',
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($id = 0) {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Nama Bilik', 'required');
            $this->form_validation->set_rules('quantity', 'Kuantiti', 'required|integer');
	    $this->form_validation->set_rules('description', 'Penerangan', 'trim');

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $db_data['name'] = $this->input->post('name');
                $db_data['description'] = $this->input->post('description');
                $db_data['quantity'] = $this->input->post('quantity');
                $db_data['status'] = $this->input->post('status');

                if ($this->Equipment_model->setRecord($db_data, $id)) {
                    if ($id) {
                        $this->Log_model->setRecord($id, 134);
                    } else {
                        $this->Log_model->setRecord($this->db->insert_id(), 130);
                    }
                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                    redirect('equipments');
                }
            }
        }

        if (!empty($id)) {
            $data['equipment'] = $this->Equipment_model->getRecordById($id);
        }

        $data['loadpage'] = 'equipments/create';
        $data['pagetitle'] = empty($id) ? 'Tambah Peralatan' : 'Kemaskini Peralatan';
        $data['breadcrumb'] = array(
            'Peralatan' => 'equipments',
            'Tambah Peralatan' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function delete($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        if ($this->Equipment_model->setRecord(NULL, $id, true)){
            $this->Log_model->setRecord($id, 132);
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Rekod telah berjaya dihapuskan.'));
        }

        redirect('equipments');
    }

}

/* End of file equipments.php */
/* Location: ./application/controllers/equipments.php */