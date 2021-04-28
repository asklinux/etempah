<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login');
        }

        $this->load->model('Booking_transport_model');
        $this->load->model('Transport_model');
        $this->load->model('User_model');
        $this->load->helper('date');
    }

    function index() {

        if ($this->input->post('report_type') == "food") {
            $this->generate_report_food();
            exit;
        }

        if ($this->input->post('report_type') == "transport") {
            $this->generate_report_transport();
            exit;
        }

        if ($this->input->post('report_type') == "equipment") {
            $this->generate_report_equipment();
            exit;
        }

        $data['loadpage'] = 'reports/create';
        $data['pagetitle'] = 'Laporan';
        $data['breadcrumb'] = array(
            'Laporan' => 'reports',
        );

        $this->load->view('include/master-auth', $data);
    }

    function generate_report_food() {
        $this->load->model('Booking_model');
        $this->load->model('Booking_food_model');

        if ($this->input->post('start_date')):
            $this->load->library('cezpdf');
            $this->load->helper('pdf');

            list($day, $month, $year) = explode('/', $this->input->post('start_date'));
            $date_start = "{$year}-{$month}-{$day}";

            $date_end = '';
            if ($this->input->post('end_date') != '') {
                list($day, $month, $year) = explode('/', $this->input->post('end_date'));
                $date_end = "{$year}-{$month}-{$day}";
            }

            $params['start_date'] = $date_start;
            if ($date_end) {
                $params['end_date'] = $date_end;
            }

            $bookings = $this->Booking_model->getRecordsForFoodByDate($params, true);
            $bookingswithroom = $this->Booking_model->getRecordsForFoodWithRoomByDate($params, true);
            
            $bookings = array_merge($bookingswithroom, $bookings);
            ksort($bookings);

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

                $booking_prev_time = '';
                foreach ($bookings as $booking):
                    if (empty($booking_prev_time) || ($booking_prev_time && ($booking_prev_time != date('d/m/Y', strtotime($booking->start_date))) )):
                        $booking_prev_time = date('d/m/Y', strtotime($booking->start_date));

                        $total_tempahan[$booking_prev_time][1] = 0; // pagi
                        $total_tempahan[$booking_prev_time][2] = 0; // tengahari
                        $total_tempahan[$booking_prev_time][3] = 0; // petang
                        $total_tempahan[$booking_prev_time][4] = 0; // malam
                    endif;

                    $food_types_array = explode(',', $booking->food_type_ids);
                    $food_types = "";
                    foreach ($food_types_array as $food_type_item):
                        $food_types .= empty($food_types) ? "{$this->Booking_food_model->food_types[$food_type_item]}" : ", {$this->Booking_food_model->food_types[$food_type_item]}";
                        $total_tempahan[$booking_prev_time][$food_type_item] = $total_tempahan[$booking_prev_time][$food_type_item] + $booking->total_pack;
                    endforeach;

                    $db_data[] = array(
                        'date' => date('d/m/Y', strtotime($booking->start_date)),
                        'detail' => $booking->full_name . "\n" . $booking->contact_office . "\n" . $booking->booking_ref_no,
                        'place' => $booking->place,
                        'type' => $food_types,
                        'total_pack' => $booking->total_pack,
                        'booking_food_description' => $booking->booking_food_description);
                endforeach;

                $col_names = array(
                    'date' => 'Tarikh Tempahan',
                    'detail' => 'Maklumat Tempahan',
                    'place' => 'Tempat',
                    'type' => 'Jenis Tempahan',
                    'total_pack' => 'Jumlah Pax',
                    'booking_food_description' => 'Penerangan'
                );

                foreach ($total_tempahan as $key_date => $value):
                    $t_total[] = array(
                    'date' => $key_date,
                    'pagi' => $total_tempahan[$key_date][1],
                    'tengahari' => $total_tempahan[$key_date][2],
                    'petang' => $total_tempahan[$key_date][3],
                    'malam' => $total_tempahan[$key_date][4],
                );
                endforeach;
                
                $t_total_name = array(
                    'date' => 'Tarikh Tempahan',
                    'pagi' => 'Pagi',
                    'tengahari' => 'Tengahari',
                    'petang' => 'Petang',
                    'malam' => 'Malam',
                );

                $date1 = $this->input->post('start_date');
                $date2 = $this->input->post('end_date');

                if ($date2):
                    $report_title = "Senarai Tempahan Makanan: $date1 - $date2";
                else:
                    $report_title = "Senarai Tempahan Makanan: $date1";
                endif;

                $this->cezpdf->ezTable($db_data, $col_names, $report_title, array('width' => 550));
                $this->cezpdf->ezText("\n");
                $this->cezpdf->ezTable($t_total, $t_total_name, 'Jumlah Tempahan ', array('width' => 550));

                $this->cezpdf->ezStream();
            else:
                echo "No record found";
            endif;

        endif;
    }

    function generate_report_transport() {
        $this->load->model('Booking_model');
        $this->load->model('Booking_transport_model');

        if ($this->input->post('start_date')):
            $this->load->library('cezpdf');
            $this->load->helper('pdf');

            list($day, $month, $year) = explode('/', $this->input->post('start_date'));
            $date_start = "{$year}-{$month}-{$day}";

            $date_end = '';
            if ($this->input->post('end_date') != '') {
                list($day, $month, $year) = explode('/', $this->input->post('end_date'));
                $date_end = "{$year}-{$month}-{$day}";
            }

            $params['start_date'] = $date_start;
            if ($date_end) {
                $params['end_date'] = $date_end;
            }

            $bookings = $this->Booking_model->getRecordsForTransportByDate($params, true);

            if (!empty($bookings)):
                $trip_types = $this->Booking_transport_model->trip_types;
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

                foreach ($bookings as $booking):
                    $db_data[] = array(
                        'detail' => $booking->full_name . "\n" . $booking->contact_office . "\n" . trim($booking->booking_ref_no),
                        'place' => "Dari: $booking->destination_from \nKe: $booking->destination_to \n({$trip_types[$booking->trip_type]})",
                        'trip_info' => "Pergi: " . date('d/m/Y h:i A', strtotime($booking->start_date)) . " \nPulang: " . date('d/m/Y h:i A', strtotime($booking->end_date)),
                        'total_passenger' => $booking->total_passenger,
                        'booking_transport_description' => $booking->booking_transport_description);
                endforeach;

                $col_names = array(
                    'detail' => 'Maklumat Tempahan',
                    'place' => 'Destinasi',
                    'trip_info' => 'Maklumat Perjalanan',
                    'total_passenger' => 'Jumlah Penumpang',
                    'booking_transport_description' => 'Penerangan'
                );

                $date1 = $this->input->post('start_date');
                $date2 = $this->input->post('end_date');

                if ($date2):
                    $report_title = "Senarai Tempahan Kenderaan: $date1 - $date2";
                else:
                    $report_title = "Senarai Tempahan Kenderaan: $date1";
                endif;

                $this->cezpdf->ezTable($db_data, $col_names, $report_title, array('width' => 550));
                $this->cezpdf->ezText("\n");
                //$this->cezpdf->ezTable($t_total, $t_total_name, 'Total Tempahan ', array('width' => 550));

                $this->cezpdf->ezStream();
            else:
                echo "No record found";
            endif;

        endif;
    }

    function generate_report_equipment() {
        $this->load->model('Booking_model');
        $this->load->model('Booking_equipment_model');
        $this->load->model('Equipment_model');

        if ($this->input->post('start_date')):
            $this->load->library('cezpdf');
            $this->load->helper('pdf');

            list($day, $month, $year) = explode('/', $this->input->post('start_date'));
            $date_start = "{$year}-{$month}-{$day}";

            $date_end = '';
            if ($this->input->post('end_date') != '') {
                list($day, $month, $year) = explode('/', $this->input->post('end_date'));
                $date_end = "{$year}-{$month}-{$day}";
            }

            $params['start_date'] = $date_start;
            if ($date_end) {
                $params['end_date'] = $date_end;
            }

            $bookings = $this->Booking_model->getRecordsForEquipmentByDate($params, true);
            $bookingswithroom = $this->Booking_model->getRecordsForEquipmentWithRoomByDate($params, true);
            
            $bookings = array_merge($bookingswithroom, $bookings);
            ksort($bookings);

            if (!empty($bookings)):
                $all_equipments = $this->Equipment_model->getAllRecords();
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

                foreach ($bookings as $booking):
                    $selected_equipments = explode(',', $booking->equipment_list);
                    $equipment_list = array();
                    foreach ($selected_equipments as $selected_equipment):
                        foreach ($all_equipments as $equipment):
                            list($equipment_id, $the_total) = explode(':', $selected_equipment);
                            if (($equipment_id == $equipment->equipment_id) && $the_total != 0):
                                $equipment_list[] = $equipment->name . " ($the_total unit)";
                            endif;
                        endforeach;
                    endforeach;

                    $equipment_list = implode("\n", $equipment_list);

                    $db_data[] = array(
                        'date' => date('d/m/Y h:i A', strtotime($booking->start_date)),
                        'detail' => $booking->full_name . "\n" . $booking->contact_office . "\n" . trim($booking->booking_ref_no),
                        'place' => "$booking->place",
                        'equipment_list' => "$equipment_list \nBantuan Teknikal: " . ($booking->require_technical_person ? "Ya" : "Tidak"),
                        'booking_equipment_description' => $booking->booking_equipment_description);
                endforeach;

                $col_names = array(
                    'date' => 'Tarikh Tempahan',
                    'detail' => 'Maklumat Tempahan',
                    'place' => 'Tempat',
                    'equipment_list' => 'Peralatan',
                    'booking_transport_description' => 'Penerangan'
                );

                $date1 = $this->input->post('start_date');
                $date2 = $this->input->post('end_date');

                if ($date2):
                    $report_title = "Senarai Tempahan Peralatan: $date1 - $date2";
                else:
                    $report_title = "Senarai Tempahan Peralatan: $date1";
                endif;

                $this->cezpdf->ezTable($db_data, $col_names, $report_title, array('width' => 550));

                //$this->cezpdf->ezTable($t_total, $t_total_name, 'Total Tempahan ', array('width' => 550));

                $this->cezpdf->ezStream();
            else:
                echo "No record found";
            endif;

        endif;
    }

}

/* End of file reports.php */
/* Location: ./application/controllers/reports.php */