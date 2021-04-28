<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Booking_transports extends CI_Controller {

        function __construct() {
                parent::__construct();

                if (!$this->tank_auth->is_logged_in()) {
                        redirect('auth/login');
                }

                $this->load->model('Booking_model');
                $this->load->model('Booking_transport_model');
                $this->load->model('Transport_model');
                $this->load->model('User_model');
                $this->load->model('Destination_model');
                $this->load->model('Email_queue_model');
                $this->load->helper('date');
        }

        function index($status_id = 0, $page = '') {
                $this->status($status_id, $page);
        }

        function status($status_id = 0, $page = '') {
                $per_page = $this->config->item('pagination_per_page');

                switch ($status_id):
                        case "telah-lulus":
                        case "approved":
                                $request_id = 1;
                                break;
                        case "belum-lulus":
                        case "not-approved-yet":
                                $request_id = 0;
                                break;
                        case "ditolak":
                        case "rejected":
                                $request_id = 6;
                                break;
                        case "batal":
                        case "cancelled":
                                $request_id = 8;
                                break;
                        case "all":
                        case "semua":
                        case 0:
                        default:
                                $request_id = 'all';
                                $status_id = 'all';
                                break;
                endswitch;

                $params1 = array(
                    'status_id' => $request_id,
                    'sorting_type' => 'form-list',
                    'where' => array(
                        'users.deleted' => 0,
                    ),
                    'pagination' => array(
                        'page' => (!empty($page) ? $page : 1),
                        'per_page' => $per_page,
                    ),
                );

                $params2 = array(
                    'status_id' => $request_id,
                );

                $bookings = $this->Booking_model->getAllRecordsForTransport($params1, true);

                $config = array();
                $config['base_url'] = site_url() . '/booking_transports/status/' . $status_id . '/';
                $config['total_rows'] = $this->Booking_model->getCountBookingRecordsForTransport($params1);
                $config['per_page'] = $per_page;
                $config['use_page_numbers'] = TRUE;
                $config['uri_segment'] = 4;

                $this->pagination->initialize($config);
                $pagination = $this->pagination->create_links();

                $data['pagination'] = $pagination;
                $data['bookings'] = $bookings;
                $data['trip_types'] = $this->Booking_transport_model->trip_types;
                $data['transport_types'] = $this->Booking_transport_model->transport_types;
                $data['loadpage'] = 'booking_transports/list';
                $data['pagetitle'] = 'Senarai Tempahan Kenderaan';
                $data['breadcrumb'] = array(
                    'Tempahan Kenderaan' => 'booking_transports',
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
                        $this->form_validation->set_rules('transport_purpose', 'Tujuan', 'required|trim');
                        $this->form_validation->set_rules('total_passenger', 'Bilangan Pegawai', 'required|integer');
                        $this->form_validation->set_rules('secretariat', 'Urusetia', 'required');

                        if ($this->form_validation->run() != FALSE) {
                                $id = $this->input->post('id');
                                $db_data_booking['booking_type'] = $this->Booking_model->booking_types['transport'];
                                $db_data_booking['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('start_date'))));
                                $db_data_booking['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('end_date'))));
                                $db_data_booking['booking_ref_no'] = "K" . date('ymdHis') . str_pad($this->tank_auth->get_user_id(), 3);
                                $db_data_booking['booking_status'] = 0;
                                $db_data_booking['created'] = date("Y-m-d H:i:s", now());
                                $db_data_booking['fk_user_id'] = $this->input->post('fk_user_id');

                                $db_data_booking_transports['secretariat'] = $this->input->post('secretariat');
                                $db_data_booking_transports['destination_from'] = $this->input->post('destination_from');
                                $db_data_booking_transports['destination_to'] = $this->input->post('destination_to');
                                $db_data_booking_transports['transport_purpose'] = $this->input->post('transport_purpose');
                                $db_data_booking_transports['total_passenger'] = $this->input->post('total_passenger');
                                $db_data_booking_transports['trip_type'] = $this->input->post('trip_type');
                                $db_data_booking_transports['booking_transport_description'] = $this->input->post('booking_transport_description');

                                $this->Destination_model->setRecord($db_data_booking_transports['destination_from']);
                                $this->Destination_model->setRecord($db_data_booking_transports['destination_to']);

//                                $selected_transports = $this->input->post('transports');
//                                $transport_list = array();
//                                foreach ($selected_transports as $key => $val) {
//                                        $transport_list[] = "$key:$val";
//                                }
//                                $transport_list = implode(',', $transport_list);
//
//                                $db_data_booking_transports['transports'] = $transport_list;

                                if ($this->Booking_transport_model->setRecord($db_data_booking_transports, $id)) {
                                        $db_data_booking['fk_booking_id'] = $this->db->insert_id();
                                        if ($this->Booking_model->setRecord($db_data_booking, $id)) {
                                                $db_data_booking['booking_id'] = $this->db->insert_id();
                                                $this->Log_model->setRecord($db_data_booking['fk_booking_id'], 250);

                                                /*                                                 * ******** Email Section ********* */
                                                // Add to email queue for later process
                                                $site_name = $this->config->item('website_name', 'tank_auth');
                                                $content_data['site_name'] = $site_name;
                                                $content_data['booking_id'] = $db_data_booking['booking_id'];
                                                $content_data['booking_ref_no'] = $db_data_booking['booking_ref_no'];
                                                $content_data['booking_type'] = 'booking_transports';
                                                $email['subject'] = sprintf($this->lang->line('auth_subject_newbooking'), $site_name);
                                                $email['message'] = $this->Email_queue_model->setMessageContent('newbooking', $content_data);

                                                // Check if head driver exists
                                                $head_driver = $this->User_model->getAllRecords(array('user_level' => '3'));
                                                if (!empty($head_driver)):
                                                        $email['to_email'] = $head_driver->email;
                                                else:
                                                        $admin_list = $this->User_model->getAllRecords(array('user_level' => '1'));
                                                        foreach ($admin_list as $admin):
                                                                $email['to_email'] = $admin->email;
                                                                $this->Email_queue_model->setRecord($email);
                                                        endforeach;
                                                endif;
                                                /*                                                 * ******** Email Section ends ********* */

                                                redirect('booking_transports');
                                        }
                                }
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                if (!empty($id)) {
                        $data['transport'] = $this->Booking_transport_model->getRecordById($id);
                }

                $destination_list = $this->Destination_model->getAllRecords();
                $destination_list_tmp = '';

                if (!empty($destination_list)):
                        foreach ($destination_list as $destination):
                                $destination_list_tmp[] = '"' . $destination->destination_name . '"';
                        endforeach;
                endif;


                if (!empty($destination_list_tmp)):
                        $data['destination_list'] = implode(',', $destination_list_tmp);
                endif;

                $user_list = $this->User_model->getAllRecords('activated = 1');
                $user_list_tmp = '';

                foreach ($user_list as $user):
                        $user_list_tmp[] = '"' . htmlentities($user->full_name, ENT_QUOTES) . '"';
                endforeach;

                if (!empty($user_list_tmp)):
                        $data['user_list'] = implode(',', $user_list_tmp);
                endif;

                $data['transport_types'] = $this->Booking_transport_model->transport_types;
                $data['transport_lists'] = $this->Transport_model->getTotalRecordsInGroup();
                $data['loadpage'] = 'booking_transports/create';
                $data['pagetitle'] = 'Permohonan Tempahan Kenderaan';
                $data['breadcrumb'] = array(
                    'Tempahan Kenderaan' => 'booking_transports',
                    'Tambah Permohonan Tempahan Kenderaan' => ''
                );

                $this->load->view('include/master-auth', $data);
        }

        function view($id) {
                $booking = $this->Booking_model->getRecordForTransportById($id);
                $data['booking'] = $booking;

                if ($this->input->post()) {
                        $this->form_validation->set_rules('booking_status', 'Tindakan', 'required|trim');

                        if ($this->form_validation->run() != FALSE) {
                                $id = $this->input->post('id');
                                $db_data_booking['booking_status'] = $this->input->post('booking_status');
                                $db_data_booking['booking_status_description'] = $this->input->post('booking_status_description');
                                $db_data_booking['booking_closed'] = 1;
                                $booking_ref_no = $this->input->post('booking_ref_no');
                                $booker_id = $this->input->post('booker');

                                if ($this->Booking_model->setBookingStatus($db_data_booking, $id)) {
                                        $booking_notification_type = "";
                                        if (in_array($db_data_booking['booking_status'], array(1, 3, 5, 7))) {
                                                $log_status = 252;
                                                $action = 'diluluskan';
                                                $booking_notification_type = "approvebooking";
                                        }
                                        if ($db_data_booking['booking_status'] == 6) {
                                                $log_status = 253;
                                                $action = 'ditolak';
                                                $booking_notification_type = "rejectbooking";
                                        }
                                        if ($db_data_booking['booking_status'] == 8) {
                                                $log_status = 254;
                                                $action = 'dibatalkan';
                                                $booking_notification_type = "cancelbooking";
                                        }
                                        $this->Log_model->setRecord($id, $log_status);

                                        $booker = array();
                                        $admin_list = array();
                                        $booker = $this->User_model->getRecordById($booker_id);
                                        $admin_list = $this->User_model->getAllRecords(array('user_level' => '1'));


                                        /*                                         * ******** Email Section ********* */
                                        // Add to email queue for later process
                                        $site_name = $this->config->item('website_name', 'tank_auth');
                                        $content_data['site_name'] = $site_name;
                                        $content_data['booking_id'] = $id;
                                        $content_data['booking_ref_no'] = $booking_ref_no;
                                        $content_data['booking_type'] = 'booking_transports';
                                        $email['subject'] = sprintf($this->lang->line('auth_subject_' . $booking_notification_type), $site_name);
                                        $email['message'] = $this->Email_queue_model->setMessageContent($booking_notification_type, $content_data);

                                        // If booking is cancelled or rejected, send notification to all involved
                                        if (in_array($db_data_booking['booking_status'], array(6, 8))) {
                                                $email['to_email'] = $booker->email;
                                                $this->Email_queue_model->setRecord($email);

                                                foreach ($admin_list as $admin):
                                                        $email['to_email'] = $admin->email;
                                                        $this->Email_queue_model->setRecord($email);
                                                endforeach;
                                        } else {
                                                $email['to_email'] = $booker->email;
                                                $this->Email_queue_model->setRecord($email);
                                        }
                                        /*                                         * ******** Email Section ends ********* */

                                        $message = "Tempahan rujukan <strong>$booking_ref_no</strong> telah berjaya $action";
                                        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> ' . $message));
                                        redirect('booking_transports');
                                }
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                $data['transport_types'] = $this->Booking_transport_model->transport_types;
                $data['trip_types'] = $this->Booking_transport_model->trip_types;
                $data['user'] = $this->User_model->getRecordById($booking->fk_user_id);
                $data['loadpage'] = 'booking_transports/view';
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

                if ($this->Booking_model->setRecord(NULL, $id, true)) {
                        $this->Log_model->setRecord($id, 251);
                        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya dihapuskan.'));
                }

                redirect('booking_transports');
        }

}

/* End of file transports.php */
/* Location: ./application/controllers/transports.php */