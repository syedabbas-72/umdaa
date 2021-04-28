<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller {
public function __construct() 
{
    parent::__construct();
	$this->load->library('mail_send', array('mailtype'=>'html'));		 

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
}
public function index(){
    $data['notification_list']=$this->db->query('select n.*, c.clinic_name, e.first_name, a.appointment_time_slot, p.last_name, p.umr_no from notiffication n inner join clinics c on n.clinic_id=c.clinic_id inner join employees e on n.employee_id=e.employee_id inner join appointments a on n.appointment_id=a.appointment_id inner join patients p on n.patient_id=p.patient_id')->result();
	$data['view'] = 'notification/notification_list';
    $this->load->view('layout', $data);
}

   
 }