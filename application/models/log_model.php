<?php

class Log_model extends CI_Model {

    var $table = 'logs';
    var $table_reference = 'user_profiles';

    function getAllRecords($params = array()) {
        $this->db->select("{$this->table}.*, {$this->table_reference}.full_name")
                ->from($this->table)->join($this->table_reference, "{$this->table}.fk_user_id={$this->table_reference}.fk_user_id", 'left');

        $this->db->order_by('logs.created DESC');
        
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

    function getRecordByUserId($params) {
        $this->db->join($this->table_reference, "{$this->table}.fk_user_id={$this->table_reference}.fk_user_id", 'left');
        $result = array();
        
        if (isset($params['pagination']['page']) && !empty($params['pagination']['page']) && ($params['pagination']['page'] > 1) && isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page'], $params['pagination']['per_page'] * ($params['pagination']['page'] - 1));
        } elseif (isset($params['pagination']['per_page'])) {
            $this->db->limit($params['pagination']['per_page']);
        }
        $this->db->where('logs.fk_user_id', $params['user_id']);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }
    
    public function getCountLogs($user_id = 0) {
        $this->db->select("{$this->table}.*")
                ->from($this->table);
        
        if (!empty($user_id)) {
            $this->db->where("logs.fk_user_id", $user_id);
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    function setRecord($item_id, $activity_code) {

        $fields = "fk_user_id={$this->tank_auth->get_user_id()}, activity='" . $this->setActivity($activity_code) . "$item_id'";

        $query_type = 'INSERT INTO';
        $query = "$query_type {$this->table} SET $fields";

        return $this->db->query($query);
    }

    function setActivity($activity_code) {
        switch ($activity_code):
            case 110:$log_string = "Tambah rekod pengguna dengan ID pengguna: ";
                break;
            case 111:$log_string = "Nyahaktifkan rekod pengguna dengan ID pengguna: ";
                break;
            case 112:$log_string = "Hapus rekod pengguna dengan ID pengguna: ";
                break;
            case 113:$log_string = "Aktifkan rekod pengguna";
                break;
            case 114:$log_string = "Kemaskini rekod pengguna";
                break;

            case 120:$log_string = "Tambah rekod bilik dengan ID bilik: ";
                break;
            case 121:$log_string = "Menyahaktifkan rekod bilik dengan ID bilik: ";
                break;
            case 122:$log_string = "Hapus rekod bilik dengan ID bilik: ";
                break;
            case 123:$log_string = "Aktifkan rekod bilik dengan ID bilik: ";
                break;
            case 124:$log_string = "Kemaskini rekod bilik dengan ID bilik: ";
                break;

            case 130:$log_string = "Tambah rekod peralatan dengan ID peralatan: ";
                break;
            case 131:$log_string = "Menyahaktifkan rekod peralatan dengan ID peralatan: ";
                break;
            case 132:$log_string = "Hapus rekod peralatan dengan ID peralatan: ";
                break;
            case 133:$log_string = "Aktifkan rekod peralatan dengan ID peralatan: ";
                break;
            case 134:$log_string = "Kemaskini rekod peralatan dengan ID peralatan: ";
                break;
            
            case 140:$log_string = "Tambah rekod kenderaan dengan ID kenderaan: ";
                break;
            case 141:$log_string = "Menyahaktifkan rekod kenderaan dengan ID kenderaan: ";
                break;
            case 142:$log_string = "Hapus rekod kenderaan dengan ID kenderaan: ";
                break;
            case 143:$log_string = "Aktifkan rekod kenderaan dengan ID kenderaan: ";
                break;
            case 144:$log_string = "Kemaskini rekod kenderaan dengan ID kenderaan: ";
                break;
            
            case 150:$log_string = "Tambah rekod jenis kenderaan dengan ID jenis kenderaan: ";
                break;
            case 151:$log_string = "Menyahaktifkan rekod jenis kenderaan dengan ID jenis kenderaan: ";
                break;
            case 152:$log_string = "Hapus rekod jenis kenderaan dengan ID jenis kenderaan: ";
                break;
            case 153:$log_string = "Aktifkan rekod jenis kenderaan dengan ID jenis kenderaan: ";
                break;
            case 154:$log_string = "Kemaskini rekod jenis kenderaan dengan ID jenis kenderaan: ";
                break;

            case 220:$log_string = "Tambah rekod tempahan bilik dengan ID tempahan: ";
                break;
            case 221:$log_string = "Hapus rekod tempahan bilik dengan ID tempahan: ";
                break;
            case 222:$log_string = "Meluluskan rekod tempahan bilik dengan ID tempahan: ";
                break;
            case 223:$log_string = "Menolak rekod tempahan bilik dengan ID tempahan: ";
                break;
            case 224:$log_string = "Membatalkan rekod tempahan bilik dengan ID tempahan: ";
                break;

            case 230:$log_string = "Tambah rekod tempahan peralatan dengan ID tempahan: ";
                break;
            case 231:$log_string = "Hapus rekod tempahan peralatan dengan ID tempahan: ";
                break;
            case 232:$log_string = "Meluluskan rekod tempahan peralatan dengan ID tempahan: ";
                break;
            case 233:$log_string = "Menolak rekod tempahan peralatan dengan ID tempahan: ";
                break;
            case 234:$log_string = "Membatalkan rekod tempahan peralatan dengan ID tempahan: ";
                break;

            case 240:$log_string = "Tambah rekod tempahan makanan dengan ID tempahan: ";
                break;
            case 241:$log_string = "Hapus rekod tempahan makanan dengan ID tempahan: ";
                break;
            case 242:$log_string = "Meluluskan rekod tempahan makanan dengan ID tempahan: ";
                break;
            case 243:$log_string = "Menolak rekod tempahan makanan dengan ID tempahan: ";
                break;
            case 244:$log_string = "Membatalkan rekod tempahan makanan dengan ID tempahan: ";
                break;

            case 250:$log_string = "Tambah rekod tempahan kenderaan dengan ID tempahan: ";
                break;
            case 251:$log_string = "Hapus rekod tempahan kenderaan dengan ID tempahan: ";
                break;
            case 252:$log_string = "Meluluskan rekod tempahan kenderaan dengan ID tempahan: ";
                break;
            case 253:$log_string = "Menolak rekod tempahan kenderaan dengan ID tempahan: ";
                break;
            case 254:$log_string = "Membatalkan rekod tempahan kenderaan dengan ID tempahan: ";
                break;

        endswitch;

        return $log_string;
    }

}

/* End of file booking_transport_model.php */
/* Location: ./application/models/booking_transport_model.php */