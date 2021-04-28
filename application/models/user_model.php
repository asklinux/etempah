<?php

class User_model extends CI_Model {

    var $table = 'users';
    var $table_pk_id = 'users.id';
    var $table_reference_user_profiles = 'user_profiles';
    var $table_reference_user_profiles_select_fields = 'user_profiles.full_name, user_profiles.position_name, user_profiles.position_level, user_profiles.department_name, user_profiles.contact_office, user_profiles.contact_mobile';
    var $table_reference_user_profiles_fk_id = 'user_profiles.fk_user_id';
    var $user_levels = array(
        0 => 'Pengguna',
        1 => 'Pentadbir Utama',
        2 => 'Pentadbir Bahagian',
        3 => 'Pentadbir Pemandu',
    );
    var $user_status = array(
        0 => 'Belum Aktif',
        1 => 'Aktif',
        2 => 'Tidak Aktif',
    );

    function getAllRecords($where_clause = '') {
        $this->db->select("{$this->table}.*, {$this->table_reference_user_profiles}.*")
                ->from($this->table)->where('deleted != 1')
                ->join($this->table_reference_user_profiles, "{$this->table_pk_id} = {$this->table_reference_user_profiles_fk_id}", 'left');

        if (!empty($where_clause)) {
            $this->db->where($where_clause);
        }

        $query = $this->db->get();

        $result = array();
        foreach ($query->result() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    function getRecordById($id) {
        $this->db->select("{$this->table}.*, {$this->table_reference_user_profiles}.*");
        $this->db->from($this->table);
        $this->db->join($this->table_reference_user_profiles, "{$this->table_pk_id} = {$this->table_reference_user_profiles_fk_id}", 'left');
        $this->db->where("{$this->table_pk_id} = $id");
        $this->db->limit(1);

        $query = $this->db->get();

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }
    
    function getRecordByEmail($email) {
        $this->db->select("{$this->table}.*, {$this->table_reference_user_profiles}.*");
        $this->db->from($this->table);
        $this->db->join($this->table_reference_user_profiles, "{$this->table_pk_id} = {$this->table_reference_user_profiles_fk_id}", 'left');
        $this->db->where("users.email = '$email'");
        $this->db->limit(1);

        $query = $this->db->get();

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }

    function setRecord($data, $id = 0, $delete = false) {
        if ($delete) {
            $id = (strpos($id, ',') !== FALSE) ? "IN ($id)" : "= $id";
            //$query = "DELETE FROM {$this->table} WHERE id $id";

            $query = "
            DELETE {$this->table}, {$this->table_reference_user_profiles}
            FROM {$this->table}
            JOIN user_profiles ON users.id = user_profiles.fk_user_id
            WHERE users.id $id";

            return $this->db->query($query);
        } else {
            if (!empty($id)) {
                $this->db->where('id', $id);

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

    function setRecordForUserProfiles($data, $id = 0, $delete = false) {
        if ($delete) {
            $id = (strpos($id, ',') !== FALSE) ? "IN ($id)" : "= $id";
            $query = "DELETE FROM {$this->table_reference_user_profiles} WHERE fk_user_id $id";

            return $this->db->query($query);
        } else {
            if (!empty($id)) {
                $this->db->where($this->table_reference_user_profiles_fk_id, $id);

                $result = $this->db->update($this->table_reference_user_profiles, $data);

                return $result;
            } else {
                if (isset($data['insert_batch']) && $data['insert_batch']) {
                    return $this->db->insert_batch($this->table_reference_user_profiles, $data['data']);
                } else {
                    return $this->db->insert($this->table_reference_user_profiles, $data);
                }
            }
        }
    }

    function getRecordHeadDriver($user_level = 3) {
        $this->db->select("{$this->table}.*, {$this->table_reference_user_profiles}.*");
        $this->db->from($this->table);
        $this->db->join($this->table_reference_user_profiles, "{$this->table_pk_id} = {$this->table_reference_user_profiles_fk_id}", 'left');
        $this->db->where("{$this->table}.user_level = $user_level");
        $this->db->limit(1);

        $query = $this->db->get();

        $result = $query->row();

        return (!empty($result)) ? $result : array();
    }

    function getUniqueness($str) {
        $this->db->select("{$this->table}.email")
                ->from($this->table)
                ->where("{$this->table}.email = '$str'")
                ->limit(1);

        $query = $this->db->get();

        return $query;
    }

}

/* End of file user.php */
/* Location: ./application/models/user.php */