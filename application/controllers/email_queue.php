<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_queue extends CI_Controller {

    function __construct() {
        parent::__construct();

        // this controller can only be called from the command line
        if (!$this->input->is_cli_request()) {
            show_error('Direct access is not allowed');
        }

        $this->load->model('Email_queue_model');
        $this->load->library('email');
    }

    function processEmail() {
        $emails = $this->Email_queue_model->getAllRecords(10);

        if (!empty($emails)):
            foreach ($emails as $email):
                $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
                //$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
                $this->email->to($email->to_email);
                $this->email->subject($email->subject);
                $this->email->message($email->message);
                //$this->email->set_alt_message($this->load->view('email/' . $type . '-txt', $data, TRUE));
                if ($this->email->send()) {
                    $this->Email_queue_model->setRecord(array('success' => 1), $email->id);
                } else {
                    $this->Email_queue_model->setRecord(array('attempts' => ($email->attempts + 1)), $email->id);
                }
            endforeach;
        endif;
    }

    private function _send_email($type, $email, $data) {
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_' . $type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/' . $type . '-html', $data, TRUE));
        //$this->email->set_alt_message($this->load->view('email/' . $type . '-txt', $data, TRUE));
        $this->email->send();
    }
    /*	
    function testNotification() {
        $this->email->from('mohdhasni.ismail@gmail.com', 'MyBooking System');
        $this->email->to('h.vintell@gmail.com');
        $this->email->cc('mhi@oscc.org.my');

        $this->email->subject('Email Test 2');
        $this->email->message('Testing the email class.');

        var_dump($this->email->send());
    }
    */

}

/* End of file email_queue.php */
/* Location: ./application/controllers/email_queue.php */



    
