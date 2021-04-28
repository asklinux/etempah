<?php

class Booking_equipment_model extends CI_Model {

        var $table = 'booking_equipments';
        var $order_by = array(
            'form-list' => 'booking_equipments.created DESC'
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

/* End of file transport.php */
/* Location: ./application/models/transport.php */