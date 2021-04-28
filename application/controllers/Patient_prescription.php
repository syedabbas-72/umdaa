<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient_prescription extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
   // $data['prescription_list']=$this->db->query('select * from patient_prescription_drug a inner join patient_prescription b on a.patient_prescription_id=b.patient_prescription_id inner join drug c on c.drug_id=a.drug_id inner join clinics d on b.clinic_id=d.clinic_id')->result();
  $data['prescription_list'] = $this->db->query("select * from patient_prescription a inner join  patients b on (a.patient_id  = b.patient_id) inner join clinics c on (a.clinic_id = c.clinic_id)")->result();
    $data['view'] = 'patients/patient_prescription_list';
    $this->load->view('layout', $data);
}
public function prescription_update($id){
    
    $data['drug_list']=$this->db->query('select * from drug')->result();

    $data['prescription_list']=$this->db->query('select a.*, b.patient_prescription_id, b.patient_id, b.appointment_id from patient_prescription_drug a inner join patient_prescription b on a.patient_prescription_id=b.patient_prescription_id where b.patient_prescription_id='.$id)->row();
   
    $data['drug']=$this->db->query('select * from drug a inner join patient_prescription_drug b on a.drug_id=b.drug_id where patient_prescription_id='.$data['prescription_list']->patient_prescription_id)->result();

    $data['patient'] = $this->db->query('select * from patients where patient_id='.$data['prescription_list']->patient_id)->row();
  
    $data['doctor'] = $this->db->query('select * from doctors a inner join appointments b on a.doctor_id=b.doctor_id where appointment_id='.$data['prescription_list']->appointment_id)->row();
    $data['clinic']=$this->db->query('select * from clinics where clinic_id='.$data['prescription_list']->patient_prescription_id)->row();

    $data['view'] = 'patients/prescription_edit';
    $this->load->view('layout', $data);
    
   
}
public function autocomplete(){

  $query = $this->input->post('query');
        $data = $this->db->query("select drug_id, CONCAT(trade_name, '( ', available_quantity,' )') as trade_name from drug where trade_name like '".$query."%'")->result_array();

        $result = array();
        foreach ($data as $key => $value) {
          $result[] = $value;
        }

        echo json_encode( $result);
     
    }  



public function prescription_edit(){
    $new_drug['patient_prescription_id'] = $this->input->post('prescrptn_id');   
    $data['drug_list']=$this->db->query('select * from drug')->result();

    $data['prescription_list']=$this->db->query('select a.*, b.patient_prescription_id, b.patient_id, b.appointment_id from patient_prescription_drug a inner join patient_prescription b on a.patient_prescription_id=b.patient_prescription_id where b.patient_prescription_id='. $new_drug['patient_prescription_id'])->row();
   
    $data['drug']=$this->db->query('select * from drug a inner join patient_prescription_drug b on a.drug_id=b.drug_id where patient_prescription_id='.$data['prescription_list']->patient_prescription_id)->result();

    $data['patient'] = $this->db->query('select * from patients where patient_id='.$data['prescription_list']->patient_id)->row();
  
    $data['doctor'] = $this->db->query('select * from doctors a inner join appointments b on a.doctor_id=b.doctor_id where appointment_id='.$data['prescription_list']->appointment_id)->row();
    $data['clinic']=$this->db->query('select * from clinics where clinic_id='.$data['prescription_list']->patient_prescription_id)->row();
       /*  updating quntity with drug_id and patient_prescription_id */
    
    $drug_check = $this->input->post('drg_check');//checkbox
    $d_id = $this->input->post('drg_id');
    $quant = $this->input->post('qty');
   //  echo "<pre>";print_r($drug_check);
   // echo "<pre>";print_r($d_id);
   //  echo "<pre>";print_r($quant);exit;
        

    $i=0;
    foreach($quant as $val){
      $drug_id = $d_id[$i]; 

      for($j=0;$j<=count($drug_check);$j++){
          if($drug_id == $drug_check[$j]){
            $param['quantity'] = $quant[$i];
            $update_qty = $this->Generic_model->updateData('patient_prescription_drug', $param, array('patient_prescription_id'=>$new_drug['patient_prescription_id'], 'drug_id'=>$drug_id));
          }
      }
      
    $i++;

     }
     
        $arry_s = implode(',', $drug_check);
     
 $data['selected_drugs'] = $this->db->query('select * from drug a inner join patient_prescription_drug b on a.drug_id = b.drug_id where patient_prescription_id="'.$data['prescription_list']->patient_prescription_id.'" and b.drug_id in ('.$arry_s.')')->result();

 //print_r($data['selected_drugs']);exit;

     $data['view'] = 'patients/prescription_edit';
  $this->load->view('layout', $data);
     $this->load->library('M_pdf');
    $html = $this->load->view('patients/prescription_invoice',$data,true);
    $pdfFilePath = "test.pdf";
    $this->m_pdf->pdf->WriteHTML($html);
    $this->m_pdf->pdf->Output("./uploads/prescriptions/".$pdfFilePath, "D");   
  
       
}
public function prescription_mrp(){
    
    $drug_id = $this->input->post('drug');
    $prescrptn_id = $this->input->post('prescrptn_id');
    $user_id = $this->session->userdata('user_id');
    //$quantity= $this->input->post('quantity');
    // $insert_data = array(
    //         'patient_prescription_id' => $prescrptn_id,
    //         'drug_id'  =>   $drug_id    
    //         );
    $insert_data['patient_prescription_id'] = $prescrptn_id;
    $insert_data['drug_id'] = $drug_id;
    $insert_data['created_by'] = $user_id;
    $insert_date['modified_by'] = $user_id;
    $insert_date['created_date_time'] = date("Y-m-d H:i:s");
    $insert_date['modified_date_time'] = date("Y-m-d H:i:s");
    $patient_prescription_drug_id=$this->Generic_model->insertDataReturnId('patient_prescription_drug', $insert_data);
    $test_drug = $this->db->query('select * from drug where drug_id='.$drug_id)->row();
    $drug_name=$test_drug->trade_name;
    $mrp = $test_drug->mrp;
    $checked=$test_drug->checked;
    $price_total=$mrp * $quantity;
    echo $price_total."_".$drug_name."_".$mrp;

    }


}
?>