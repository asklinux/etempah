<?php

class Booking_model extends CI_Model {

    var $table = 'booking';
    var $table_field_status = 'booking.booking_status';
    var $table_field_booking_ref_no = 'booking.booking_ref_no';
    var $table_field_booking_type = 'booking.booking_type';
    var $table_pk_id = 'booking.booking_id';
    var $table_user_id = 'booking.fk_user_id';
    var $table_fk_booking_id = 'booking.fk_booking_id';
    var $table_reference_booking_rooms = 'booking_rooms';
    var $table_reference_booking_rooms_pk_id = 'booking_rooms.booking_room_id';
    var $table_reference_booking_rooms_user_id = 'booking_rooms.fk_user_id';
    var $table_reference_booking_rooms_name = 'booking_rooms.room_purpose';
    var $table_reference_booking_rooms_pk_room_id = 'booking_rooms.fk_room_id';
    var $table_reference_booking_rooms_pk_booking_food_id = 'booking_rooms.fk_booking_food_id';
    var $table_reference_booking_rooms_pk_booking_equipment_id = 'booking_rooms.fk_booking_equipment_id';
    var $table_reference_rooms = 'rooms';
    var $table_reference_rooms_pk_id = 'rooms.room_id';
    var $table_reference_rooms_user_id = 'rooms.fk_user_id';
    var $table_select_room_name = 'rooms.name as room_name, rooms.location, rooms.building, rooms.floor';
    var $table_reference_equipments = 'equipments';
    var $table_reference_equipments_pk_id = 'equipments.equipment_id';
    var $table_reference_booking_foods = 'booking_foods';
    var $table_reference_booking_foods_pk_id = 'booking_foods.booking_food_id';
    var $table_reference_booking_equipments = 'booking_equipments';
    var $table_reference_booking_equipments_select_field2 = 'booking_equipments.equipment_list';
    var $table_reference_booking_equipments_pk_id = 'booking_equipments.booking_equipment_id';
    var $table_reference_booking_transports = 'booking_transports';
    var $table_reference_booking_transports_pk_id = 'booking_transports.booking_transport_id';
    var $table_reference_users = 'users';
    var $table_reference_users_pk_id = 'users.id';
    var $order_by = array(
        'form-list-desc' => 'booking.created DESC',
        'form-list-asc' => 'booking.created ASC', // General sort
        'form-list-startdate' => 'booking.start_date ASC', // Sort by nearest date - suitable to use on not-yet-approved booking
        'calendar-list-room' => 'booking.start_date ASC, rooms.location ASC, rooms.building ASC, rooms.floor ASC, rooms.name ASC'
    );
    var $booking_types = array(
        'room' => 1,
        'food' => 2,
        'equipment' => 3,
        'transport' => 4,
    );
    var $booking_status = array(
        0 => 'Belum Lulus',
        1 => 'Lulus',
        6 => 'Ditolak',
        8 => 'Dibatalkan',
    );

