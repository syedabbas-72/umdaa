<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}

public function index($patient_id=''){
	$clinic_id = $this->session->userdata('clinic_id');
     $data['patient_id']=$patient_id;
$data['patient_prescription'] = $this->db->query("select *,p.created_date_time as pdate from patient_prescription p inner join patient_prescription_drug pd on(p.patient_prescription_id = pd.patient_prescription_id) inner join patients ps on(p.patient_id = ps.patient_id) where p.clinic_id='".$clinic_id."' and p.patient_id='".$patient_id."' group by p.patient_prescription_id order by p.patient_prescription_id desc")->result();

	$data['view'] = 'profile/prescription_list';
    $this->load->view('layout', $data);
}


public function print($pid){
	    $data['patient_prescription_drug']=$this->db->query("select pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks from patient_prescription_drug pd left join drug d on (pd.drug_id=d.drug_id) where pd.patient_prescription_id='" . $pid . "' ")->result();
	    $data['patient_info']= $this->db->query("select * from patients p inner join patient_prescription pp on(p.patient_id = pp.patient_id) where patient_prescription_id='".$pid."'")->row();
	     $data['clinic_info']= $this->db->query("select * from clinics c inner join patient_prescription pp on(c.clinic_id = pp.clinic_id) where patient_prescription_id='".$pid."'")->row();


 $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['clinic_info']->clinic_id."'")->row();
  
        
    $this->load->library('M_pdf');
    $html = $this->load->view('patients/prescription_pdf', $data, true);
    $pdfFilePath = "prescription_" .$data['patient_info']->patient_id."_".date('dmy').".pdf";
    $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;
 
    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/prescriptions/" . $pdfFilePath, "F");
    redirect("uploads/prescriptions/".$pdfFilePath);
}



}
?>