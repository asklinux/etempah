<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking extends CI_Controller {

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

    function index() {
        $order_by = '';
        $data['transport_types'] = $this->Booking_transport_model->transport_types;
        //$data['transport_total'] = $this->Booking_transport_model->getTotalRecordsInGroup();
        $data['bookings'] = $this->Booking_model->getAllRecordsForTransport();
        $data['loadpage'] = 'booking_transports/list';
        $data['pagetitle'] = 'Senarai Tempahan Kenderaan';
        $data['breadcrumb'] = array(
            'Tempahan Kenderaan' => 'transports',
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($id = 0) {
        if ($this->input->post()) {
            $this->form_validation->set_rules('start_date', 'Masa Pergi', 'required|trim');

            if ($this->input->post('trip_type') == 2):
                $this->form_validation->set_rules('end_date', 'Masa Balik', 'required|trim');
            endif;

            $this->form_validation->set_rules('destination_from', 'Dari', 'required|trim');
            $this->form_validation->set_rules('destination_to', 'Hingga', 'required|trim');
            $this->form_validation->set_rules('purpose', 'Tujuan', 'required|trim');
            $this->form_validation->set_rules('user_id', 'Hingga', 'required|integer');
            $this->form_validation->set_rules('total_passenger', 'Bilangan Pegawai', 'required|integer');

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $db_data_booking['booking_type'] = $this->Booking_model->booking_types['transport'];
                $db_data_booking['start_date'] = date('Y-m-d H:i:s', strtotime($this->input->post('start_date')));
                $db_data_booking['end_date'] = date('Y-m-d H:i:s', strtotime($this->input->post('end_date')));
                $db_data_booking['booking_status'] = 0;
                $db_data_booking['created'] = date("Y-m-d H:i:s", now());
                $db_data_booking['user_id'] = $this->input->post('user_id');

                $db_data_booking_transports['destination_from'] = $this->input->post('destination_from');
                $db_data_booking_transports['destination_to'] = $this->input->post('destination_to');
                $db_data_booking_transports['purpose'] = $this->input->post('purpose');
                $db_data_booking_transports['total_passenger'] = $this->input->post('total_passenger');
                $db_data_booking_transports['trip_type'] = $this->input->post('trip_type');
                $db_data_booking_transports['description'] = $this->input->post('description');

                $selected_transports = $this->input->post('transports');
                $transport_list = array();
                foreach ($selected_transports as $key => $val) {
                    $transport_list[] = "$key:$val";
                }
                $transport_list = implode(',', $transport_list);

                $db_data_booking_transports['transports'] = $transport_list;

                if ($this->Booking_transport_model->setRecord($db_data_booking_transports, $id)) {
                    $db_data_booking['booking_id'] = $this->db->insert_id();
                    if ($this->Booking_model->setRecord($db_data_booking, $id)) {
                        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya disimpan.'));
                        redirect('booking_transports');
                    }
                }
            }
        }

        if (!empty($id)) {
            $data['transport'] = $this->Booking_transport_model->getRecordById($id);
        }

        $data['transport_types'] = $this->Transport_model->transport_types;
        $data['transport_lists'] = $this->Transport_model->getTotalRecordsInGroup();
        $data['user_list'] = $this->User_model->getAllRecords();
        $data['loadpage'] = 'booking_transports/create';
        $data['pagetitle'] = 'Permohonan Tempahan Kenderaan';
        $data['breadcrumb'] = array(
            'Tempahan Kenderaan' => 'booking_transports',
            'Tambah Permohonan Tempahan Kenderaan' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function delete($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        $this->Transport_model->setRecord(NULL, $id, true);

        redirect('transports');
    }

}

/* End of file booking.php */
/* Location: ./application/controllers/booking.php */