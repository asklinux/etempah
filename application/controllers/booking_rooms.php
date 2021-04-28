<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking_rooms extends CI_Controller {

    var $admin_email = '';

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }

        $this->load->model('Room_model');
        $this->load->model('Booking_room_model');
        $this->load->model('Booking_food_model');
        $this->load->model('Booking_equipment_model');
        $this->load->model('Equipment_model');
        $this->load->model('Booking_model');
        $this->load->model('User_model');
        $this->load->model('Email_queue_model');
        $this->load->library('email');

        $this->admin_email = $this->config->config['admin_email'];
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
            case "dalam-proses":
            case "processing":
                $request_id = 5;
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

        $bookings = $this->Booking_model->getAllRecordsForRoom($params1, true);

        $config = array();
        $config['base_url'] = site_url() . '/booking_rooms/status/' . $status_id . '/';
        $config['total_rows'] = $this->Booking_model->getCountBookingRecordsForRoom($params1);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $pagination = $this->pagination->create_links();

        $data['pagination'] = $pagination;
        $data['bookings'] = $bookings;
        $data['loadpage'] = 'booking_rooms/list';
        $data['pagetitle'] = 'Senarai Tempahan Bilik';
        $data['breadcrumb'] = array(
            'Tempahan Bilik' => 'booking_rooms',
        );

        $this->load->view('include/master-auth', $data);
    }

    function create($id = 0) {

        if ($this->input->post()) {

            $this->form_validation->set_rules('start_date', 'Masa Mula', 'required');
            $this->form_validation->set_rules('end_date', 'Masa Tamat', 'required');
            $this->form_validation->set_rules('fk_room_id', 'Bilik', 'required');
            $this->form_validation->set_rules('fk_user_id', 'Pegawai', 'required|integer');
            $this->form_validation->set_rules('room_purpose', 'Tujuan', 'required');
            $this->form_validation->set_rules('total_from_agensi', 'Bil. Pegawai Agensi', 'required');
            $this->form_validation->set_rules('total_from_nonagensi', 'Bil. Pegawai Luar', 'required');
            $this->form_validation->set_rules('secretariat', 'Urusetia', 'required');

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $db_data_booking['booking_type'] = $this->Booking_model->booking_types['room'];
                $db_data_booking['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('start_date'))));
                $db_data_booking['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('end_date'))));
                $db_data_booking['booking_ref_no'] = "B" . date('ymdHis') . str_pad($this->tank_auth->get_user_id(), 3);
                $db_data_booking['full_day'] = $this->input->post('full_day');
                $db_data_booking['booking_status'] = 0;
                $db_data_booking['created'] = date("Y-m-d H:i:s", now());
                $db_data_booking['fk_user_id'] = $this->input->post('fk_user_id');

                $selected_food_types = $this->input->post('food_type_id');
                $food_type_list = is_array($selected_food_types) ? implode(',', $selected_food_types) : '';

                if (!empty($food_type_list)):
                    $db_data_booking_foods['place'] = $this->input->post('fk_room_id');
                    $db_data_booking_foods['secretariat'] = $this->input->post('secretariat');
                    $db_data_booking_foods['food_purpose'] = $this->input->post('food_purpose');
                    $db_data_booking_foods['total_pack'] = $this->input->post('total_pack');
                    $db_data_booking_foods['food_type_ids'] = $food_type_list;
                    $db_data_booking_foods['booking_food_description'] = $this->input->post('food_description');
                endif;

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
                    $db_data_booking_equipments['place'] = $this->input->post('fk_room_id');
                    $db_data_booking_equipments['equipment_purpose'] = $this->input->post('equipment_purpose');
                    $db_data_booking_equipments['require_technical_person'] = $this->input->post('require_technical_person');
                    $db_data_booking_equipments['equipment_list'] = $equipment_type_list;
                    $db_data_booking_equipments['booking_equipment_description'] = $this->input->post('equipment_description');
                endif;

                $db_data_booking_rooms['fk_room_id'] = $this->input->post('fk_room_id');
                $db_data_booking_rooms['room_purpose'] = $this->input->post('room_purpose');
                $db_data_booking_rooms['total_from_agensi'] = $this->input->post('total_from_agensi');
                $db_data_booking_rooms['total_from_nonagensi'] = $this->input->post('total_from_nonagensi');
                $db_data_booking_rooms['description'] = $this->input->post('description');
                $db_data_booking_rooms['secretariat'] = $this->input->post('secretariat');
                $db_data_booking_rooms['chairman'] = $this->input->post('chairman');

                $db_data_booking_rooms['fk_booking_food_id'] = 0;
                if (!empty($food_type_list)):
                    $this->Booking_food_model->setRecord($db_data_booking_foods, 0);
                    $db_data_booking_rooms['fk_booking_food_id'] = $this->db->insert_id();
                endif;

                $db_data_booking_rooms['fk_booking_equipment_id'] = 0;
                if (!empty($equipment_type_list)):
                    $this->Booking_equipment_model->setRecord($db_data_booking_equipments, 0);
                    $db_data_booking_rooms['fk_booking_equipment_id'] = $this->db->insert_id();
                endif;

                if ($this->Booking_room_model->setRecord($db_data_booking_rooms, $id)) {

                    $db_data_booking['fk_booking_id'] = $this->db->insert_id();

                    $mode_approval_food = 2;
                    $mode_approval_equipment = 4;
                    $total_approval = 1;
                    if ($db_data_booking_rooms['fk_booking_food_id']):
                        $total_approval = $total_approval + $mode_approval_food;
                    endif;
                    if ($db_data_booking_rooms['fk_booking_equipment_id']):
                        $total_approval = $total_approval + $mode_approval_equipment;
                    endif;

                    $db_data_booking['booking_mode'] = $total_approval;
                    if ($this->Booking_model->setRecord($db_data_booking, $id)) {
                        $db_data_booking['booking_id'] = $this->db->insert_id();
                        $this->Log_model->setRecord($db_data_booking['fk_booking_id'], 220);

                        $data = array();
                        if ($db_data_booking):
                            if (!empty($food_type_list)):
                                $db_data_booking['food'] = true;
                            endif;

                            if (!empty($equipment_type_list)):
                                $db_data_booking['equipment'] = true;
                            endif;

                            $data['booking-room'] = $db_data_booking;
                        endif;

                        // Add to email queue for later process
                        $site_name = $this->config->item('website_name', 'tank_auth');
                        $content_data['booking_id'] = $db_data_booking['booking_id'];
                        $content_data['booking_ref_no'] = $db_data_booking['booking_ref_no'];
                        $content_data['booking_type'] = 'booking_rooms';
                        $email['subject'] = sprintf($this->lang->line('auth_subject_newbooking'), $site_name);
                        $email['message'] = $this->Email_queue_model->setMessageContent('newbooking', $content_data);

                        $owner_room = $this->Room_model->getRecordByUserId($db_data_booking_rooms['fk_room_id']);

                        /*
                         *  Check if owner of the room is pentabir bahagian, then only send notification to him
                         *  If no owner id found for the room, send notification to all pentadbir utama
                         */
                        if (!empty($owner_room->id)):
                            $email['to_email'] = $owner_room->email;
                            $this->Email_queue_model->setRecord($email);
                        else:
                            $admin_list = $this->User_model->getAllRecords(array('user_level' => '1'));
                            foreach ($admin_list as $admin):
                                $email['to_email'] = $admin->email;
                                $this->Email_queue_model->setRecord($email);
                            endforeach;
                        endif;

                        redirect('booking_rooms');
                    }
                }
            }
            else {
                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
            }
        }

        if (!empty($id)) {
            $data['transport'] = $this->Booking_room_model->getRecordById($id);
        }

        $equipments = $this->Equipment_model->getAllRecords();
        $data['equipments'] = $equipments;

        $user_list = $this->User_model->getAllRecords('activated = 1');
        $user_list_tmp = '';

        foreach ($user_list as $user):
            $user_list_tmp[] = '"' . htmlentities($user->full_name, ENT_QUOTES) . '"';
        endforeach;

        if (!empty($user_list_tmp)):
            $data['user_list'] = implode(',', $user_list_tmp);
        endif;

        $data['food_types'] = $this->Booking_food_model->food_types;
        $data['room_list'] = $this->Room_model->getAllRecords($list_type = 'dropdown');
        $data['loadpage'] = 'booking_rooms/create';
        $data['pagetitle'] = 'Permohonan Tempahan Bilik';
        $data['breadcrumb'] = array(
            'Tempahan Bilik' => 'booking_rooms',
            'Tambah Permohonan Tempahan Bilik' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function view($id) {
        if ($this->input->post()) {

            if ($this->input->post('approve_food')):
                $this->form_validation->set_rules('approve_food', 'Tindakan', 'required|trim');
            endif;
            if ($this->input->post('approve_equipment')):
                $this->form_validation->set_rules('approve_equipment', 'Tindakan', 'required|trim');
            endif;
            if ($this->input->post('approve_room')):
                $this->form_validation->set_rules('approve_room', 'Tindakan', 'required|trim');
            endif;

            if ($this->form_validation->run() != FALSE) {
                $id = $this->input->post('id');
                $db_data_booking['booking_status_description'] = $this->input->post('booking_status_description');
                $booking_ref_no = trim($this->input->post('booking_ref_no'));

                $add_approval = 0;
                if ($this->input->post('booking_status') == 1) {
                    if ($this->input->post('approve_food') == 2): // compare to the value
                        $add_approval += $this->input->post('approve_food') ? $this->input->post('approve_food') : 0;
                    endif;
                    if ($this->input->post('approve_food') == 6):
                        $add_approval += 0;
                    endif;

                    if ($this->input->post('approve_equipment') == 4): // compare to the value
                        $add_approval += $this->input->post('approve_equipment') ? $this->input->post('approve_equipment') : 0;
                    endif;
                    if ($this->input->post('approve_equipment') == 6):
                        $add_approval += 0;
                    endif;

                    //$add_approval = $this->input->post('approve_food') + $this->input->post('approve_equipment');
                    $db_data_booking['booking_status'] = $this->input->post('booking_status') + $add_approval;
                    $db_data_booking['booking_closed'] = 1;

                    if ($this->input->post('approve_room') && $this->input->post('booking_closed')):
                        $db_data_booking['booking_status'] = $this->input->post('approve_room');
                    endif;

                    if ($this->input->post('approve_room') == 8):
                        $db_data_booking['booking_status'] = $this->input->post('approve_room');
                    endif;
                }
                elseif (!$this->input->post('booking_status')) {
                    $db_data_booking['booking_status'] = $this->input->post('approve_room');
                    if ($this->input->post('booking_mode') == 1 || ( ($this->input->post('booking_mode') > 1) && ($this->input->post('room_owner') == 0))):
                        $db_data_booking['booking_closed'] = 1;
                    else:
                        $db_data_booking['booking_closed'] = 0;
                    endif;
                }
                elseif (($this->input->post('approve_room') == 8)) {
                    $db_data_booking['booking_status'] = $this->input->post('approve_room');
                    $db_data_booking['booking_closed'] = 1;
                }

                if ($this->Booking_model->setBookingStatus($db_data_booking, $id)) {
                    $booking_notification_type = "";
                    if (in_array($db_data_booking['booking_status'], array(1, 3, 5, 7))) {
                        $log_status = 242;
                        $action = 'diluluskan';
                        $booking_notification_type = "approvebooking";
                    }
                    if ($db_data_booking['booking_status'] == 6) {
                        $log_status = 243;
                        $action = 'ditolak';
                        $booking_notification_type = "rejectbooking";
                    }
                    if ($db_data_booking['booking_status'] == 8) {
                        $log_status = 244;
                        $action = 'dibatalkan';
                        $booking_notification_type = "cancelbooking";
                    }
                    $this->Log_model->setRecord($id, $log_status);

                    /** email queue part * */
                    /*
                     * Booking types: newbooking, approvebooking, rejectbooking, cancelbooking
                     * Content Data: booking_id, booking_ref_no
                     */
                    $admin_list = array();
                    $owner_room = $this->Room_model->getRecordByUserId($id);
                    $owner = 'penyelia';
                    if (empty($owner_room->id)):
                        $owner = 'admin';
                        $owner_room = $this->User_model->getAllRecords(array('user_level' => '1'));
                    endif;
                    $booker = $this->User_model->getRecordById($id);

                    $site_name = $this->config->item('website_name', 'tank_auth');
                    $content_data['site_name'] = $site_name;
                    $content_data['booking_id'] = $id;
                    $content_data['booking_ref_no'] = $booking_ref_no;
                    $content_data['booking_type'] = 'booking_rooms';
                    $email['subject'] = sprintf($this->lang->line('auth_subject_' . $booking_notification_type), $site_name);
                    $email['message'] = $this->Email_queue_model->setMessageContent($booking_notification_type, $content_data);


                    if ($owner == 'penyelia'):
                        $email['to_email'] = $owner_room->email;
                        $this->Email_queue_model->setRecord($email);
                    else:
                        foreach ($owner_room as $admin):
                            $email['to_email'] = $admin->email;
                            $this->Email_queue_model->setRecord($email);
                        endforeach;
                    endif;
                    $email['to_email'] = $booker->email;
                    $this->Email_queue_model->setRecord($email);

                    /** end of email queue part * */
                    $message = "Tempahan rujukan <strong>$booking_ref_no</strong> telah berjaya dikemaskinikan";
                    $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> ' . $message));
                    redirect('booking_rooms');
                }
            } else {
                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
            }
        }

        if (!empty($id)) {
            $booking = $this->Booking_model->getRecordForRoomById($id);

            $data['booking'] = $booking;
        }

        $room_results = $this->Room_model->getAllRecords($list_type = 'dropdown');

        foreach ($room_results as $room_result) {
            $room[$room_result->room_id] = "{$room_result->name}, {$room_result->floor} {$room_result->building} {$room_result->location}";
        }

        $equipments = $this->Equipment_model->getAllRecords();
        $data['equipments'] = $equipments;
        $data['food_types'] = $this->Booking_food_model->food_types;
        $data['room_list'] = $room;
        $data['user'] = $this->User_model->getRecordById($booking->fk_user_id);
        $data['owner'] = $this->User_model->getRecordById($booking->room_owner);
        $data['loadpage'] = 'booking_rooms/view';
        $data['pagetitle'] = 'Permohonan Tempahan Bilik';
        $data['breadcrumb'] = array(
            'Tempahan Bilik' => 'booking_rooms',
            'Tambah Permohonan Tempahan Bilik' => ''
        );

        $this->load->view('include/master-auth', $data);
    }

    function delete($id = 0) {
        if ($this->input->post('ids')) {
            $id = $this->input->post('ids');
        }

        if ($this->Booking_model->setRecord(NULL, $id, true)) {
            $this->Log_model->setRecord($id, 221);
            $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya dihapuskan.'));
        }

        redirect('booking_rooms');
    }

    function _send_email($type, $email, &$data) {
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_' . $type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/' . $type . '-html', $data, TRUE));
        //$this->email->set_alt_message($this->load->view('email/' . $type . '-txt', $data, TRUE));
        $this->email->send();
    }

    function sendNotification() {
        	
        $this->email->from('no-reply@localhost', 'MyBooking System');
        $this->email->to('sender4local@gmail.com');
        $this->email->cc('mhi@oscc.org.my');

        $this->email->subject('Email Test 2');
        $this->email->message('Testing the email class.');

        $this->email->send();
    }

}

/* End of file booking_rooms.php */
/* Location: ./application/controllers/booking_rooms.php */