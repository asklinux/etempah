<?php

class Booking_food_model extends CI_Model {

        var $table = 'booking_foods';
        var $order_by = array(
            'form-list' => 'booking_foods.created ASC'
        );
        var $food_types = array(
            1 => "Pagi",
            2 => "Tengahari",
            3 => "Petang",
            4 => "Malam",
        );

        function getFoodTypeList($data, $type = 'stringtolist') {
                $result = '';
                if ($type == 'stringtolist') {
                        $selectedFoodTypeIds = explode(',', $data);

                        foreach ($selectedFoodTypeIds as $id) {
                                $result[] = $this->food_types[$id];
                        }

                        $result = implode(', ', $result);
                }
                
                return $result;
        }

        function getAllRecords($order_by = '') {
                $this->db->select("{$this->table}.*")
                        ->from($this->table);

                if (!empty($order_by)) {
                        $this->db->order_by($order_by);
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
                $query = $this->db->get_where($this->table, array('booking_food_id' => $id));

                $result = $query->row();

                return (!empty($result)) ? $result : array();
        }

        function getAjaxRecordWithConditions($where_conditions) {
                $this->db->select()
                        ->from($this->table)
                        ->where($where_conditions);

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
                        $query = "DELETE FROM {$this->table} WHERE booking_food_id $id";
                } else {
                        $fields = array();
                        foreach ($data as $key => $val):
                                $fields[] = "$key='$val'";
                        endforeach;

                        $fields = implode(',', $fields);

                        if (empty($id)) {
                                $query_type = 'INSERT INTO';
                        } else {
                                $query_type = 'UPDATE';
                        }

                        $query = "$query_type {$this->table} SET $fields";

                        if (!empty($id)) {
                                $query .= "WHERE booking_id=$id";
                        }
                }

                return $this->db->query($query);
        }

}

/* End of file booking_transport_model.php */
/* Location: ./application/models/booking_transport_model.php */