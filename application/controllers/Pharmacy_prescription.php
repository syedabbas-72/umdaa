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
        $this->db->select('PP.patient_prescription_id, PP.patient_id, PP.doctor_id, PP.appointment_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name,P.title, P.first_name as patient_first_name, P.last_name as patient_last_name, P.umr_no, PP.created_date_time as prescription_date, A.appointment_date, A.appointment_time_slot');
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

    //Get Prescriptions List
    public function getPrescriptionsList()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $start = $_POST['startDate'];
        $end = date('Y-m-d', strtotime($_POST['endDate'] . ' +1 day'));
        if($start == $end)
        {
            $where = $this->db->where('PP.clinic_id ="'.$clinic_id.'" and PP.created_date_time LIKE "'.$start.'%"');
        }
        else
        {
            $where = $this->db->where('PP.clinic_id ="'.$clinic_id.'" and PP.created_date_time BETWEEN "'.$start.'%" AND "'.$end.'%"');
        }


        // Get Patient Prescriptions Date wise (DESC)
        $this->db->select('PP.patient_prescription_id, PP.patient_id, PP.doctor_id, PP.appointment_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name,P.title, P.first_name as patient_first_name, P.last_name as patient_last_name, P.umr_no, PP.created_date_time as prescription_date, A.appointment_date, A.appointment_time_slot');
        $this->db->from('patient_prescription PP');
        $this->db->join('patients P','PP.patient_id = P.patient_id','inner');
        $this->db->join('doctors Doc','PP.doctor_id = Doc.doctor_id','inner');
        $this->db->join('appointments A','PP.appointment_id = A.appointment_id','inner');
        $where;
        $this->db->order_by("PP.created_date_time","DESC");

        $patient_prescription = $this->db->get()->result();
        $i=1;
        // if(count($patient_prescription)<=0)
        // {
        //     ?>
            <!--  <tr>
                <td colspan="5" class="text-center">
                     <span>No Prescriptions Found On This Date.</span>
                 </td>
             </tr> -->
             <?php
        // }
        // else
        // {
            foreach($patient_prescription as $prescription){
                $check = $this->db->select("*")->from("patient_prescription_drug")->where("patient_prescription_id='".$prescription->patient_prescription_id."'")->get()->num_rows();
                if($check<=0)
                {
                    continue;
                }
                $total +=$bills->amount;
                $title = '';
                if($prescription->title!="")
                    $title = $prescription->title.". ";
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <span><?php echo $title.ucwords(strtolower($prescription->patient_first_name." ".$prescription->patient_last_name)); ?></span><br>
                        <?php echo $prescription->umr_no; ?>
                    </td>
                    <td>
                        <span><?php echo "Dr. ".ucwords($prescription->doc_first_name." ".$prescription->doc_last_name); ?></span><br>
                        <?php echo "On ".date("d-m-Y",strtotime($prescription->appointment_date))." @ ".date("H:i a", strtotime($prescription->appointment_time_slot)); ?>
                    </td>
                    <td><?php echo date("d-m-Y",strtotime($prescription->prescription_date)); ?></td>
                    <td class="actions">
                        <a data-toggle="modal" data-target="#viewPrescriptionModal" class="viewPrescription" id="<?=$prescription->patient_prescription_id?>"><i class="fa fa-eye viewSmall"></i></a>
                        <a href="<?php echo base_url('New_order/add_order/'.$prescription->patient_prescription_id);?>"><i class="fa fa-shopping-basket cartSmall"></i></a> 
                        <a href="<?php echo base_url('Pharmacy_prescription/print/'.$prescription->patient_prescription_id);?>" target="blank"><i class="fa fa-print cartSmall"></i></a>                                                                        
                    </td>
                </tr>
            <?php }
        // }
        

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


    public function getPrescription(){

        $pid = $_POST['prescriptionId'];

        $this->db->select('pd.drug_id,pd.remarks,pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks');
        $this->db->from('patient_prescription_drug pd');
        $this->db->join('drug d', 'pd.drug_id=d.drug_id','left');
        $this->db->where('pd.patient_prescription_id',$pid);
        $data['patient_prescription_drug'] = $this->db->get()->result();
        
        $this->db->select('*');
        $this->db->from('patients p');
        $this->db->join('patient_prescription pp', 'p.patient_id = pp.patient_id');
        $this->db->where('pp.patient_prescription_id',$pid);
        $patient_info = $this->db->get()->row();
        $title = '';
        if($patient_info->title!="")
            $title = $patient_info->title.". ";
        if($patient_info->title!="")
            $address_line = $patient_info->address_line.", ";
        $patient['patient_name'] = $title.$patient_info->first_name." ".$patient_info->last_name;
        $patient['umr_no'] = $patient_info->umr_no;
        $patient['qrcode'] = $patient_info->qrcode;
        $patient['address'] = $address_line.$patient_info->location;
        $patient['cartLink'] = base_url('New_order/add_order/'.$patient_info->patient_prescription_id);
        echo json_encode($patient)."*$";
        $i = 1;
        foreach ($data['patient_prescription_drug'] as $key => $value) { 
          $M = 0;
          $dayM = "M";
          $dayA = "A";
          $dayN = "N";
          $N = 0;
          $A = 0;
          $dose = 1;
          $Mday = '';

            if($value->preffered_intake == "AF"){
              $intake = "After Food";
            }
            if($value->preffered_intake == "BF"){
              $intake = "Before Food";
            }
          


            if($value->day_schedule !=""){
            $split_schedule = explode(",",$value->day_schedule);

        if(in_array("M", $split_schedule)){
          $M = "<span style='font-size:20px'>&#10004;</span>";
          $dayM = "<span>M</span>";
       
        }
        else{
          $M = "<span style='font-size:20px'>&#215;</span>";
          $dayM = "<span>M</span>";
        }
         if(in_array("A", $split_schedule)){
          $A = "<span style='font-size:20px'>&#10004;</span>";
          $dayA = "<span>A</span>";
       
        }
        else{
          $A = "<span style='font-size:20px'>&#215;</span>";
          $dayA = "<span>A</span>";
        }
         if(in_array("N", $split_schedule)){
          $N = "<span style='font-size:20px'>&#10004;</span>";
          $dayN = "<span>N</span>";
       
        }
        else{
          $N = "<span style='font-size:20px'>&#215;</span>";
          $dayN = "<span>N</span>";
        }
          }
          ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><label class="font-weight-bold"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></label>
                  <span class="formulation"><?php echo $value->formulation; ?></span>
                  <p style="padding: 0px !important;"><?=$value->composition?></p>
                
                </td>
                <td class="text-center">
                  <?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
                  <span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 13px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
                <?php } else { ?>
                  <span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
                <?php } ?>

                </td>
                
                <td>
                    <b><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." Days"; } ?><br> <?php   if($intake!="" || $intake!=NULL){ echo "(".$intake.")" ;} ?></b>
                </td>
                  <td><b><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></b></td>
                <td> <?php echo ucfirst($value->remarks); ?></td>
              </tr>
             
              <?php 
          }
    }


    public function print($pid){

        $patientPrescription = $this->db->query("select * from patient_prescription where patient_prescription_id='".$pid."'")->row();

        $this->db->select('pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks');
        $this->db->from('patient_prescription_drug pd');
        $this->db->join('drug d', 'pd.drug_id=d.drug_id','left');
        $this->db->where('pd.patient_prescription_id',$pid);
        $data['patient_prescription_drug'] = $this->db->get()->result();

        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $patientPrescription->appointment_id . "'")->get()->row();

    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

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