    function getAllRecordsForRoom($params, $check_user = true) {
        $where_clause = "";

        if ($check_user):
            if ($this->tank_auth->is_admin()):
                $where_clause = " AND ({$this->table_user_id} = {$this->tank_auth->get_user_id()} 
                OR {$this->table_reference_rooms_user_id} = 0  
                OR ({$this->table_reference_rooms_user_id} != 0 AND (booking_rooms.fk_booking_equipment_id !=0 OR booking_rooms.fk_booking_food_id !=0)) 
                )";
            elseif ($this->tank_auth->is_subadmin()):
                $where_clause = " AND ({$this->table_user_id} = {$this->tank_auth->get_user_id()} OR {$this->table_reference_rooms_user_id} = {$this->tank_auth->get_user_id()})";
            elseif ($this->tank_auth->is_normal_user()):
                $where_clause = " AND {$this->table_user_id} = {$this->tank_auth->get_user_id()}";
            endif;
        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_rooms}.*, {$this->table_select_room_name}, rooms.fk_user_id AS room_owner")
                ->from($this->table)
                ->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} = {$this->table_reference_booking_rooms_pk_id}", "left")
                ->join($this->table_reference_rooms, "{$this->table_reference_rooms_pk_id} = {$this->table_reference_booking_rooms_pk_room_id}", "left")
                ->join($this->table_reference_users, "{$this->table_reference_users_pk_id} = {$this->table_user_id}", 'left');

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            if ($params['status_id'] == 5): // Booking room in process
                $this->db->where("booking.booking_closed = 0 && booking.booking_status = 1 && booking.booking_mode > booking.booking_status");
            elseif ($params['status_id'] == 1):
                $this->db->where("booking.booking_closed = 1 AND booking.booking_status NOT IN(6,8) AND booking.booking_status >= 1");
            else:
                $this->db->where("{$this->table_field_status} = {$params['status_id']}");
            endif;

            if (isset($params['list_for']) && $params['list_for'] == 'calendar'):
                $this->db->where("booking.booking_status NOT IN(6,8)");
            endif;
        endif;


        $this->db->where("{$this->table_field_booking_type} = 1 $where_clause");

        if ($params['where']):
            $this->db->where($params['where']);
        endif;

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                if (empty($params['status_id'])):
                //$this->db->order_by($this->order_by['form-list-startdate']);
                else:
                //$this->db->order_by($this->order_by['form-list-asc']);
                endif;
                $this->db->order_by($this->order_by['form-list-startdate']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        if (isset($params['pagination']['page']) && !empty($params['pagination']['page']) && ($params['pagination']['page'] > 1) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['per_page'] * ($params['pagination']['page'] - 1));
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }

        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getAllRecordsForRoomByName($params, $check_user = true) {
        $where_clause = "";

        if (!empty($params['search_text'])):

            // Search by meeting name
            if ($params['search_filter'] == 1):
                $where_clause = " AND LOWER({$this->table_reference_booking_rooms_name}) LIKE LOWER('%{$params['search_text']}%')";
            elseif ($params['search_filter'] == 2):
                $where_clause = " AND {$this->table_field_booking_ref_no} = '{$params['search_text']}'";
            endif;

        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_rooms}.*, {$this->table_select_room_name}")
                ->from($this->table)
                ->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} = {$this->table_reference_booking_rooms_pk_id}")
                ->join($this->table_reference_rooms, "{$this->table_reference_rooms_pk_id} = {$this->table_reference_booking_rooms_pk_room_id}");

        $this->db->where("{$this->table_field_booking_type} = 1 $where_clause");

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                $this->db->order_by($this->order_by['form-list-asc']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        if (isset($params['pagination']['page']) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['page']);
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

//    public function getCountBookingRecordsForRoom($params = '') {
//        $this->db->select("{$this->table}.*, {$this->table_reference_booking_rooms}.*, {$this->table_select_room_name}")
//                ->from($this->table)
//                ->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} = {$this->table_reference_booking_rooms_pk_id}")
//                ->join($this->table_reference_rooms, "{$this->table_reference_rooms_pk_id} = {$this->table_reference_booking_rooms_pk_room_id}");
//
//        $this->db->where("{$this->table_field_booking_type} = 1");
//
//        if ($this->tank_auth->is_admin()):
//            $this->db->where("rooms.fk_user_id = 0");
//            $this->db->or_where("booking.fk_user_id", $this->tank_auth->get_user_id());
//        elseif ($this->tank_auth->is_subadmin()):
//            $this->db->where("rooms.fk_user_id = {$this->tank_auth->get_user_id()}");
//        elseif ($this->tank_auth->is_normal_user() || $this->tank_auth->is_headdriver()):
//            $this->db->where("bookings.fk_user_id = {$this->tank_auth->get_user_id()}");
//        endif;
//
//        if (isset($params['status_id']) && is_numeric($params['status_id'])):
//            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
//        endif;
//
//        $query = $this->db->get();
//        
//        return $query->num_rows();
//    }

    function getCountBookingRecordsForRoom($params, $check_user = true) {
        $where_clause = "";

        if ($check_user):
            if ($this->tank_auth->is_admin()):
                $where_clause = " AND ({$this->table_user_id} = {$this->tank_auth->get_user_id()} 
                OR {$this->table_reference_rooms_user_id} = 0  
                OR ({$this->table_reference_rooms_user_id} != 0 AND (booking_rooms.fk_booking_equipment_id !=0 OR booking_rooms.fk_booking_food_id !=0)) 
                )";
            elseif ($this->tank_auth->is_subadmin()):
                $where_clause = " AND ({$this->table_user_id} = {$this->tank_auth->get_user_id()} OR {$this->table_reference_rooms_user_id} = {$this->tank_auth->get_user_id()})";
            elseif ($this->tank_auth->is_normal_user()):
                $where_clause = " AND {$this->table_user_id} = {$this->tank_auth->get_user_id()}";
            endif;
        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_rooms}.*, {$this->table_select_room_name}")
                ->from($this->table)
                ->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} = {$this->table_reference_booking_rooms_pk_id}", "left")
                ->join($this->table_reference_rooms, "{$this->table_reference_rooms_pk_id} = {$this->table_reference_booking_rooms_pk_room_id}", "left")
                ->join($this->table_reference_users, "{$this->table_reference_users_pk_id} = {$this->table_user_id}", 'left');

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            if ($params['status_id'] == 5): // Booking room in process
                $this->db->where("booking.booking_closed = 0 && booking.booking_status = 1 && booking.booking_mode > booking.booking_status");
            elseif ($params['status_id'] == 1):
                $this->db->where("booking.booking_closed = 1 AND booking.booking_status NOT IN(6,8) AND booking.booking_status >= 1");
            else:
                $this->db->where("{$this->table_field_status} = {$params['status_id']}");
            endif;

            if (isset($params['list_for']) && $params['list_for'] == 'calendar'):
                $this->db->where("booking.booking_status NOT IN(6,8)");
            endif;
        endif;

        $this->db->where("{$this->table_field_booking_type} = 1 $where_clause");

        if ($params['where']):
            $this->db->where($params['where']);
        endif;

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function getCountRecordsForBookingEquipment($params) {
        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}");

        if (isset($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        $this->db->where("{$this->table_field_booking_type} = 1");
        $query = $this->db->get();

        return $query->num_rows();
    }

    function getRecordForRoomById($id) {
        $this->db->select("
        {$this->table}.*, 
        {$this->table_reference_booking_rooms}.*, 
        booking_foods.booking_food_id, booking_foods.food_type_ids, booking_foods.place, booking_foods.food_purpose, booking_foods.total_pack, booking_foods.booking_food_description,
        booking_equipments.booking_equipment_id, booking_equipments.booking_with_room, booking_equipments.equipment_purpose, booking_equipments.place, booking_equipments.require_technical_person, booking_equipments.booking_equipment_description, booking_equipments.equipment_list,
        {$this->table_reference_booking_foods}.secretariat AS food_secretariat,
        {$this->table_reference_booking_equipments}.secretariat AS equipment_secretariat, rooms.fk_user_id AS room_owner
       ")
                ->from($this->table)
                ->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} = {$this->table_reference_booking_rooms_pk_id}")
                ->join($this->table_reference_booking_foods, "{$this->table_reference_booking_rooms_pk_booking_food_id} = {$this->table_reference_booking_foods_pk_id}", 'left')
                ->join($this->table_reference_booking_equipments, "{$this->table_reference_booking_rooms_pk_booking_equipment_id} = {$this->table_reference_booking_equipments_pk_id}", 'left')
                ->join('rooms', "rooms.room_id=booking_rooms.fk_room_id", 'left')
                ->where("{$this->table_pk_id} = $id");

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getAllRecordsForEquipment($params, $check_user = true) {
        $where_clause = "";

        if ($check_user):
            if ($this->tank_auth->is_admin()):
                $where_clause = "";
            elseif ($this->tank_auth->is_subadmin() || $this->tank_auth->is_normal_user() || $this->tank_auth->is_headdriver()):
                $where_clause = " AND {$this->table_user_id} = {$this->tank_auth->get_user_id()}";
            endif;
        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}")
                ->join($this->table_reference_users, "{$this->table_reference_users_pk_id} = {$this->table_user_id}", 'left');

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        $this->db->where("{$this->table_field_booking_type} = 3 $where_clause");

        if ($params['where']):
            $this->db->where($params['where']);
        endif;

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                $this->db->order_by($this->order_by['form-list-asc']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        if (isset($params['pagination']['page']) && !empty($params['pagination']['page']) && ($params['pagination']['page'] > 1) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['per_page'] * ($params['pagination']['page'] - 1));
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getAjaxAvailableEquipments($params) {
        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}")
                ->where("{$this->table_field_status} = {$params['status_id']} AND {$this->table_field_booking_type} = 3");

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                $this->db->order_by($this->order_by['form-list-asc']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getRecordForEquipmentById($id) {
        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}")
                ->where("{$this->table_pk_id} = $id");

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    public function getCountBookingRecordsForEquipment($params = '') {
        $this->db->select("{$this->table}.*")
                ->from($this->table);

        $this->db->where("{$this->table_field_booking_type} = 3");

        if (!$this->tank_auth->is_admin()):
            $this->db->where("{$this->table_user_id} = {$this->tank_auth->get_user_id()}");
        endif;

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        $query = $this->db->get();

        return $query->num_rows();
    }

    function getAllRecordsForFood($params, $check_user = true) {
        $where_clause = "";

        if ($check_user):
            if ($this->tank_auth->is_admin()):
                $where_clause = "";
            elseif ($this->tank_auth->is_subadmin() || $this->tank_auth->is_normal_user() || $this->tank_auth->is_headdriver()):
                $where_clause = " AND {$this->table_user_id} = {$this->tank_auth->get_user_id()}";
            endif;
        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_foods}.*, user_profiles.full_name")
                ->from($this->table)
                ->join($this->table_reference_booking_foods, "{$this->table_fk_booking_id} = {$this->table_reference_booking_foods_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                ->join($this->table_reference_users, "{$this->table_reference_users_pk_id} = {$this->table_user_id}", 'left')
                ->where("{$this->table_field_booking_type} = 2 $where_clause");

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        if ($params['where']):
            $this->db->where($params['where']);
        endif;

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                $this->db->order_by($this->order_by['form-list-asc']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        if (isset($params['pagination']['page']) && !empty($params['pagination']['page']) && ($params['pagination']['page'] > 1) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['per_page'] * ($params['pagination']['page'] - 1));
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getRecordsForFoodByDate($params) {
        $where_clause = "";

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_foods}.*, user_profiles.full_name, user_profiles.contact_office")
                ->from($this->table)
                ->join($this->table_reference_booking_foods, "{$this->table_fk_booking_id} = {$this->table_reference_booking_foods_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                ->where("({$this->table_field_booking_type} = 2 OR ({$this->table_field_booking_type} = 1 AND booking.booking_mode > 1 AND booking.booking_status > 1)) $where_clause");

        if (isset($params['end_date'])) {
            $this->db->where("booking.start_date BETWEEN '" . mysql_real_escape_string($params['start_date']) . " 00:00:00' AND '" . mysql_real_escape_string($params['end_date']) . " 23:59:00'");
        } else {
            $this->db->where("DATE_FORMAT(booking.start_date, '%Y-%m-%d') = '" . mysql_real_escape_string($params['start_date']) . "'");
        }

        $this->db->where("{$this->table_field_status} = 1");
        $this->db->order_by('booking.start_date ASC');
        $this->db->order_by('booking_foods.place ASC');
        //$this->db->order_by($this->order_by['form-list-asc']);

        $query = $this->db->get();
//echo $this->db->last_query();exit;
        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[$row->start_date] = $row;
                }
            }
        }

        return $result;
    }
    
    function getRecordsForFoodWithRoomByDate($params) {
        $where_clause = "";

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_foods}.*, user_profiles.full_name, user_profiles.contact_office")
                ->from($this->table)
                ->join($this->table_reference_booking_foods, "{$this->table_fk_booking_id} = {$this->table_reference_booking_foods_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                ->where("{$this->table_field_booking_type} = 1 AND booking.booking_status >= 3 AND booking.booking_mode >= 3 AND booking.booking_closed = 1 AND booking.booking_status != 6 AND booking.booking_status != 8 $where_clause");

        if (isset($params['end_date'])) {
            $this->db->where("booking.start_date BETWEEN '" . mysql_real_escape_string($params['start_date']) . " 00:00:00' AND '" . mysql_real_escape_string($params['end_date']) . " 23:59:00'");
        } else {
            $this->db->where("DATE_FORMAT(booking.start_date, '%Y-%m-%d') = '" . mysql_real_escape_string($params['start_date']) . "'");
        }

        //$this->db->where("{$this->table_field_status} = 1");
        $this->db->order_by('booking.start_date ASC');
        $this->db->order_by('booking_foods.place ASC');

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[$row->start_date] = $row;
                }
            }
        }

        return $result;
    }

    function getRecordsForTransportByDate($params) {
        $where_clause = "";

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_transports}.*, user_profiles.full_name, user_profiles.contact_office")
                ->from($this->table)
                ->join($this->table_reference_booking_transports, "{$this->table_fk_booking_id} = {$this->table_reference_booking_transports_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                ->where("{$this->table_field_booking_type} = 4 $where_clause");

        if (isset($params['end_date'])) {
            $this->db->where("booking.start_date BETWEEN '" . mysql_real_escape_string($params['start_date']) . " 00:00:00' AND '" . mysql_real_escape_string($params['end_date']) . " 23:59:00'");
        } else {
            $this->db->where("DATE_FORMAT(booking.start_date, '%Y-%m-%d') = '" . mysql_real_escape_string($params['start_date']) . "'");
        }

        $this->db->where("{$this->table_field_status} = 1");
        $this->db->order_by('booking.start_date ASC');
        $this->db->order_by('booking_transports.destination_from ASC');
        //$this->db->order_by($this->order_by['form-list-asc']);

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }
    
    function getRecordsForEquipmentByDate($params) {
        $where_clause = "";

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*, user_profiles.full_name, user_profiles.contact_office")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                ->where("{$this->table_field_booking_type} = 3 $where_clause");

        if (isset($params['end_date'])) {
            $this->db->where("booking.start_date BETWEEN '" . mysql_real_escape_string($params['start_date']) . " 00:00:00' AND '" . mysql_real_escape_string($params['end_date']) . " 23:59:00'");
        } else {
            $this->db->where("DATE_FORMAT(booking.start_date, '%Y-%m-%d') = '" . mysql_real_escape_string($params['start_date']) . "'");
        }

        $this->db->where("{$this->table_field_status} = 1");
        $this->db->order_by('booking.start_date ASC');
        $this->db->order_by('booking_equipments.place ASC');
        //$this->db->order_by($this->order_by['form-list-asc']);

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[$row->start_date] = $row;
                }
            }
        }

        return $result;
    }
    
    function getRecordsForEquipmentWithRoomByDate($params) {
        $where_clause = "";

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_equipments}.*, user_profiles.full_name, user_profiles.contact_office")
                ->from($this->table)
                ->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} = {$this->table_reference_booking_equipments_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id")
                //->where("{$this->table_field_booking_type} = 3 $where_clause");
                ->where("{$this->table_field_booking_type} = 1 AND booking.booking_status >= 5 AND booking.booking_mode >= 5 AND booking.booking_closed = 1 AND booking.booking_status != 6 AND booking.booking_status != 8 $where_clause");

        if (isset($params['end_date'])) {
            $this->db->where("booking.start_date BETWEEN '" . mysql_real_escape_string($params['start_date']) . " 00:00:00' AND '" . mysql_real_escape_string($params['end_date']) . " 23:59:00'");
        } else {
            $this->db->where("DATE_FORMAT(booking.start_date, '%Y-%m-%d') = '" . mysql_real_escape_string($params['start_date']) . "'");
        }

        //$this->db->where("{$this->table_field_status} = 1");
        $this->db->order_by('booking.start_date ASC');
        $this->db->order_by('booking_equipments.place ASC');
        //$this->db->order_by($this->order_by['form-list-asc']);

        $query = $this->db->get();
//echo $this->db->last_query();exit;
        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[$row->start_date] = $row;
                }
            }
        }

        return $result;
    }

    public function getCountBookingRecordsForFood($params = '') {
        $this->db->select("{$this->table}.*")->from($this->table);

        $this->db->where("{$this->table_field_booking_type} = 2");

        if (!$this->tank_auth->is_admin()):
            $this->db->where("{$this->table_user_id} = {$this->tank_auth->get_user_id()}");
        endif;

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        $query = $this->db->get();

        return $query->num_rows();
    }

    function getRecordForFoodById($id) {
        $this->db->select("{$this->table}.*, {$this->table_reference_booking_foods}.*, user_profiles.full_name")
                ->from($this->table)
                ->join($this->table_reference_booking_foods, "{$this->table_fk_booking_id} = {$this->table_reference_booking_foods_pk_id}")
                ->join('user_profiles', "user_profiles.fk_user_id = booking.fk_user_id");

        if (!empty($id)) {
            $this->db->where("{$this->table_pk_id} = $id");
        }

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    public function getCountBookingRecordsForTransport($params = '') {
        $this->db->select("{$this->table}.*")
                ->from($this->table);

        $this->db->where("{$this->table_field_booking_type} = 4");

        if (!$this->tank_auth->is_admin()):
            $this->db->where("{$this->table_user_id} = {$this->tank_auth->get_user_id()}");
        endif;

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        $query = $this->db->get();

        return $query->num_rows();
    }

    function getAllRecordsForTransport($params, $check_user = true) {
        $where_clause = "";

        if ($check_user):
            if ($this->tank_auth->is_admin()):
                $where_clause = "";
            elseif ($this->tank_auth->is_subadmin()):
                $where_clause = " AND 0 = {$this->tank_auth->get_user_id()}"; // Purposely make this fail
            elseif ($this->tank_auth->is_normal_user()):
                $where_clause = " AND {$this->table_user_id} = {$this->tank_auth->get_user_id()}";
            endif;
        endif;

        $this->db->select("{$this->table}.*, {$this->table_reference_booking_transports}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_transports, "{$this->table_fk_booking_id} = {$this->table_reference_booking_transports_pk_id}")
                ->join($this->table_reference_users, "{$this->table_reference_users_pk_id} = {$this->table_user_id}", 'left')
                ->where("{$this->table_field_booking_type} = 4 $where_clause");

        if (isset($params['status_id']) && is_numeric($params['status_id'])):
            $this->db->where("{$this->table_field_status} = {$params['status_id']}");
        endif;

        if ($params['where']):
            $this->db->where($params['where']);
        endif;

        if (isset($params['sorting_type']) || $params['sorting_type'] == 'form-list') {
            if ($this->tank_auth->is_admin()) {
                $this->db->order_by($this->order_by['form-list-asc']);
            } else {
                $this->db->order_by($this->order_by['form-list-desc']);
            }
        } else {
            $this->db->order_by($this->order_by['calendar-list-room']);
        }

        if (isset($params['pagination']['page']) && !empty($params['pagination']['page']) && ($params['pagination']['page'] > 1) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['per_page'] * ($params['pagination']['page'] - 1));
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }

        $query = $this->db->get();

        $result = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }

    function getRecordForTransportById($id) {
        $this->db->select("{$this->table}.*, {$this->table_reference_booking_transports}.*")
                ->from($this->table)
                ->join($this->table_reference_booking_transports, "{$this->table_fk_booking_id} = {$this->table_reference_booking_transports_pk_id}")
                ->where("{$this->table_pk_id} = $id");

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            if (!empty($id)) {
                $result = $query->row();
            } else {
                foreach ($query->result() as $row) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    function getTotalRecordsInGroup($order_by = '') {
        $this->db->select("{$this->table_select_total}")
                ->from($this->table)->group_by($this->table_group_by_1);

        $query = $this->db->get();

        $result = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }

    function getRecordById($id) {
        $query = $this->db->get_where($this->table, array('id' => $id));

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }

    function getAjaxRecordWithConditions($where_conditions, $booking_types, $params = array()) {

        switch ($booking_types):

            // Check available rooms
            case '1':
                $booked_rooms = array();
                $available_rooms = array();

                // Get all booked rooms for the selected date
                $this->db->select('rooms.room_id');
                $this->db->from($this->table);
                $this->db->where($where_conditions);
                $this->db->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} =  {$this->table_reference_booking_rooms_pk_id}");
                $this->db->join($this->table_reference_rooms, "{$this->table_reference_booking_rooms_pk_room_id} =  {$this->table_reference_rooms_pk_id}");
                $query = $this->db->get();

                // If found any booked rooms, exclude it from room list
                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $row) {
                        array_push($booked_rooms, $row['room_id']);
                    }
                }

                // Get all active rooms as well as exclude booked rooms
                if (!empty($booked_rooms)):
                    $this->db->where_not_in('room_id', $booked_rooms);
                endif;

                $this->db->order_by("rooms.location, rooms.building, rooms.floor, rooms.name");
                $query = $this->db->get_where($this->table_reference_rooms, array('status' => 1));

                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $row) {
                        $available_rooms[] = $row;
                    }
                }

                return $available_rooms;

                break;

            // Equipment
            case '3':
                $this->db->from($this->table);
                $this->db->select($this->table_reference_booking_equipments_select_field2);
                $this->db->where($where_conditions);

                if (isset($params['booking_with_room'])):
                    $this->db->join($this->table_reference_booking_rooms, "{$this->table_fk_booking_id} =  {$this->table_reference_booking_rooms_pk_id}");
                    $this->db->join($this->table_reference_booking_equipments, "{$this->table_reference_booking_rooms_pk_booking_equipment_id} =  {$this->table_reference_booking_equipments_pk_id}", "left");
                else:
                    $this->db->join($this->table_reference_booking_equipments, "{$this->table_fk_booking_id} =  {$this->table_reference_booking_equipments_pk_id}");
                endif;

                break;
        endswitch;

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }

    function setRecord($data, $id = 0, $delete = false) {
        if ($delete) {
            $id = (strpos($id, ',') !== FALSE) ? "IN ($id)" : "= $id";
            $query = "DELETE FROM {$this->table} WHERE booking_id $id";

            return $this->db->query($query);
        } else {
            if (!empty($id)) {
                $this->db->where('booking_id', $id);

                $result = $this->db->update($this->table, $data);

                return $result;
            } else {
                if (isset($data['insert_batch']) && $data['insert_batch']) {
                    return $this->db->insert_batch($this->table, $data['data']);
                } else {
                    return $this->db->insert($this->table, $data);
                }
            }
        }
    }

    function setBookingStatus($data, $booking_id = 0) {
        if (!empty($booking_id)) {
            $this->db->where('booking_id', $booking_id);

            $result = $this->db->update($this->table, $data);

            return $result;
        } else {
            if (isset($data['insert_batch']) && $data['insert_batch']) {
                return $this->db->insert_batch($this->table, $data['data']);
            } else {
                return $this->db->insert($this->table, $data);
            }
        }
    }

    function getMysqlFormattedDate($date_input) {
        list($date, $month, $year) = explode("/", $date_input);

        return date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $date, $year));
    }

}

/* End of file booking_model.php */
/* Location: ./application/models/booking_model.php */