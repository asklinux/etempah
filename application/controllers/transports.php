<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transports extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }
        if ($this->tank_auth->is_admin() || $this->tank_auth->is_headdriver()){
        }
        else {
            redirect('auth/login');
        }

        $this->load->model('Transport_model');
        $this->load->model('Transport_type_model');
    }

    function index() {
        $order_by = '';
        $data['transport_types'] = $this->Transport_type_model->getAllRecords();
        $data['transport_type_total'] = $this->Transport_model->getTotalRecordsInGroup();
        $data['transport_total'] = $this->Transport_model->getTotalRecordsInGroup();
        $data['transports'] = $this->Transport_model->getAllRecords($this->Transport_model->order_by['form-list']);
        $data['cargo_types'] = $this->Transport_type_model->cargo_types;
        $data['cargo_types_name'] = $this->Transport_type_model->cargo_types_name;
        $data['loadpage'] = 'transports/list';
        $data['pagetitle'] = 'Senarai Kenderaan';
        $data['breadcrumb'] = array(
            'Kenderaan' => 'transports',
        );

        $this->load->view('include/master-auth', $data);
    }

    function list_type() {

        $order_by = '';
        $data['transport_type_total'] = $this->Transport_type_model->getTotalRecordsInGroup();
        $data['loadpage'] = 'transports/list_type';
        $data['pagetitle'] = 'Senarai Jenis Kenderaan';
        $data['breadcrumb'] = array(
            'Jenis Kenderaan' => 'transports/list_type'
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($id = 0) {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Nama Kenderaan', 'required');
	    $this->form_validation->set_rules('transport_type', 'Jenis', 'trim');
            $this->form_validation->set_rules('capacity', 'Berat Muatan (tan) / <br/>Bil. Penumpang (orang)', 'required|integer');
            $this->form_validation->set_rules('transport_type', 'Jenis Kenderaan', 'required');
	    $this->form_validation->set_rules('description', 'Penerangan', 'trim');

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $db_data['name'] = $this->input->post('name');
                $db_data['description'] = $this->input->post('description');
                $db_data['transport_type'] = $this->input->post('transport_type');
                $db_data['capacity'] = $this->input->post('capacity');
                $db_data['status'] = $this->input->post('status');

                if ($this->Transport_model->setRecord($db_data, $id)) {
                    if ($id) {
                        $this->Log_model->setRecord($id, 144);
                    } else {
                        $this->Log_model->setRecord($this->db->insert_id(), 140);
                    }
                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                    redirect('transports');
                }
            }
        }

        if (!empty($id)) {
            $data['transport'] = $this->Transport_model->getRecordById($id);
        }

        $data['cargo_types'] = $this->Transport_type_model->cargo_types;
        $data['transport_types'] = $this->Transport_type_model->getAllRecords();
        $data['loadpage'] = 'transports/create';
        $data['pagetitle'] = empty($id) ? 'Tambah Kenderaan' : 'Kemaskini Kenderaan';
        $data['breadcrumb'] = array(
            'Kenderaan' => 'transports',
            'Tambah Kenderaan' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function delete($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        if ($this->Transport_model->setRecord(NULL, $id, true)) {
            $this->Log_model->setRecord($id, 142);
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Rekod telah berjaya dihapuskan.'));
        }


        redirect('transports');
    }

    function delete_type($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }
        $this->Transport_type_model->setRecord(NULL, $id, true);
        $this->Log_model->setRecord($id, 152);
        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya dihapus.'));

        redirect('transports/list_type');
    }

    function create_type($id = 0) {
        if ($this->input->post()) {
            if (!$id){
                $this->form_validation->set_rules('transport_type_name', 'Jenis Kenderaan', 'required|trim|is_unique[transport_types.transport_type_name]');
            }
            $this->form_validation->set_rules('transport_type_cargo', 'Jenis Muatan', 'required');

            if ($this->form_validation->run() == TRUE) {
                $id = $this->input->post('id');
                $db_data['transport_type_name'] = $this->input->post('transport_type_name');
                $db_data['transport_type_cargo'] = $this->input->post('transport_type_cargo');

                if ($this->Transport_type_model->setRecord($db_data, $id)) {
                    if ($id) {
                        $this->Log_model->setRecord($id, 154);
                    } else {
                        $this->Log_model->setRecord($this->db->insert_id(), 150);
                    }
                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                    redirect('transports/list_type');
                }
            } else {
                $this->session->set_userdata(array('error_message' => '<i class="icon-exclamation-sign"></i> Sila lengkapkan maklumat dan jenis kenderaan hendaklah unik.'));
            }
        }

        if (!empty($id)) {
            $data['transport_type'] = $this->Transport_type_model->getRecordById($id);
        }

        asort($this->Transport_type_model->cargo_types);

        $data['cargo_types'] = $this->Transport_type_model->cargo_types;
        $data['loadpage'] = 'transports/create_type';
        $data['pagetitle'] = empty($id) ? 'Tambah Jenis' : 'Kemaskini Jenis';
        $data['breadcrumb'] = array(
            'Kenderaan' => 'transports',
            'Tambah Jenis Kenderaan' => 'transports/create_type'
        );

        $this->load->view('include/master-auth', $data);
    }

}

/* End of file transports.php */
/* Location: ./application/controllers/transports.php */