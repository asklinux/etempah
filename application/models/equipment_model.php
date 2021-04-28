<?php

class Equipment_model extends CI_Model {

    var $table = 'equipments';
    var $table_pk = 'equipments.equipment_id';
    var $table_deleted = 'equipments.deleted';
    var $order_by = array(
        'form-list' => 'equipments.name ASC'
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
        $query = $this->db->get_where($this->table, array($this->table_pk => $id));

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }

    function setRecord($data, $id = 0, $delete = false) {
        if ($delete) {
            $id = (strpos($id, ',') !== FALSE) ? "IN ($id)" : "= $id";
            $query = "UPDATE {$this->table} SET deleted = 1 WHERE {$this->table_pk} $id";

            //$query = "DELETE FROM {$this->table} WHERE {$this->table_pk} $id";\
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
                $query .= "WHERE {$this->table_pk}=$id";
            }
        }

        return $this->db->query($query);
    }

}

/* End of file equipment.php */
/* Location: ./application/models/equipment.php */