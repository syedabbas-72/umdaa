<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_prescription extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));         
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');    
    }


    public function index(){

        $clinic_id = $this->session->userdata('clinic_id');

        // Get Patient Prescriptions Date wise (DESC)
        $this->db->select('PP.patient_prescription_id, PP.patient_id, PP.doctor_id, PP.appointment_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, P.first_name as patient_first_name, P.last_name as patient_last_name, P.umr_no, PP.created_date_time as prescription_date, A.appointment_date, A.appointment_time_slot');
        $this->db->from('patient_prescription PP');
        $this->db->join('patients P','PP.patient_id = P.patient_id','inner');
        $this->db->join('doctors Doc','PP.doctor_id = Doc.doctor_id','inner');
        $this->db->join('appointments A','PP.appointment_id = A.appointment_id','inner');
        $this->db->where('PP.clinic_id =',$clinic_id);
        $this->db->order_by("PP.created_date_time","DESC");

        $data['patient_prescription'] = $this->db->get()->result();
        $data['view'] = 'patients/prescription_list';

        $this->load->view('layout', $data);

        // $this->db->select('*,p.created_date_time as pdate');
        // $this->db->from('patient_prescription p');
        // $this->db->join('patient_prescription_drug pd', 'p.patient_prescription_id = pd.patient_prescription_id');
        // $this->db->join('patients ps', 'p.patient_id = ps.patient_id');
        // $this->db->where('p.clinic_id',$clinic_id);
        // $this->db->group_by("p.patient_prescription_id");
        // $this->db->order_by('p.patient_prescription_id','desc');
        // $data['patient_prescription'] = $this->db->get()->result();

        // $data['view'] = 'patients/prescription_list';
        // $this->load->view('layout', $data);

    }


    public function view_prescription($pid){

        $this->db->select('pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks');
        $this->db->from('patient_prescription_drug pd');
        $this->db->join('drug d', 'pd.drug_id=d.drug_id','left');
        $this->db->where('pd.patient_prescription_id',$pid);
        $data['patient_prescription_drug'] = $this->db->get()->result();

        $this->db->select('*');
        $this->db->from('patients p');
        $this->db->join('patient_prescription pp', 'p.patient_id = pp.patient_id');
        $this->db->where('pp.patient_prescription_id',$pid);
        $data['patient_info'] = $this->db->get()->row();
        $data['view'] = 'patients/view_prescription';
        $this->load->view('layout', $data);
    }


    public function print($pid){

        $this->db->select('pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks');
        $this->db->from('patient_prescription_drug pd');
        $this->db->join('drug d', 'pd.drug_id=d.drug_id','left');
        $this->db->where('pd.patient_prescription_id',$pid);
        $data['patient_prescription_drug'] = $this->db->get()->result();

        $data['patient_info'] = $this->db->select('*')->from('patients p')->join('patient_prescription pp', 'p.patient_id = pp.patient_id')->where('pp.patient_prescription_id',$pid)->get()->row();

        $data['clinic_info'] = $this->db->select('*')->from('clinics')->where('clinic_id',$this->session->userdata('clinic_id'))->get()->row();

        $data['pdf_settings'] = $pdf_settings = $this->Generic_model->getSingleRecord('clinic_pdf_settings',array('clinic_id'=>$this->session->userdata('clinic_id')),'');

        
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
