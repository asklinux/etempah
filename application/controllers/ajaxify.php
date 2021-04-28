<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajaxify extends CI_Controller {

    var $booking_types;

    function __construct() {
        parent::__construct();

        $unblocked_pages = array('getTempahanList');
        if (!$this->tank_auth->is_logged_in() && !in_array($this->router->method, $unblocked_pages)) {
            redirect('auth/login');
        }

        $this->load->model('Room_model');
        $this->load->model('Equipment_model');
        $this->load->model('Booking_room_model');
        $this->load->model('Booking_model');
        $this->load->model('User_model');

        $this->booking_types = $this->Booking_model->booking_types;
    }

    function index() {
        
    }

    function check_availability($booking_type) {
        // Checking rooms availablity

        if ($booking_type == $this->booking_types['room']) {

            if ($this->input->is_ajax_request() && $this->input->post('start_date') && $this->input->post('end_date')) {
                $where_conditions =
                        "(" .
                        "(booking.start_date >= '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.start_date < '" . date('Y-d-m H:i:s', strtotime($this->input->post('end_date'))) . "') OR " .
                        "(booking.start_date <= '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.end_date > '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "') OR " .
                        "(booking.end_date > '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.end_date <= '" . date('Y-d-m H:i:s', strtotime($this->input->post('end_date'))) . "')" .
                        ") AND " .
                        "booking.booking_status >=1 AND booking.booking_status != 6 AND booking.booking_status != 8 AND " .
                        "booking.full_day != 1 AND " .
                        "booking.booking_type = '$booking_type' " .
                        "ORDER BY booking.start_date ASC, rooms.location, rooms.building, rooms.floor, rooms.name";
                $available_rooms = $this->Booking_model->getAjaxRecordWithConditions($where_conditions, $booking_type);

                if (count($available_rooms) == 0) {
                    // return false if no rooms available
                    echo json_encode(array('result' => false));
                } else {
                    // Generate options for drop down with available rooms
                    $option_string = '';
                    $location = '';
                    foreach ($available_rooms as $available_room):
                        if ($available_room['location'] != $location):
                            $option_string .= "<optgroup label='{$available_room['location']}'></optgroup>";
                        endif;

                        $option_string .= "<option value='{$available_room['room_id']}'>{$available_room['building']} {$available_room['floor']}, {$available_room['name']}</option>\n";
                        $location = $available_room['location'];
                    endforeach;

                    echo json_encode(array('result' => true, 'data' => $option_string));
                }
            }
        }

        // Checking and get available equipments based on selected date
        if ($booking_type == $this->booking_types['equipment']) {
            if ($this->input->is_ajax_request() && $this->input->post('start_date') && $this->input->post('end_date')) {
                $where_conditions =
                        "(" .
                        "(booking.start_date >= '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.start_date < '" . date('Y-d-m H:i:s', strtotime($this->input->post('end_date'))) . "') OR " .
                        "(booking.start_date <= '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.end_date > '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "') OR " .
                        "(booking.end_date > '" . date('Y-d-m H:i:s', strtotime($this->input->post('start_date'))) . "' AND booking.end_date <= '" . date('Y-d-m H:i:s', strtotime($this->input->post('end_date'))) . "')" .
                        ") AND " .
                        "booking.booking_status = 1 ";

                $order_by = " ORDER BY booking.start_date ASC";

                $result1 = $result2 = array();

                // Get equipment booking
                $where_conditions_for_equipment = " AND booking.booking_type = '3'";
                $result2 = $this->Booking_model->getAjaxRecordWithConditions($where_conditions . $where_conditions_for_equipment . $order_by, $booking_type);

                if ((count($result1) > 0) || (count($result2) > 0)) {
                    if (count($result1) > 0) {
                        $data = $result1;
                    } else {
                        $data = $result2;
                    }

                    $equipments = $this->Equipment_model->getAllRecords();

                    $equipment_list = array();
                    foreach ($equipments as $equipment):
                        $equipment_list[$equipment->equipment_id] = $equipment->quantity;
                    endforeach;

                    foreach ($data as $resultrecord):
                        $equipment_array = explode(',', $resultrecord->equipment_list);
                        foreach ($equipment_array as $equipment_item):

                            list($equipment_type_id, $equipment_id) = explode(':', $equipment_item);

                            // Deduct from the total
                            $equipment_list[$equipment_type_id] = $equipment_list[$equipment_type_id] - $equipment_id;

                        endforeach;
                    endforeach;

                    echo json_encode($equipment_list);
                }
            }
        }
    }

    public function getTempahanList($booking_type = 1) {
        $params = array(
            'status_id' => 1,
            'booking_type' => $booking_type,
            'sorting_type' => 'calendar-list',
            'where' => array(
                'users.deleted' => 0,
            ),
            'list_for' => 'calendar',
        );
        if ($booking_type == 1):
            $params['booking_closed'] = 1;
        endif;

        if ($booking_type == 1) {
            $bookings = $this->Booking_model->getAllRecordsForRoom($params, false);
        } else if ($booking_type == 3) {
            $bookings = $this->Booking_model->getAllRecordsForEquipment($params, false);
        } else if ($booking_type == 4) {
            $bookings = $this->Booking_model->getAllRecordsForTransport($params, false);
        }

        $prepared_booking = array();


        foreach ($bookings as $booking):
            $start_date = strtotime($booking->start_date) * 1000;
            $end_date = strtotime($booking->end_date) * 1000;

            if ($booking_type == 1 && $booking->booking_closed):
                $prepared_booking[] = array(
                    'date' => "$start_date",
                    'end_date' => "$end_date",
                    'type' => 'meeting',
                    'title' => $booking->room_purpose,
                    'url' => site_url("booking_rooms/view/" . $booking->booking_id),
                    'openEventInNewWindow' => false,
                    'description' => "Urusetia: " . $booking->secretariat,
                    'room_id' => $booking->fk_room_id,
                    'room_name' => "{$booking->room_name}, {$booking->floor} {$booking->building}, {$booking->location}",
                );
            endif;

            if ($booking_type == 4):
                $prepared_booking[] = array(
                    'date' => "$start_date",
                    'end_date' => "$end_date",
                    'type' => 'meeting',
                    'title' => $booking->transport_purpose,
                    'url' => site_url("booking_transports/view/" . $booking->booking_id),
                    'openEventInNewWindow' => false,
                );
            endif;

        endforeach;
        
        echo !empty($prepared_booking) ? json_encode($prepared_booking) : '';
    }

    public function setRecordAdmin() {
        $id = 0;

        if ($this->input->post('action') == 'add') {
            $email_id = $this->input->post('id');
            $userData = $this->User_model->getRecordByEmail($email_id);

            if ($userData):
                $id = $userData->id;
            endif;
        }
        else {
            $id = $this->input->post('id');
        }

        $driver = $this->User_model->getRecordHeadDriver();
        $selected = 'selected';
        $add = array(
            'user_level' => '1',
        );
        $del = array(
            'user_level' => '0',
        );
        if (empty($id)) {
            echo json_encode(array('result' => false));
            exit;
        }
        if ($this->input->post('action') == 'delete') {
            $result = $this->User_model->setRecord($del, $id);
            if ($result) {
                $dataAll = $this->User_model->getAllRecords();
                $strAdmin = "<option value=''>--Sila Pilih</option>\n";
                $strDriver = "<option value=''>--Sila Pilih</option>\n";
                foreach ($dataAll as $dataOne) {
                    if ($dataOne->user_level != 1) {
                        $strAdmin .= "<option value='" . $dataOne->id . "'> " . $dataOne->email . "</option>\n";
                    }
                    if ($dataOne->user_level != 1 && $dataOne->user_level != 2) {
                        $selected = ($dataOne->id == $driver->id) ? 'selected' : '';
                        $strDriver .= "<option value='{$dataOne->id}' $selected >{$dataOne->email}</option>\n";
                    }
                }
                echo json_encode(array('result' => true, 'data' => $strAdmin, 'id' => $id, 'driver_email' => $strDriver));
                exit;
            } else {
                echo json_encode(array('result' => false));
                exit;
            }
        } else {
            $result = $this->User_model->setRecord($add, $id);
            if ($result) {
                $dataAll = $this->User_model->getAllRecords();
                $strAdmin = '';
                $str = "<option value=''>--Sila Pilih</option>\n";
                $strDriver = "<option value=''>--Sila Pilih</option>\n";
                foreach ($dataAll as $dataOne) {
                    if ($dataOne->id == $id) {
                        $strAdmin = '<input type="text" disabled name="admin_email[' . $dataOne->id . ']" id="admin_email[' . $dataOne->id . ']" class="span3" value="' . $dataOne->email . '"> <button type="button" class="btn btn-mini btn-danger" value="' . $dataOne->id . '" name="Delete[' . $dataOne->id . ']"><i class="icon-minus-sign icon-white"></i><strong> Hapus</strong></button> ';
                    } else if ($dataOne->user_level != 1) {
                        $str .= "<option value='" . $dataOne->id . "'> " . $dataOne->email . "</option>\n";
                    }
                    if ($dataOne->user_level != 1 && $dataOne->user_level != 2) {
                        $selected = ($dataOne->id == $driver->id) ? 'selected' : '';
                        $strDriver .= "<option value='{$dataOne->id}' $selected >{$dataOne->email}</option>\n";
                    }
                }

                echo json_encode(array('result' => true, 'data' => $str, 'admin' => $strAdmin, 'driver_email' => $strDriver));
                exit;
            } else {
                echo json_encode(array('result' => false));
                exit;
            }
        }
    }

    public function getUserList() {
        $user_list = $this->User_model->getAllRecords('activated = 1 AND user_profiles.full_name != ""');
        $users = array();
        foreach ($user_list as $user):
            $users[$user->id] = $user->full_name;
        endforeach;

        echo json_encode($users);
        exit;
    }

    public function getUserEmail() {
        $user_list = $this->User_model->getAllRecords('activated = 1');
        $users = array();
        foreach ($user_list as $user):
            $users[$user->id] = $user->email;
        endforeach;

        echo json_encode($users);
        exit;
    }

}
/* End of file transports.php */
/* Location: ./application/controllers/transports.php */