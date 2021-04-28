<?php

class Destination_model extends CI_Model {

    var $table = 'destinations';

    function getAllRecords() {
        $this->db->select("{$this->table}.*")
                ->from($this->table)
                ->order_by('destinations.destination_name ASC');


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

    function setRecord($data, $destination_name = '', $delete = false) {
        if ($delete) {
            $query = "DELETE FROM {$this->table} WHERE destination_name=$destination_name";
        } else {
            $query = "INSERT IGNORE INTO {$this->table} SET destination_name='$data'";
        }

        return $this->db->query($query);
    }

}

/* End of file destination_model.php */
/* Location: ./application/models/destination_model.php */