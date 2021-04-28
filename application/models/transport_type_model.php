<?php

class Transport_type_model extends CI_Model {

    var $table = 'transport_types';
    var $table_pk = 'transport_types.transport_type_id';
    var $table_reference_transport_fk_id = 'transports.transport_type';
    var $table_reference_transport = 'transports';
    var $table_select_total = 'transport_type, count(transport_type) as total, transport_type_id, transport_type_name, transport_type_cargo';
    var $table_group_by_1 = 'transport_type';
    var $order_by = array(
	'form-list' => 'transport_types.transport_type_name ASC'
    );
    var $cargo_types = array(
	1 => 'Orang',
	2 => 'Kargo',
    );
    var $cargo_types_name = array(
	1 => 'orang',
	2 => 'tan',
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
//	$this->db->select("*, {$this->table_select_total}")
//		->from($this->table)
//		->join($this->table_reference_transport, "{$this->table_pk} = {$this->table_reference_transport_fk_id}", 'left outer')
//		->group_by($this->table_group_by_1);
	$query = $this->db->query("select * from transport_types types
	    left outer join (select transport_type, count(transport_type) as total from transports group by transport_type) items
	    on types.transport_type_id = items.transport_type order by total DESC");

//"select * from transport_types a
//left outer join
//(select transport_type,count(transport_type) from transports group by transport_type) b
//on a.transport_type_id = b.transport_type"
//	$query = $this->db->get();

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