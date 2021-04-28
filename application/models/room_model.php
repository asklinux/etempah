<?php

class Room_model extends CI_Model {

    var $table = 'rooms';
    var $table_pk = 'rooms.room_id';
    var $table_fk_1 = 'rooms.fk_user_id';
    var $table_reference_1 = 'users';
    var $table_reference_1_pk = 'users.id';
    var $table_reference_1_email = 'users.email';
    var $table_reference_alias_1 = 'users.username as penyelia';
    var $order_by = array(
        'form-list' => 'rooms.location ASC, rooms.building ASC, rooms.floor ASC, rooms.name ASC'
    );
    var $status = array(
        1 => 'Aktif',
        0 => 'Tidak Aktif',
    );
    var $has_one = array('user');

    function getAllRecords($list_type = 'all') {
        switch ($list_type):
            case 'all':

                if ($this->tank_auth->is_admin()):
                    $this->db->select("{$this->table}.*")
                            ->from($this->table);
                elseif ($this->tank_auth->is_subadmin()):
                    $this->db->select("{$this->table}.*, {$this->table_reference_alias_1}")
                            ->from($this->table)->where("{$this->table_fk_1} = {$this->tank_auth->get_user_id()}")
                            ->join($this->table_reference_1, "{$this->table_fk_1} = {$this->table_reference_1_pk}");
                endif;

                break;

            case 'dropdown':
                $this->db->select("{$this->table}.*")
                        ->from($this->table)
                        ->where('status = 1');
                break;
        endswitch;

        $this->db->order_by($this->order_by['form-list']);

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

    function getRecordByUserId($room_id) {


        $this->db->select("{$this->table_reference_1}.*")
                ->from($this->table)->where("{$this->table_pk} = '$room_id'")
                ->join($this->table_reference_1, "{$this->table_fk_1} = {$this->table_reference_1_pk}");

        $query = $this->db->get();

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

/* End of file room.php */
/* Location: ./application/models/room.php */