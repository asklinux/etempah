<?php

class Transport_model extends CI_Model {

    var $table = 'transports';
    var $table_pk = 'transports.transport_id';
    var $table_fk = 'transports.transport_type';
    var $table_reference_transport_types = 'transport_types';
    var $table_reference_transport_types_fk_id = 'transport_types.transport_type_id';
    var $table_select_total = 'transport_type, count(transport_type) as total, transport_type_id, transport_type_name, transport_type_cargo';
    var $table_group_by_1 = 'transport_type';
    var $order_by = array(
        'form-list' => 'transports.name ASC'
    );

    function getAllRecords($order_by = '', $where = '') {
        $this->db->select("{$this->table}.*, {$this->table_reference_transport_types}.*")
                ->from($this->table)
                ->join($this->table_reference_transport_types, "{$this->table_fk} = {$this->table_reference_transport_types_fk_id}", 'left');

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }
	
	if (!empty($where)) {
	    $this->db->where($where);
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
		->join($this->table_reference_transport_types, "{$this->table_fk} = {$this->table_reference_transport_types_fk_id}", 'right')
		->where('status = 1')
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
        $query = $this->db->get_where($this->table, array($this->table_pk => $id));

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }

    function setRecord($data, $id = 0, $delete = false) {
        if ($delete) {
            $id = (strpos($id, ',') !== FALSE) ? "IN ($id)" : "= $id";
            $query = "DELETE FROM {$this->table} WHERE {$this->table_pk} $id";
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

/* End of file transport.php */
/* Location: ./application/models/transport.php */