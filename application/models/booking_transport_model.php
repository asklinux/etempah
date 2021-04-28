<?php

class Booking_transport_model extends CI_Model {

    var $table = 'booking_transports';
    var $table_select_total = 'booking_transports, count(booking_transports) as total';
    var $table_group_by_1 = 'booking_transports';
    var $order_by = array(
        'form-list' => 'booking_transports.created ASC'
    );
    var $transport_types = array(
        1 => array('Kereta', 'orang'),
        2 => array('MPV', 'orang'),
        3 => array('Lori', 'tan'),
        4 => array('SUV', 'orang'),
        5 => array('Bas', 'orang'),
    );
    var $trip_types = array(
        1 => 'Satu Hala',
        2 => 'Dua Hala',
    );

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
                ->from($this->table)
                ->group_by($this->table_group_by_1);

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
            $query = "DELETE FROM {$this->table} WHERE id $id";
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
                $query .= "WHERE id=$id";
            }
        }

        return $this->db->query($query);
    }

}

/* End of file booking_transport_model.php */
/* Location: ./application/models/booking_transport_model.php */