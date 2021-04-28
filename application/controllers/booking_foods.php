<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Booking_foods extends CI_Controller {

        function __construct() {
                parent::__construct();

                if (!$this->tank_auth->is_logged_in()) {
                        redirect('auth/login');
                }

                $this->load->model('Booking_model');
                $this->load->model('Booking_food_model');
                $this->load->model('User_model');
                $this->load->model('Room_model');
                $this->load->model('Email_queue_model');

                $this->load->helper('download');
                $this->load->library('cezpdf');
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

                $bookings = $this->Booking_model->getAllRecordsForFood($params1, true);

                $config = array();
                $config['base_url'] = site_url() . '/booking_foods/status/' . $status_id . '/';
                $config['total_rows'] = $this->Booking_model->getCountBookingRecordsForFood($params2);
                $config['per_page'] = $per_page;
                $config['use_page_numbers'] = TRUE;
                $config['uri_segment'] = 4;

                $this->pagination->initialize($config);
                $pagination = $this->pagination->create_links();

                $data['pagination'] = $pagination;
                $data['bookings'] = $bookings;
                $data['food_types'] = $this->Booking_food_model->food_types;
                $data['loadpage'] = 'booking_foods/list';
                $data['pagetitle'] = 'Senarai Tempahan Makanan';
                $data['breadcrumb'] = array(
                    'Tempahan Makanan' => 'booking_foods',
                );

                $this->load->view('include/master-auth', $data);
        }

        function create() {
                if ($this->input->post()) {

                        $this->form_validation->set_rules('start_date', 'Tarikh Mula', 'required|trim');
                        $this->form_validation->set_rules('end_date', 'Tarikh Tamat', 'required|trim');
                        $this->form_validation->set_rules('secretariat', 'Urusetia', 'required');
                        $this->form_validation->set_rules('place', 'Tempat', 'required');
                        $this->form_validation->set_rules('food_purpose', 'Tujuan', 'required|trim');
                        $this->form_validation->set_rules('total_pack', 'Hingga', 'required|integer');

                        if ($this->form_validation->run() != FALSE) {

                                $id = $this->input->post('id');
                                $db_data_booking['booking_type'] = $this->Booking_model->booking_types['food'];
                                $db_data_booking['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('start_date'))));
                                $db_data_booking['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('end_date'))));
                                $db_data_booking['booking_ref_no'] = "M" . date('ymdHis') . str_pad($this->tank_auth->get_user_id(), 3);
                                $db_data_booking['booking_status'] = 0;
                                $db_data_booking['created'] = date("Y-m-d H:i:s", now());
                                $db_data_booking['fk_user_id'] = $this->input->post('fk_user_id');

                                $selected_food_types = $this->input->post('food_type_id');
                                $food_type_list = implode(',', $selected_food_types);

                                $db_data_booking_foods['place'] = $this->input->post('place');
                                $db_data_booking_foods['secretariat'] = $this->input->post('secretariat');
                                $db_data_booking_foods['food_purpose'] = $this->input->post('food_purpose');
                                $db_data_booking_foods['total_pack'] = $this->input->post('total_pack');
                                $db_data_booking_foods['food_type_ids'] = $food_type_list;
                                $db_data_booking_foods['booking_food_description'] = $this->input->post('booking_food_description');

                                if ($this->Booking_food_model->setRecord($db_data_booking_foods, $id)) {
                                        $db_data_booking['fk_booking_id'] = $this->db->insert_id();
                                        if ($this->Booking_model->setRecord($db_data_booking, $id)) {
                                                $db_data_booking['booking_id'] = $this->db->insert_id();
                                                $this->Log_model->setRecord($db_data_booking['fk_booking_id'], 240);

                                                /*                                                 * ******** Email Section ********* */
                                                // Add to email queue for later process
                                                $site_name = $this->config->item('website_name', 'tank_auth');
                                                $content_data['site_name'] = $site_name;
                                                $content_data['booking_id'] = $db_data_booking['booking_id'];
                                                $content_data['booking_ref_no'] = $db_data_booking['booking_ref_no'];
                                                $content_data['booking_type'] = 'booking_foods';
                                                $email['subject'] = sprintf($this->lang->line('auth_subject_newbooking'), $site_name);
                                                $email['message'] = $this->Email_queue_model->setMessageContent('newbooking', $content_data);

                                                $admin_list = $this->User_model->getAllRecords(array('user_level' => '1'));
                                                foreach ($admin_list as $admin):
                                                        $email['to_email'] = $admin->email;
                                                        $this->Email_queue_model->setRecord($email);
                                                endforeach;
                                                /*                                                 * ******** Email Section ends ********* */

                                                redirect('booking_foods');
                                        }
                                }
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                if (!empty($id)) {
                        $data['booking'] = $this->Booking_model->getRecordsForFoodById($id);
                }

                $room_list = $this->Room_model->getAllRecords($list_type = 'dropdown');
                $room_list_tmp = '';

                foreach ($room_list as $room):
                        $room_list_tmp[] = '"' . "{$room->name} {$room->floor}, {$room->building} {$room->location}" . '"';
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

                $data['food_types'] = $this->Booking_food_model->food_types;
                $data['loadpage'] = 'booking_foods/create';
                $data['pagetitle'] = 'Permohonan Tempahan Makanan';
                $data['breadcrumb'] = array(
                    'Tempahan Makanan' => 'booking_foods',
                    'Tambah Permohonan Tempahan Makanan' => ''
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
                                        $content_data['booking_type'] = 'booking_foods';
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
                                        redirect('booking_foods');
                                }
                        } else {
                                $this->session->set_userdata(array('error_message' => '<i class="icon-remove-sign"></i> Sila lengkapkan borang untuk teruskan.'));
                        }
                }

                if (!empty($id)) {
                        $booking = $this->Booking_model->getRecordForFoodById($id);
                        $data['booking'] = $booking;
                }

                $data['user'] = $this->User_model->getRecordById($booking->fk_user_id);
                $data['loadpage'] = 'booking_foods/view';
                $data['pagetitle'] = 'Permohonan Tempahan Makanan';
                $data['breadcrumb'] = array(
                    'Tempahan Makanan' => 'booking_foods',
                    'Tambah Permohonan Tempahan Makanan' => ''
                );

                $this->load->view('include/master-auth', $data);
        }

        function delete($id = 0) {
                if ($this->input->post('ids')) {
                        $id = $this->input->post('ids');
                }

                if ($this->Booking_model->setRecord(NULL, $id, true)) {
                        $this->Log_model->setRecord($id, 241);
                        $this->session->set_userdata(array('action_message' => '<i class="icon-ok-sign"></i> Maklumat telah berjaya dihapuskan.'));
                }

                redirect('booking_foods');
        }

        function report() {
                if ($this->input->post('report_date')):
                        $this->load->library('cezpdf');
                        $this->load->helper('pdf');

                        list($day, $month, $year) = explode('/', $this->input->post('report_date'));

                        $date = "{$year}-{$month}-{$day}";

                        $params = array(
                            'date' => $date
                        );

                        $bookings = $this->Booking_model->getRecordsForFoodByDate($params, true);

                        if (!empty($bookings)):
                                $footer_content = array();
                                $agency_name = $this->config->item('agency_name', 'setting');
                                $agency_address = $this->config->item('agency_address', 'setting');
                                $agency_phone = $this->config->item('agency_phone', 'setting');
                                $agency_fax = $this->config->item('agency_fax', 'setting');

                                if (!empty($agency_name)):
                                        $footer_content['agency_name'] = $this->config->item('agency_name', 'setting');
                                endif;

                                if (!empty($agency_address)):
                                        $footer_content['agency_address'] = $this->config->item('agency_address', 'setting');
                                endif;

                                if (!empty($agency_phone)):
                                        $footer_content['contact'] = "Telefon: " . $this->config->item('agency_phone', 'setting');
                                endif;

                                if (!empty($agency_fax)):
                                        if (isset($footer_content['contact'])):
                                                $footer_content['contact'] .= "\t\t\tFax: " . $this->config->item('agency_fax', 'setting');
                                        else:
                                                $footer_content['contact'] = "Fax: " . $this->config->item('agency_fax', 'setting');
                                        endif;

                                endif;

                                prep_pdf('portrait', $footer_content); // creates the footer for the document we are creating.

                                $db_data = array();
                                $total_tempahan = array();
                                $total_tempahan[1] = 0; // pagi
                                $total_tempahan[2] = 0; // tengahari
                                $total_tempahan[3] = 0; // petang
                                $total_tempahan[4] = 0; // malam

                                foreach ($bookings as $booking):
                                        $food_types_array = explode(',', $booking->food_type_ids);
                                        $food_types = "";
                                        foreach ($food_types_array as $food_type_item):
                                                $food_types .= empty($food_types) ? "{$this->Booking_food_model->food_types[$food_type_item]}" : ", {$this->Booking_food_model->food_types[$food_type_item]}";
                                                $total_tempahan[$food_type_item] = $total_tempahan[$food_type_item] + $booking->total_pack;
                                        endforeach;

                                        $db_data[] = array(
                                            'detail' => $booking->full_name . "\n" . $booking->contact_office . "\n" . $booking->booking_ref_no,
                                            'place' => $booking->place,
                                            'type' => $food_types,
                                            'total_pack' => $booking->total_pack,
                                            'booking_food_description' => $booking->booking_food_description);
                                endforeach;

                                $col_names = array(
                                    'detail' => 'Maklumat Tempahan',
                                    'place' => 'Tempat',
                                    'type' => 'Jenis Tempahan',
                                    'total_pack' => 'Jumlah Pack',
                                    'booking_food_description' => 'Catatan'
                                );

                                $t_total[] = array(
                                    'pagi' => $total_tempahan[1],
                                    'tengahari' => $total_tempahan[2],
                                    'petang' => $total_tempahan[3],
                                    'malam' => $total_tempahan[4],
                                );
                                $t_total_name = array(
                                    'pagi' => 'Pagi',
                                    'tengahari' => 'Tengahari',
                                    'petang' => 'Petang',
                                    'malam' => 'Malam',
                                );

                                $this->cezpdf->ezTable($db_data, $col_names, 'Senarai Tempahan Makanan Pada ' . $this->input->post('report_date'), array('width' => 550));
                                $this->cezpdf->ezText("\n");
                                $this->cezpdf->ezTable($t_total, $t_total_name, 'Total Tempahan ', array('width' => 550));

                                $this->cezpdf->ezStream();
                        else:
                                echo "No record found";
                                exit;
                        endif;

                endif;
        }

}

/* End of file transports.php */
/* Location: ./application/controllers/transports.php */