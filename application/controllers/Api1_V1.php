<?php


defined('BASEPATH') OR exit('No direct script access allowed');

// error_reporting(0);
ini_set('memory_limit', '-1');
define('headers', getallheaders());

require APPPATH . '/libraries/REST_Controller.php';

// require APPPATH . '/controllers/Api.php';

class Api1_V1 extends REST_Controller
{
    public function getMaster($parameters, $method, $user_id)
    {

        $consent_forms = $this->db->select("a.consent_form_id, a.consent_form_title, b.department_id")->from("consent_form a")->join("consent_form_department b", "a.consent_form_id = b.consent_form_id")->where("b.department_id='" . $parameters['department_id'] . "'")->order_by('a.consent_form_title', 'ASC')->get()->result();
        $para['checklist_master'] = $this->db->select("checklist_id,description")->from("checklist_master")->get()->result();

        $i = 0;
        foreach ($consent_forms as $cform) {
            $dept_info = $this->db->query("select department_name from department where department_id='" . $cform->department_id . "'")->row();
            $para['consent_form'][$i]['department_name'] = $dept_info->department_name;
            $para['consent_form'][$i]['consent_form_id'] = $cform->consent_form_id;
            $para['consent_form'][$i]['consent_form_title'] = $cform->consent_form_title;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Consent Form Details ', 'result' => $para, 'requestname' => $method));
    }


    public function patient_consent_form_add($parameters,$method,$user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $consent_form_id = $parameters['consent_form_id'];
        $patient_id = $parameters['patient_id'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id =  $parameters['doctor_id'];

        $cf['consent_form_id'] = $consent_form_id;
        $cf['appointment_id'] = $appointment_id;
        $cf['patient_id'] = $patient_id;
        $cf['clinic_id'] = $clinic_id;
        $cf['doctor_id'] = $doctor_id;
        $cf['created_date_time'] = date('Y-m-d H:i:s');
        $cf['modified_date_time'] = date('Y-m-d H:i:s');
        $pcf_id = $this->Generic_model->insertDataReturnId('patient_consent_forms',$cf);

        $clist = $this->db->select("*")->from("checklist_consent_form cf")
            ->where("cf.patient_consent_form_id='" . $consent_form_id . "' order by cf.position")
            ->get()
            ->result();

        $i = 0;
        foreach ($clist as $cvalue) {
            $insert_checklist['patient_consent_form_id'] = $pcf_id;
            $insert_checklist['checklist_id'] = $cvalue->checklist_id;
            $insert_checklist['doctor_id'] = $doctor_id;
            $insert_checklist['appointment_id'] = $appointment_id;
            $insert_checklist['category'] = strtolower($cvalue->category);
            $insert_checklist['created_by'] = $user_id;
            $insert_checklist['status'] = 1;
            $insert_checklist['modified_by'] = $user_id;
            $insert_checklist['created_date_time'] = date('Y-m-d H:i:s');
            $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('patient_checklist', $insert_checklist);
            $cf[] = $insert_checklist;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $cf, 'requestname' => $method));
    }

    public function prescription_template_delete($parameters, $method, $user_id) 
    {
        extract($parameters);
        $check_exist = $this->db->select("*")->from("prescription_template")->where("prescription_template_id ='" . $parameters['prescription_template_id'] . "'")->get()->row();

        $prescription_template_delete = $parameters['prescription_template_id'];
        // $type = $prescription_template_delete[0]['type'];
        // Delete
        if($type == "del")
        {
            $res = $this->Generic_model->deleteRecord('prescription_template',array('prescription_template_id'=>$parameters['prescription_template_id']));
            $res = $this->Generic_model->deleteRecord('prescription_template_line_items',array('prescription_template_id'=>$parameters['prescription_template_id']));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => NULL, 'requestname' => $method));  
        }
        else if($type == 'edit'){
            $i = 0;
            $prescription_template_edit['prescription_template_name'] = $prescription_template_name;
            $updateRes = $this->Generic_model->updateData('prescription_template', $prescription_template_edit, array('prescription_template_id'=>$prescription_template_id));

        $this->response(array('code' => '200', 'message' => 'Prescription Saved Successfully', 'result' =>NULL, 'requestname' => $method));
        }
        else{
            $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => NULL, 'requestname' => $method));
        }
    }

    public function investigation_template_delete($parameters, $method, $user_id) 
    {
        extract($parameters);
        $check_exist = $this->db->select("*")->from("doctor_investigation_template")->where("investigation_template_id ='" . $parameters['investigation_template_id'] . "'")->get()->row();

        $investigation_template_delete = $parameters['investigation_template_id'];
        // $type = $prescription_template_delete[0]['type'];
        // Delete
        if($type == "del")
        {
            $res = $this->Generic_model->deleteRecord('doctor_investigation_template',array('investigation_template_id'=>$investigation_template_delete[0]['investigation_template_id']));
            $res = $this->Generic_model->deleteRecord('doctor_investigation_template_line_items',array('investigation_template_id'=>$investigation_template_delete[0]['investigation_template_id']));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => NULL, 'requestname' => $method));  
        }
        else if($type == 'edit'){
            $i = 0;
            $investigation_template_edit['investigation_template_name'] = $investigation_template_name;
            $updateRes = $this->Generic_model->updateData('doctor_investigation_template', $investigation_template_edit, array('investigation_template_id'=>$investigation_template_id));

            $this->response(array('code' => '200', 'message' => 'Investigation Saved Successfully', 'result' => NULL, 'requestname' => $method));
        }
        else{
            $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => NULL, 'requestname' => $method));
        }
    }

    // public function get_latestPrescription($parameters, $method, $user_id) {
    //     $getAppointments = $this->db->select("*")->from("appointments")->where("appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."'")->get()->row();
    //     $parent_appointment_id = $getAppointments->parent_appointment_id;
    //     $data['appointment_id'] = $getAppointments->appointment_id;
    //     $data['clinic_id'] = $getAppointments->clinic_id;
    //     $data['doctor_id'] = $getAppointments->doctor_id;
    //     $data['patient_id'] = $getAppointments->patient_id;
    //     $data['umr_no'] = $getAppointments->umr_no;

    //     $parent_appointment = $this->db->select("*")->from("appointments")->where("parent_appointment_id='" . $parameters['appointment_id'] . "'")->order_by("appointment_id","asc")->get()->row();

    //     if (count($parent_appointment) > 0) {

    //         $data['appointment_id'] = $parent_appointment->appointment_id;
    //         $data['next_followup_date'] = $parent_appointment->appointment_date;
    //         $data['time_slot'] = $parent_appointment->appointment_time_slot;
    //         $data['parent_appointment_id'] = $parent_appointment->parent_appointment_id;
    //     } else {
    //         $data['appointment_id'] = "";
    //         $data['next_followup_date'] = "";
    //         $data['time_slot'] = "";
    //         $data['parent_appointment_id'] = "";
    //     }

    //     $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$getAppointments->patient_id."' and pp.appointment_id='".$getAppointments->appointment_id."' and pp.clinic_id='".$getAppointments->clinic_id."' ")->get()->result();
        
    //     $prescription_plan = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$getAppointments->appointment_id),'');
        
    //     if($parent_appointment_id == 0 || $parent_appointment_id == NULL)
    //     {
    //         $apt_id = $getAppointments->appointment_id;
    //     }else{
    //         $get_all_appointments = $this->db->select("*")->from("appointments")->where("(parent_appointment_id='".$parent_appointment_id."' or appointment_id='".$parent_appointment_id."') and appointment_id NOT IN  (".$getAppointments->appointment_id.")")->order_by('appointment_id','DESC')->get()->result();
            
    //         $previous_patient_id = $get_all_appointments[0]->patient_id;
    //         $previous_appointment_id = $get_all_appointments[0]->appointment_id;
    //         $previous_clinic_id = $get_all_appointments[0]->clinic_id;
    //         $previous_doctor_id = $get_all_appointments[0]->doctor_id;
            
    //         $apt_id = $get_all_appointments[0]->appointment_id;
            
    //         if(count($p_p_lineitems)<=0)
    //         {
    //             $prescription_plan = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$previous_appointment_id),'');
                
    //             $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$previous_patient_id."' and pp.appointment_id='".$previous_appointment_id."' and pp.clinic_id='".$previous_clinic_id."'")->get()->result();
    //         }           
    //     }
        
    //     $data['plan'] = $prescription_plan->plan;
    //     $data['instructions'] = $prescription_plan->general_instructions;
        
    //     $p=0;
    //     if(count($p_p_lineitems)>0){
    //         foreach($p_p_lineitems as $ppl)
    //         {
    //             $data['prescription'][$p]['patient_prescription_drug_id'] = $ppl->patient_prescription_drug_id;
    //             $data['prescription'][$p]['patient_prescription_id'] = $ppl->patient_prescription_id;
    //             $data['prescription'][$p]['appointment_id'] = $apt_id;
    //             $data['prescription'][$p]['day_schedule'] = $ppl->day_schedule;
    //             $data['prescription'][$p]['day_dosage'] = $ppl->day_dosage;
    //             $data['prescription'][$p]['dosage_frequency'] = $ppl->dosage_frequency;
    //             $data['prescription'][$p]['dose_course'] = $ppl->dose_course;
    //             $data['prescription'][$p]['drug_dose'] = $ppl->drug_dose;
    //             $data['prescription'][$p]['dosage_unit'] = $ppl->dosage_unit;
    //             $data['prescription'][$p]['mode'] = $ppl->mode;
    //             $data['prescription'][$p]['drug_id'] = $ppl->drug_id;
    //             $get_composition = $this->db->select("composition")->from("drug")->where("drug_id='".$ppl->drug_id."'")->get()->row();
    //             $data['prescription'][$p]['composition'] = $get_composition->composition;
    //             $data['prescription'][$p]['medicine_name'] = $ppl->medicine_name;
    //             $data['prescription'][$p]['preffered_intake'] = $ppl->preffered_intake;
    //             $data['prescription'][$p]['quantity'] = $ppl->quantity;
    //             $data['prescription'][$p]['remarks'] = $ppl->remarks;

    //             $p++;
    //         }
    //     }else{
    //         $data['prescription'] = array();
    //     }

    //     $this->response(array('code' => '200', 'message' => 'Precription List', 'result' => $data, 'requestname' => $method));
    // }

    public function get_latestPrescription($parameters, $method, $user_id)
    {
        
        $appointment_id = $parameters['appointment_id'];
        $clinic_id = $parameters['clinic_id'];
    

        $clist = $this->db->select("*")->from("appointments ap")
        ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='".$clinic_id."'")
        ->get()
        ->row();
        $patient_id = $clist->patient_id;
        $doctor_id = $clist->doctor_id;

        $clist1 = $this->db->select("*")->from("appointments ap")
        ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='".$doctor_id."'")
        ->order_by("appointment_id","desc")
        ->limit("2")
        ->get()
        ->result();

        $nizam = $clist1[1]->appointment_id;


        //Start prescription
      
       
      $clist220 = $this->db->select("patient_prescription_id")->from("patient_prescription pp")
      ->where("pp.appointment_id='" . $nizam . "'")
      ->get()
      ->row();

      $ab220 = $this->db->select("*")->from("patient_prescription_drug pp")
      ->where("pp.patient_prescription_id='" . $clist220->patient_prescription_id . "'")
      ->get()
      ->result();
             
      $chec = $this->db->select("*")->from("patient_prescription pp")
      ->where("pp.appointment_id='" . $appointment_id . "' ")
      ->get()
      ->row();

      if(count($chec) == 0)
       {
          $insert['appointment_id'] =  $appointment_id;
          $insert['patient_id'] = $patient_id;
          $insert['doctor_id'] = $doctor_id;
          $insert['clinic_id'] = $clinic_id;
          $insert['modified_by'] = $doctor_id;
          $insert['created_by'] = $doctor_id;
          $insert['status'] = 1;
          $insert['created_date_time'] = date('Y-m-d H:i:s');
          $insert['modified_date_time'] = date('Y-m-d H:i:s');
          $iddd = $this->Generic_model->insertDataReturnId('patient_prescription', $insert);  
   
              $check1234 = $this->db->select("*")->from("patient_prescription_drug pp")
              ->where("pp.patient_prescription_id='" . $iddd . "' ")
              ->get()
              ->row();

              if(count($ab220) != 0)
              {
                $e=0;
                $j=0;
                foreach($ab220 as $clist12)
                {
                    $paraaa['patient_prescription_id'] = $iddd;
                    $paraaa['drug_id'] = $clist12->drug_id;
                    $paraaa['medicine_name']= $clist12->medicine_name;
                    $paraaa['day_schedule'] = $clist12->day_schedule;
                    $paraaa['preffered_intake'] = $clist12->preffered_intake;
                    $paraaa['day_dosage']= $clist12->day_dosage;
                    $paraaa['dose_course']= $clist12->dose_course;
                    $paraaa['drug_dose'] = $clist12->drug_dose;
                    $paraaa['dosage_frequency'] = $clist12->dosage_frequency;
                    $paraaa['quantity']= $clist12->quantity;
                    $paraaa['remarks']= $clist12->remarks;
                    $paraaa['created_date_time'] = date('Y-m-d H:i:s');
                    $paraaa['modified_date_time'] = date('Y-m-d H:i:s');
                    $pid = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $paraaa);       
                    $paraa['prescription'][$j]['patient_prescription_drug_id'] = $pid;
                    $paraa['prescription'][$j]['patient_prescription_id'] = $iddd;
                    $paraa['prescription'][$j]['day_schedule'] = $clist12->day_schedule;
                    $paraa['prescription'][$j]['day_dosage'] = $clist12->day_dosage;
                    $paraa['prescription'][$j]['dose_course'] = $clist12->dose_course;
                    $paraa['prescription'][$j]['drug_dose'] = $clist12->drug_dose;
                    $paraa['prescription'][$j]['dosage_frequency'] = $clist12->dosage_frequency;
                    $paraa['prescription'][$j]['dosage_unit'] = $clist12->dosage_unit;
                    $paraa['prescription'][$j]['drug_id'] = $clist12->drug_id;
                    $paraa['prescription'][$j]['medicine_name'] = $clist12->medicine_name;
                    $paraa['prescription'][$j]['preffered_intake'] = $clist12->preffered_intake;
                    $paraa['prescription'][$j]['quantity'] = $clist12->quantity;
                    $paraa['prescription'][$j]['remarks'] = $clist12->remarks;
  
                    $j++;
  
                    $e++;
  
                
                }
              }
              
        
              else{
                  $paraa['prescription']=array();
              }

    
    
       }
       else{
   

     
       $inv_lineitems =  $this->db->select("*")
       ->from("patient_prescription_drug ppd")
       ->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")
       ->where("pp.appointment_id='".$appointment_id."' ")
       ->get()
       ->result();
       
       if(count($inv_lineitems) != 0)
       {
        $j=0;
        foreach($inv_lineitems as $inv_lineitem){
                                                 
                $paraa['prescription'][$j]['patient_prescription_drug_id'] = $inv_lineitem->patient_prescription_drug_id;
                $paraa['prescription'][$j]['patient_prescription_id'] = $inv_lineitem->patient_prescription_id;
                // $paraa['prescription'][$j]['appointment_id'] = $apt_id;
                $paraa['prescription'][$j]['day_schedule'] = $inv_lineitem->day_schedule;
                $paraa['prescription'][$j]['day_dosage'] = $inv_lineitem->day_dosage;
                $paraa['prescription'][$j]['dose_course'] = $inv_lineitem->dose_course;
                $paraa['prescription'][$j]['drug_dose'] = $inv_lineitem->drug_dose;
                $paraa['prescription'][$j]['dosage_frequency'] = $inv_lineitem->dosage_frequency;
                $paraa['prescription'][$j]['dosage_unit'] = $inv_lineitem->dosage_unit;
                $paraa['prescription'][$j]['drug_id'] = $inv_lineitem->drug_id;
                $paraa['prescription'][$j]['medicine_name'] = $inv_lineitem->medicine_name;
                $paraa['prescription'][$j]['preffered_intake'] = $inv_lineitem->preffered_intake;
                $paraa['prescription'][$j]['quantity'] = $inv_lineitem->quantity;
                $paraa['prescription'][$j]['remarks'] = $inv_lineitem->remarks;

                $j++;
                       
        }
       }
       else{
           $paraa['prescription']=array();
       }
         
        //End Prescription
        }

         
     
        
      
             


       

        $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$paraa, 'requestname' => $method));        
    
    }

    public function get_latestClinicalDiagnosis($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $clinic_id = $parameters['clinic_id'];
    

        $clist = $this->db->select("*")->from("appointments ap")
        ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='".$clinic_id."'")
        ->get()
        ->row();
        $patient_id = $clist->patient_id;
        $doctor_id = $clist->doctor_id;

        $clist1 = $this->db->select("*")->from("appointments ap")
        ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='".$doctor_id."'")
        ->order_by("appointment_id","desc")
        ->limit("2")
        ->get()
        ->result();

        $nizam = $clist1[1]->appointment_id;


     

      
        
        //Start CD

            
      $clist123 = $this->db->select("patient_clinical_diagnosis_id")->from("patient_clinical_diagnosis pp")
      ->where("pp.appointment_id='" . $nizam . "'")
      ->get()
      ->row();

      $abcd = $this->db->select("*")->from("patient_cd_line_items pp")
      ->where("pp.patient_clinical_diagnosis_id='" . $clist123->patient_clinical_diagnosis_id . "'")
      ->get()
      ->result();
             
      $chec99 = $this->db->select("*")->from("patient_clinical_diagnosis pp")
      ->where("pp.appointment_id='" . $appointment_id . "' ")
      ->get()
      ->row();

      if(count($chec99) == 0)
       {
          $insert2['appointment_id'] =  $appointment_id;
          $insert2['patient_id'] = $patient_id;
          $insert2['doctor_id'] = $doctor_id;
          $insert2['clinic_id'] = $clinic_id;
          $insert2['modified_by'] = $doctor_id;
          $insert2['created_by'] = $doctor_id;
          $insert2['status'] = 1;
          $insert2['created_date_time'] = date('Y-m-d H:i:s');
          $insert2['modified_date_time'] = date('Y-m-d H:i:s');
          $cdd = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $insert2);  
   
              $check1234 = $this->db->select("*")->from("patient_cd_line_items pp")
              ->where("pp.patient_clinical_diagnosis_id='" . $cdd . "' ")
              ->get()
              ->row();

          
              $e=0;$j = 0;
              foreach($abcd as $clist12)
              {
                  $paraaa1['patient_clinical_diagnosis_id'] = $cdd;
                  $paraaa1['disease_name']= $clist12->disease_name;
                  $paraaa1['created_date_time'] = date('Y-m-d H:i:s');
                  $paraaa1['modified_date_time'] = date('Y-m-d H:i:s');
                  $pid = $this->Generic_model->insertDataReturnId('patient_cd_line_items', $paraaa1);  
                  $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $pid;
                  $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $cdd;
                  // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
                  $paraa['clinicaldiagnosis'][$j]['disease_name'] = $clist12->disease_name;
                  $e++;
                $j++;
              
              }

    
    
       }
       else{
        $inv_lineitems =  $this->db->select("*")
        ->from("patient_cd_line_items ppd")
        ->join("patient_clinical_diagnosis pp","ppd.patient_clinical_diagnosis_id=pp.patient_clinical_diagnosis_id")
        ->where("pp.appointment_id='".$appointment_id."' ")
        ->get()
        ->result();
        
   
           $j=0;
           foreach($inv_lineitems as $inv_lineitem){
 
             $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $inv_lineitem->patient_cd_line_item_id;
             $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $inv_lineitem->patient_clinical_diagnosis_id;
             // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
             $paraa['clinicaldiagnosis'][$j]['disease_name'] = $inv_lineitem->disease_name;
                
                   $j++;
                          
           }
 
       }
     
       
        //End CD

   

        $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$paraa, 'requestname' => $method));  
    }

    public function patient_consent_form_list($parameters,$method,$user_id)
    {
        $appointment_id = $parameters['appointment_id'];

        $clist = $this->db->select("*")->from("patient_consent_forms cf")
            ->where("cf.appointment_id='" . $appointment_id . "' order by created_date_time")
            ->get()
            ->result();

        $i = 0;
        foreach ($clist as $cform) {
            $para['consent_form'][$i]['consent_form_id'] = $cform->consent_form_id;
            $para['consent_form'][$i]['patient_consent_form_id'] = $cform->patient_consent_form_id;
            $para['consent_form'][$i]['patient_consent_form'] = $cform->patient_consent_form;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
    }

   

    public function consentFormDownload($parameters, $method, $user_id) {
        $patient_consent_form_id = $parameters['patient_consent_form_id'];
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $user_id;
        $consent_form_id = $parameters['consent_form_id'];
        $data['appointment'] = $this->db->select("a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id,p.patient_id,p.title,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.umr_no,p.age,p.age_unit,p.gender as p_gender,c.clinic_id,c.clinic_name")
            ->from("appointments a")
            ->join("doctors d","a.doctor_id = d.doctor_id","left")
            ->join("department de","d.department_id=de.department_id","left")->join("patients p","a.patient_id=p.patient_id","left")
            ->join("clinics c","a.clinic_id=c.clinic_id","left")
            ->where("a.appointment_id='" . $appointment_id . "'")->order_by("a.appointment_id","desc")->get()->row();
        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->join(" consent_form_department c","c.consent_form_id = a.consent_form_id")->join("department b","c.department_id = b.department_id")->where("a.archieve != 1 and a.consent_form_id ='" . $consent_form_id . "'")->get()->row();
        $data['consent_form_id'] = $id[0];


        $html = $this->load->view('consentform/consentform_patient_pdf', $data, true);
        $pdfFilePath = rand(10,100)."_".$clinic_id."_".$patient_id."_".$appointment_id."_".date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['patient_consent_form'] = $pdfFilePath;

        $ok = $this->Generic_model->updateData("patient_consent_forms", $para, array('patient_consent_form_id' => $patient_consent_form_id));

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
    }

    public function patient_checklist($parameters, $method, $user_id) {

        for ($v = 0; $v < count($parameters['consent_check_list']); $v++) {
            $insert_checklist['category'] = $parameters['consent_check_list'][$v]['category'];
            $insert_checklist['checked'] = $parameters['consent_check_list'][$v]['checked'];
            if ($parameters['consent_check_list'][$v]['nurse_review'] == 0) {
                $doctor_review = 1;
                $nurse_review = 0;
            }
            if ($parameters['consent_check_list'][$v]['doctor_review'] == 0) {
                $doctor_review = 0;
                $nurse_review = 1;
            }
            $insert_checklist['nurse_review'] = $nurse_review;
            $insert_checklist['doctor_review'] = $doctor_review;
            $insert_checklist['remark'] = $parameters['consent_check_list'][$v]['remark'];
            $insert_checklist['created_by'] = $user_id;
            $insert_checklist['modified_by'] = $user_id;
            $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("patient_checklist", $insert_checklist, array('patient_checklist_id' => $parameters['consent_check_list'][$v]['patient_checklist_id']));
        }
        //updating in patient consent form
        $data1['checked_by'] = $parameters['checked_by'];
        $data1['done_by'] = $parameters['done_by'];
        $data1['assisted_by'] = $parameters['assisted_by'];
        $data1['nurse'] = $parameters['nurse'];
        $data1['anesthetist'] = $parameters['anesthetist'];
        $this->Generic_model->updateData("patient_consent_forms", $data1, array('patient_consent_form_id' => $parameters['patient_consent_form_id']));

        $this->response(array('code' => '200', 'message' => 'Check List Updated Successfully', 'requestname' => $method));
    }

    public function systemicExamination($parameters, $method, $user_id)
    {
        $form_type = $parameters['form_type'];
        $department_id = $parameters['department_id'];
        $appointment_id = $parameters['appointment_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];

        //step1
        $clist1 = $this->db->select("form_id  ,form_name,department_id")->from("form cf")
            ->where("cf.department_id='" .$department_id  . "' and cf.form_type='" ."Systemic Examination"  . "'  ")
            ->get()
            ->result();
        $i = 0;
        foreach ($clist1 as $cform1) {
            $para['form'][$i]['form_id'] = $cform1->form_id;
            $para['form'][$i]['form_name'] = $cform1->form_name;
            $para['form'][$i]['department_id'] = $cform1->department_id;
            //step2
            $clist2 = $this->db->select("title ,section_id,form_id")->from("section cs")
                ->where("cs.form_id='" . $cform1->form_id  . "' and cs.parent_section_id='0' ")
                ->get()
                ->result();
            $x = 0;
            foreach ($clist2 as $cform2) {
                $para['heading'][$i][$x]['title'] = $cform2->title;
                $para['heading'][$i][$x]['form_id'] = $cform2->form_id;
                $para['heading'][$i][$x]['section_id'] = $cform2->section_id;
                //step3
                $clist3 = $this->db->select("title,section_id,form_id,parent_section_id")->from("section css")
                    ->where("css.form_id='" . $cform2->form_id  . "' and css.parent_section_id='" . $cform2->section_id  . "' ")
                    ->get()
                    ->result();
                $y = 0;
                foreach ($clist3 as $cform3) {
                    $para['sub_heading'][$i][$x][$y]['title'] = $cform3->title;
                    $para['sub_heading'][$i][$x][$y]['form_id'] = $cform2->form_id;
                    $para['sub_heading'][$i][$x][$y]['parent_section_id'] = $cform3->parent_section_id;
                    $para['sub_heading'][$i][$x][$y]['section_id'] = $cform3->section_id;
                    //step4

                    $clist4 = $this->db->select("field_name,field_id,section_id,field_type")->from("field ff")
                        ->where("ff.section_id='" . $cform3->section_id . "' and parent_field_id = '0' and parent_option_id = '0' ORDER BY field_id ASC")
                        ->get()
                        ->result();
                    $z = 0;
                    foreach ($clist4 as $cform4) {
                        $para['field_name'][$i][$x][$y][$z]['field_name'] = $cform4->field_name;
                        $para['field_name'][$i][$x][$y][$z]['section_id'] = $cform4->section_id;
                        $para['field_name'][$i][$x][$y][$z]['field_id'] = $cform4->field_id;
                        $para['field_name'][$i][$x][$y][$z]['field_type'] = $cform4->field_type;
                        //step5

                        $clist5 = $this->db->select("option_name,field_id,option_id")->from("field_option ff")
                            ->where("ff.field_id='" . $cform4->field_id . "'  ")
                            ->get()
                            ->result();
                        $a = 0;
                        foreach ($clist5 as $cform5) {
                            $para['field_option'][$i][$x][$y][$z][$a]['option_name'] = $cform5->option_name;
                            $para['field_option'][$i][$x][$y][$z][$a]['option_id'] = $cform5->option_id;
                            $para['field_option'][$i][$x][$y][$z][$a]['field_id'] = $cform5->field_id;
                            $a++;
                        }
                        $z++;
                    }
                    $y++;
                    }
                $x++;
            }
            $i++;

        }


        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
    }


//     public function commonDashboard($parameters, $method, $user_id) {

//         $clinic_id = $parameters['clinic_id'];
//         $role_id = $parameters['role_id'];
//         $profile_id = $parameters['profile_id'];
//         $dashboard_type = $parameters['dashboard_type'];
//         $from_date = $parameters['from_date'];
//         $to_date = $parameters['to_date'];
//         $today = date('Y-m-d');

//         if ($role_id == 6) { // Nurse
//             $data['leftPane']['header'] = 'ALL APPOINTMENTS';
//             $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='".$today."'")
//             ->order_by("FIELD(a.status,'vital_signs','checked_in')")
//             ->order_by("FIELD(a.priority, 'pregnancy', 'elderly', 'children','none') ")
//             ->order_by("a.appointment_time_slot","asc")
//             ->order_by("a.appointment_date","asc")
//             ->order_by("a.check_in_time","asc")->get()->result_array();

            
//             $i = 0;
//             foreach ($patients as $result) {
//                 if ($result["qrcode"] != NULL) {
//                     $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
//                 } else {
//                     $qrcode = NULL;
//                 }
//                 $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
//                 $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
//                 $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
//                 $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
//                 $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
//                 $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
//                 $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
//                 $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
//                 $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
//                 $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
//                 $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
//                 $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
//                 $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
//                 $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

//                 //eliminate comms
//                 $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

//                 $data['leftPane']['PatientsList'][$i]['contact'] = DataCrypt($result['mobile'],'decrypt');
//                 $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
//                 $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
//                 $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
//                 $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
//                 $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
//                 $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
//                 $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
//                 $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
//                 $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
//                 $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
//                 $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
//                 $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
//                 $i++;
//             }
//             $data['rightPane']['header'] = 'ANALYTICS';
//             $data['rightPane']['analyticalList'][0]['number'] = count($patients);
//             $data['rightPane']['analyticalList'][0]['title'] = "Appointments";
//             $data['rightPane']['analyticalList'][0]['split'] = array();
//         }
        
//         if($role_id == 4){ // Doctor

//           $data['leftPane']['header'] = 'ALL APPOINTMENTS';
//           $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='".$user_id."' and a.appointment_date = '" . $today . "'")
//           ->order_by("FIELD(a.status,'in_consultation','waiting')")
//           ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
//           ->order_by("a.check_in_time","asc")
//           ->order_by("a.appointment_time_slot","asc")
//           ->order_by("a.appointment_date","asc")->get()->result_array();

//           $i = 0;
//           foreach ($patients as $result) {
//             if ($result["qrcode"] != NULL) {
//                 $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
//             } else {
//                 $qrcode = NULL;
//             }
//             $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
//             $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
//             $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
//             $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
//             $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
//             $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
//             $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
//             $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
//             $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
//             $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
//             $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
//             $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
//             $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
//             $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
//             $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
//             $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
//             $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

//                 //eliminate comms
//             $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

//             $data['leftPane']['PatientsList'][$i]['contact'] =DataCrypt( $result['mobile'],'decrypt');
//             $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
//             $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
//             $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
//             $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
//             $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
//             $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
//             $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
//             $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
//             $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
//             $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
//             $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
//             $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
//             $i++;
//         }

//         if(trim($from_date) == trim($to_date)){
//             $data['rightPane']['header'] = 'TODAY ('.date('d M. Y', strtotime($from_date)).')';    
//         }else{
//             $data['rightPane']['header'] = 'FROM ('.date('d M. Y', strtotime($from_date)).' - '.date('d M. Y', strtotime($to_date)).')';
//         }
        

//         // Get Total Billing records for today
//         $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date');
//         $this->db->from('billing');
//         $this->db->where('clinic_id =',$clinic_id);
//         $this->db->where('doctor_id =',$user_id);

//         if(trim($from_date) == trim($to_date)){
//             $this->db->like("created_date_time",$from_date);
//         }else{
//             $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
//         }

//         $finances = $this->db->get()->result_array();

//         if(count($finances) > 0){
//             $i=0;
//             foreach($finances as $financeRec){
//                 $sql = "SELECT billing_line_item_id, amount, discount, discount_unit, created_date_time,  
//                         IF(discount_unit = 'INR',amount-discount, IF(discount_unit = '%', ROUND(amount - (amount*discount/100),2), amount))
//                         AS payable_amount,
//                         IF(discount_unit = 'INR',discount, IF(discount_unit = '%', ROUND((amount*discount)/100,2), 0))
//                        AS discounted_amount
//                        FROM billing_line_items WHERE billing_id = ?";

//                 $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

//                 $clist = $this->db->select("billing_line_item_id,item_information,sum(amount) as amount1")->from("billing_line_items bl")
//                 ->where("bl.billing_id='" . $financeRec->billing_id. "'")
//                 ->group_by("item_information")
//                 ->get()
//                 ->result();

//                 $j=0;
//                 $flat = "";
//                 foreach($clist as $clist1){

//                     $paraa['rightPane'][$i][$j]= $clist1->billing_line_item_id;
//                     $myJSON = json_encode($paraa);
//                     $abc = $abc.",'".$clist1->billing_line_item_id."'";
//                     $flat = $flat.$abc;
//                     $flatt = substr($flat,1);
//                     // $paraa['rightPane'][$i][$j]['amount'] = $clist1->amount1;  
//                     if($clist1->item_information == 'Consultation')
//                     {
//                         // $paraa['rightPane'][$i][$j]['testCount'] = $i+1;        
//                         // $paraa['rightPane'][$i][$j]['totalAmount']=$clist1->amount; 
//                         $clist123 = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as totalConsultationAmount")->from("billing_line_items bl")
//                         ->where("bl.item_information='Consultation'")
//                         ->where("bl.billing_line_item_id IN (".$flatt.") ")
//                         ->get()
//                         ->row();   
//                         $dataa['consultationCount']=  $clist123->totalConsultationCount;
//                         $dataa['consultationAmount']=  $clist123->totalConsultationAmount;
//                     }             
//                     $j++;
//                 }
//                 // $flat = $flat.$abc;
//                  $i++;
//                  $totalRevenue = $totalRevenue + $itemRec->payable_amount;
//                  $totalDiscount = $totalDiscount + $itemRec->discounted_amount;
         
//             }
//         }

       
//         // // Ready the right pane JSON
//         // $data['rightPane']['analyticalList'][0]['number'] = NULL;
//         // $data['rightPane']['analyticalList'][0]['title'] = "Finances";
//         // $data['rightPane']['analyticalList'][0]['split'][0]['number'] = NULL;
//         // $data['rightPane']['analyticalList'][0]['split'][0]['title'] = "Revenue";
//         // $data['rightPane']['analyticalList'][0]['split'][0]['value'] = $totalRevenue;

//         // $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
//         // $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
//         // $data['rightPane']['analyticalList'][0]['split'][1]['value'] = $totalDiscount;

//         // // Consultation JSON
//         // $data['rightPane']['analyticalList'][1]['number'] = $clist123->totalConsultationCount;
//         // $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
//         // $data['rightPane']['analyticalList'][1]['value'] = $clist123->totalConsultationAmount;

//         // // Procedure Revenue JSON
//         // $data['rightPane']['analyticalList'][2]['number'] = (int)$totalProcedures;
//         // $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
//         // $data['rightPane']['analyticalList'][2]['value'] = $totalProcedureRevenue;

//         // // Pharmacy Revenue JSON
//         // $data['rightPane']['analyticalList'][3]['number'] = (int)$totalPrescriptions;
//         // $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
//         // $data['rightPane']['analyticalList'][3]['value'] = $totalPharmacyRevenue;
//     }

//     $this->response(array('code' => '200', 'message' => 'success ', 'result' => $dataa, 'requestname' => $method));
// }

//        public function commonDashboard($parameters, $method, $user_id) {

//         $clinic_id = $parameters['clinic_id'];
//         $doctor_id = $user_id;
//         $role_id = $parameters['role_id'];
//         $profile_id = $parameters['profile_id'];
//         $dashboard_type = $parameters['dashboard_type'];
//         $from_date = $parameters['from_date'];
//         $to_date = $parameters['to_date'];
//         $today = date('Y-m-d');

//         if ($role_id == 6) { // Nurse
//             $data['leftPane']['header'] = 'ALL APPOINTMENTS';
//             $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='".$today."'")
//             ->order_by("FIELD(a.status,'vital_signs','checked_in')")
//             ->order_by("FIELD(a.priority, 'pregnancy', 'elderly', 'children','none') ")
//             ->order_by("a.appointment_time_slot","asc")
//             ->order_by("a.appointment_date","asc")
//             ->order_by("a.check_in_time","asc")->get()->result_array();

            
//             $i = 0;
//             foreach ($patients as $result) {
//                 if ($result["qrcode"] != NULL) {
//                     $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
//                 } else {
//                     $qrcode = NULL;
//                 }
//                 $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
//                 $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
//                 $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
//                 $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
//                 $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
//                 $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
//                 $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
//                 $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
//                 $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
//                 $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
//                 $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
//                 $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
//                 $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
//                 $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
//                 $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

//                 //eliminate comms
//                 $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

//                 $data['leftPane']['PatientsList'][$i]['contact'] = DataCrypt($result['mobile'],'decrypt');
//                 $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
//                 $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
//                 $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
//                 $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
//                 $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
//                 $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
//                 $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
//                 $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
//                 $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
//                 $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
//                 $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
//                 $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
//                 $i++;
//             }
//             $data['rightPane']['header'] = 'ANALYTICS';
//             $data['rightPane']['analyticalList'][0]['number'] = count($patients);
//             $data['rightPane']['analyticalList'][0]['title'] = "Appointments";
//             $data['rightPane']['analyticalList'][0]['split'] = array();
//         }
        
//         if($role_id == 4){ // Doctor
        
//           $data['leftPane']['header'] = 'ALL APPOINTMENTS';
//           $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='".$user_id."' and a.appointment_date = '" . $today . "'")
//           ->order_by("FIELD(a.status,'in_consultation','waiting')")
//           ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
//           ->order_by("a.check_in_time","asc")
//           ->order_by("a.appointment_time_slot","asc")
//           ->order_by("a.appointment_date","asc")->get()->result_array();

//           $i = 0;
//           foreach ($patients as $result) {
//             if ($result["qrcode"] != NULL) {
//                 $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
//             } else {
//                 $qrcode = NULL;
//             }
//             $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
//             $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
//             $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
//             $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
//             $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
//             $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
//             $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
//             $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
//             $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
//             $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
//             $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
//             $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
//             $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
//             $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
//             $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
//             $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
//             $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

//                 //eliminate comms
//             $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

//             $data['leftPane']['PatientsList'][$i]['contact'] =DataCrypt( $result['mobile'],'decrypt');
//             $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
//             $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
//             $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
//             $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
//             $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
//             $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
//             $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
//             $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
//             $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
//             $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
//             $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
//             $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
//             $i++;
//         }

//        if(trim($from_date) == trim($to_date)){
//             $data['rightPane']['header'] = 'TODAY ('.date('d M. Y', strtotime($from_date)).')';    
//         }else{
//             $data['rightPane']['header'] = 'FROM ('.date('d M. Y', strtotime($from_date)).' - '.date('d M. Y', strtotime($to_date)).')';
//         }
        
//         $to_date = date("Y-m-d", strtotime($to_date.'+1 day'));

//         // Get Total Billing records for today
//         $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date,status');
//         $this->db->from('billing');
//         $this->db->where('clinic_id =',$clinic_id);
//         $this->db->where('doctor_id =',$doctor_id);

//         if(trim($from_date) == trim($to_date)){
//             $this->db->like("created_date_time",$from_date);
//         }else{
//             $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
//         }

//         $finances = $this->db->get()->result();

//         $totalRevenue = 0;
//         $totalDiscount = 0;
        
//         // $totalConsultationRevenue = 0;
//         $totalConsultations = 0;
        
//         $totalProcedureRevenue = 0;
//         $totalProcedures = 0;

//         $totalPharmacyRevenue = 0;
//         $totalPrescriptions = 0;

//         // if(count($finances) > 0){
//             $i=0;
//             $proCount = 0;$phCount = 0;
//             $proAmount = 0; $phAmount = 0;
//             $conCount = 0; $conAmount = 0;
//             $proDisc=0;
//             $conDisc=0;$phDisc=0;
//             foreach($finances as $financeRec){
//                 if($financeRec->status == 2)
//                     continue;
//                 $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

//                 $clist = $this->db->select("billing_line_item_id,item_information,amount,discount,discount_unit,total_amount")->from("billing_line_items bl")
//                 ->where("bl.billing_id='" . $financeRec->billing_id. "'")
//                 ->group_by("item_information")
//                 ->get()
//                 ->result();

//                 if($financeRec->billing_type == 'Procedure')
//                 {
//                     $Disc = 0;
//                     if($financeRec->discount != 0 || $financeRec->discount != NULL)
//                     {
//                         if($financeRec->discount_unit == "%")
//                         {
//                             $amount = $financeRec->total_amount - ($financeRec->total_amount*$financeRec->discount/100);
//                             $Disc = $financeRec->total_amount*$financeRec->discount/100;
//                         }
//                         elseif($financeRec->discount_unit == "INR")
//                         {
//                             $amount = $financeRec->total_amount - $financeRec->discount;
//                             $Disc = $financeRec->discount;
//                         }
//                     }
//                     else
//                     {
//                         $amount = $financeRec->total_amount;
//                     }
//                     $proDisc +=  $Disc;
//                     $proAmount = $proAmount + $amount;
//                     $proCount = $proCount + 1;
//                 }

                
//           $sql = "SELECT billing_line_item_id, amount, discount, discount_unit, created_date_time,  
//           IF(discount_unit = 'INR',amount-discount, IF(discount_unit = '%', ROUND(amount - (amount*discount/100),2), amount))
//           AS payable_amount,
//           IF(discount_unit = 'INR',discount, IF(discount_unit = '%', ROUND((amount*discount)/100,2), 0))
//           AS discounted_amount
//           FROM billing_line_items WHERE billing_id = ?";
//                 $j=0;
//                 $flat = "";
//                 foreach($clist as $clist1){

//                     $paraa['rightPane'][$i][$j]= $clist1->billing_line_item_id;
//                     $myJSON = json_encode($paraa);
//                     $abc = $abc.",'".$clist1->billing_line_item_id."'";
//                     $flat = $flat.$abc;
//                     $flatt = substr($flat,1);
                  
//                     if($clist1->item_information == 'Consultation')
//                     {
//                         $conInfo = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as consultationAmount,sum(discount) as discount")
//                         ->from("billing_line_items bl")
//                         ->where("bl.item_information='Consultation'")
//                         ->where("bl.billing_line_item_id IN (".$flatt.") ")
//                         ->get()
//                         ->row();   
//                         if($clist1->discount != 0 || $clist1->discount != NULL)
//                         {
//                             if($clist1->discount_unit == "%")
//                             {
//                                 $amount = number_format($clist1->amount - ($clist1->amount*$clist1->discount/100),2);
//                                 $Disc = ($clist1->amount*$clist1->discount)/100;
//                             }
//                             elseif($clist1->discount_unit == "INR")
//                             {
//                                 $amount = number_format($clist1->amount - ($clist1->discount),2);
//                                 $Disc = $clist1->discount;
//                             }
//                         }
//                         else
//                         {
//                             $Disc = 0;
//                         }
//                         $conDisc = $conDisc + $Disc;
//                         $conAmount = $conAmount + $amount;
//                         $conCount = $conInfo->totalConsultationCount;
//                     }   

//                     if($financeRec->billing_type == 'Pharmacy')
//                     {
//                         if($clist1->discount != 0 || $clist1->discount != NULL)
//                         {
//                             if($clist1->discount_unit == "%")
//                             {
//                                 $Disc = ($clist1->total_amount*$clist1->discount)/100;
//                             }
//                             elseif($financeRec->discount_unit == "INR")
//                             {
//                                 $Disc = $clist1->discount;
//                             }
//                         }
//                         else
//                         {
//                             $Disc = 0;
//                         }
//                         $phDisc += $Disc;
//                         $phAmount =  $clist1->total_amount-$Disc;
//                         $phCount = $phCount + 1;
//                     }          
//                     $j++;
//                 }
//                 $i++;
         
//             }

//             if($conInfo->discount == '')
//             {
//                 $conInfo->discount =0;
//             }
//             if( $conInfo->consultationAmount =='' && $conInfo->totalConsultationCount=='')
//             {
//                 $conInfo->totalConsultationCount = 0;
//                 $conInfo->consultationAmount = 0;
//             }

//         // }
//         // $flat = substr($flat,1);
//         // Ready the right pane JSON
//         $data['rightPane']['analyticalList'][0]['number'] = NULL;
//         $data['rightPane']['analyticalList'][0]['title'] = "Finances";
//         $data['rightPane']['analyticalList'][0]['split'][0]['number'] = NULL;
//         $data['rightPane']['analyticalList'][0]['split'][0]['title'] = "Revenue";
//         $data['rightPane']['analyticalList'][0]['split'][0]['value'] = number_format($conAmount+$phAmount+$proAmount,2);

//         $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
//         $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
//         $data['rightPane']['analyticalList'][0]['split'][1]['value'] = number_format($conDisc+$phDisc+$proDisc,2);

//         // Consultation JSON
//         $data['rightPane']['analyticalList'][1]['number'] =  $conCount;
//         $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
//         $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount,2);

//         // Procedure Revenue JSON
//         $data['rightPane']['analyticalList'][2]['number'] = $proCount;
//         $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
//         $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount,2);

//         // Pharmacy Revenue JSON
//         $data['rightPane']['analyticalList'][3]['number'] = $phCount;
//         $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
//         $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount,2);

//     }
//     $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$data, 'requestname' => $method));
// }

 public function commonDashboard($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $user_id;
        $role_id = $parameters['role_id'];
        $profile_id = $parameters['profile_id'];
        $dashboard_type = $parameters['dashboard_type'];
        $from_date = $parameters['from_date'];
        $to_date = $parameters['to_date'];
        $today = date('Y-m-d');

        if ($role_id == 6) { // Nurse
            $data['leftPane']['header'] = 'ALL APPOINTMENTS';
            $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='".$today."'")
            ->order_by("FIELD(a.status,'vital_signs','checked_in')")
            ->order_by("FIELD(a.priority, 'pregnancy', 'elderly', 'children','none') ")
            ->order_by("a.appointment_time_slot","asc")
            ->order_by("a.appointment_date","asc")
            ->order_by("a.check_in_time","asc")->get()->result_array();

            
            $i = 0;
            foreach ($patients as $result) {
                if ($result["qrcode"] != NULL) {
                    $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
                } else {
                    $qrcode = NULL;
                }
                $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
                $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
                $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
                $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
                $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
                $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
                $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
                $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
                $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
                $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
                $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
                $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
                $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
                $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
                $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
                $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
                $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

                //eliminate comms
                $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

                $data['leftPane']['PatientsList'][$i]['contact'] = DataCrypt($result['mobile'],'decrypt');
                $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
                $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
                $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
                $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
                $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
                $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
                $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
                $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
                $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
                $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
                $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
                $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
                $i++;
            }

       if(trim($from_date) == trim($to_date)){
            $data['rightPane']['header'] = 'TODAY ('.date('d M. Y', strtotime($from_date)).')';    
        }else{
            $data['rightPane']['header'] = 'FROM ('.date('d M. Y', strtotime($from_date)).' - '.date('d M. Y', strtotime($to_date)).')';
        }
        
        $to_date = date("Y-m-d", strtotime($to_date.'+1 day'));

        // Get Total Billing records for today
        $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date,status');
        $this->db->from('billing');
        $this->db->where('clinic_id =',$clinic_id);
        // $this->db->where('doctor_id =',$doctor_id);

        if(trim($from_date) == trim($to_date)){
            $this->db->like("created_date_time",$from_date);
        }else{
            $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
        }

        $finances = $this->db->get()->result();

        $totalRevenue = 0;
        $totalDiscount = 0;
        
        // $totalConsultationRevenue = 0;
        $totalConsultations = 0;
        
        $totalProcedureRevenue = 0;
        $totalProcedures = 0;

        $totalPharmacyRevenue = 0;
        $totalPrescriptions = 0;

        // if(count($finances) > 0){
            $i=0;
            $proCount = 0;$phCount = 0;
            $proAmount = 0; $phAmount = 0;
            $conCount = 0; $conAmount = 0;
            $proDisc=0;
            $conDisc=0;$phDisc=0;
            foreach($finances as $financeRec){
                if($financeRec->status == 2)
                    continue;
                $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

                $clist = $this->db->select("billing_line_item_id,item_information,amount,discount,discount_unit,total_amount")->from("billing_line_items bl")
                ->where("bl.billing_id='" . $financeRec->billing_id. "'")
                ->group_by("item_information")
                ->get()
                ->result();

                if($financeRec->billing_type == 'Procedure')
                {
                    $Disc = 0;
                    if($financeRec->discount != 0 || $financeRec->discount != NULL)
                    {
                        if($financeRec->discount_unit == "%")
                        {
                            $amount = $financeRec->total_amount - ($financeRec->total_amount*$financeRec->discount/100);
                            $Disc = $financeRec->total_amount*$financeRec->discount/100;
                        }
                        elseif($financeRec->discount_unit == "INR")
                        {
                            $amount = $financeRec->total_amount - $financeRec->discount;
                            $Disc = $financeRec->discount;
                        }
                    }
                    else
                    {
                        $amount = $financeRec->total_amount;
                    }
                    $proDisc +=  $Disc;
                    $proAmount = $proAmount + $amount;
                    $proCount = $proCount + 1;
                }

                
          $sql = "SELECT billing_line_item_id, amount, discount, discount_unit, created_date_time,  
          IF(discount_unit = 'INR',amount-discount, IF(discount_unit = '%', ROUND(amount - (amount*discount/100),2), amount))
          AS payable_amount,
          IF(discount_unit = 'INR',discount, IF(discount_unit = '%', ROUND((amount*discount)/100,2), 0))
          AS discounted_amount
          FROM billing_line_items WHERE billing_id = ?";
                $j=0;
                $flat = "";
                foreach($clist as $clist1){

                    $paraa['rightPane'][$i][$j]= $clist1->billing_line_item_id;
                    $myJSON = json_encode($paraa);
                    $abc = $abc.",'".$clist1->billing_line_item_id."'";
                    $flat = $flat.$abc;
                    $flatt = substr($flat,1);
                  
                    if($clist1->item_information == 'Consultation')
                    {
                        $conInfo = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as consultationAmount,sum(discount) as discount")
                        ->from("billing_line_items bl")
                        ->where("bl.item_information='Consultation'")
                        ->where("bl.billing_line_item_id IN (".$flatt.") ")
                        ->get()
                        ->row();   
                        if($clist1->discount != 0 || $clist1->discount != NULL)
                        {
                            if($clist1->discount_unit == "%")
                            {
                                $amount = number_format($clist1->amount - ($clist1->amount*$clist1->discount/100),2);
                                $Disc = ($clist1->amount*$clist1->discount)/100;
                            }
                            elseif($clist1->discount_unit == "INR")
                            {
                                $amount = number_format($clist1->amount - ($clist1->discount),2);
                                $Disc = $clist1->discount;
                            }
                        }
                        else
                        {
                            $Disc = 0;
                        }
                        $conDisc = $conDisc + $Disc;
                        $conAmount = $conAmount + $amount;
                        $conCount = $conInfo->totalConsultationCount;
                    }   

                    if($financeRec->billing_type == 'Pharmacy')
                    {
                        if($clist1->discount != 0 || $clist1->discount != NULL)
                        {
                            if($clist1->discount_unit == "%")
                            {
                                $Disc = ($clist1->total_amount*$clist1->discount)/100;
                            }
                            elseif($financeRec->discount_unit == "INR")
                            {
                                $Disc = $clist1->discount;
                            }
                        }
                        else
                        {
                            $Disc = 0;
                        }
                        $phDisc += $Disc;
                        $phAmount =  $clist1->total_amount-$Disc;
                        $phCount = $phCount + 1;
                    }          
                    $j++;
                }
                $i++;
         
            }

            if($conInfo->discount == '')
            {
                $conInfo->discount =0;
            }
            if( $conInfo->consultationAmount =='' && $conInfo->totalConsultationCount=='')
            {
                $conInfo->totalConsultationCount = 0;
                $conInfo->consultationAmount = 0;
            }

        // }
        // $flat = substr($flat,1);
        // Ready the right pane JSON
        $data['rightPane']['analyticalList'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['title'] = "Finances";
        $data['rightPane']['analyticalList'][0]['split'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][0]['title'] = "Revenue";
        $data['rightPane']['analyticalList'][0]['split'][0]['value'] = number_format($conAmount+$phAmount+$proAmount,2);

        $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
        $data['rightPane']['analyticalList'][0]['split'][1]['value'] = number_format($conDisc+$phDisc+$proDisc,2);

        // Consultation JSON
        $data['rightPane']['analyticalList'][1]['number'] =  $conCount;
        $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
        $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount,2);

        // Procedure Revenue JSON
        $data['rightPane']['analyticalList'][2]['number'] = $proCount;
        $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
        $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount,2);

        // Pharmacy Revenue JSON
        $data['rightPane']['analyticalList'][3]['number'] = $phCount;
        $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
        $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount,2);
        }
        
        if($role_id == 4){ // Doctor
        
          $data['leftPane']['header'] = 'ALL APPOINTMENTS';
          $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='".$user_id."' and a.appointment_date = '" . $today . "'")
          ->order_by("FIELD(a.status,'in_consultation','waiting')")
          ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
          ->order_by("a.check_in_time","asc")
          ->order_by("a.appointment_time_slot","asc")
          ->order_by("a.appointment_date","asc")->get()->result_array();

          $i = 0;
          foreach ($patients as $result) {
            if ($result["qrcode"] != NULL) {
                $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
            } else {
                $qrcode = NULL;
            }
            $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
            $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
            $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
            $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
            $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
            $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
            $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
            $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
            $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
            $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
            $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
            $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
            $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
            $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
            $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
            $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
            $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

                //eliminate comms
            $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

            $data['leftPane']['PatientsList'][$i]['contact'] =DataCrypt( $result['mobile'],'decrypt');
            $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
            $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
            $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
            $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
            $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
            $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
            $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
            $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
            $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
            $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
            $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
            $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
            $i++;
        }

       if(trim($from_date) == trim($to_date)){
            $data['rightPane']['header'] = 'TODAY ('.date('d M. Y', strtotime($from_date)).')';    
        }else{
            $data['rightPane']['header'] = 'FROM ('.date('d M. Y', strtotime($from_date)).' - '.date('d M. Y', strtotime($to_date)).')';
        }
        
        $to_date = date("Y-m-d", strtotime($to_date.'+1 day'));

        // Get Total Billing records for today
        $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date,status');
        $this->db->from('billing');
        $this->db->where('clinic_id =',$clinic_id);
        $this->db->where('doctor_id =',$doctor_id);

        if(trim($from_date) == trim($to_date)){
            $this->db->like("created_date_time",$from_date);
        }else{
            $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
        }

        $finances = $this->db->get()->result();

        $totalRevenue = 0;
        $totalDiscount = 0;
        
        // $totalConsultationRevenue = 0;
        $totalConsultations = 0;
        
        $totalProcedureRevenue = 0;
        $totalProcedures = 0;

        $totalPharmacyRevenue = 0;
        $totalPrescriptions = 0;

        // if(count($finances) > 0){
            $i=0;
            $proCount = 0;$phCount = 0;
            $proAmount = 0; $phAmount = 0;
            $conCount = 0; $conAmount = 0;
            $proDisc=0;
            $conDisc=0;$phDisc=0;
            foreach($finances as $financeRec){
                if($financeRec->status == 2)
                    continue;
                $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

                $clist = $this->db->select("billing_line_item_id,item_information,amount,discount,discount_unit,total_amount")->from("billing_line_items bl")
                ->where("bl.billing_id='" . $financeRec->billing_id. "'")
                ->group_by("item_information")
                ->get()
                ->result();

                if($financeRec->billing_type == 'Procedure')
                {
                    $Disc = 0;
                    if($financeRec->discount != 0 || $financeRec->discount != NULL)
                    {
                        if($financeRec->discount_unit == "%")
                        {
                            $amount = $financeRec->total_amount - ($financeRec->total_amount*$financeRec->discount/100);
                            $Disc = $financeRec->total_amount*$financeRec->discount/100;
                        }
                        elseif($financeRec->discount_unit == "INR")
                        {
                            $amount = $financeRec->total_amount - $financeRec->discount;
                            $Disc = $financeRec->discount;
                        }
                    }
                    else
                    {
                        $amount = $financeRec->total_amount;
                    }
                    $proDisc +=  $Disc;
                    $proAmount = $proAmount + $amount;
                    $proCount = $proCount + 1;
                }

                
          $sql = "SELECT billing_line_item_id, amount, discount, discount_unit, created_date_time,  
          IF(discount_unit = 'INR',amount-discount, IF(discount_unit = '%', ROUND(amount - (amount*discount/100),2), amount))
          AS payable_amount,
          IF(discount_unit = 'INR',discount, IF(discount_unit = '%', ROUND((amount*discount)/100,2), 0))
          AS discounted_amount
          FROM billing_line_items WHERE billing_id = ?";
                $j=0;
                $flat = "";
                foreach($clist as $clist1){

                    $paraa['rightPane'][$i][$j]= $clist1->billing_line_item_id;
                    $myJSON = json_encode($paraa);
                    $abc = $abc.",'".$clist1->billing_line_item_id."'";
                    $flat = $flat.$abc;
                    $flatt = substr($flat,1);
                  
                    if($clist1->item_information == 'Consultation')
                    {
                        $conInfo = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as consultationAmount,sum(discount) as discount")
                        ->from("billing_line_items bl")
                        ->where("bl.item_information='Consultation'")
                        ->where("bl.billing_line_item_id IN (".$flatt.") ")
                        ->get()
                        ->row();   
                        if($clist1->discount != 0 || $clist1->discount != NULL)
                        {
                            if($clist1->discount_unit == "%")
                            {
                                $amount = number_format($clist1->amount - ($clist1->amount*$clist1->discount/100),2);
                                $Disc = ($clist1->amount*$clist1->discount)/100;
                            }
                            elseif($clist1->discount_unit == "INR")
                            {
                                $amount = number_format($clist1->amount - ($clist1->discount),2);
                                $Disc = $clist1->discount;
                            }
                        }
                        else
                        {
                            $Disc = 0;
                        }
                        $conDisc = $conDisc + $Disc;
                        $conAmount = $conAmount + $amount;
                        $conCount = $conInfo->totalConsultationCount;
                    }   

                    if($financeRec->billing_type == 'Pharmacy')
                    {
                        if($clist1->discount != 0 || $clist1->discount != NULL)
                        {
                            if($clist1->discount_unit == "%")
                            {
                                $Disc = ($clist1->total_amount*$clist1->discount)/100;
                            }
                            elseif($financeRec->discount_unit == "INR")
                            {
                                $Disc = $clist1->discount;
                            }
                        }
                        else
                        {
                            $Disc = 0;
                        }
                        $phDisc += $Disc;
                        $phAmount =  $clist1->total_amount-$Disc;
                        $phCount = $phCount + 1;
                    }          
                    $j++;
                }
                $i++;
         
            }

            if($conInfo->discount == '')
            {
                $conInfo->discount =0;
            }
            if( $conInfo->consultationAmount =='' && $conInfo->totalConsultationCount=='')
            {
                $conInfo->totalConsultationCount = 0;
                $conInfo->consultationAmount = 0;
            }

        // }
        // $flat = substr($flat,1);
        // Ready the right pane JSON
        $data['rightPane']['analyticalList'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['title'] = "Finances";
        $data['rightPane']['analyticalList'][0]['split'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][0]['title'] = "Revenue";
        $data['rightPane']['analyticalList'][0]['split'][0]['value'] = number_format($conAmount+$phAmount+$proAmount,2);

        $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
        $data['rightPane']['analyticalList'][0]['split'][1]['value'] = number_format($conDisc+$phDisc+$proDisc,2);

        // Consultation JSON
        $data['rightPane']['analyticalList'][1]['number'] =  $conCount;
        $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
        $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount,2);

        // Procedure Revenue JSON
        $data['rightPane']['analyticalList'][2]['number'] = $proCount;
        $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
        $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount,2);

        // Pharmacy Revenue JSON
        $data['rightPane']['analyticalList'][3]['number'] = $phCount;
        $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
        $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount,2);

    }
    $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$data, 'requestname' => $method));
}

    public function get_fi($parameters, $method, $user_id) {
        $appointment_id = $parameters['appointment_id'];
        $clinic_id = $parameters['clinic_id'];
    

        $clist = $this->db->select("*")->from("appointments ap")
        ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='".$clinic_id."'")
        ->get()
        ->row();
        $patient_id = $clist->patient_id;
        $doctor_id = $clist->doctor_id;

        $clist1 = $this->db->select("*")->from("appointments ap")
        ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='".$doctor_id."'")
        ->order_by("appointment_id","desc")
        ->limit("2")
        ->get()
        ->result();

        $nizam = $clist1[1]->appointment_id;


        //Start prescription
      
       
      $clist220 = $this->db->select("patient_prescription_id")->from("patient_prescription pp")
      ->where("pp.appointment_id='" . $nizam . "'")
      ->get()
      ->row();

      $ab220 = $this->db->select("*")->from("patient_prescription_drug pp")
      ->where("pp.patient_prescription_id='" . $clist220->patient_prescription_id . "'")
      ->get()
      ->result();
             
      $chec = $this->db->select("*")->from("patient_prescription pp")
      ->where("pp.appointment_id='" . $appointment_id . "' ")
      ->get()
      ->row();

      if(count($chec) == 0)
       {
          $insert['appointment_id'] =  $appointment_id;
          $insert['patient_id'] = $patient_id;
          $insert['doctor_id'] = $doctor_id;
          $insert['clinic_id'] = $clinic_id;
          $insert['modified_by'] = $doctor_id;
          $insert['created_by'] = $doctor_id;
          $insert['status'] = 1;
          $insert['created_date_time'] = date('Y-m-d H:i:s');
          $insert['modified_date_time'] = date('Y-m-d H:i:s');
          $iddd = $this->Generic_model->insertDataReturnId('patient_prescription', $insert);  
   
              $check1234 = $this->db->select("*")->from("patient_prescription_drug pp")
              ->where("pp.patient_prescription_id='" . $iddd . "' ")
              ->get()
              ->row();

          
              $e=0;
              $j=0;
              foreach($ab220 as $clist12)
              {
                  $paraaa['patient_prescription_id'] = $iddd;
                  $paraaa['drug_id'] = $clist12->drug_id;
                  $paraaa['medicine_name']= $clist12->medicine_name;
                  $paraaa['day_schedule'] = $clist12->day_schedule;
                  $paraaa['preffered_intake'] = $clist12->preffered_intake;
                  $paraaa['day_dosage']= $clist12->day_dosage;
                  $paraaa['dose_course']= $clist12->dose_course;
                  $paraaa['drug_dose'] = $clist12->drug_dose;
                  $paraaa['dosage_frequency'] = $clist12->dosage_frequency;
                  $paraaa['quantity']= $clist12->quantity;
                  $paraaa['remarks']= $clist12->remarks;
                  $paraaa['created_date_time'] = date('Y-m-d H:i:s');
                  $paraaa['modified_date_time'] = date('Y-m-d H:i:s');
                  $pid = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $paraaa);       
                  $paraa['prescription'][$j]['patient_prescription_drug_id'] = $pid;
                  $paraa['prescription'][$j]['patient_prescription_id'] = $iddd;
                  $paraa['prescription'][$j]['day_schedule'] = $clist12->day_schedule;
                  $paraa['prescription'][$j]['day_dosage'] = $clist12->day_dosage;
                  $paraa['prescription'][$j]['dose_course'] = $clist12->dose_course;
                  $paraa['prescription'][$j]['drug_dose'] = $clist12->drug_dose;
                  $paraa['prescription'][$j]['dosage_frequency'] = $clist12->dosage_frequency;
                  $paraa['prescription'][$j]['dosage_unit'] = $clist12->dosage_unit;
                  $paraa['prescription'][$j]['drug_id'] = $clist12->drug_id;
                  $paraa['prescription'][$j]['medicine_name'] = $clist12->medicine_name;
                  $paraa['prescription'][$j]['preffered_intake'] = $clist12->preffered_intake;
                  $paraa['prescription'][$j]['quantity'] = $clist12->quantity;
                  $paraa['prescription'][$j]['remarks'] = $clist12->remarks;

                  $j++;

                  $e++;

              
              }

    
    
       }
       else{
          //  echo "safe";

     
       $inv_lineitems =  $this->db->select("*")
       ->from("patient_prescription_drug ppd")
       ->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")
       ->where("pp.appointment_id='".$appointment_id."' ")
       ->get()
       ->result();
       
  
          $j=0;
          foreach($inv_lineitems as $inv_lineitem){
                                                   
                  $paraa['prescription'][$j]['patient_prescription_drug_id'] = $inv_lineitem->patient_prescription_drug_id;
                  $paraa['prescription'][$j]['patient_prescription_id'] = $inv_lineitem->patient_prescription_id;
                  // $paraa['prescription'][$j]['appointment_id'] = $apt_id;
                  $paraa['prescription'][$j]['day_schedule'] = $inv_lineitem->day_schedule;
                  $paraa['prescription'][$j]['day_dosage'] = $inv_lineitem->day_dosage;
                  $paraa['prescription'][$j]['dose_course'] = $inv_lineitem->dose_course;
                  $paraa['prescription'][$j]['drug_dose'] = $inv_lineitem->drug_dose;
                  $paraa['prescription'][$j]['dosage_frequency'] = $inv_lineitem->dosage_frequency;
                  $paraa['prescription'][$j]['dosage_unit'] = $inv_lineitem->dosage_unit;
                  $paraa['prescription'][$j]['drug_id'] = $inv_lineitem->drug_id;
                  $paraa['prescription'][$j]['medicine_name'] = $inv_lineitem->medicine_name;
                  $paraa['prescription'][$j]['preffered_intake'] = $inv_lineitem->preffered_intake;
                  $paraa['prescription'][$j]['quantity'] = $inv_lineitem->quantity;
                  $paraa['prescription'][$j]['remarks'] = $inv_lineitem->remarks;

                  $j++;
                         
          }
        //End Prescription
        }

        //Start Investigation
          
      $clist12 = $this->db->select("patient_investigation_id")->from("patient_investigation pp")
      ->where("pp.appointment_id='" . $nizam . "'")
      ->get()
      ->row();

      $abc = $this->db->select("*")->from("patient_investigation_line_items pp")
      ->where("pp.patient_investigation_id='" . $clist12->patient_investigation_id . "'")
      ->get()
      ->result();
             
      $chec1 = $this->db->select("*")->from("patient_investigation pp")
      ->where("pp.appointment_id='" . $appointment_id . "' ")
      ->get()
      ->row();


            if(count($chec1) == 0)
            {

                $insert1['appointment_id'] =  $appointment_id;
                $insert1['patient_id'] = $patient_id;
                $insert1['doctor_id'] = $doctor_id;
                $insert1['clinic_id'] = $clinic_id;
                $insert1['modified_by'] = $doctor_id;
                $insert1['created_by'] = $doctor_id;
                $insert1['status'] = 1;
                $insert1['created_date_time'] = date('Y-m-d H:i:s');
                $insert1['modified_date_time'] = date('Y-m-d H:i:s');
                $iddddd = $this->Generic_model->insertDataReturnId('patient_investigation', $insert1); 
                $j = 0;
                foreach($abc as $clist12)
                    {

                        $paraaaq['patient_investigation_id'] = $iddddd;
                        $paraaaq['checked']= $clist12->checked;
                        $paraaaq['investigation_name'] = $clist12->investigation_name;
                        $paraaaq['created_date_time'] = date('Y-m-d H:i:s');
                        $paraaaq['modified_date_time'] = date('Y-m-d H:i:s');
                        $pid = $this->Generic_model->insertDataReturnId('patient_investigation_line_items', $paraaaq);   
                        $paraa['investigation'][$j]['patient_investigation_line_item_id'] = $pid;
                        $paraa['investigation'][$j]['patient_investigation_id'] = $iddddd;
                        // $paraa['investigation'][$j]['appointment_id'] = $apt_id;
                        // $paraa['investigation'][$j]['category'] = $inv_lineitem->category;
                        $paraa['investigation'][$j]['checked'] = $clist12->checked;
                        $paraa['investigation'][$j]['investigation_id'] = $clist12->investigation_id;
                        $paraa['investigation'][$j]['investigation'] = $clist12->investigation_name;
                        $j++;           
                    }
     
    
        }
        else{
            $inv_lineitems =  $this->db->select("*")
            ->from("patient_investigation_line_items ppd")
            ->join("patient_investigation pp","ppd.patient_investigation_id=pp.patient_investigation_id")
            ->where("pp.appointment_id='".$appointment_id."' ")
            ->get()
            ->result();
            
       
               $j=0;
               foreach($inv_lineitems as $inv_lineitem){
     
                 $paraa['investigation'][$j]['patient_investigation_line_item_id'] = $inv_lineitem->patient_investigation_line_item_id;
                 $paraa['investigation'][$j]['patient_investigation_id'] = $inv_lineitem->patient_investigation_id;
                 // $paraa['investigation'][$j]['appointment_id'] = $apt_id;
                 // $paraa['investigation'][$j]['category'] = $inv_lineitem->category;
                 $paraa['investigation'][$j]['checked'] = $inv_lineitem->checked;
                 $paraa['investigation'][$j]['investigation_id'] = $inv_lineitem->investigation_id;
                 $paraa['investigation'][$j]['investigation'] = $inv_lineitem->investigation_name;
                 // $paraa['investigation'][$j]['mrp'] = $inv_lineitem->mrp;
     
                                                 
                       $j++;
                              
               }
     
        }

   


    
    
    //         }
    //    else{
    //       //  echo "safe";
    //       $paraaa['patient_investigation_id'] = 'Sorry';
    //    }
     
       

        //ENd Investigations
        
        //Start CD

            
      $clist123 = $this->db->select("patient_clinical_diagnosis_id")->from("patient_clinical_diagnosis pp")
      ->where("pp.appointment_id='" . $nizam . "'")
      ->get()
      ->row();

      $abcd = $this->db->select("*")->from("patient_cd_line_items pp")
      ->where("pp.patient_clinical_diagnosis_id='" . $clist123->patient_clinical_diagnosis_id . "'")
      ->get()
      ->result();
             
      $chec99 = $this->db->select("*")->from("patient_clinical_diagnosis pp")
      ->where("pp.appointment_id='" . $appointment_id . "' ")
      ->get()
      ->row();

      if(count($chec99) == 0)
       {
          $insert2['appointment_id'] =  $appointment_id;
          $insert2['patient_id'] = $patient_id;
          $insert2['doctor_id'] = $doctor_id;
          $insert2['clinic_id'] = $clinic_id;
          $insert2['modified_by'] = $doctor_id;
          $insert2['created_by'] = $doctor_id;
          $insert2['status'] = 1;
          $insert2['created_date_time'] = date('Y-m-d H:i:s');
          $insert2['modified_date_time'] = date('Y-m-d H:i:s');
          $cdd = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $insert2);  
   
              $check1234 = $this->db->select("*")->from("patient_cd_line_items pp")
              ->where("pp.patient_clinical_diagnosis_id='" . $cdd . "' ")
              ->get()
              ->row();

          
              $e=0;$j = 0;
              foreach($abcd as $clist12)
              {
                  $paraaa1['patient_clinical_diagnosis_id'] = $cdd;
                  $paraaa1['disease_name']= $clist12->disease_name;
                  $paraaa1['created_date_time'] = date('Y-m-d H:i:s');
                  $paraaa1['modified_date_time'] = date('Y-m-d H:i:s');
                  $pid = $this->Generic_model->insertDataReturnId('patient_cd_line_items', $paraaa1);  
                  $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $pid;
                  $paraa['clinicaldiagnosis'][$j]['patient_clinical_diagnosis_id'] = $cdd;
                  // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
                  $paraa['clinicaldiagnosis'][$j]['description'] = $clist12->disease_name;
                  $e++;
                $j++;
              
              }

    
    
       }
       else{
        $inv_lineitems =  $this->db->select("*")
        ->from("patient_cd_line_items ppd")
        ->join("patient_clinical_diagnosis pp","ppd.patient_clinical_diagnosis_id=pp.patient_clinical_diagnosis_id")
        ->where("pp.appointment_id='".$appointment_id."' ")
        ->get()
        ->result();
        
   
           $j=0;
           foreach($inv_lineitems as $inv_lineitem){
 
             $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $inv_lineitem->patient_cd_line_item_id;
             $paraa['clinicaldiagnosis'][$j]['patient_clinical_diagnosis_id'] = $inv_lineitem->patient_clinical_diagnosis_id;
             // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
             $paraa['clinicaldiagnosis'][$j]['description'] = $inv_lineitem->disease_name;
                
                   $j++;
                          
           }
 
       }
     
       
        //End CD

             


        $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');
        
       
        $paraa['appointment_id']= $parameters['appointment_id'];
        $paraa['clinic_id']= $parameters['clinic_id'];
        $paraa['patient_id']= $clist->patient_id;
        $paraa['doctor_id'] = $clist->doctor_id;
        $paraa['umr_no'] = $clist->umr_no;
        if($parent_appointment->parent_appointment_id==0)
        {
            $next_follow_up=$this->db->select("*")->from("appointments")->where("parent_appointment_id='".$parameters['appointment_id']."'")->order_by("appointment_id","desc")->get()->row();
            if($next_follow_up->appointment_id==$appointment_id)
            {
                $paraa['follow_up_date'] = '';
            }else{
                $paraa['follow_up_date'] = date('d-M-Y',strtotime($next_follow_up->appointment_date))." ".date('H:i A',strtotime($next_follow_up->appointment_time_slot));
            }
        }else{
            $next_follow_up=$this->db->select("*")->from("appointments")->where("parent_appointment_id='".$parent_appointment->parent_appointment_id."'")->order_by("appointment_id","desc")->get()->row();
            if($next_follow_up->appointment_id==$parameters['appointment_id'])
            {
                $paraa['follow_up_date'] = '';
            }else{
                $paraa['follow_up_date'] = date('d-M-Y',strtotime($next_follow_up->appointment_date))." ".date('H:i A',strtotime($next_follow_up->appointment_time_slot));
            }
        }
        $paraa['plan']= $clist3->plan;

        $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$paraa, 'requestname' => $method));        
    }
}
