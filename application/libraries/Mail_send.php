<?php
ob_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class mail_send {

    public function Authentication_send($from = '', $to = '', $subject = '', $cc = '', $bcc = '',$message = '') {

        $CI = & get_instance();
        
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'umdaahealthcare@gmail.com', // change it to yours
            'smtp_pass' => 'Succes5fu!', // change it to yours
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'crlf' =>"\r\n",
            'wordwrap' => TRUE
        );

        //$message = '';
        $CI->load->library('email');
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");  
        $CI->email->from($from);
        $CI->email->to($to);
        $CI->email->subject($subject);

        $body = $message;

        $CI->email->set_mailtype("html");
        $CI->email->message($body);
        if($CI->email->send())
        {

        }
        else
        {
            show_error($this->$CI->print_debugger());
        }

        // $data=$message;

        // $header = "From:" . $from . " \r\n";
        // $header .= "Cc:" . $cc . " \r\n";
        // $header .= "MIME-Version: 1.0\r\n";
        // $header .= "Content-type: text/html\r\n";

        // $retval = mail($to, $subject,$data,$header);

        // if ($retval == true) {
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function Authentication_send_forgot_password($from = '', $to, $subject, $cc = '', $bcc = '',$data =array()) {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
        'smtp_user' => 'amar.palle@suprasoft.com', // change it to yours
        'smtp_pass' => 'Password@@', // change it to yours
        'mailtype' => 'html',
        'charset' => 'iso-8859-1',
        'wordwrap' => TRUE
    );
        $CI = & get_instance();
        $message = '';
        $CI->load->library('email');
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");  
        $CI->email->from($from);
        $CI->email->to($to);
        $CI->email->subject($subject);
        $body = $CI->load->view('forgot_password_mail',$data,TRUE);

        $CI->email->set_mailtype("html");
        $CI->email->message($body);
        $retval=$CI->email->send();
        if ($retval == true) {
            return true;
        } else {
            return false;
        }  
    }


    public function Authentication_send_registration($from = '', $to, $subject, $cc = '', $bcc = '',$data_1 =array()) {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'amar.palle@suprasoft.com', // change it to yours
            'smtp_pass' => 'Password@@', // change it to yours
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );

        $CI = & get_instance();

        $message = '';
        $CI->load->library('email');
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");  
        $CI->email->from($from);
        $CI->email->to($to);
        $CI->email->subject($subject);
        $body = $CI->load->view('registration_mail',$data_1,TRUE);

        $CI->email->set_mailtype("html");
        $CI->email->message($body);
        $retval=$CI->email->send();
        if ($retval == true) {
            return true;
        } else {
            return false;
        }  

    }


    public function Authentication_send_forgot_password_phpmail($from = '', $to, $subject, $cc = '', $bcc = '',$data =array()) {

        $CI = & get_instance();
        $body = $CI->load->view('forgot_password_mail', $data, TRUE);

        $header = "From:" . $from . " \r\n";
        $header .= "Cc:" . $cc . " \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";

        $retval = mail($to, $subject,$body,$header);

        if ($retval == true) {
            return true;
        } else {
            return false;
        }
    }

    public function Content_send_all_mail($from = '', $to, $subject, $c, $bcc = '', $message = '') {

        $CI = & get_instance();
        // $from='uday@beaut.in';
        $from='noreply@umdaa.co';
        
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
            // 'smtp_user' => 'uday@beaut.in', // change it to yours
            'smtp_user' => 'noreply@umdaa.co', // change it to yours
            // 'smtp_pass' => 'Ch@o5mantra', // change it to yours
            'smtp_pass' => 'Noreply@2015', // change it to yours
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'crlf' =>"\r\n",
            'wordwrap' => TRUE
        );

        $CI->load->library('email');
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");  
        $CI->email->from($from);
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->set_mailtype("html");
        $CI->email->message($message);

        if($CI->email->send()){
            return true;
        }else{
            show_error($CI->email->print_debugger());
        }
    }
}

?>