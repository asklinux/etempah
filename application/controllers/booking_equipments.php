<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Booking_equipments extends CI_Controller {

        function __construct() {
                parent::__construct();

                if (!$this->tank_auth->is_logged_in()) {
                        redirect('auth/login');
                }

                $this->load->model('Equipment_model');
                $this->load->model('Booking_equipment_model');
                $this->load->model('Booking_model');
                $this->load->model('User_model');
                $this->load->model('Room_model');
                $this->load->model('Email_queue_model');

                //$this->output->enable_profiler(TRUE);
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

                $bookings = $this->Booking_model->getAllRecordsForEquipment($params1, true);

                $config = array();
                $config['base_url'] = site_url() . '/booking_equipments/status/' . $status_id . '/';
                $config['total_rows'] = $this->Booking_model->getCountBookingRecordsForEquipment($params2);
                $config['per_page'] = $per_page;
                $config['use_page_numbers'] = TRUE;
                $config['uri_segment'] = 4;

                $this->pagination->initialize($config);
                $pagination = $this->pagination->create_links();

                $data['pagination'] = $pagination;
                $data['bookings'] = $bookings;
                $data['equipments'] = $this->Equipment_model->getAllRecords();
                $data['loadpage'] = 'booking_equipments/list';
                $data['pagetitle'] = 'Senarai Tempahan Peralatan';
                $data['breadcrumb'] = array(
                    'Tempahan Peralatan' => 'booking_equipments',
                );

                $this->load->view('include/master-auth', $data);
        }

        function create() {
                if ($this->input->post()) {
                        $this->form_validation->set_rules('start_date', 'Masa Mula', 'required');
                        $this->form_validation->set_rules('end_date', 'Masa Tamat', 'required');
                        $this->form_validation->set_rules('place', 'Tempat', 'required');
                        $this->form_validation->set_rules('equipment_purpose', 'Tujuan', 'required');
                        $this->form_validation->set_rules('secretariat', 'Urusetia', 'required');

                        if ($this->form_validation->run() != FALSE) {
                                $id = $this->input->post('id');
                                $db_data_booking['booking_type'] = $this->Booking_model->booking_types['equipment'];
                                $db_data_booking['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('start_date'))));
                                $db_data_booking['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('end_date'))));
                                $db_data_booking['booking_ref_no'] = "P" . date('ymdHis') . str_pad($this->tank_auth->get_user_id(), 3);
                                $db_data_booking['full_day'] = $this->input->post('full_day');
                                $db_data_booking['booking_status'] = 0;
                                $db_data_booking['created'] = date("Y-m-d H:i:s", now());
                                $db_data_booking['fk_user_id'] = $this->input->post('fk_user_id');

                                $selected_equipment_types = $this->input->post('equipments_type_id');
                                $equipment_type_list = array();
                                foreach ($selected_equipment_types as $key => $val) {
                                        if (!empty($val))
                                                $equipment_type_list[] = "$key:$val";
                                }
                                $equipment_type_list = implode(',', $equipment_type_list);

                                if (!empty($equipment_type_list)):
                                        $db_data_booking_equipments['booking_with_room'] = 1;
                                        $db_data_booking_equipments['secretariat'] = $this->input->post('secretariat');
                                        ;
                                        $db_data_booking_equipments['place'] = $this->input->post('place');
                                        $db_data_booking_equipments['equipment_purpose'] = $this->input->post('equipment_purpose');
                                        $db_data_booking_equipments['require_technical_person'] = $this->input->post('require_technical_person');
                                        $db_data_booking_equipments['equipment_list'] = $equipment_type_list;
                                        $db_data_booking_equipments['booking_equipment_description'] = $this->input->post('booking_equipment_description');
                                endif;

                                $db_data_booking_rooms['booking_equipment_id'] = 0;
                                if (!empty($equipment_type_list)):
                                        if ($this->Booking_equipment_model->setRecord($db_data_booking_equipments, 0)):
                                                $db_data_booking['fk_booking_id'] = $this->db->insert_id();

                                                if ($this->Booking_model->setRecord($db_data_booking, $id)) {
                                                        $db_data_booking['booking_id'] = $this->db->insert_id();
                                                        $this->Log_model->setRecord($db_data_booking['fk_booking_id'], 230);

                                                        /*                                                         * ******** Email Section ********* */
                                                        // Add to email queue for later process
                                                        $site_name = $this->config->item('website_name', 'tank_auth');
                                                        $content_data['site_name'] = $site_name;
                                                        $content_data['booking_id'] = $db_data_booking['booking_id'];
                                                        $content_data['booking_ref_no'] = $db_data_booking['booking_ref_no'];
                                                        $content_data['booking_type'] = 'booking_equipments';
                                                        $email['subject'] = sprintf($this->lang->line('auth_subject_newbooking'), $site_name);
                                                        $email['message'] = $this->Email_queue_model->setMessageContent('newbooking', $content_data);

                                                        $admin_list = $this->User_model->getAllRecords(array('user_level' => '1'));
                                                        foreach ($admin_list as $admin):
                                                                $email['to_email'] = $admin->email;
                                                                $this->Email_queue_model->setRecord($email);
                                                        endforeach;
                                                        /*                                                         * ******** Email Section ends ********* */

                                                        redirect('booking_equipments');
                                                }
                                        endif;
                                endif;
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                if (!empty($id)) {
                        $data['booking'] = $this->Booking_equipment_model->getRecordById($id);
                }

                $room_list = $this->Room_model->getAllRecords($list_type = 'dropdown');
                $room_list_tmp = '';

                foreach ($room_list as $room):
                        $room_list_tmp[] = '"' . "{$room->name}, aras {$room->floor} blok {$room->building} {$room->location}" . '"';
                endforeach;

                if (!empty($room_list_tmp)):
                        $data['room_list'] = implode(',', $room_list_tmp);
                endif;

                $user_list = $this->User_model->getAllRecords('activated = 1 AND user_profiles.full_name != ""');
                $user_list_tmp = '';

                foreach ($user_list as $user):
                        $user_list_tmp[] = '"' . htmlentities($user->full_name, ENT_QUOTES) . '"';
                endforeach;

                if (!empty($user_list_tmp)):
                        $data['user_list'] = implode(',', $user_list_tmp);
                endif;

                $equipments = $this->Equipment_model->getAllRecords();
                $data['equipments'] = $equipments;
                $data['loadpage'] = 'booking_equipments/create';
                $data['pagetitle'] = 'Permohonan Tempahan Peralatan';
                $data['breadcrumb'] = array(
                    'Tempahan Peralatan' => 'booking_equipments',
                    'Tambah Permohonan Tempahan Peralatan' => ''
                );

                $this->load->view('include/master-auth', $data);
        }

        function view($id) {
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
                                        if (in_array($db_data_booking['booking_status'], array(1,3,5,7))) {
                                                $log_status = 232;
                                                $action = 'diluluskan';
                                                $booking_notification_type = "approvebooking";
                                        }
                                        if ($db_data_booking['booking_status'] == 6) {
                                                $log_status = 233;
                                                $action = 'ditolak';
                                                $booking_notification_type = "rejectbooking";
                                        }
                                        if ($db_data_booking['booking_status'] == 8) {
                                                $log_status = 234;
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
                                        $content_data['booking_type'] = 'booking_equipments';
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
                                        redirect('booking_equipments');
                                }
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                if (!empty($id)) {
                        $booking = $this->Booking_model->getRecordForEquipmentById($id);
                        $data['booking'] = $booking;
                }

                $data['user'] = $this->User_model->getRecordById($booking->fk_user_id);
                $data['equipments'] = $this->Equipment_model->getAllRecords();
                $data['loadpage'] = 'booking_equipments/view';
                $data['pagetitle'] = 'Permohonan Tempahan Peralatan';
                $data['breadcrumb'] = array(
                    'Tempahan Peralatan' => 'booking_equipments',
                    'Tambah Permohonan Tempahan Peralatan' => ''
                );

                $this->load->view('include/master-auth', $data);
        }

        function delete($id = 0) {
                if ($this->input->post('ids')) {
                        $id = $this->input->post('ids');
                }

                if ($this->Booking_model->setRecord(NULL, $id, true)) {
                        $this->Log_model->setRecord($id, 231);
                        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya dihapuskan.'));
                }

                redirect('booking_equipments');
        }

}

/* End of file booking_equipments.php */
/* Location: ./application/controllers/booking_equipments.php */