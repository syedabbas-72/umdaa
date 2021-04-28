<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_Billing extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}

public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
$cond = '';
if(count($this->input->post())>0){
	$data['from'] = $this->input->post('date_from');
	$data['to'] = $this->input->post('date_to');
	
if($clinic_id!=0)
	$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
else
	$cond = "where b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
	}
	else{
		if($clinic_id!=0)
	$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and b.created_date_time like'".date('Y-m-d')."%'";
else
	$cond = "where b.billing_type='Pharmacy'  and b.created_date_time like'".date('Y-m-d')."%'";
	}

$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,c.clinic_name,sum(bi.amount) as bamount
FROM `billing` b
left join billing_line_items bi on b.billing_id=bi.billing_id
left join patients p on p.patient_id=b.patient_id
left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
group by bi.billing_id order by b.billing_id desc")->result();
/*  "SELECT a.*,c.clinic_name FROM `appointments` a 
left join clinics c on a.clinic_id = c.clinic_id 
left join patients p on p.patient_id = a.patient_id
left join doctors d on d.doctor_id = a.doctor_id
order by a.appointment_id desc"*/
	$data['view'] = 'billing/billing';
    $this->load->view('layout', $data);
}
public function billing_report($from='',$to='')
{
	$clinic_id = $this->session->userdata('clinic_id');
$cond = '';

	$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($from)) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($to)) ."'";
	$data['report_heading'] = "Report Date From : ".date("d M Y",strtotime($from))." Date Till : ".date("d M Y",strtotime($to));

$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,p.umr_no,c.clinic_name,sum(bi.amount) as bamount
FROM `billing` b
left join billing_line_items bi on b.billing_id=bi.billing_id
left join patients p on p.patient_id=b.patient_id
left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
group by bi.billing_id order by b.billing_id")->result();

	$this->load->library('M_pdf');
    $html = $this->load->view('new_order/billing_report',$data,true);
    $pdfFilePath = "pharmacy".date("MdY").".pdf";
    $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");




	$this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F");
	redirect('uploads/billings/'.$pdfFilePath);
}
}
?>