<?php

class Email_queue_model extends CI_Model {

    var $table = 'email_queue';
    var $table_pk = 'email_queue.id';
    var $order_by = array(
        'form-list' => 'equipments.name ASC'
    );

    function getAllRecords($limit = 10) {
        $this->db->select("{$this->table}.*")
                ->from($this->table)->limit($limit);

        $this->db->where('success', 0);
        $this->db->where('attempts <', 3);

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

    function setMessageContent($message_type, $data = array()) {
        switch ($message_type):
            case 'newbooking':
                $message_content =
                        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' .
                        "<html>
    <head><title>eTempah :: Tempahan Baru</title></head>
    <body>
        <div style='max-width: 800px; margin: 0; padding: 30px 0;'>
            <table width=80% border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td width='5%'></td>
                    <td align='left' width='95%' style='font: 13px/18px Arial, Helvetica, sans-serif;'>
                        <p>Assalamualaikum Dan Salam Sejahtera</p>
                        <p><strong><span style='text-decoration: underline;'>PEMBERITAHUAN TEMPAHAN BARU</span></strong></p>
                        <p>Anda telah menerima satu tempahan baru yang bernombor <strong>{$data['booking_ref_no']}</strong>.</p>
                        <p>Untuk melihat maklumat tempahan tersebut,
                            <a href='" . site_url('/' . $data['booking_type'] .'/view/' . $data['booking_id']) . "' style='color: #3366cc;'>sila klik di sini</a>.
                        </p>
                        <p>Terima kasih,<br />
                        The {$data['site_name']} Team
                        <p><small>Emel ini dihantar secara automatik melalui Sistem eTempah.</small></p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
";
                break;

            case 'approvebooking':
                $message_content =
                        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' .
                        "<html>
    <head><title>eTempah :: Tempahan Baru</title></head>
    <body>
        <div style='max-width: 800px; margin: 0; padding: 30px 0;'>
            <table width=80% border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td width='5%'></td>
                    <td align='left' width='95%' style='font: 13px/18px Arial, Helvetica, sans-serif;'>
                        <p>Assalamualaikum Dan Salam Sejahtera</p>
                        <p><strong><span style='text-decoration: underline;'>PEMBERITAHUAN STATUS TEMPAHAN</span></strong></p>
                        <p>Sukacita dimaklumkan bahawa tempahan yang bernombor <strong>{$data['booking_ref_no']}</strong> <strong>TELAH DILULUSKAN</strong>.</p>
                        <p>Untuk melihat maklumat tempahan tersebut,
                            <a href='" . site_url('/' . $data['booking_type'] .'/view/' . $data['booking_id']) . "' style='color: #3366cc;'>sila klik di sini</a>.
                        </p>
                        <p>Terima kasih,<br />
                        The {$data['site_name']} Team
                        <p><small>Email ini dihantar secara automatik melalui Sistem eTempah.</small></p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
";
                break;

            case 'rejectbooking':
                $message_content =
                        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' .
                        "<html>
    <head><title>eTempah :: Tempahan Baru</title></head>
    <body>
        <div style='max-width: 800px; margin: 0; padding: 30px 0;'>
            <table width=80% border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td width='5%'></td>
                    <td align='left' width='95%' style='font: 13px/18px Arial, Helvetica, sans-serif;'>
                        <p>Assalamualaikum Dan Salam Sejahtera</p>
                        <p><strong><span style='text-decoration: underline;'>PEMBERITAHUAN STATUS TEMPAHAN</span></strong></p>
                        <p>Dukacita dimaklumkan bahawa tempahan yang bernombor <strong>{$data['booking_ref_no']}</strong> <strong>TELAH DITOLAK</strong>.</p>
                        <p>Untuk melihat maklumat tempahan tersebut,
                            <a href='" . site_url('/' . $data['booking_type'] .'/view/' . $data['booking_id']) . "' style='color: #3366cc;'>sila klik di sini</a>.
                        </p>
                        <p>Terima kasih,<br />
                        The {$data['site_name']} Team
                        <p><small>Email ini dihantar secara automatik melalui Sistem eTempah.</small></p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
";
                break;

            case 'cancelbooking':
                $message_content =
                        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' .
                        "<html>
    <head><title>eTempah :: Tempahan Baru</title></head>
    <body>
        <div style='max-width: 800px; margin: 0; padding: 30px 0;'>
            <table width=80% border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td width='5%'></td>
                    <td align='left' width='95%' style='font: 13px/18px Arial, Helvetica, sans-serif;'>
                        <p>Assalamualaikum Dan Salam Sejahtera</p>
                        <p><strong><span style='text-decoration: underline;'>PEMBERITAHUAN STATUS TEMPAHAN</span></strong></p>
                        <p>Dukacita dimaklumkan bahawa tempahan yang bernombor <strong>{$data['booking_ref_no']}</strong> <strong>TELAH DIBATALKAN</strong>.</p>
                        <p>Untuk melihat maklumat tempahan tersebut,
                            <a href='" . site_url('/' . $data['booking_type'] .'/view/' . $data['booking_id']) . "' style='color: #3366cc;'>sila klik di sini</a>.
                        </p>
                        <p>Terima kasih,<br />
                        The {$data['site_name']} Team
                        <p><small>Email ini dihantar secara automatik melalui Sistem eTempah.</small></p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
";
                break;
        endswitch;

        return mysql_real_escape_string($message_content);
    }

}

/* End of file email_queue.php */
/* Location: ./application/models/email_queue.php */
