<?php
ob_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class mail_send {

    public function Authentication_send($from = '', $to, $subject, $cc = '', $bcc = '',$message) {

 

        if($from==""){
       // $from = "palleamar123@gmail.com";
        }
        $CI = & get_instance();
        //$body = $CI->load->view('email_template', $data, TRUE);
       // $body = $this->mail_log($from, $to, $subject, $body, $cc = '', $bcc = '');
        $data=$message;

        $header = "From:" . $from . " \r\n";
        $header .= "Cc:" . $cc . " \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        
        // $subject="tesxt";
        //   $body="tesxt";  
        $retval = mail($to, $subject,$data,$header);
         //$retval = mail ('amar.palle@suprasoft.com',$subject,$body,$header);
        //$retval=true;
        if ($retval == true) {
            return true;
        } else {
             return false;
        }
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

       // //  if($from==""){
       // // // $from = "palleamar123@gmail.com";
       // //  }
       //  $CI = & get_instance();
       //  $body = $CI->load->view('forgot_password_mail', $data, TRUE);

       // // $body = $this->mail_log($from, $to, $subject, $body, $cc = '', $bcc = '');
       //  //$data=$message;

       //  $header = "From:" . $from . " \r\n";
       //  $header .= "Cc:" . $cc . " \r\n";
       //  $header .= "MIME-Version: 1.0\r\n";
       //  $header .= "Content-type: text/html\r\n";
        
       //  // $subject="tesxt";
       //  //   $body="tesxt";  
       //  $retval = mail($to, $subject,$body,$header);
       //   //$retval = mail ('amar.palle@suprasoft.com',$subject,$body,$header);
       //  //$retval=true;
       //  if ($retval == true) {
       //      return true;
       //  } else {
       //       return false;
       //  }
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


       //  if($from==""){
       // // $from = "palleamar123@gmail.com";
       //  }
        $CI = & get_instance();
        $body = $CI->load->view('forgot_password_mail', $data, TRUE);

       // $body = $this->mail_log($from, $to, $subject, $body, $cc = '', $bcc = '');
        //$data=$message;

        $header = "From:" . $from . " \r\n";
        $header .= "Cc:" . $cc . " \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        
        // $subject="tesxt";
        //   $body="tesxt";  
        $retval = mail($to, $subject,$body,$header);
         //$retval = mail ('amar.palle@suprasoft.com',$subject,$body,$header);
        //$retval=true;
        if ($retval == true) {
            return true;
        } else {
             return false;
        }
    }

   

}

?>