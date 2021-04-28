<?php


defined('BASEPATH') or exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');
define('headers', getallheaders());

require APPPATH . '/libraries/REST_Controller.php';

// require APPPATH . '/controllers/Api.php';

class V1_1 extends REST_Controller
{

    //Doctor Details
    public function getDoctorInfo($parameters, $method, $user_id)
    {
        $docInfo = doctorDetails($user_id);
        if (count($docInfo) > 0) {
            $clinicInfo = $this->Generic_model->getAllRecords('clinic_doctor', array('doctor_id' => $user_id));
            $data['docDetails']['docName'] = getDoctorName($docInfo->doctor_id);
            $data['docDetails']['google_review_link'] = $docInfo->google_review_link;
            $data['docDetails']['languages'] = $docInfo->languages;
            if (count($clinicInfo) > 0) {
                $i = 0;
                foreach ($clinicInfo as $val) {
                    $cInfo = clinicDetails($val->clinic_id);
                    $data['docDetails']['clinicInfo'][$i]['clinic_id'] = $val->clinic_id;
                    $data['docDetails']['clinicInfo'][$i]['clinic_doctor_id'] = $val->clinic_doctor_id;
                    $data['docDetails']['clinicInfo'][$i]['clinic_name'] = $cInfo->clinic_name;
                    $data['docDetails']['clinicInfo'][$i]['location'] = $cInfo->location;
                    $i++;
                }
            }
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $data, 'method' => $method));
        }
    }


    // get Reports Images with Report_id
    public function reportImages($parameters, $method, $user_id)
    {
        extract($parameters);
        $reptInfo = $this->db->query("select images from previous_documents where previous_document_id='" . $previous_document_id . "'")->row();
        $images = explode(",", $reptInfo->images);
        $i = 0;
        foreach ($images as $value) {
            //    echo "ss". substr($value,-3);   
            if (substr($value, -3) == "pdf") {
                $data[$i]['image'] = base_url('uploads/previous_documents/' . $value);
                $data[$i]['type'] = "pdf";
            } else if (substr($value, -3) == "doc") {
                $data[$i]['type'] = "doc";
                $data[$i]['image'] = base_url('uploads/previous_documents/' . $value);
            } else {
                $data[$i]['image'] = base_url('uploads/previous_documents/' . $value);
                $data[$i]['type'] = "images";
            }
            $i++;
            // $data[]['image'] = base_url('uploads/previous_documents/'.$value);
        }
        $this->response(array('code' => '200', 'message' => 'Report Images', 'result' => $data, 'requestname' => $method));
    }

    //How to use umdaa
    public function getTutorialLinks($parameters, $method, $user_id)
    {
        $tutorialLinks = $this->db->select("*")->from("umdaa_tutorials")->where("tutorial_type='doctor'")->get()->result();
        if (sizeof($tutorialLinks) > 0) {
            $i = 0;
            foreach ($tutorialLinks as $value) {
                $data['tutorial'][$i]['tutorial_id'] = $value->umdaa_tutorial_id;
                $data['tutorial'][$i]['tutorial_name'] = $value->tutorial_name;
                $data['tutorial'][$i]['tutorial_link'] = $value->tutorial_link;
                $data['tutorial'][$i]['video_thumbnail'] = base_url() . "uploads/thumbnails/" . $value->video_thumbnail;
                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'Tutorial Videos', 'result' => $data, 'requestname' => $method));
        } else {
            $this->response(array('code' => '201', 'message' => 'No Videos Found', 'requestname' => $method));
        }
    }

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

    public function checkListMaster($parameters, $method, $user_id)
    {
        $consent_forms = $this->db->select("a.consent_form_id, a.consent_form_title, b.department_id")->from("consent_form a")->join("consent_form_department b", "a.consent_form_id = b.consent_form_id")->where("b.department_id='" . $parameters['department_id'] . "'")->order_by('a.consent_form_title', 'ASC')->get()->result();
        $para['checklist_master'] = $this->db->select("checklist_id,description")->from("checklist_master")->get()->result();

        $this->response(array('code' => '200', 'message' => 'Consent Form Details ', 'result' => $para, 'requestname' => $method));
    }


    public function patient_consent_form_add($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $consent_form_id = $parameters['consent_form_id'];
        $patient_id = $parameters['patient_id'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id =  $parameters['doctor_id'];

        $reptInfo = $this->db->query("select * from patient_consent_forms where consent_form_id='" . $consent_form_id . "'
        and appointment_id='" . $appointment_id . "'
        and patient_id='" . $patient_id . "'
        and clinic_id='" . $clinic_id . "' 
        and doctor_id='" . $doctor_id . "' ")->row();

        if (count($reptInfo) > 0) {
            $this->response(array('code' => '201', 'message' => 'Already Exsits', 'result' => 'Already Exsits', 'requestname' => $method));
        } else {
            $cf['consent_form_id'] = $consent_form_id;
            $cf['appointment_id'] = $appointment_id;
            $cf['patient_id'] = $patient_id;
            $cf['clinic_id'] = $clinic_id;
            $cf['doctor_id'] = $doctor_id;
            $cf['created_date_time'] = date('Y-m-d H:i:s');
            $cf['modified_date_time'] = date('Y-m-d H:i:s');
            $pcf_id = $this->Generic_model->insertDataReturnId('patient_consent_forms', $cf);

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
    }

    public function prescription_template_delete($parameters, $method, $user_id)
    {
        extract($parameters);
        $check_exist = $this->db->select("*")->from("prescription_template")->where("prescription_template_id ='" . $parameters['prescription_template_id'] . "'")->get()->row();

        $prescription_template_delete = $parameters['prescription_template_id'];
        // $type = $prescription_template_delete[0]['type'];
        // Delete
        if ($type == "del") {
            $res = $this->Generic_model->deleteRecord('prescription_template', array('prescription_template_id' => $parameters['prescription_template_id']));
            $res = $this->Generic_model->deleteRecord('prescription_template_line_items', array('prescription_template_id' => $parameters['prescription_template_id']));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => NULL, 'requestname' => $method));
        } else if ($type == 'edit') {
            $i = 0;
            $prescription_template_edit['prescription_template_name'] = $prescription_template_name;
            $updateRes = $this->Generic_model->updateData('prescription_template', $prescription_template_edit, array('prescription_template_id' => $prescription_template_id));

            $this->response(array('code' => '200', 'message' => 'Prescription Saved Successfully', 'result' => NULL, 'requestname' => $method));
        } else {
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
        if ($type == "del") {
            $res = $this->Generic_model->deleteRecord('doctor_investigation_template', array('investigation_template_id' => $investigation_template_delete[0]['investigation_template_id']));
            $res = $this->Generic_model->deleteRecord('doctor_investigation_template_line_items', array('investigation_template_id' => $investigation_template_delete[0]['investigation_template_id']));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => NULL, 'requestname' => $method));
        } else if ($type == 'edit') {
            $i = 0;
            $investigation_template_edit['investigation_template_name'] = $investigation_template_name;
            $updateRes = $this->Generic_model->updateData('doctor_investigation_template', $investigation_template_edit, array('investigation_template_id' => $investigation_template_id));

            $this->response(array('code' => '200', 'message' => 'Investigation Saved Successfully', 'result' => NULL, 'requestname' => $method));
        } else {
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
            ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='" . $clinic_id . "'")
            ->get()
            ->row();
        if (count($clist) > 0) {
            $patient_id = $clist->patient_id;
            $doctor_id = $clist->doctor_id;

            $patient_allergy = $this->db->select("*")->from("patients ps")
                ->where("ps.patient_id='" . $patient_id . "' ")
                ->get()
                ->row();

            $paraa['drug_allergy'] = $patient_allergy->allergy == 'No' ? '' : $patient_allergy->allergy;

            $clist1 = $this->db->select("*")->from("appointments ap")
                ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='" . $doctor_id . "'")
                ->order_by("appointment_id", "desc")
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

            if (count($chec) == 0) {
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

                if (count($ab220) != 0) {
                    $e = 0;
                    $j = 0;
                    foreach ($ab220 as $clist12) {
                        $paraaa['patient_prescription_id'] = $iddd;
                        $paraaa['drug_id'] = $clist12->drug_id;
                        $paraaa['medicine_name'] = $clist12->medicine_name;
                        $paraaa['day_schedule'] = $clist12->day_schedule;
                        $paraaa['preffered_intake'] = $clist12->preffered_intake;
                        $paraaa['day_dosage'] = $clist12->day_dosage;
                        $paraaa['dose_course'] = $clist12->dose_course;
                        $paraaa['drug_dose'] = $clist12->drug_dose;
                        $paraaa['dosage_frequency'] = $clist12->dosage_frequency;
                        $paraaa['quantity'] = $clist12->quantity;
                        $paraaa['remarks'] = $clist12->remarks;
                        $paraaa['created_date_time'] = date('Y-m-d H:i:s');
                        $paraaa['modified_date_time'] = date('Y-m-d H:i:s');
                        $pid = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $paraaa);

                        $paraa['prescription'][$j]['patient_prescription_drug_id'] = $pid;
                        $paraa['prescription'][$j]['patient_prescription_id'] = $iddd;
                        // $paraa['prescription'][$j]['patient_allergy'] = $patient_allergy->allergy;
                        $paraa['prescription'][$j]['day_schedule'] = $clist12->day_schedule;
                        $paraa['prescription'][$j]['day_dosage'] = $clist12->day_dosage;
                        $paraa['prescription'][$j]['dose_course'] = $clist12->dose_course;
                        $paraa['prescription'][$j]['mode'] = $clist12->mode;
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
                } else {
                    $paraa['prescription'] = array();
                }
            } else {

                $inv_lineitems =  $this->db->select("*")
                    ->from("patient_prescription_drug ppd")
                    ->join("patient_prescription pp", "ppd.patient_prescription_id=pp.patient_prescription_id")
                    ->where("pp.appointment_id='" . $appointment_id . "' ")
                    ->get()
                    ->result();

                if (count($inv_lineitems) != 0) {
                    $j = 0;
                    foreach ($inv_lineitems as $inv_lineitem) {
                        // $paraa['prescription'][$j]['patient_allergy'] = $patient_allergy->allergy;
                        $paraa['prescription'][$j]['patient_prescription_drug_id'] = $inv_lineitem->patient_prescription_drug_id;
                        $paraa['prescription'][$j]['patient_prescription_id'] = $inv_lineitem->patient_prescription_id;
                        // $paraa['prescription'][$j]['appointment_id'] = $apt_id;
                        $paraa['prescription'][$j]['day_schedule'] = $inv_lineitem->day_schedule;
                        $paraa['prescription'][$j]['day_dosage'] = $inv_lineitem->day_dosage;
                        $paraa['prescription'][$j]['mode'] = $inv_lineitem->mode;
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
                } else {
                    $paraa['prescription'] = array();
                }

                //End Prescription
            }

            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'No Previous Appointments', 'result' => null, 'requestname' => $method));
        }
    }

    public function get_latestClinicalDiagnosis($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $clinic_id = $parameters['clinic_id'];


        $clist = $this->db->select("*")->from("appointments ap")
            ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='" . $clinic_id . "'")
            ->get()
            ->row();
        $patient_id = $clist->patient_id;
        $doctor_id = $clist->doctor_id;

        $clist1 = $this->db->select("*")->from("appointments ap")
            ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='" . $doctor_id . "'")
            ->order_by("appointment_id", "desc")
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

        if (count($chec99) == 0) {
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


            $e = 0;
            $j = 0;
            foreach ($abcd as $clist12) {
                $paraaa1['patient_clinical_diagnosis_id'] = $cdd;
                $paraaa1['disease_name'] = $clist12->disease_name;
                $paraaa1['created_date_time'] = date('Y-m-d H:i:s');
                $paraaa1['modified_date_time'] = date('Y-m-d H:i:s');
                $pid = $this->Generic_model->insertDataReturnId('patient_cd_line_items', $paraaa1);
                $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $pid;
                $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $cdd;
                $paraa['clinicaldiagnosis'][$j]['cd_id'] = $clist12->clinical_diagnosis_id;
                // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
                $paraa['clinicaldiagnosis'][$j]['disease_name'] = $clist12->disease_name;
                $e++;
                $j++;
            }
        } else {
            $inv_lineitems =  $this->db->select("*")
                ->from("patient_cd_line_items ppd")
                ->join("patient_clinical_diagnosis pp", "ppd.patient_clinical_diagnosis_id=pp.patient_clinical_diagnosis_id")
                ->where("pp.appointment_id='" . $appointment_id . "' ")
                ->get()
                ->result();


            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {
                $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $inv_lineitem->patient_cd_line_item_id;
                $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $inv_lineitem->patient_clinical_diagnosis_id;
                $paraa['clinicaldiagnosis'][$j]['cd_id'] = $inv_lineitem->clinical_diagnosis_id;
                // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
                $paraa['clinicaldiagnosis'][$j]['disease_name'] = $inv_lineitem->disease_name;

                $j++;
            }
        }


        //End CD



        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa, 'requestname' => $method));
    }

    public function getLatestCD($parameters, $method, $user_id)
    {
        extract($parameters);
        $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id));
        $check = $this->db->query("select * from patient_clinical_diagnosis where appointment_id='" . $appointment_id . "'")->row();
        // echo $this->db->last_query();
        // for this appointment if patient clinical diagnosis not exists
        if (count($check) <= 0) {
            // then find for previous appointment having clinical diagnosis
            $app_id = $this->db->query("select appointment_id from appointments where patient_id='" . $appInfo->patient_id . "' and doctor_id='" . $appInfo->doctor_id . "' and clinic_id='" . $appInfo->clinic_id . "' order by appointment_id DESC LIMIT 1,1")->row();
            // echo $this->db->last_query();
            // if appointment found
            if (count($app_id) > 0) {
                $cdInfo = $this->db->query("select * from patient_clinical_diagnosis pcd,patient_cd_line_items pcdl where pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id and pcd.appointment_id='" . $app_id->appointment_id . "' and pcdl.created_by='" . $app_id->doctor_id . "'")->result();
                if (count($cdInfo) > 0) {
                    // if clinical diagnosis exits add to present appointment 
                    $data['patient_id'] = $appInfo->patient_id;
                    $data['clinic_id'] = $appInfo->clinic_id;
                    $data['doctor_id'] = $appInfo->doctor_id;
                    $data['appointment_id'] = $appInfo->appointment_id;
                    $data['umr_no'] = $appInfo->umr_no;
                    $data['status'] = 1;
                    $data['created_by'] = $user_id;
                    $data['created_date_time'] = date('Y-m-d H:i:s');
                    $data['modified_by'] = $user_id;
                    $data['modified_date_time'] = date('Y-m-d H:i:s');
                    $patient_clinical_diagnosis_id = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $data);

                    $j = 0;

                    foreach ($cdInfo as $value) {
                        // Add Clinical diagnosis Line Items to present appointment
                        if ($value->code == "") {
                            $cdMaster = $this->Generic_model->getSingleRecord('clinical_diagnosis', array('clinical_diagnosis_id' => $value->clinical_diagnosis_id));
                            if (count($cdMaster) > 0) {
                                $code = $cdMaster->code;
                            } else {
                                $code = "";
                            }
                        } else {
                            $code = $value->code;
                        }

                        $data2['patient_clinical_diagnosis_id'] = $patient_clinical_diagnosis_id;
                        $data2['clinical_diagnosis_id'] = $value->clinical_diagnosis_id;
                        $data2['disease_name'] = $value->disease_name;
                        $data2['code'] = $code;
                        $data2['created_by'] = $user_id;
                        $data2['created_date_time'] = date('Y-m-d H:i:s');
                        $data2['modified_by'] = $user_id;
                        $data2['modified_date_time'] = date('Y-m-d H:i:s');
                        $patient_cd_line_item_id = $this->Generic_model->insertDataReturnId('patient_cd_line_items', $data2);

                        $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $value->patient_cd_line_item_id;
                        $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $value->patient_clinical_diagnosis_id;
                        $paraa['clinicaldiagnosis'][$j]['cd_id'] = $value->clinical_diagnosis_id;
                        $paraa['clinicaldiagnosis'][$j]['disease_name'] = $value->disease_name . " (" . $code . ")";
                        $j++;
                    }
                    $this->response(array('code' => '200', 'message' => 'success', 'result' => $paraa, 'method' => $method));
                } else {
                    $paraa['clinicaldiagnosis'] = [];
                    $this->response(array('code' => '200', 'message' => 'success', 'result' => $paraa, 'method' => $method));
                }
            } else {
                $paraa['clinicaldiagnosis'] = [];
                $this->response(array('code' => '200', 'message' => 'success', 'result' => $paraa, 'method' => $method));
            }
        }
        // if clinical diagnosis exists for present appointment
        else {
            $cdInfo = $this->db->query("select * from patient_clinical_diagnosis pcd,patient_cd_line_items pcdl where pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id and pcd.appointment_id='" . $appointment_id . "' and pcdl.created_by='" . $appInfo->doctor_id . "'")->result();
            if (count($cdInfo) > 0) {
                $j = 0;
                foreach ($cdInfo as $value) {
                    if ($value->code == "") {
                        $cdMaster = $this->Generic_model->getSingleRecord('clinical_diagnosis', array('clinical_diagnosis_id' => $value->clinical_diagnosis_id));
                        if (count($cdMaster) > 0) {
                            $code = $cdMaster->code;
                        } else {
                            $code = "";
                        }
                    } else {
                        $code = $value->code;
                    }

                    $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $value->patient_cd_line_item_id;
                    $paraa['clinicaldiagnosis'][$j]['clinical_diagnosis_id'] = $value->patient_clinical_diagnosis_id;
                    $paraa['clinicaldiagnosis'][$j]['cd_id'] = $value->clinical_diagnosis_id;
                    $paraa['clinicaldiagnosis'][$j]['disease_name'] = $value->disease_name . " (" . $code . ")";
                    $j++;
                }
                $this->response(array('code' => '200', 'message' => 'success', 'result' => $paraa, 'method' => $method));
            } else {
                $paraa['clinicaldiagnosis'] = [];
                $this->response(array('code' => '200', 'message' => 'success', 'result' => $paraa, 'method' => $method));
            }
        }
    }

    public function patient_consent_form_list($parameters, $method, $user_id)
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

            $consent_name = $this->db->select("*")->from("consent_form pp")
                ->where("pp.consent_form_id='" . $cform->consent_form_id . "' ")
                ->get()
                ->row();

            $para['consent_form'][$i]['patient_consent_form_name'] = $consent_name->consent_form_title;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Success', 'count' => count($clist), 'result' => $para, 'requestname' => $method));
    }





    public function consentFormDownload($parameters, $method, $user_id)
    {
        $patient_consent_form_id = $parameters['patient_consent_form_id'];
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $user_id;
        $consent_form_id = $parameters['consent_form_id'];
        $data['appointment'] = $this->db->select("a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id,p.patient_id,p.title,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.umr_no,p.age,p.age_unit,p.gender as p_gender,c.clinic_id,c.clinic_name")
            ->from("appointments a")
            ->join("doctors d", "a.doctor_id = d.doctor_id", "left")
            ->join("department de", "d.department_id=de.department_id", "left")->join("patients p", "a.patient_id=p.patient_id", "left")
            ->join("clinics c", "a.clinic_id=c.clinic_id", "left")
            ->where("a.appointment_id='" . $appointment_id . "'")->order_by("a.appointment_id", "desc")->get()->row();
        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->join(" consent_form_department c", "c.consent_form_id = a.consent_form_id")->join("department b", "c.department_id = b.department_id")->where("a.archieve != 1 and a.consent_form_id ='" . $consent_form_id . "'")->get()->row();
        $data['consent_form_id'] = $id[0];


        $html = $this->load->view('consentform/consentform_patient_pdf', $data, true);
        $pdfFilePath = rand(10, 100) . "_" . $clinic_id . "_" . $patient_id . "_" . $appointment_id . "_" . date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['patient_consent_form'] = $pdfFilePath;

        $ok = $this->Generic_model->updateData("patient_consent_forms", $para, array('patient_consent_form_id' => $patient_consent_form_id));

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
    }

    public function consent_form_pdf($parameters, $method, $user_id)
    {
        $web_patient_consent_form_id = $parameters['web_patient_consent_form_id'];
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];

        $data['details'] = $this->db->select("*")->from("webpatients_consent_form")
            ->where("web_patient_consent_form_id='" . $web_patient_consent_form_id . "'
     and appointment_id='" . $appointment_id . "'")
            ->get()->row();

        $html = $this->load->view('consentform/consentform_web_patient_pdf', $data, true);
        $pdfFilePath = rand(10, 100) . "_" . $clinic_id . "_" . $patient_id . "_" . $appointment_id . "_" . date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['pdf_name'] = base_url() . 'uploads/consentforms/' . $pdfFilePath;
        // $para['web_patient_consent_form'] = $pdfFilePath;

        // $ok = $this->Generic_model->updateData("patient_consent_forms", $para, array('patient_consent_form_id' => $patient_consent_form_id));

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
    }

    public function more_forms_pdf($parameters, $method, $user_id)
    {
        $id = $parameters['id'];
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $form_list_line_item_id = $parameters['form_list_line_item_id'];

        $data['details'] = $this->db->select("*")->from("patient_form_list")
            ->where("id='" . $id . "'
     and appointment_id='" . $appointment_id . "'")
            ->get()->row();


        $html = $this->load->view('consentform/moreform_web_patient_pdf', $data, true);
        $pdfFilePath = rand(10, 100) . "_" . $clinic_id . "_" . $patient_id . "_" . $appointment_id . "_" . date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['pdf_name'] = base_url() . 'uploads/consentforms/' . $pdfFilePath;
        // $para['web_patient_consent_form'] = $pdfFilePath;

        // $ok = $this->Generic_model->updateData("patient_consent_forms", $para, array('patient_consent_form_id' => $patient_consent_form_id));

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
    }


    public function patient_checklist($parameters, $method, $user_id)
    {

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

    //21-01-2020
    public function CompleteSee($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $form_type = "Systemic Examination";

        $appointment  = $this->db->select("*")->from("appointments")
            ->where("patient_id= '" . $patient_id . "'")
            ->get()
            ->row();
        $doctor_id = $this->db->select("*")->from("doctors")
            ->where("doctor_id= '" . $appointment->doctor_id . "'")
            ->get()
            ->row();

        $abc  = $this->db->select("*")->from("form")
            ->join("form_department", "form_department.form_id = form.form_id")
            ->where("form_department.department_id = '" . $doctor_id->department_id . "' and form.form_type = '" . $form_type . "' ")
            ->get()
            ->result();

        $i = 0;
        foreach ($abc as $cform1) {
            $para['form_information'][$i]['form_id'] = $cform1->form_id;
            $para['form_information'][$i]['form_name'] = $cform1->form_name;
            $para['form_information'][$i]['form_type'] = $cform1->form_type;
            $para['form_information'][$i]['department_id'] = $cform1->department_id;

            //Inner Heading
            // $form_id = $parameters['form_id'];
            $clist2 = $this->db->select("title ,section_id,form_id")->from("section cs")
                ->where("cs.form_id='" . $cform1->form_id  . "' and cs.parent_section_id='0' ")
                ->get()
                ->result();

            $x = 0;
            foreach ($clist2 as $cform2) {
                $para['form_information'][$i]['heading'][$x]['id'] = $x;
                $para['form_information'][$i]['heading'][$x]['title'] = $cform2->title;
                $para['form_information'][$i]['heading'][$x]['form_id'] = $cform2->form_id;
                $para['form_information'][$i]['heading'][$x]['section_id'] = $cform2->section_id;
                //Inner Sub Heading
                $clist3 = $this->db->select("title,section_id,format_type,form_id,parent_section_id")->from("section css")
                    ->where("css.form_id='" . $cform2->form_id . "' and css.parent_section_id='" . $cform2->section_id . "' ")
                    ->get()
                    ->result();

                $y = 0;
                foreach ($clist3 as $cform3) {
                    $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['title'] = $cform3->title;
                    $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['form_id'] = $cform2->form_id;
                    $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['parent_section_id'] = $cform3->parent_section_id;
                    $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['section_id'] = $cform3->section_id;
                    $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['format_type'] = $cform3->format_type;

                    $clist4 = $this->db->select("field_name,field_id,section_id,field_type")->from("field ff")
                        ->where("ff.section_id='" . $cform3->section_id . "' and parent_field_id = '0' and parent_option_id = '0' ORDER BY field_id ASC")
                        ->get()
                        ->result();
                    $z = 0;
                    foreach ($clist4 as $cform4) {
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_name'] = $cform4->field_name;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['section_id'] = $cform4->section_id;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_id'] = $cform4->field_id;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_type'] = $cform4->field_type;

                        $clist5 = $this->db->select("option_name,option_default,field_id,option_id")->from("field_option ff")
                            ->where("ff.field_id='" . $cform4->field_id . "'  ")
                            ->get()
                            ->result();
                        $a = 0;
                        foreach ($clist5 as $cform5) {
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['option_type'] = 'radio';
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['option_value'] = $cform5->option_name;
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['label_id'] = $cform5->field_id;
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['option_id'] = $cform5->option_id;
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['option_default'] = $cform5->option_default;
                            $para['form_information'][$i]['heading'][$x]['sub_heading'][$y]['label'][$z][$a]['label_value'] = $cform4->field_name;
                            $a++;
                        }
                        $z++;
                    }
                    $y++;
                }
                //Inner Sub Heading
                $x++;
            }
            //Inner Heading
            $i++;
        }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
    }
    //21-01-2020

    // 04-01-2020 SE Api
    public function CompleteSe($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $form_type = $parameters['form_type'];


        // $form_type = "HOPI";

        // $appointment  = $this->db->select("*")->from("appointments")
        // ->where("patient_id= '".$patient_id."'")
        // ->get()
        // ->row();

        // $doctor_department = $this->db->select("*")->from("department")
        // ->where("doctor_id= '".$doctor_id ."'")
        // ->get()
        // ->row();

        // $appointment  = $this->db->select("*")->from("appointments")
        // ->where("patient_id= '".$patient_id."'")
        // ->get()
        // ->row();

        // $doctor_id = $this->db->select("*")->from("doctors")
        // ->where("doctor_id= '".$appointment->doctor_id."'")
        // ->get()
        // ->row();

        //Hopi API START

        // $hopi=  $this->db->select("form_id")->from("patient_ps_line_items")
        // ->join("patient_presenting_symptoms",
        // "patient_presenting_symptoms.patient_presenting_symptoms_id  = 
        //  patient_ps_line_items.patient_presenting_symptoms_id ")
        // ->where("patient_id = '" .$patient_id . "' ")
        // ->get()
        // ->result();

        // foreach ($hopi as $ho)
        // {
        //     $abc[]  = $this->db->select("*")->from("form")
        //     ->where("form_id = '" .$ho->form_id. "' ")
        //     ->get()
        //     ->row();
        // }


        //Hopi API END

        //OS,GPE,HISTORY API START
        // $abc  = $this->db->select("*")->from("form")
        // // ->join("form_department","form_department.form_id = form.form_id")
        // ->where(" form.form_type = '" .$form_type. "' ")
        // ->get()
        // ->result();
        //OS,GPE,HISTORY API END



        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();

        $doctor_details = $this->db->select("*")->from('doctors')
            ->where("doctor_id='" . $appointmentList->doctor_id . "'")
            ->get()->row();

        if ($form_type == "Systemic Examination") {
            // SE API Start
            $abc  = $this->db->select("*")->from("form")
                ->join("form_department", "form_department.form_id = form.form_id")
                ->where("form_department.department_id = '" . $doctor_details->department_id . "' and form.form_type = '" . $form_type . "' ")
                ->get()
                ->result();
            // SE API End
        } else if (
            $form_type == "Other Systems" || $form_type == "GPE" || $form_type == "Past History"
            || $form_type == "Personal History" ||  $form_type == "Treatment History"
            || $form_type == "Family History" || $form_type == "Social History"
        ) {
            // OS,GPE,HISTORY API START
            $abc  = $this->db->select("*")->from("form")
                ->where(" form.form_type = '" . $form_type . "' ")
                ->get()
                ->result();
            // OS,GPE,HISTORY API END
        } else {
            //Hopi Start
            $hopi =  $this->db->select("form_id")->from("patient_ps_line_items")
                ->join(
                    "patient_presenting_symptoms",
                    "patient_presenting_symptoms.patient_presenting_symptoms_id  = 
         patient_ps_line_items.patient_presenting_symptoms_id "
                )
                ->where("appointment_id = '" . $appointment_id . "' ")
                ->get()
                ->result();

            foreach ($hopi as $ho) {
                $abc[]  = $this->db->select("*")->from("form")
                    ->where("form_id = '" . $ho->form_id . "' ")
                    ->get()
                    ->row();
            }
            //Hopi End

        }



        $i = 0;
        foreach ($abc as $cform1) {
            $dataaa['form_information'][$i]['form_id'] = $cform1->form_id;
            $dataaa['form_information'][$i]['form_name'] = $cform1->form_name;
            $dataaa['form_information'][$i]['form_type'] = $cform1->form_type;
            $dataaa['form_information'][$i]['department_id'] = $cform1->department_id;

            //Inner Heading
            // $form_id = $parameters['form_id'];
            $clist2 = $this->db->select("title ,section_id,form_id")->from("section cs")
                ->where("cs.form_id='" . $cform1->form_id  . "' and cs.parent_section_id='0' ")
                ->get()
                ->result();

            $x = 0;
            foreach ($clist2 as $cform2) {
                $dataaa['form_information'][$i]['heading'][$x]['id'] = $x;
                $dataaa['form_information'][$i]['heading'][$x]['title'] = $cform2->title;
                $dataaa['form_information'][$i]['heading'][$x]['form_id'] = $cform2->form_id;
                $dataaa['form_information'][$i]['heading'][$x]['section_id'] = $cform2->section_id;
                //Inner Sub Heading
                $clist3 = $this->db->select("title,section_id,format_type,form_id,parent_section_id")->from("section css")
                    ->where("css.form_id='" . $cform2->form_id . "' and css.parent_section_id='" . $cform2->section_id . "' ")
                    ->get()
                    ->result();

                $y = 0;
                foreach ($clist3 as $cform3) {
                    $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['title'] = $cform3->title;
                    $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['form_id'] = $cform2->form_id;
                    $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['parent_section_id'] = $cform3->parent_section_id;
                    $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['section_id'] = $cform3->section_id;
                    $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['format_type'] = $cform3->format_type;

                    $clist4 = $this->db->select("field_name,field_id,section_id,field_type")->from("field ff")
                        ->where("ff.section_id='" . $cform3->section_id . "' and parent_field_id = '0' and parent_option_id = '0' ORDER BY field_id ASC")
                        ->get()
                        ->result();

                    $z = 0;
                    foreach ($clist4 as $cform4) {
                        $abc = rand(10, 10000);
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['r_number'] = strval($abc);
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_name'] = $cform4->field_name;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['section_id'] = $cform4->section_id;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_id'] = $cform4->field_id;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_type'] = $cform4->field_type;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['format_type'] = $cform3->format_type;
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['option_id'] = "0";
                        $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['parent_section_id'] =  $cform3->parent_section_id;

                        $clist5 = $this->db->select("option_name,option_default,field_id,option_id")->from("field_option ff")
                            ->where("ff.field_id='" . $cform4->field_id . "'  ")
                            ->get()
                            ->result();
                        $a = 0;
                        foreach ($clist5 as $cform5) {
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_name'] = strval($abc);
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_name'] = $cform5->option_name;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['field_id'] = $cform5->field_id;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_id'] = $cform5->option_id;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_default'] = $cform5->option_default;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['section_id'] = $cform4->section_id;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['format_type'] = $cform3->format_type;
                            $dataaa['form_information'][$i]['heading'][$x]['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['parent_section_id'] =  $cform3->parent_section_id;
                            $a++;
                        }
                        $z++;
                    }
                    $y++;
                }
                //Inner Sub Heading
                $x++;
            }
            //Inner Heading
            $i++;
        }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $dataaa, 'requestname' => $method));
    }
    // 04-01-2020 End Api

    // 07-02-2020 Api
    public function custom_form_recursive_systemicc($parameters, $method, $user_id)
    {
        $condition = array('form_type' => $parameters['form_type']);
        $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');
        // echo $this->db->last_query();
        $sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief ,a.section_image")->from("section a")->join("form b", "a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and b.form_id='" . $parameters['form_id'] . "' and parent_section_id = 0")->order_by("a.section_id ASC")->get()->result();
        // echo $this->db->last_query();
        // echo "\n-----------------------------------------------------------------------\n";
        $a = 0;
        foreach ($sections as $key => $pvalue) {
            $data['form']['formType'] = $pvalue->form_type;
            $data['form']['formName'] = $pvalue->form_name;
            $data['form']['form_id'] = $pvalue->form_id;
            $data['form']['heading'][$a]['section_id'] = $pvalue->section_id;
            if ($pvalue->section_image != "" || $pvalue->section_image != NULL) {
                $data['form']['heading'][$a]['image_path'] = base_url() . "/uploads/section_images/" . $pvalue->section_image;
            } else {
                $data['form']['heading'][$a]['image_path'] = "";
            }

            $data['form']['heading'][$a]['title'] = $pvalue->title;
            $data['form']['heading'][$a]['brief'] = $pvalue->brief;
            $data['form']['heading'][$a]['textbox'] = 1;
            $data['form']['heading'][$a]['collapse'] = 0;

            $sub_sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief")->from("section a")->join("form b", "a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and b.form_id='" . $parameters['form_id'] . "' and parent_section_id='" . $pvalue->section_id . "'")->order_by("a.section_id")->get()->result();
            // echo "\nss".$this->db->last_query();
            // echo "\n-----------------------------------------------------------------------\n";
            $i = 0;
            foreach ($sub_sections as $key => $value) {
                $data['form']['heading'][$a]['sub_heading'][$i]['sub_section_id'] = $value->section_id;
                $data['form']['heading'][$a]['sub_heading'][$i]['title'] = $value->title;
                $data['form']['heading'][$a]['sub_heading'][$i]['format'] = $value->format_type;

                if ($value->format_type == 'tabular') {
                    $label_result = $this->db->select("*")->from("field")->where("row_index IS NOT NULL and column_index IS NOT NULL and section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->group_by("row_index")->order_by("field_id")->get()->result();
                    // echo "\nIF=".$this->db->last_query();
                    // echo "\n-----------------------------------------------------------------------\n";
                    $j = 0;
                    $di = 0;
                    $array_dup = array();
                    foreach ($label_result as $key2 => $value2) {
                        $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['row_title'] = strtok($value2->field_name, '_');
                        $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['row_index'] = $value2->row_index;
                        $row_elements = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and row_index='" . $value2->row_index . "'")->order_by("field_id", "asc")->get()->result();
                        // echo "\nIF=".$this->db->last_query();
                        // echo "\n-----------------------------------------------------------------------\n";

                        $k = 0;
                        foreach ($row_elements as $key => $rresult) {
                            $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['id'] = $rresult->field_id;
                            $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['widgetType'] = $rresult->field_type;
                            $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['labelText'] = substr($rresult->field_name, (strpos($rresult->field_name, '_') ?: -1) + 1);
                            $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['row_index'] = $rresult->row_index;
                            $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['column_index'] = $rresult->column_index;
                            $options = $this->db->select("*")->from("field_option")->where("field_id='" . $rresult->field_id . "'")->order_by("field_id", "ASC")->get()->result();
                            // echo "\nIF=".$this->db->last_query();
                            // echo "\n-----------------------------------------------------------------------\n";
                            $l = 0;
                            foreach ($options as $okey => $oresult) {
                                $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['field_option'][$l]['optionText'] = $oresult->option_name;
                                $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['field_option'][$l]['default'] = $oresult->option_default;
                                $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['field_option'][$l]['id'] = $oresult->option_id;
                                $data['form']['heading'][$a]['sub_heading'][$i]['rows'][$j]['field_name'][$k]['field_option'][$l]['label_id'] = $oresult->field_id;
                                // if ($oresult->dependency == 1) {
                                //     $dep_chk = $this->db->query("select * from field where parent_field_id=" . $oresult->field_id . " and parent_option_id=" . $oresult->option_id." order by field_id ASC")->result();
                                //     $m = 0;
                                //     $n = 0;
                                //     foreach ($dep_chk as $depkey => $depresult) {
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['id'] = $depresult->field_id;
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['widgetType'] = $depresult->field_type;
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['labelText'] = substr($depresult->field_name, (strpos($depresult->field_name, '_') ?: -1) + 1);
                                //         $dep_options = $this->db->query("select * from field_option where field_id=" . $depresult->field_id." order by field_id ASC")->result();
                                //         $o = 0;
                                //         foreach ($dep_options as $depokey => $deporesult) {
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['optionText'] = $deporesult->option_name;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['default'] = $deporesult->option_default;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['id'] = $deporesult->option_id;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['label_id'] = $deporesult->field_id;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['dependency'] = array();
                                //             $o++;
                                //         }
                                //         $m++;
                                //         $n++;
                                //     }
                                // } else {
                                //     $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'] = array();
                                // }
                                $l++;
                            }
                            $k++;
                        }
                        $j++;
                    }
                } else {
                    $label_result = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->order_by("field_id", "ASC")->get()->result();
                    // echo "\nELSE=".$this->db->last_query();
                    // echo "\n-----------------------------------------------------------------------\n";
                    $j = 0;
                    $di = 0;
                    $array_dup = array();
                    foreach ($label_result as $key2 => $value2) {
                        $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['id'] = $value2->field_id;
                        $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['widgetType'] = $value2->field_type;
                        $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['labelText'] = $value2->field_name;
                        $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
                        // echo "\nELSE=".$this->db->last_query();
                        // echo "\n-----------------------------------------------------------------------\n";
                        $k = 0;
                        foreach ($field_option_result as $key3 => $value3) {
                            $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['field_option'][$k]['optionText'] = $value3->option_name;
                            $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['field_option'][$k]['default'] = $value3->option_default;
                            $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['field_option'][$k]['id'] = $value3->option_id;
                            $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['field_option'][$k]['label_id'] = $value2->field_id;
                            if ($value3->dependency == 1) {
                                $depresult = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->order_by("field_id", "ASC")->get()->result_array();
                                // echo "\nELSE=".$this->db->last_query();
                                // echo "\n-----------------------------------------------------------------------\n";

                                $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id, $di, $data['form']['heading'][$a]['sub_heading'][$i]['field_name'][$j]['field_option'][$k]['dependency'][0]);
                            } else {
                                $depinfo = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->order_by("field_id", "ASC")->get()->result_array();
                                // echo "\nELSE=".$this->db->last_query();
                                // echo "\n-----------------------------------------------------------------------\n";
                            }

                            $k++;
                        }
                        $j++;
                    }
                }
                $i++;
            }
            $a++;
        }
        $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
    }
    // 07-02-2020n End Api

    //11-02-2020 start Api


    public function getchilddetailss(array $elements, $parentfield_id = 0, $parentoption_id = 0, $section_id = 0, &$rdi, &$in_arr = array())
    {
        //$recuarray = $this->db->query("select * from field where section_id=".$section_id." and parent_field_id=".$parentfield_id." and parent_option_id=".$parentoption_id)->result_array();
        $ri = 0;

        foreach ($elements as $result) {
            $in_arr['labels'][$ri]['id'] = $result['field_id'];
            $in_arr['labels'][$ri]['widgetType'] = $result['field_type'];
            $in_arr['labels'][$ri]['labelText'] = $result['field_name'];
            $dep_field_option_result = $this->db->select('*')->from('field_option')->where('field_id=' . $result['field_id'])->get()->result();
            $l = 0;
            if (count($dep_field_option_result) > 0) {
                foreach ($dep_field_option_result as $key5 => $value5) {
                    $in_arr['labels'][$ri]['options'][$l]['optionText'] = $value5->option_name;
                    $in_arr['labels'][$ri]['options'][$l]['default'] = $value5->option_default;
                    $in_arr['labels'][$ri]['options'][$l]['id'] = $value5->option_id;
                    $in_arr['labels'][$ri]['options'][$l]['label_id'] = $result['field_id'];
                    $recuarray = $this->db->select("*")->from("field")->where("section_id=" . $section_id . " and parent_field_id=" . $result['field_id'] . " and parent_option_id=" . $value5->option_id)->get()->result_array();
                    //$in_arr['labels'][$ri]['options'][$l]['dependency'] = array();
                    $recuresult = $this->getchilddetailss($recuarray, $result['field_id'], $value5->option_id, $section_id, $rdi);
                    if (count($recuresult) > 0) {
                        $in_arr['labels'][$ri]['options'][$l]['dependency'][] = $recuresult;
                        $rdi++;
                    } else {
                        $in_arr['labels'][$ri]['options'][$l]['dependency'] = array();
                    }
                    $l++;
                }
                $ri++;
            } else {
                $ri++;
            }
        }
        return $in_arr;
    }

    //11-02-2020 End Api

    //Systemic Examination Api on 30-12-2019.
    public function get_systemic_examination_main_data($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];
        $form_type = $parameters['form_type'];

        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();

        $doctor_details = $this->db->select("*")->from('doctors')
            ->where("doctor_id='" . $appointmentList->doctor_id . "'")
            ->get()->row();

        if ($form_type == "Systemic Examination") {
            $abc  = $this->db->select("*")->from("form")
                ->join("form_department", "form_department.form_id = form.form_id")
                ->where("form_department.department_id = '" . $doctor_details->department_id . "' and form.form_type = '" . $form_type . "' ")
                ->get()
                ->result();
        } else if (
            $form_type == "Other Systems" || $form_type == "GPE" || $form_type == "Past History"
            || $form_type == "Personal History" ||  $form_type == "Treatment History"
            || $form_type == "Family History" || $form_type == "Social History"
        ) {
            // OS,GPE,HISTORY API START
            $abc  = $this->db->select("*")->from("form")
                ->where(" form.form_type = '" . $form_type . "' ")
                ->get()
                ->result();
            // OS,GPE,HISTORY API END
        } else {
            //Hopi Start
            $hopi =  $this->db->select("form_id")->from("patient_ps_line_items")
                ->join(
                    "patient_presenting_symptoms",
                    "patient_presenting_symptoms.patient_presenting_symptoms_id  = 
         patient_ps_line_items.patient_presenting_symptoms_id "
                )
                ->where("appointment_id = '" . $appointment_id . "' ")
                ->get()
                ->result();

            // echo $this->db->last_query();

            foreach ($hopi as $ho) {
                $abc[]  = $this->db->select("*")->from("form")
                    ->where("form_id = '" . $ho->form_id . "' ")
                    ->get()
                    ->row();
            }
            //Hopi End

        }

        if (count($abc) == 0) {
            // $dataaa['form_information'] = "No Data";
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'No Data', 'requestname' => $method));
        } else {
            $i = 0;
            foreach ($abc as $cform1) {
                $dataaa['form_information'][$i]['form_id'] = $cform1->form_id;
                $dataaa['form_information'][$i]['form_name'] = $cform1->form_name;
                $dataaa['form_information'][$i]['form_type'] = $cform1->form_type;
                $dataaa['form_information'][$i]['department_id'] = $cform1->department_id;
                $i++;
            }

            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $dataaa, 'requestname' => $method));
        }
    }


    public function get_systemic_examination_inner_heading($parameters, $method, $user_id)
    {
        $form_id = $parameters['form_id'];
        $clist2 = $this->db->select("title ,section_id,form_id,section_image")->from("section cs")
            ->where("cs.form_id='" . $form_id  . "' and cs.parent_section_id='0' ")
            ->get()
            ->result();
        if (count($clist2) == 0) {
            // $para['heading']="No Data";
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'No Data', 'requestname' => $method));
        } else {
            $x = 0;
            foreach ($clist2 as $cform2) {
                $para['heading'][$x]['id'] = $x;
                $para['heading'][$x]['title'] = $cform2->title;

                if ($cform2->section_image != '') {
                    $para['heading'][$x]['image_path'] = base_url() . "uploads/section_images/" . $cform2->section_image;
                    $para['heading'][$x]['image'] = $cform2->section_image;
                } else {
                    $para['heading'][$x]['image_path'] = '';
                }

                $para['heading'][$x]['form_id'] = $cform2->form_id;
                $para['heading'][$x]['section_id'] = $cform2->section_id;
                $x++;
            }
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
        }
    }

    public function get_systemic_examination_inner_sub_headingg($parameters, $method, $user_id)
    {
        $form_id = $parameters['form_id'];
        $section_id = $parameters['section_id'];

        $clist3 = $this->db->select("title,section_id,format_type,form_id,parent_section_id")->from("section css")
            ->where("css.form_id='" . $form_id  . "' and css.parent_section_id='" . $section_id . "' ")
            ->get()
            ->result();

        $y = 0;
        foreach ($clist3 as $cform3) {
            $para['sub_heading'][$y]['title'] = $cform3->title;
            $para['sub_heading'][$y]['form_id'] = $cform2->form_id;
            $para['sub_heading'][$y]['parent_section_id'] = $cform3->parent_section_id;
            $para['sub_heading'][$y]['section_id'] = $cform3->section_id;
            $para['sub_heading'][$y]['format_type'] = $cform3->format_type;

            $clist4 = $this->db->select("field_name,field_id,section_id,field_type")->from("field ff")
                ->where("ff.section_id='" . $cform3->section_id . "' and parent_field_id = '0' and parent_option_id = '0' ORDER BY field_id ASC")
                ->get()
                ->result();
            $z = 0;
            foreach ($clist4 as $cform4) {
                $para['field_name'][$y][$z]['field_name'] = $cform4->field_name;
                $para['field_name'][$y][$z]['section_id'] = $cform4->section_id;
                $para['field_name'][$y][$z]['field_id'] = $cform4->field_id;
                $para['field_name'][$y][$z]['field_type'] = $cform4->field_type;

                $clist5 = $this->db->select("option_name,field_id,option_id")->from("field_option ff")
                    ->where("ff.field_id='" . $cform4->field_id . "' and ff.option_default=1   ")
                    ->get()
                    ->result();
                $a = 0;
                foreach ($clist5 as $cform5) {
                    $paraa['label'][$z][$a]['format'] =  $cform3->format_type;
                    $paraa['label'][$z][$a]['label_id'] = $cform5->field_id;
                    $paraa['label'][$z][$a]['label_value'] =  $cform4->field_name;
                    $paraa['label'][$z][$a]['option_id'] = $cform5->option_id;
                    $paraa['label'][$z][$a]['option_value'] = $cform5->option_name;
                    $paraa['label'][$z][$a]['parent_label_id'] = 0;
                    $paraa['label'][$z][$a]['section_id'] =  $cform3->section_id;
                    $a++;
                }
                $z++;
            }
            $y++;
        }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $paraa, 'requestname' => $method));
    }

    public function get_systemic_examination_inner_sub_heading($parameters, $method, $user_id)
    {
        $form_id = $parameters['form_id'];
        $section_id = $parameters['section_id'];

        $clist3 = $this->db->select("title,section_id,format_type,form_id,parent_section_id")->from("section css")
            ->where("css.form_id='" . $form_id  . "' and css.parent_section_id='" . $section_id . "' ")
            ->get()
            ->result();

        $y = 0;
        foreach ($clist3 as $cform3) {
            $dataaa['sub_heading'][$y]['title'] = $cform3->title;
            $dataaa['sub_heading'][$y]['form_id'] = $form_id;
            $dataaa['sub_heading'][$y]['parent_section_id'] = $cform3->parent_section_id;
            $dataaa['sub_heading'][$y]['section_id'] = $cform3->section_id;
            $dataaa['sub_heading'][$y]['format_type'] = $cform3->format_type;

            $clist4 = $this->db->select("field_name,field_id,section_id,field_type")->from("field ff")
                ->where("ff.section_id='" . $cform3->section_id . "' and parent_field_id = '0' and parent_option_id = '0' ORDER BY field_id ASC")
                ->get()
                ->result();
            $z = 0;
            foreach ($clist4 as $cform4) {
                $abc = rand(10, 10000);
                $dataaa['sub_heading'][$y]['field_name'][$z]['r_number'] = strval($abc);
                $dataaa['sub_heading'][$y]['field_name'][$z]['field_name'] = $cform4->field_name;
                $dataaa['sub_heading'][$y]['field_name'][$z]['section_id'] = $cform4->section_id;
                $dataaa['sub_heading'][$y]['field_name'][$z]['field_id'] = $cform4->field_id;
                $dataaa['sub_heading'][$y]['field_name'][$z]['field_type'] = $cform4->field_type;
                $dataaa['sub_heading'][$y]['field_name'][$z]['format_type'] = $cform3->format_type;
                $dataaa['sub_heading'][$y]['field_name'][$z]['option_id'] = "0";
                $dataaa['sub_heading'][$y]['field_name'][$z]['parent_section_id'] =  $cform3->parent_section_id;
                // $para['sub_heading'][$y]['field_name'][$z]['field_name'] = $cform4->field_name;
                // $para['sub_heading'][$y]['field_name'][$z]['section_id'] = $cform4->section_id;
                // $para['sub_heading'][$y]['field_name'][$z]['field_id'] = $cform4->field_id;
                // $para['sub_heading'][$y]['field_name'][$z]['field_type'] = $cform4->field_type;

                $clist5 = $this->db->select("option_name,field_id,option_id,option_default")->from("field_option ff")
                    ->where("ff.field_id='" . $cform4->field_id . "' ")
                    // ->where("ff.option_default=1")
                    ->get()
                    ->result();
                $a = 0;
                foreach ($clist5 as $cform5) {
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_name'] = strval($abc);
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_name'] = $cform5->option_name;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['field_id'] = $cform5->field_id;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_id'] = $cform5->option_id;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_default'] = $cform5->option_default;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['section_id'] = $cform4->section_id;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['format_type'] = $cform3->format_type;
                    $dataaa['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['parent_section_id'] =  $cform3->parent_section_id;
                    // $para['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_name'] = $cform5->option_name;
                    // $para['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['field_id'] = $cform5->field_id;
                    // $para['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_id'] = $cform5->option_id;
                    // $para['sub_heading'][$y]['field_name'][$z]['field_option'][$a]['option_default'] = $cform5->option_default;
                    $a++;
                }
                $z++;
            }
            $y++;
        }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $dataaa, 'requestname' => $method));
    }

    public function get_systemic_examination_dependencies($parameters, $method, $user_id)
    {
        $form_id = $parameters['form_id'];
        $f_type = $parameters['f_type'];
        $section_id = $parameters['section_id'];
        $parent_field_id = $parameters['field_id'];
        $parent_option_id = $parameters['option_id'];
        $r_number = $parameters['r_number'];
        $type = $parameters['type'];


        $clist4 = $this->db->select("field_name,field_id,parent_option_id,section_id,parent_field_id,field_type")->from("field")
            ->where("section_id='" . $section_id . "' and parent_field_id = '" . $parent_field_id . "' and parent_option_id = '" . $parent_option_id . "' ORDER BY field_id ASC")
            ->get()
            ->result();

        if (count($clist4) == 0) {
            // $para['dependency']= array();
            // $para['dependency'][0]['number'] = $r_number;
            $para['dependency'][0]['form_id'] = $form_id;
            $para['dependency'][0]['format_type'] = $f_type;
            $para['dependency'][0]['parent_field_type'] = $type;
            $para['dependency'][0]['field_name'] = '';
            $para['dependency'][0]['section_id'] = $section_id;
            $para['dependency'][0]['field_id'] = '';
            $para['dependency'][0]['field_type'] = '';
            $para['dependency'][0]['parent_field_id'] = $parent_field_id;
            // $para['dependency'][0]['super_parent_field_id'] = $clist4->parent_field_id;
            $para['dependency'][0]['parent_option_id'] =  $parent_option_id;

            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
        } else {
            $z = 0;
            foreach ($clist4 as $cform4) {
                // $para['dependency'][$z]['number'] = $r_number;
                $para['dependency'][$z]['form_id'] = $form_id;
                $para['dependency'][$z]['format_type'] = $f_type;
                $para['dependency'][$z]['parent_field_type'] = $type;
                $para['dependency'][$z]['field_name'] = $cform4->field_name;
                $para['dependency'][$z]['section_id'] = $cform4->section_id;
                $para['dependency'][$z]['field_id'] = $cform4->field_id;
                $para['dependency'][$z]['field_type'] = $cform4->field_type;
                $para['dependency'][$z]['parent_field_id'] = $cform4->parent_field_id;
                // $para['dependency'][$z]['super_parent_field_id'] = $cform4->parent_field_id;
                $para['dependency'][$z]['parent_option_id'] = $cform4->parent_option_id;

                //radio
                if ($cform4->field_type == "radio") {
                    $clist5 = $this->db->select("*")->from("field_option ff")
                        ->where("ff.field_id='" . $cform4->field_id . "'  ")
                        ->get()
                        ->result();
                    $a = 0;
                    foreach ($clist5 as $cform5) {
                        //  $para['dependency'][$z]['numberr'] = $r_number;
                        // $para['dependency'][$z]['radio_list'][$a]['parent_field_type'] = $type;
                        $para['dependency'][$z]['radio_list'][$a]['form_id'] = $form_id;
                        $para['dependency'][$z]['radio_list'][$a]['format_type'] = $f_type;
                        $para['dependency'][$z]['radio_list'][$a]['option_name'] = $cform5->option_name;
                        $para['dependency'][$z]['radio_list'][$a]['field_id'] = $cform5->field_id;
                        $para['dependency'][$z]['radio_list'][$a]['option_id'] = $cform5->option_id;
                        $para['dependency'][$z]['radio_list'][$a]['option_default'] = $cform5->option_default;
                        $para['dependency'][$z]['radio_list'][$a]['section_id'] = $cform4->section_id;
                        $a++;
                    }
                }

                //Checkboxes
                if ($cform4->field_type == "checkbox") {
                    $clist5 = $this->db->select("option_name,field_id,option_id")->from("field_option ff")
                        ->where("ff.field_id='" . $cform4->field_id . "'  ")
                        ->get()
                        ->result();
                    $a = 0;
                    foreach ($clist5 as $cform5) {
                        $para['dependency'][$z]['number'] = $r_number;
                        // $para['dependency'][$z]['checkbox_list'][$a]['parent_field_type'] = $type;
                        $para['dependency'][$z]['checkbox_list'][$a]['form_id'] = $form_id;
                        $para['dependency'][$z]['checkbox_list'][$a]['format_type'] = $f_type;
                        $para['dependency'][$z]['checkbox_list'][$a]['option_name'] = $cform5->option_name;
                        $para['dependency'][$z]['checkbox_list'][$a]['field_id'] = $cform5->field_id;
                        $para['dependency'][$z]['checkbox_list'][$a]['option_id'] = $cform5->option_id;
                        $para['dependency'][$z]['checkbox_list'][$a]['option_default'] = $cform5->option_default;
                        $para['dependency'][$z]['checkbox_list'][$a]['section_id'] = $cform4->section_id;
                        $a++;
                    }
                }

                $z++;
            }


            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $para, 'requestname' => $method));
        }
    }
    //End Systemic Examination Api on 30-12-2019.




    public function systemicExamination($parameters, $method, $user_id)
    {
        $form_type = $parameters['form_type'];
        $department_id = $parameters['department_id'];
        $appointment_id = $parameters['appointment_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];

        //step1
        $clist1 = $this->db->select("form_id  ,form_name,department_id")->from("form cf")
            ->where("cf.department_id='" . $department_id  . "' and cf.form_type='" . "Systemic Examination"  . "'  ")
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

    //  public function commonDashboard($parameters, $method, $user_id) {

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
    //             $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname,b.mobile as pmobile,b.alternate_mobile as palt_mobile,b.email_id as pemail,b.location as plocation")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='".$today."'")
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
    //                 $data['leftPane']['PatientsList'][$i]['appointment_date'] =  date("d-m-Y", strtotime($result['appointment_date']));
    //                 // $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
    //                 $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
    //                 $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
    //                 $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
    //                 // $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];
    //                 //eliminate comms
    //                 $data['leftPane']['PatientsList'][$i]['location'] = $this->eliminateComma($result['plocation']);
    //                 $data['leftPane']['PatientsList'][$i]['mobile'] = DataCrypt($result['pmobile'],'decrypt'); 
    //                 $data['leftPane']['PatientsList'][$i]['alternate_mobile'] = DataCrypt($result['palt_mobile'],'decrypt');   
    //                 $data['leftPane']['PatientsList'][$i]['email'] = DataCrypt($result['pemail'],'decrypt');
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
    //         // $this->db->where('doctor_id =',$doctor_id);

    //         if(trim($from_date) == trim($to_date)){
    //             $this->db->like("created_date_time",$from_date);
    //             $cond_crd = "and created_date_time LIKE '".$from_date."%'";
    //         }else{
    //             $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
    //             $cond_crd = "and (created_date_time between '".$from_date."%' AND '".$to_date."%')";
    //         }

    //         $finances = $this->db->get()->result();
    //         $counts = $this->db->query("select count(*) as count from billing where doctor_id='".$doctor_id."' and billing_type='Pharmacy' ".$cond_crd)->row();

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
    //                                 $amount = $clist1->amount - ($clist1->amount*$clist1->discount/100);
    //                                 $Disc = ($clist1->amount*$clist1->discount)/100;
    //                             }
    //                             elseif($clist1->discount_unit == "INR")
    //                             {
    //                                 $amount = $clist1->amount - ($clist1->discount);
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
    //                         $phCount += 1;
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
    //         // $data['rightPane']['analyticalList'][1]['value'] = "1500";
    //         $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount,2);

    //         // Procedure Revenue JSON
    //         $data['rightPane']['analyticalList'][2]['number'] = $proCount;
    //         $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
    //         $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount,2);

    //         // Pharmacy Revenue JSON
    //         $data['rightPane']['analyticalList'][3]['number'] = $phCount;
    //         $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
    //         $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount,2);
    //         }

    //         if($role_id == 4){ // Doctor

    //           $data['leftPane']['header'] = 'ALL APPOINTMENTS';
    //           $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname,b.mobile as pmobile,b.location as plocation,b.email_id as pemail,b.alternate_mobile as palt_mobile")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='".$user_id."' and a.appointment_date = '" . $today . "'")
    //           ->order_by("FIELD(a.status,'in_consultation','waiting')")
    //           ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
    //           ->order_by("a.check_in_time","asc")
    //           ->order_by("a.appointment_time_slot","asc")
    //           ->order_by("a.appointment_date","asc")->get()->result_array();
    //         //   echo $this->db->last_query();

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
    //             $data['leftPane']['PatientsList'][$i]['appointment_date'] =  $result['appointment_date'];
    //             $data['leftPane']['PatientsList'][$i]['web_appointment_date'] = date("d-m-Y", strtotime( $result['appointment_date']));
    //             $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
    //             $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
    //             $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
    //             // $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

    //                 //eliminate comms
    //             // $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);
    //             $data['leftPane']['PatientsList'][$i]['location'] = $this->eliminateComma($result['plocation']);
    //             $data['leftPane']['PatientsList'][$i]['mobile'] = DataCrypt($result['pmobile'],'decrypt'); 
    //             $data['leftPane']['PatientsList'][$i]['alternate_mobile'] = DataCrypt($result['palt_mobile'],'decrypt');   
    //             $data['leftPane']['PatientsList'][$i]['email'] = DataCrypt($result['pemail'],'decrypt');
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
    //         $this->db->where('status !="2"');
    //         // echo $this->db->last_query();

    //         if(trim($from_date) == trim($to_date)){
    //             $this->db->like("created_date_time",$from_date);
    //             $cond_crd = "and created_date_time LIKE '".$from_date."%'";
    //         }else{
    //             $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
    //             $cond_crd = "and (created_date_time between '".$from_date."%' AND '".$to_date."%')";
    //         }

    //         $finances = $this->db->get()->result();
    //         $counts = $this->db->query("select count(*) as count from billing where doctor_id='".$doctor_id."' and billing_type='Pharmacy' ".$cond_crd)->row();

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
    //                 // echo $this->db->last_query();

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
    //                             $Disc = 10;
    //                             $amount=number_format($clist1->amount,2);
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
    //                         $phAmount +=  $clist1->amount;
    //                         $phCount +=1;
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
    //         $data['rightPane']['analyticalList'][1]['value'] =  number_format($conAmount,2);
    //         // $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount,2);

    //         // Procedure Revenue JSON
    //         $data['rightPane']['analyticalList'][2]['number'] = $proCount;
    //         $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
    //         $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount,2);

    //         // Pharmacy Revenue JSON
    //         $data['rightPane']['analyticalList'][3]['number'] = $counts->count;
    //         $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
    //         $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount,2);

    //     }
    //     $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$data, 'requestname' => $method));
    // }

    public function commonDashboard($parameters, $method, $user_id)
    {

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
            $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname,b.mobile as pmobile,b.alternate_mobile as palt_mobile,b.email_id as pemail,b.location as plocation")->from("appointments a")->join("patients b", "a.patient_id=b.patient_id")->join("doctors c", "a.doctor_id=c.doctor_id")->join("department d", "c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='" . $today . "'")
                ->order_by("FIELD(a.status,'vital_signs','checked_in')")
                ->order_by("FIELD(a.priority, 'pregnancy', 'elderly', 'children','none') ")
                ->order_by("a.appointment_time_slot", "asc")
                ->order_by("a.appointment_date", "asc")
                ->order_by("a.check_in_time", "asc")->get()->result_array();

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
                $data['leftPane']['PatientsList'][$i]['appointment_date'] =  date("d-m-Y", strtotime($result['appointment_date']));
                // $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
                $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
                $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
                $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
                // $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];
                //eliminate comms
                $data['leftPane']['PatientsList'][$i]['location'] = $this->eliminateComma($result['plocation']);
                $data['leftPane']['PatientsList'][$i]['mobile'] = DataCrypt($result['pmobile'], 'decrypt');
                $data['leftPane']['PatientsList'][$i]['alternate_mobile'] = DataCrypt($result['palt_mobile'], 'decrypt');
                $data['leftPane']['PatientsList'][$i]['email'] = DataCrypt($result['pemail'], 'decrypt');
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

            if (trim($from_date) == trim($to_date)) {
                $data['rightPane']['header'] = 'TODAY (' . date('d M. Y', strtotime($from_date)) . ')';
            } else {
                $data['rightPane']['header'] = 'FROM (' . date('d M. Y', strtotime($from_date)) . ' - ' . date('d M. Y', strtotime($to_date)) . ')';
            }

            $to_date = date("Y-m-d", strtotime($to_date . '+1 day'));

            // Get Total Billing records for today
            $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date,status');
            $this->db->from('billing');
            $this->db->where('clinic_id =', $clinic_id);
            // $this->db->where('doctor_id =',$doctor_id);

            if (trim($from_date) == trim($to_date)) {
                $this->db->like("created_date_time", $from_date);
                $cond_crd = "and created_date_time LIKE '" . $from_date . "%'";
            } else {
                $this->db->where("(created_date_time between '" . $from_date . "%' AND '" . $to_date . "%')");
                $cond_crd = "and (created_date_time between '" . $from_date . "%' AND '" . $to_date . "%')";
            }

            $finances = $this->db->get()->result();
            $counts = $this->db->query("select count(*) as count from billing where doctor_id='" . $doctor_id . "' and billing_type='Pharmacy' " . $cond_crd)->row();

            $totalRevenue = 0;
            $totalDiscount = 0;

            // $totalConsultationRevenue = 0;
            $totalConsultations = 0;

            $totalProcedureRevenue = 0;
            $totalProcedures = 0;

            $totalPharmacyRevenue = 0;
            $totalPrescriptions = 0;

            // if(count($finances) > 0){
            $i = 0;
            $proCount = 0;
            $phCount = 0;
            $proAmount = 0;
            $phAmount = 0;
            $conCount = 0;
            $conAmount = 0;
            $proDisc = 0;
            $conDisc = 0;
            $phDisc = 0;
            foreach ($finances as $financeRec) {
                if ($financeRec->status == 2)
                    continue;
                $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

                $clist = $this->db->select("billing_line_item_id,item_information,amount,discount,discount_unit,total_amount")->from("billing_line_items bl")
                    ->where("bl.billing_id='" . $financeRec->billing_id . "'")
                    ->group_by("item_information")
                    ->get()
                    ->result();

                if ($financeRec->billing_type == 'Procedure') {
                    $Disc = 0;
                    if ($financeRec->discount != 0 || $financeRec->discount != NULL) {
                        if ($financeRec->discount_unit == "%") {
                            $amount = $financeRec->total_amount - ($financeRec->total_amount * $financeRec->discount / 100);
                            $Disc = $financeRec->total_amount * $financeRec->discount / 100;
                        } elseif ($financeRec->discount_unit == "INR") {
                            $amount = $financeRec->total_amount - $financeRec->discount;
                            $Disc = $financeRec->discount;
                        }
                    } else {
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
                $j = 0;
                $flat = "";
                foreach ($clist as $clist1) {

                    $paraa['rightPane'][$i][$j] = $clist1->billing_line_item_id;
                    $myJSON = json_encode($paraa);
                    $abc = $abc . ",'" . $clist1->billing_line_item_id . "'";
                    $flat = $flat . $abc;
                    $flatt = substr($flat, 1);

                    if ($clist1->item_information == 'Consultation') {
                        $conInfo = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as consultationAmount,sum(discount) as discount")
                            ->from("billing_line_items bl")
                            ->where("bl.item_information='Consultation'")
                            ->where("bl.billing_line_item_id IN (" . $flatt . ") ")
                            ->get()
                            ->row();
                        if ($clist1->discount != 0 || $clist1->discount != NULL) {
                            if ($clist1->discount_unit == "%") {
                                $amount = number_format($clist1->amount - ($clist1->amount * $clist1->discount / 100), 2);
                                $Disc = ($clist1->amount * $clist1->discount) / 100;
                            } elseif ($clist1->discount_unit == "INR") {
                                $amount = number_format($clist1->amount - ($clist1->discount), 2);
                                $Disc = $clist1->discount;
                            }
                        } else {
                            $Disc = 0;
                        }
                        $conDisc = $conDisc + $Disc;
                        $conAmount = $conAmount + $amount;
                        $conCount = $conInfo->totalConsultationCount;
                    }

                    if ($financeRec->billing_type == 'Pharmacy') {
                        if ($clist1->discount != 0 || $clist1->discount != NULL) {
                            if ($clist1->discount_unit == "%") {
                                $Disc = ($clist1->total_amount * $clist1->discount) / 100;
                            } elseif ($financeRec->discount_unit == "INR") {
                                $Disc = $clist1->discount;
                            }
                        } else {
                            $Disc = 0;
                        }
                        $phDisc += $Disc;
                        $phAmount =  $clist1->total_amount - $Disc;
                        $phCount += 1;
                    }
                    $j++;
                }
                $i++;
            }

            if ($conInfo->discount == '') {
                $conInfo->discount = 0;
            }
            if ($conInfo->consultationAmount == '' && $conInfo->totalConsultationCount == '') {
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
            $data['rightPane']['analyticalList'][0]['split'][0]['value'] = number_format($conAmount + $phAmount + $proAmount, 2);

            $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
            $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
            $data['rightPane']['analyticalList'][0]['split'][1]['value'] = number_format($conDisc + $phDisc + $proDisc, 2);

            // Consultation JSON
            $data['rightPane']['analyticalList'][1]['number'] =  $conCount;
            $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
            $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount, 2);

            // Procedure Revenue JSON
            $data['rightPane']['analyticalList'][2]['number'] = $proCount;
            $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
            $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount, 2);

            // Pharmacy Revenue JSON
            $data['rightPane']['analyticalList'][3]['number'] = $phCount;
            $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
            $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount, 2);
        }

        if ($role_id == 4) { // Doctor

            $data['leftPane']['header'] = 'ALL APPOINTMENTS';
            $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname,b.mobile as pmobile,b.location as plocation,b.email_id as pemail,b.alternate_mobile as palt_mobile")->from("appointments a")->join("patients b", "a.patient_id=b.patient_id")->join("doctors c", "a.doctor_id=c.doctor_id")->join("department d", "c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='" . $user_id . "' and a.appointment_date = '" . $today . "'")
                ->order_by("FIELD(a.status,'in_consultation','waiting')")
                ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
                ->order_by("a.check_in_time", "asc")
                ->order_by("a.appointment_time_slot", "asc")
                ->order_by("a.appointment_date", "asc")->get()->result_array();
            //   echo $this->db->last_query();

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
                $data['leftPane']['PatientsList'][$i]['appointment_date'] =  $result['appointment_date'];
                $data['leftPane']['PatientsList'][$i]['web_appointment_date'] = date("d-m-Y", strtotime($result['appointment_date']));
                $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
                $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
                $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
                // $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

                //eliminate comms
                // $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);
                $data['leftPane']['PatientsList'][$i]['location'] = $this->eliminateComma($result['plocation']);
                $data['leftPane']['PatientsList'][$i]['mobile'] = DataCrypt($result['pmobile'], 'decrypt');
                $data['leftPane']['PatientsList'][$i]['alternate_mobile'] = DataCrypt($result['palt_mobile'], 'decrypt');
                $data['leftPane']['PatientsList'][$i]['email'] = DataCrypt($result['pemail'], 'decrypt');
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

            if (trim($from_date) == trim($to_date)) {
                $data['rightPane']['header'] = 'TODAY (' . date('d M. Y', strtotime($from_date)) . ')';
            } else {
                $data['rightPane']['header'] = 'FROM (' . date('d M. Y', strtotime($from_date)) . ' - ' . date('d M. Y', strtotime($to_date)) . ')';
            }

            $to_date = date("Y-m-d", strtotime($to_date . '+1 day'));

            // Get Total Billing records for today
            $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date,status');
            $this->db->from('billing');
            $this->db->where('clinic_id =', $clinic_id);
            $this->db->where('doctor_id =', $doctor_id);
            $this->db->where('status !="2"');
            // echo $this->db->last_query();

            if (trim($from_date) == trim($to_date)) {
                $this->db->like("created_date_time", $from_date);
                $cond_crd = "and created_date_time LIKE '" . $from_date . "%'";
            } else {
                $this->db->where("(created_date_time between '" . $from_date . "%' AND '" . $to_date . "%')");
                $cond_crd = "and (created_date_time between '" . $from_date . "%' AND '" . $to_date . "%')";
            }

            $finances = $this->db->get()->result();
            $counts = $this->db->query("select count(*) as count from billing where doctor_id='" . $doctor_id . "' and billing_type='Pharmacy' " . $cond_crd)->row();

            $totalRevenue = 0;
            $totalDiscount = 0;

            // $totalConsultationRevenue = 0;
            $totalConsultations = 0;

            $totalProcedureRevenue = 0;
            $totalProcedures = 0;

            $totalPharmacyRevenue = 0;
            $totalPrescriptions = 0;

            // if(count($finances) > 0){
            $i = 0;
            $proCount = 0;
            $phCount = 0;
            $proAmount = 0;
            $phAmount = 0;
            $conCount = 0;
            $conAmount = 0;
            $proDisc = 0;
            $conDisc = 0;
            $phDisc = 0;
            foreach ($finances as $financeRec) {
                if ($financeRec->status == 2)
                    continue;
                $para['billing_id'][$i]['billing_id'] = $financeRec->billing_id;

                $clist = $this->db->select("billing_line_item_id,item_information,amount,discount,discount_unit,total_amount")->from("billing_line_items bl")
                    ->where("bl.billing_id='" . $financeRec->billing_id . "'")
                    ->group_by("item_information")
                    ->get()
                    ->result();
                // echo $this->db->last_query();

                if ($financeRec->billing_type == 'Procedure') {
                    $Disc = 0;
                    if ($financeRec->discount != 0 || $financeRec->discount != NULL) {
                        if ($financeRec->discount_unit == "%") {
                            $amount = $financeRec->total_amount - ($financeRec->total_amount * $financeRec->discount / 100);
                            $Disc = $financeRec->total_amount * $financeRec->discount / 100;
                        } elseif ($financeRec->discount_unit == "INR") {
                            $amount = $financeRec->total_amount - $financeRec->discount;
                            $Disc = $financeRec->discount;
                        }
                    } else {
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
                $j = 0;
                $flat = "";
                foreach ($clist as $clist1) {

                    $paraa['rightPane'][$i][$j] = $clist1->billing_line_item_id;
                    $myJSON = json_encode($paraa);
                    $abc = $abc . ",'" . $clist1->billing_line_item_id . "'";
                    $flat = $flat . $abc;
                    $flatt = substr($flat, 1);

                    if ($clist1->item_information == 'Consultation') {
                        $conInfo = $this->db->select("count(item_information) as totalConsultationCount,sum(amount) as consultationAmount,sum(discount) as discount")
                            ->from("billing_line_items bl")
                            ->where("bl.item_information='Consultation'")
                            ->where("bl.billing_line_item_id IN (" . $flatt . ") ")
                            ->get()
                            ->row();
                        if ($clist1->discount != 0 || $clist1->discount != NULL) {
                            if ($clist1->discount_unit == "%") {
                                $amount = $clist1->amount - ($clist1->amount * $clist1->discount / 100);
                                $Disc = ($clist1->amount * $clist1->discount) / 100;
                            } elseif ($clist1->discount_unit == "INR") {
                                $amount = $clist1->amount - ($clist1->discount);
                                $Disc = $clist1->discount;
                            }
                        }
                        if ($clist1->discount == 0) {
                            $Disc = 0;
                            $amount =  $clist1->amount;
                        }
                        $conDisc = $conDisc + $Disc;
                        $conAmount = $conAmount + $amount;
                        $conCount = $conInfo->totalConsultationCount;
                    }

                    if ($financeRec->billing_type == 'Pharmacy') {
                        if ($clist1->discount != 0 || $clist1->discount != NULL) {
                            if ($clist1->discount_unit == "%") {
                                $Disc = ($clist1->total_amount * $clist1->discount) / 100;
                            } elseif ($financeRec->discount_unit == "INR") {
                                $Disc = $clist1->discount;
                            }
                        } else {
                            $Disc = 0;
                        }
                        $phDisc += $Disc;
                        $phAmount +=  $clist1->amount;
                        $phCount += 1;
                    }
                    $j++;
                }
                $i++;
            }

            if ($conInfo->discount == '') {
                $conInfo->discount = 0;
            }
            if ($conInfo->consultationAmount == '' && $conInfo->totalConsultationCount == '') {
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
            $data['rightPane']['analyticalList'][0]['split'][0]['value'] = number_format($conAmount + $phAmount + $proAmount, 2);

            $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
            $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
            $data['rightPane']['analyticalList'][0]['split'][1]['value'] = number_format($conDisc + $phDisc + $proDisc, 2);

            // Consultation JSON
            $data['rightPane']['analyticalList'][1]['number'] =  $conCount;
            $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
            $data['rightPane']['analyticalList'][1]['value'] =   number_format($conAmount, 2);

            // Procedure Revenue JSON
            $data['rightPane']['analyticalList'][2]['number'] = $proCount;
            $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
            $data['rightPane']['analyticalList'][2]['value'] = number_format($proAmount, 2);

            // Pharmacy Revenue JSON
            $data['rightPane']['analyticalList'][3]['number'] = $counts->count;
            $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
            $data['rightPane']['analyticalList'][3]['value'] = number_format($phAmount, 2);
        }
        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
    }
    public function get_fi($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $clinic_id = $parameters['clinic_id'];


        $clist = $this->db->select("*")->from("appointments ap")
            ->where("ap.appointment_id='" . $appointment_id . "'  and clinic_id='" . $clinic_id . "'")
            ->get()
            ->row();
        $patient_id = $clist->patient_id;
        $doctor_id = $clist->doctor_id;

        $clist1 = $this->db->select("*")->from("appointments ap")
            ->where("ap.patient_id='" . $patient_id . "'  and doctor_id='" . $doctor_id . "'")
            ->order_by("appointment_id", "desc")
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

        if (count($chec) == 0) {
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


            $e = 0;
            $j = 0;
            foreach ($ab220 as $clist12) {
                $paraaa['patient_prescription_id'] = $iddd;
                $paraaa['drug_id'] = $clist12->drug_id;
                $paraaa['medicine_name'] = $clist12->medicine_name;
                $paraaa['day_schedule'] = $clist12->day_schedule;
                $paraaa['preffered_intake'] = $clist12->preffered_intake;
                $paraaa['day_dosage'] = $clist12->day_dosage;
                $paraaa['dose_course'] = $clist12->dose_course;
                $paraaa['drug_dose'] = $clist12->drug_dose;
                $paraaa['dosage_frequency'] = $clist12->dosage_frequency;
                $paraaa['quantity'] = $clist12->quantity;
                $paraaa['remarks'] = $clist12->remarks;
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
        } else {
            //  echo "safe";


            $inv_lineitems =  $this->db->select("*")
                ->from("patient_prescription_drug ppd")
                ->join("patient_prescription pp", "ppd.patient_prescription_id=pp.patient_prescription_id")
                ->where("pp.appointment_id='" . $appointment_id . "' ")
                ->get()
                ->result();


            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {

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


        if (count($chec1) == 0) {

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
            foreach ($abc as $clist12) {

                $paraaaq['patient_investigation_id'] = $iddddd;
                $paraaaq['checked'] = $clist12->checked;
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
        } else {
            $inv_lineitems =  $this->db->select("*")
                ->from("patient_investigation_line_items ppd")
                ->join("patient_investigation pp", "ppd.patient_investigation_id=pp.patient_investigation_id")
                ->where("pp.appointment_id='" . $appointment_id . "' ")
                ->get()
                ->result();


            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {

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

        if (count($chec99) == 0) {
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


            $e = 0;
            $j = 0;
            foreach ($abcd as $clist12) {
                $paraaa1['patient_clinical_diagnosis_id'] = $cdd;
                $paraaa1['disease_name'] = $clist12->disease_name;
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
        } else {
            $inv_lineitems =  $this->db->select("*")
                ->from("patient_cd_line_items ppd")
                ->join("patient_clinical_diagnosis pp", "ppd.patient_clinical_diagnosis_id=pp.patient_clinical_diagnosis_id")
                ->where("pp.appointment_id='" . $appointment_id . "' ")
                ->get()
                ->result();


            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {

                $paraa['clinicaldiagnosis'][$j]['patient_cd_line_item_id'] = $inv_lineitem->patient_cd_line_item_id;
                $paraa['clinicaldiagnosis'][$j]['patient_clinical_diagnosis_id'] = $inv_lineitem->patient_clinical_diagnosis_id;
                // $paraa['clinicaldiagnosis'][$j]['appointment_id'] = $apt_id;
                $paraa['clinicaldiagnosis'][$j]['description'] = $inv_lineitem->disease_name;

                $j++;
            }
        }


        //End CD




        $parent_appointment = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $parameters['appointment_id']), '');


        $paraa['appointment_id'] = $parameters['appointment_id'];
        $paraa['clinic_id'] = $parameters['clinic_id'];
        $paraa['patient_id'] = $clist->patient_id;
        $paraa['doctor_id'] = $clist->doctor_id;
        $paraa['umr_no'] = $clist->umr_no;
        if ($parent_appointment->parent_appointment_id == 0) {
            $next_follow_up = $this->db->select("*")->from("appointments")->where("parent_appointment_id='" . $parameters['appointment_id'] . "'")->order_by("appointment_id", "desc")->get()->row();
            // $paraa['if'] = $this->db->last_query();
            if (count($next_follow_up) > 0) {
                if ($next_follow_up->appointment_id == $appointment_id) {
                    $paraa['follow_up_date'] = '';
                } else {
                    $paraa['follow_up_date'] = date('d-M-Y', strtotime($next_follow_up->appointment_date)) . " " . date('H:i A', strtotime($next_follow_up->appointment_time_slot));
                }
            } else {
                $paraa['follow_up_date'] = '';
            }
        } else {
            $next_follow_up = $this->db->select("*")->from("appointments")->where("parent_appointment_id='" . $parent_appointment->parent_appointment_id . "'")->order_by("appointment_id", "desc")->get()->row();
            // $paraa['else'] = $this->db->last_query();
            if (count($next_follow_up) > 0) {
                if ($next_follow_up->appointment_id == $parameters['appointment_id']) {
                    $paraa['follow_up_date'] = '';
                } else {
                    $paraa['follow_up_date'] = date('d-M-Y', strtotime($next_follow_up->appointment_date)) . " " . date('H:i A', strtotime($next_follow_up->appointment_time_slot));
                }
            } else {
                $paraa['follow_up_date'] = '';
            }
        }
        $paraa['plan'] = $chec->plan;

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa, 'requestname' => $method));
    }
    //PdfToHtmlConversions
    public function consentFormPdfToHtml($parameters, $method, $user_id)
    {
        $patient_consent_form_id = $parameters['patient_consent_form_id'];
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $user_id;
        $consent_form_id = $parameters['consent_form_id'];
        $data['appointment'] = $this->db->select("a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id,p.patient_id,p.title,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.umr_no,p.age,p.age_unit,p.gender as p_gender,c.clinic_id,c.clinic_name")
            ->from("appointments a")
            ->join("doctors d", "a.doctor_id = d.doctor_id", "left")
            ->join("department de", "d.department_id=de.department_id", "left")->join("patients p", "a.patient_id=p.patient_id", "left")
            ->join("clinics c", "a.clinic_id=c.clinic_id", "left")
            ->where("a.appointment_id='" . $appointment_id . "'")->order_by("a.appointment_id", "desc")->get()->row();
        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->join(" consent_form_department c", "c.consent_form_id = a.consent_form_id")->join("department b", "c.department_id = b.department_id")->where("a.archieve != 1 and a.consent_form_id ='" . $consent_form_id . "'")->get()->row();
        $data['consent_form_id'] = $id[0];


        $dataaa['html'] = $this->load->view('consentform/consentform_patient_pdf', $data, true);
        $pdfFilePath = rand(10, 100) . "_" . $clinic_id . "_" . $patient_id . "_" . $appointment_id . "_" . date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['patient_consent_form'] = $pdfFilePath;

        $ok = $this->Generic_model->updateData("patient_consent_forms", $para, array('patient_consent_form_id' => $patient_consent_form_id));

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $dataaa, 'requestname' => $method));
    }

    public function investigation_invoice_PdfToHtml($parameters, $method, $user_id)
    {
        $data['patient_investigation_id'] = $parameters['patient_investigation_id'];
        $data2['patient_id'] = $parameters['patient_id'];
        $patient_info = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $parameters['patient_id']), '');


        $data2['patient_name'] = strtoupper($patient_info->first_name . ' ' . $patient_info->last_name);
        $data2['umr_no'] = $patient_info->umr_no;
        $appointment_info = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $parameters['appointment_id']), '');

        $doctors_info = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), '');
        $data['appointment_id'] = $appointment_info->appointment_id;
        $data['appointment_date'] = $appointment_info->appointment_date;
        $data['doctor_id'] = $doctors_info->doctor_id;
        $data2['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . ' ' . $doctors_info->last_name);


        // $inr = $this->db->select("count(*) as invoiceno")->from("billing")->where("clinic_id='" . $parameters['clinic_id'] . "'")->get()->row();
        // $inv_gen = ($inr->invoiceno) + 1;
        // $receipt_no = 'RCT-' . $parameters['clinic_id'] . '-' . $inv_gen;
        // $invoice_no = 'INV-' . $parameters['clinic_id'] . '-' . $inv_gen;

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($parameters['clinic_id']);
        $invoice_no = $parameters['clinic_id'] . $invoice_no_alias;

        $billing_p['invoice_no'] = $invoice_no;
        $billing_p['invoice_no_alias'] = $invoice_no_alias;
        $billing_p['patient_id'] = $parameters['patient_id'];
        $billing_p['clinic_id'] = $parameters['clinic_id'];
        $billing_p['umr_no'] = $parameters['umr_no'];
        $billing_p['invoice_pdf'] = "INV_" . $parameters['clinic_id'] . "_" . date('dhi') . ".pdf";
        $billing_p['created_by'] = $user_id;
        $billing_p['created_date_time'] = date('Y-m-d H:i:s');
        $billing_p['modified_by'] = $user_id;
        $billing_p['modified_date_time'] = date('Y-m-d H:i:s');

        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_p);

        for ($i = 0; $i < count($parameters['investigations_list']); $i++) {
            $data['investigation_id'] = $parameters['investigations_list'][$i]['investigation_id'];
            $update['checked'] = 1;
            $this->Generic_model->updateData("patient_investigation_line_items", $update, array('patient_investigation_line_item_id' => $parameters['investigations_list'][$i]['patient_investigation_line_item_id']));

            $patient_bank['billing_id'] = $billing_id;
            $patient_bank['doctor_id'] = $doctors_info->doctor_id;
            $patient_bank['billing_type'] = 'Investigations';
            $patient_bank['quantity'] = 1;
            $patient_bank['mode_of_payment'] = 'Cash';
            $patient_bank['billing_date_time'] = date('Y-m-d H:i:s');


            $patient_bank['amount'] = round($parameters['investigations_list'][$i]['mrp'], 2);
            $patient_bank['item_information'] = $parameters['investigations_list'][$i]['investigation_name'];

            $patient_bank['created_date_time'] = date('Y-m-d H:i:s');

            $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('billing_line_items', $patient_bank);
        }

        $data2['doctor_name'] = $doctors_info->first_name . " " . $doctors_info->last_name;
        $data2['qualification'] = $doctors_info->qualification;
        $address = $this->db->query('select * from clinics where clinic_id = "' . $parameters['clinic_id'] . '"')->row();
        $data2['clinic_address'] = $address->address;
        $data2['clinic_name'] = $address->clinic_name;
        $data2['clinic_logo'] = $address->clinic_logo;
        $data2['clinic_phone'] = $address->clinic_phone;
        $billing_info = $this->db->query('select * from billing where billing_id = ' . $billing_id)->row();
        $data2['invoice_number'] = $billing_info->invoice_no;

        $billing_status['status'] = 2;
        $billing_status['modified_by'] = $user_id;
        $billing_status['modified_date_time'] = date('Y-m-d H:i:s');
        $condition['patient_investigation_id'] = $parameters['patient_investigation_id'];
        $this->Generic_model->updateData("patient_investigation", $billing_status, $condition);

        $data2['patient_address'] = $patient_info->address_line;
        $data2['mode_of_payment'] = 'Cash';
        $data2['updated_info'] = $parameters['investigations_list'];

        $dataaa['html'] = $this->load->view('investigation/investigation_invoice', $data2, true);

        $pdfFilePath = "INV_" . $parameters['clinic_id'] . "_" . date('dhi') . ".pdf";
        $data3['pdf_name'] = base_url() . 'uploads/investigation_invoice/' . $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/investigation_invoice/" . $pdfFilePath, "F");

        $this->response(array('code' => '200', 'message' => 'Invoice Generated Successfully', 'result' => $dataaa, 'requestname' => $method));
    }


    public function save_billing_PdfToHtml($parameters, $method, $user_id)
    {


        extract($parameters);

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($clinic_id);
        $invoice_no = $clinic_id . $invoice_no_alias;

        $billing_master = $parameters;
        unset($billing_master['billing_line_items']);
        unset($billing_master['con_payment_status']);
        unset($billing_master['reg_payment_status']);
        unset($billing_master['priority']);
        unset($billing_master['sms']);


        // Get patient Info
        $this->db->select('patient_id, umr_no, first_name, last_name, mobile, alternate_mobile');
        $this->db->from('patients');
        $this->db->where('patient_id =', $patient_id);
        $patientInfo = $this->db->get()->row();

        $billing_master['invoice_no'] = $invoice_no;
        $billing_master['invoice_no_alias'] = $invoice_no_alias;
        $billing_master['guest_name'] = ucwords($patientInfo->$first_name . " " . $patientInfo->$last_name);
        $billing_master['guest_mobile'] = ($patientInfo->mobile != '' ? $patientInfo->mobile : $patientInfo->alternate_mobile);
        $billing_master['billing_date_time'] = date('Y-m-d H:i:s');
        $billing_master['created_by'] = $user_id;
        $billing_master['created_date_time'] = date('Y-m-d H:i:s');
        $billing_master['modified_by'] = $user_id;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
        $billing_master['payment_status'] = $con_payment_status;

        // Insert billing master informaiton and get Billing Id
        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        // Create Billing line items
        $billing_line_items = $parameters['billing_line_items'];

        $invoiceAmount = 0;

        for ($i = 0; $i < count($billing_line_items); $i++) {
            $billing_line_items[$i]['billing_id'] = $billing_id;
            $billing_line_items[$i]['quantity'] = 1;
            $billing_line_items[$i]['unit_price'] = $billing_line_items[$i]['amount'];
            $billing_line_items[$i]['created_by'] = $user_id;
            $billing_line_items[$i]['created_date_time'] = date('Y-m-d H:i:s');
            $billing_line_items[$i]['modified_by'] = $user_id;
            $billing_line_items[$i]['modified_date_time'] = date('Y-m-d H:i:s');

            $amount = $billing_line_items[$i]['amount'];
            $discount = $billing_line_items[$i]['discount'];
            $unit = $billing_line_items[$i]['discount_unit'];

            if ($discount == 0) {
                $invoiceAmount = $invoiceAmount + $amount;
            } else {
                if ($unit == "%") {
                    $amount = $amount - ($amount * $discount / 100);
                } else if ($unit == "INR") {
                    $amount = $amount - $discount;
                }
                $invoiceAmount = $invoiceAmount + $amount;
            }

            $invoiceData['total_amount'] = $invoiceAmount;
            $patientData['payment_status'] = $reg_payment_status;

            $this->Generic_model->insertData('billing_line_items', $billing_line_items[$i]);

            // Update calculated total invoice amount in the billing db for billing id
            $this->Generic_model->updateData('billing', $invoiceData, array('billing_id' => $billing_id));

            // Update patient table with payment status
            $this->Generic_model->updateData('patients', $patientData, array('patient_id' => $patient_id));
        }

        // $clinic_details = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']), $order = '');
        // $doctor_details = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), $order = '');
        // $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_details->department_id), $order = '');
        // $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        // $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');
        // $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');
        // $review_details = $this->Generic_model->getSingleRecord('clinic_doctor',array('doctor_id'=>$$doctor_details->doctor_id,'clinic_id'=>$clinic_details->clinic_id),$order='');
        //$state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details_state_id), $order = '');
        //Make Payment Status 1
        $apData['payment_status'] = 1;
        $this->Generic_model->updateData('appointments', $apData, array('appointment_id' => $appointment_id));

        $info = $this->db->select('*')->from('appointments A')->join('doctors Doc', 'A.doctor_id = Doc.doctor_id')->where('A.appointment_id =', $appointment_id)->get()->row();

        $clinic_details = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $clinic_id), $order = '');

        $doctor_details = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $doctor_id), $order = '');
        $review_details = $this->Generic_model->getSingleRecord('clinic_doctor', array('doctor_id' => $doctor_id, 'clinic_id' => $clinic_id), $order = '');

        $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_details->department_id), $order = '');
        $billing_master = $this->Generic_model->getSingleRecord('billing', array('billing_id' => $billing_id), $order = '');
        $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');

        $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');

        $state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details->state_id), $order = '');

        $data['clinic_logo'] = $clinic_details->clinic_logo;
        $data['review_days'] = $review_details->review_days;
        $data['clinic_phone'] = $clinic_details->clinic_phone;
        $data['clinic_name'] = $clinic_details->clinic_name;
        $data['address'] = $clinic_details->address;
        $data['doctor_name'] = "Dr." . strtoupper($doctor_details->first_name . " " . $doctor_details->last_name);
        $data['qualification'] = $doctor_details->qualification;
        $data['department_name'] = $departments->departmentname;
        $data['patient_name'] = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age'] = $patient_details->age . ' ' . $patient_details->age_unit;
        $data['age_unit'] = $patient_details->age_unit;
        $data['gender'] = $patient_details->gender;
        $data['umr_no'] = $umr_no;
        $data['patient_address'] = $patient_details->address_line . "," . $district_details->district_name . "," . $state_details->state_name . "," . $patient_details->pincode;
        $data['billing'] = $billing;
        $data['invoice_no'] = $invoice_no;
        $data['invoice_no_alias'] = $invoice_no_alias;

        $param['html'] = $this->load->view('billing/generate_billing', $data, true);
        $pdfFilePath = "billing_" . $patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];
        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;
        $param['appointment']['pdf_file'] = $pdf;

        $doctors_info = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $doctor_id), '');

        $dept_info = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctors_info->department_id), '');

        $param['appointment']['appointment_id'] = $appointment_id;
        $param['appointment']['clinic_id'] = $clinic_id;
        $param['appointment']['patient_id'] = $patient_id;
        $param['appointment']['umr_no'] = $umr_no;
        $param['appointment']['doctor_id'] = $doctor_id;
        $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
        $param['appointment']['department'] = $dept_info->department_name;
        $param['appointment']['department_id'] = $doctors_info->department_id;
        $param['appointment']['appointment_type'] = $info->appointment_type;
        $param['appointment']['appointment_date'] = $info->appointment_date;
        $param['appointment']['appointment_time_slot'] = $info->appointment_time_slot;
        $param['appointment']['priority'] = $info->priority;
        $param['appointment']['status'] = $info->status;

        $this->response(array('code' => '200', 'message' => 'Appointment Booked', 'result' => $param, 'requestname' => $method));
    }

    public function shortSummary_PdfToHtml($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];

        //HandWriting
        $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='" . $appointment_id . "'")->result();

        $i = 0;
        foreach ($section_image as $image) {
            $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
            $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
                ->where("pil.patient_form_id='" . $image->patient_form_id . "'")
                ->get()
                ->result();
            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {
                $abcd['scribbling'][0]['images'] = $inv_lineitem->scribbling_image;
                $j++;
            }
            $i++;
        }
        //End HandWriting 

        //$data['visit']=$visit;
        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
            ->from("appointments a")->join("clinics c", "a.clinic_id = c.clinic_id", "left")->join("patients p", "a.patient_id = p.patient_id", "left")->join("doctors d", "a.doctor_id = d.doctor_id", "left")->join('department dep', 'd.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();

        $patient_name = $data['appointments']->pname . date('Ymd') . $appointment_id;

        $visit_no = $this->Generic_model->getAllRecords('appointments', array('clinic_id' => $data['appointments']->clinic_id, 'patient_id' => $data['appointments']->patient_id, 'doctor_id' => $data['appointments']->doctor_id), array('field' => 'appointment_id', 'type' => 'desc'));


        $visit_count = count($visit_no);

        foreach ($visit_no as $key => $value) {
            if ($value->appointment_id == $appointment_id) {
                $visit_count--;
                $data['visit'] = $visit_count;
            }
        }

        //$vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));

        $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = " . $appointment_id . ") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();

        foreach ($vital_sign as $key => $value) {
            if ($value->vital_sign == 'SBP') { // Capture Systolic Blood Pressure Value
                if ($bp != '') {
                    $bp = $value->vital_result . "/" . $bp;
                } else {
                    $bp = $value->vital_result;
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp . ' mmHg';
            } else if ($value->vital_sign == 'DBP') { // Capture Diastolic Blood Pressure Value
                if ($bp != '') {
                    $bp = $bp . "/" . $value->vital_result;
                } else {
                    $bp = $value->vital_result;
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp . ' mmHg';
            } else {
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='" . $value->vital_sign . "'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result . " " . $v_unit->unit;
            }
        }

        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id), '');

        $data['pcd'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id), '');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id' => $pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='" . $appointment_id . "'")->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id), '');


        $parent_appointment = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id), '');

        if ($parent_appointment->parent_appointment_id == 0) {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        } else {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $parent_appointment->parent_appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }




        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='" . $data['appointments']->clinic_id . "'")->get()->row();


        $this->load->library('M_pdf');
        $dataa['html'] = $this->load->view('reports/short_summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;

        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html, 2);
        $this->m_pdf->pdf->Output("./uploads/summary_reports/short-" . $pdfFilePath, "F");
        //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
        $para['pdf_name'] = base_url() . 'uploads/summary_reports/short-' . $pdfFilePath;

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $dataa, 'requestname' => $method));
    }

    public function dischargeSummary_pdfToHTml($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];
        //$data['visit']=$visit;
        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
            ->from("appointments a")->join("clinics c", "a.clinic_id = c.clinic_id", "left")->join("patients p", "a.patient_id = p.patient_id", "left")->join("doctors d", "a.doctor_id = d.doctor_id", "left")->join('department dep', 'd.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();

        $patient_name = $data['appointments']->pname . date('Ymd') . $appointment_id;

        $visit_no = $this->Generic_model->getAllRecords('appointments', array('clinic_id' => $data['appointments']->clinic_id, 'patient_id' => $data['appointments']->patient_id, 'doctor_id' => $data['appointments']->doctor_id), array('field' => 'appointment_id', 'type' => 'desc'));


        $visit_count = count($visit_no);

        foreach ($visit_no as $key => $value) {
            if ($value->appointment_id == $appointment_id) {
                $visit_count--;
                $data['visit'] = $visit_count;
            }
        }
        $vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign', array('appointment_id' => $appointment_id), array('field' => 'position', 'type' => 'asc'));


        foreach ($vital_sign as $key => $value) {
            if ($value->vital_sign == 'DBP') {
                $dbp = $value->vital_result;
            } else if ($value->vital_sign == 'SBP') {
                $data['vital_sign']['BP'] = $value->vital_result . '/' . $dbp . ' mmHg';
            } else {
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='" . $value->vital_sign . "'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result . " " . $v_unit->unit;
            }
        }

        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id), '');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id' => $pcd->patient_clinical_diagnosis_id), $order = '');


        $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='" . $appointment_id . "'")->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id), '');


        $parent_appointment = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id), '');

        if ($parent_appointment->parent_appointment_id == 0) {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        } else {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $parent_appointment->parent_appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }




        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='" . $data['appointments']->clinic_id . "'")->get()->row();


        $this->load->library('M_pdf');
        $dataaa['html'] = $this->load->view('reports/discharge_summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;

        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html, 2);
        $this->m_pdf->pdf->Output("./uploads/summary_reports/discharge-" . $pdfFilePath, "F");
        //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
        $para['pdf_name'] = base_url() . 'uploads/summary_reports/discharge-' . $pdfFilePath;

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $dataaa, 'requestname' => $method));
    }

    public function fullSummary_PdfToHtml($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];
        // dd($parameters);

        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
            ->from("appointments a")->join("clinics c", "a.clinic_id = c.clinic_id", "left")->join("patients p", "a.patient_id = p.patient_id", "left")->join("doctors d", "a.doctor_id = d.doctor_id", "left")->join('department dep', 'd.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
        $patient_name = $data['appointments']->pname . date('Ymd') . $appointment_id;

        $visit_no = $this->Generic_model->getAllRecords('appointments', array('clinic_id' => $data['appointments']->clinic_id, 'patient_id' => $data['appointments']->patient_id, 'doctor_id' => $data['appointments']->doctor_id), array('field' => 'appointment_id', 'type' => 'desc'));

        $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='" . $appointment_id . "'")->result();

        $i = 0;
        foreach ($section_image as $image) {
            $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
            $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
                ->where("pil.patient_form_id='" . $image->patient_form_id . "'")
                ->get()
                ->result();
            $j = 0;
            foreach ($inv_lineitems as $inv_lineitem) {
                $abcd['scribbling'][0]['images'] = $inv_lineitem->scribbling_image;
                $j++;
            }
            $i++;
        }


        $visit_count = count($visit_no);

        foreach ($visit_no as $key => $value) {
            if ($value->appointment_id == $appointment_id) {
                $visit_count--;
                $data['visit'] = $visit_count;
            }
        }

        $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = " . $appointment_id . ") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();

        $sbp = '';

        foreach ($vital_sign as $key => $value) {

            if ($value->vital_sign == 'SBP') { // Capture Systolic Blood Pressure Value
                if ($bp != '') {
                    $bp = $value->vital_result . "/" . $bp;
                } else {
                    $bp = $value->vital_result;
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp . ' mmHg';
            } else if ($value->vital_sign == 'DBP') { // Capture Diastolic Blood Pressure Value
                if ($bp != '') {
                    $bp = $bp . "/" . $value->vital_result;
                } else {
                    $bp = $value->vital_result;
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp . ' mmHg';
            } else {
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='" . $value->vital_sign . "'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result . " " . $v_unit->unit;
            }
        }


        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id), '');
        $data['pcd'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id), '');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id' => $pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='" . $appointment_id . "'")->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id), '');


        $parent_appointment = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id), '');

        if ($parent_appointment->parent_appointment_id == 0) {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        } else {
            $next_follow_up = $this->Generic_model->getSingleRecord('appointments', array('parent_appointment_id' => $parent_appointment->parent_appointment_id), array('field' => 'appointment_id', 'type' => 'desc'));

            if ($next_follow_up->appointment_id == $appointment_id) {
                $data['follow_up_date'] = '';
            } else {
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }

        // Previous Documents
        $data['previous_documents'] = $this->db->select("*")->from("previous_documents")->where("appointment_id='" . $appointment_id . "'")->order_by("previous_document_id", "desc")->get()->result();

        //presenting symtoms
        $data['presenting_symptoms'] = $this->db->select("*")->from("patient_presenting_symptoms ps")->join("patient_ps_line_items psl", "ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '" . $appointment_id . "'")->get()->result();

        // Get Patient Consent Forms Checklist
        $data['patient_consent_form'] = $this->db->query("select * from patient_consent_forms pcf,consent_form cf where cf.consent_form_id=pcf.consent_form_id and pcf.appointment_id='" . $appointment_id . "'")->result();
        // $data['patient_consent_checklist'] = $this->db->query("select * from patient_checklist pc,checklist_master cm where pc.checklist_id=cm.checklist_id and  pc.appointment_id='".$appointment_id."' and pc.checked='1'")->result();


        // Get Patient's Systemic Examination Form Info

        // $data['get_se_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Systemic Examination'")->get()->row();
        $data['get_se_info'] = $this->db->query("select * from patient_form where appointment_id='" . $appointment_id . "' and form_type='Systemic Examination' order by patient_form_id DESC")->result();

        // Get Patient's General Physical Examination info
        $data['get_gpe_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='GPE'")->get()->result();

        // Get Patient's HOPI info
        $data['get_hopi_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='HOPI'")->get()->result();

        // Get Patient's Past History info
        $data['get_past_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Past History'")->get()->result();

        // Get Patient's Past History info
        $data['get_personal_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Personal History'")->get()->result();

        // Get Patient's Treatment History info
        $data['get_treatment_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Treatment History'")->get()->result();

        // Get Patient's Treatment History info
        $data['get_family_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Family History'")->get()->result();

        // Get Patient's Social History info
        $data['get_social_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Social History'")->get()->result();

        // Get Patient's Other History info
        $data['get_other_systems_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='" . $appointment_id . "' and form_type='Other Systems'")->get()->result();

        // $data['other_systems_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_other_systems_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();

        // $data['get_other_systems_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_other_systems_info']->form_id."'")->get()->row();


        //Get invoice information
        $data['get_billing_info'] = $this->db->select("*")->from("billing")->where("appointment_id='" . $appointment_id . "'")->get()->result();

        //Patient Procedures
        $data['patient_procedures'] = $this->db->query("select pp.*,mp.medical_procedure as procedure_title from patient_procedure pp,medical_procedures mp where pp.medical_procedure_id=mp.medical_procedure_id and pp.appointment_id='" . $appointment_id . "'")->result();

        // PDF Settings
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='" . $data['appointments']->clinic_id . "'")->get()->row();
        // echo json_encode($data);

        $this->load->library('M_pdf');
        $dataaa['html'] = $this->load->view('reports/full_summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;
        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;

        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html, 2);

        $this->m_pdf->pdf->Output("./uploads/summary_reports/full-" . $pdfFilePath, "F");
        //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
        $para['pdf_name'] = base_url() . 'uploads/summary_reports/full-' . $pdfFilePath;


        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $dataaa, 'requestname' => $method));
    }

    public function patient_procedure_PdfToHtml($parameters, $method, $user_id)
    {

        $data['patient_id'] = $parameters['patient_id'];
        $data['doctor_id'] = $parameters['doctor_id'];
        $data['appointment_id'] = $parameters['appointment_id'];
        $data['surgeon'] = $parameters['surgeon'];
        $data['anesthetist'] = $parameters['anesthetist'];
        $data['assisting_surgeon'] = $parameters['assisting_surgeon'];
        $data['type_of_anesthesia'] = $parameters['type_of_anesthesia'];
        $data['assisting_nurse'] = $parameters['assisting_nurse'];
        $data['postoperative_diagnosis'] = $parameters['postoperative_diagnosis'];
        $data['preoperative_diagnosis'] = $parameters['preoperative_diagnosis'];
        //$medical_procedure =  $parameters['medical_procedure'];
        $data['indication'] = $parameters['indication'];
        $data['medical_procedure_id'] = $parameters['medical_procedure_id'];
        $data['position'] = $parameters['position'];


        $doctor_medical_procedures_list = $this->db->select("*")->from("doctor_medical_procedures")->where("medical_procedure_id ='" . $parameters['medical_procedure_id'] . "' and doctor_id = '" . $parameters['doctor_id'] . "'")->get()->row();

        if (count($doctor_medical_procedures_list) > 0) {
            $data['medical_procedure'] = $doctor_medical_procedures_list->medical_procedure;
        } else {
            $medical_procedures_list = $this->db->select("*")->from("medical_procedures")->where("medical_procedure_id ='" . $parameters['medical_procedure_id'] . "'")->get()->row();

            $data['medical_procedure'] = $medical_procedures_list->procedure_description;
        }


        if ($parameters['patient_id'] != "" || $parameters['patient_id'] != NULL) {
            $data_val['patient_list'] = $this->db->select("*")->from("patients")->where("patient_id = '" . $parameters['patient_id'] . "'")->get()->row();
        }

        $checking_patient_procedure = $this->db->select("*")->from("patient_procedure")->where("patient_id ='" . $data['patient_id'] . "' and  doctor_id = '" . $data['doctor_id'] . "' and appointment_id = '" . $data['appointment_id'] . "' and  medical_procedure_id = '" . $data['medical_procedure_id'] . "'")->get()->row();
        if (count($checking_patient_procedure) > 0) {
            unset($data['medical_procedure']);

            $ok = $this->Generic_model->updateData("patient_procedure", $data, array('patient_id' => $data['patient_id'], "doctor_id" => $data['doctor_id'], "appointment_id" => $data['appointment_id'], "medical_procedure_id" => $data['medical_procedure_id']));
            $patient_procedure_id = $checking_patient_procedure->patient_procedure_id;
        } else {
            $patient_procedure_id = $this->Generic_model->insertDataReturnId('patient_procedure', $data);
        }

        if ($patient_procedure_id != "" || $patient_procedure_id != NULL) {
            $data_val['patient_procedure'] = $this->db->select("*")->from("patient_procedure")->where("patient_procedure_id = '" . $patient_procedure_id . "'")->get()->row();
            //$data_val['']
            $pdf_name_val = str_replace(" ", "_", $data['patient_list']->first_name);
            $dataaa['html'] = $this->load->view('procedures/generate_patient_pdf', $data_val, true);
            $pdfFilePath = strtolower($pdf_name_val . "" . date('md')) . ".pdf";
            $data['procedure_patient_pdf'] = $pdfFilePath;
            $this->load->library('M_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            //download it.
            $this->m_pdf->pdf->Output("./uploads/procedures/" . $pdfFilePath, "F");
            $data_1['procedure_patient_pdf'] = base_url("uploads/procedures/" . $pdfFilePath . "");
            $this->response(array('code' => '200', 'message' => 'Patient Procedure', 'result' => $dataaa, 'requestname' => $method));
        } else {
            $this->response(array('code' => '400', 'message' => 'not updated'));
        }
    }

    public function healtRecordPDF_PdfTo_Html($parameters, $method, $user_id)
    {

        $data['getAppointment'] = $this->db->select("a.*,b.*,c.*,c.address as clinic_address,d.*,b.first_name as dfname,b.last_name as dlname,e.*,e.first_name as pfname,e.last_name as plname,e.middle_name as pmname,e.umr_no as umrno")->from("appointments a")->join("doctors b", "a.doctor_id=b.doctor_id")->join("clinics c", "a.clinic_id = c.clinic_id")->join("department d", "b.department_id = d.department_id")->join("patients e", "a.patient_id=e.patient_id")->where("a.appointment_id='" . $parameters['appointment_id'] . "' ")->get()->row();

        $data['pvrdt'] = $this->db->select("DISTINCT(vital_sign_recording_date_time) as vital_sign_time")->from("patient_vital_sign")->where("appointment_id = '" . $parameters['appointment_id'] . "'")->get()->result();

        $data['consent_forms'] = $this->db->select("*")->from("patient_consent_forms")->where("appointment_id = '" . $parameters['appointment_id'] . "'")->get()->result();


        $patient_name = $parameters['appointment_id'] . "_" . $data['getAppointment']->pfname . "_" . $data['getAppointment']->plname . "_" . $data['getAppointment']->appointment_date;
        $this->load->library('M_pdf');
        $dataaa['html'] = $this->load->view('patients/health_cards_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $this->load->library('M_pdf');
        //$this->m_pdf->pdf->SetAutoFont();
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/health_cards/" . $pdfFilePath, "F");
        $param['health_card'] = base_url() . 'uploads/health_cards/' . $pdfFilePath;

        $this->response(array('code' => '200', 'message' => 'Patient Appoitment Records', 'result' => $dataaa, 'requestname' => $method));
    }

    public function getSavedRecursiveForm_pdf_PdfToHtml($parameters, $method, $user_id)
    {
        $patient_form_id = $parameters['patient_form_id'];
        $patient_id = $parameters['patient_id'];
        $appointment_id = $parameters['appointment_id'];
        $doctor_id = $parameters['doctor_id'];

        $data['patient_list'] = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), '');
        // echo "select * from patient_form where patient_form_id='".$patient_form_id."' and patient_id='".$patient_id."' and doctor_id='".$doctor_id."'";
        // $pform_data = $this->db->query("select * from patient_form where form_id='".$patient_form_id."' and patient_id='".$patient_id."' and doctor_id='".$doctor_id."'")->row();
        $pform_data = $this->Generic_model->getSingleRecord('patient_form', array('patient_form_id' => $patient_form_id), '');

        $images = $this->db->query("select * from patient_form_scribbling_images where  patient_form_id='" . $patient_form_id . "'")->result();

        $data['scribbling_images'] = $images;

        $data['plform_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from(" patient_form_line_items")->where("patient_form_id='" . $pform_data->patient_form_id . "'")->order_by("section_id", "asc")->get()->result();

        $appointmentsInfo = $this->db->query("select doctor_id from appointments where appointment_id='" . $pform_data->appointment_id . "'")->row();
        $doctor_id = $appointmentsInfo->doctor_id;

        $data['doctor_info'] = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='" . $doctor_id . "'")->row();

        $form_name = $this->Generic_model->getSingleRecord('form', array('form_id' => $pform_data->form_id), '');
        $data['form_name'] = $form_name->form_name;
        $data['patient_form_id'] = $patient_form_id;
        $data['sticky_note_image'] = $pform_data->sticky_note_image;
        $pdf_name_val = str_replace(" ", "_", $data['patient_list']->first_name);
        $dataaa['html'] = $this->load->view('patients/patient_form_pdf', $data, true);
        // $pdfFilePath = strtolower($pdf_name_val . "" . date('md')) . ".pdf";

        $pdfFilePath = time() . rand(111, 999) . ".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $data['patient_form_pdf'] = $pdfFilePath;
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;
        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html, 2);
        //download it.
        $this->m_pdf->pdf->Output("./uploads/patient_form/" . $pdfFilePath, "F");
        $data_1['procedure_patient_pdf'] = base_url("uploads/patient_form/" . $pdfFilePath . "");

        $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $dataaa, 'requestname' => $method));
    }
    //End PdfToHtml Conversions

    // public function health_records_modifications($parameters, $method, $user_id)
    // {

    //     extract($parameters);
    //     $check_exist = $this->db->select("*")->from("patient_family_health_records")->where("id ='" . $parameters['id'] . "'")->get()->row();

    //     $id = $parameters['id'];
    //     $type=$parameters['type'];
    //     $full_name = $parameters['full_name'];
    //     $this->response(array('code' => '200', 'message' => 'Family Records Saved Successfully', 'result' =>NULL, 'requestname' => $method));
    //     if($type == "del")
    //     {
    //         $res = $this->Generic_model->deleteRecord('patient_family_health_records',array('id'=>$parameters['id']));
    //         $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => $type, 'requestname' => $method));  
    //     }
    //     else if($type == 'edit'){
    //         $i = 0;
    //         $family_records_edit['parent_patient_id'] =  $parameters['parent_patient_id'];
    //         $family_records_edit['phone_number'] =  $parameters['phone_number'];
    //         $family_records_edit['age'] =  $parameters['age'];
    //         $family_records_edit['gender'] =  $parameters['gender'];
    //         $family_records_edit['relationship'] =  $parameters['relationship'];
    //         $family_records_edit['full_name'] =  $parameters['full_name'];
    //         $updateRes = $this->Generic_model->updateData('patient_family_health_records', $family_records_edit, array('id'=>$id));

    //     $this->response(array('code' => '200', 'message' => 'Family Records Saved Successfully', 'result' =>NULL, 'requestname' => $method));
    //     }
    //     else{
    //         $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => $type, 'requestname' => $method));
    //     }
    // }

    public function patientList($parameters, $method, $user_id)
    {
        $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => $parameters, 'requestname' => $method));
    }

    public function patientList1()
    {
        echo "welcome";
        //   $abcd  = $this->db->select("*")->from("appointments")
        //             ->where("appointment_id='108'")
        //             ->get()
        //             ->row();
        // $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => $abcd, 'requestname' => $method));

    }

    public function patientList12($id)
    {
        //  echo $id;
        $abcd  = $this->db->select("*")->from("appointments")
            ->where("appointment_id='" . $id . "'")
            ->get()
            ->row();
        echo json_encode($abcd);
        // $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => $abcd, 'requestname' => $method));

    }

    public function delete_drug($parameters, $method, $user_id)
    {
        $id = $this->db->select("*")->from("patient_prescription_drug")
            ->where("patient_prescription_drug_id='" . $parameters['patient_prescription_drug_id'] . "'")
            ->get()
            ->row();
        if ($id != '') {
            $res = $this->Generic_model->deleteRecord('patient_prescription_drug', array('patient_prescription_drug_id' => $parameters['patient_prescription_drug_id']));
            $this->response(array('code' => '200', 'result' => "Successfully Deleted", 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'result' => "Already Deleted", 'requestname' => $method));
        }
    }


    public function getSavedRecursiveForms_web($parameters, $method, $user_id)
    {

        $form_type = $parameters['form_type'];
        // $department_id = $parameters['department_id'];
        // $patient_id = $parameters['patient_id'];
        $appointment_id = $parameters['appointment_id'];
        // $doctor_id = $user_id;


        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();

        $doctor_details = $this->db->select("*")->from('doctors')
            ->where("doctor_id='" . $appointmentList->doctor_id . "'")
            ->get()->row();

        $doctor_id = $doctor_details->doctor_id;
        $department_id = $doctor_details->department_id;
        $patient_id =  $appointmentList->patient_id;
        // echo $doctor_id;
        // echo $department_id;
        // echo $patient_id;
        // echo $appointment_id;


        $get_form = $this->db->select('GROUP_CONCAT(form_id) as form_ids')
            ->from('patient_form')
            ->where('form_type="' . $form_type . '" 
            and patient_id="' . $patient_id . '" 
            and doctor_id="' . $doctor_id . '"')
            ->get()
            ->row();

        // echo $this->db->last_query();
        // echo $patient_id;
        // print_r($get_form);
        // exit();

        if ($get_form->form_ids != "") {
            $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")
                ->join("patient_ps_line_items psl", "ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '" . $parameters['app_id'] . "' and form_id NOT IN(" . $get_form->form_ids . ")")->get()->result();

            // $data['query1'] = $this->db->last_query();
            // echo $this->db->last_query();
        } else {
            $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")
                ->join("patient_ps_line_items psl", "ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '" . $parameters['app_id'] . "'")->get()->result();

            // $data['query'] = $this->db->last_query();////
        }


        $ps = 0;
        if (count($presenting_symptoms) > 0) {

            foreach ($presenting_symptoms as $psl) {
                if ($psl->form_id != 0) {

                    $form_name = $this->Generic_model->getSingleRecord('form', array('form_id' => $psl->form_id), '');
                    $form_type = $form_name->form_type;
                } else {
                    $form_name = "Generic";
                    $form_type = "HOPI";
                }
                $data['forms'][$ps]['No'] = $ps;
                $data['forms'][$ps]['form_id'] = $psl->form_id;
                $data['forms'][$ps]['form_name'] = $form_name->form_name;
                $data['forms'][$ps]['form_type'] = $form_type;
                $data['forms'][$ps]['patient_id'] = $patient_id;
                $data['forms'][$ps]['doctor_id'] = $doctor_id;
                $data['forms'][$ps]['type'] = 1;
                $data['forms'][$ps]['appointment_id'] = (string)$parameters['app_id'];
                $data['forms'][$ps]['date'] = date("Y-m-d H:i:s", strtotime($psl->created_date_time));

                $ps++;
            }
        }


        $gform_data = $this->Generic_model->getAllRecords('patient_form', array('form_type' => $form_type, 'patient_id' => $patient_id, 'doctor_id' => $doctor_id), array('field' => 'created_date_time', 'type' => 'desc'));

        $gform_data_appointments = $this->Generic_model->getAllRecords('patient_form', array('form_type' => $form_type, 'patient_id' => $patient_id, 'doctor_id' => $doctor_id, 'appointment_id' => $appointment_id), array('field' => 'created_date_time', 'type' => 'desc'));

        if (count($gform_data) > 0) {

            $k = $ps;
            foreach ($gform_data as $result) {
                $form_name = $this->Generic_model->getSingleRecord('form', array('form_id' => $result->form_id), '');
                $data['forms'][$k]['No'] = $k + 1;
                $data['forms'][$k]['patient_form_id'] = $result->patient_form_id;
                $data['forms'][$k]['form_type'] = $result->form_type;
                $data['forms'][$k]['form_id'] = $result->form_id;
                $data['forms'][$k]['form_name'] = $form_name->form_name;
                $data['forms'][$k]['appointment_id'] = $result->appointment_id;
                $data['forms'][$k]['patient_id'] = $patient_id;
                $data['forms'][$k]['doctor_id'] = $doctor_id;
                $data['forms'][$k]['date'] = $result->created_date_time;
                $data['forms'][$k]['type'] = 2;
                $k++;
            }
        }

        if (empty($data['forms'])) {
            $data['forms'] = array();
        }

        if ($department_id == '' || $department_id == NULL || $department_id == 0) {
            $departcondition = "";
        } else {
            $departcondition = $department_id;
        }

        if ($form_type == 'Systemic Examination') {
            if ($departcondition != '' || $departcondition != NULL || $departcondition != 0) {
                $systemic_examination = $this->db->select("b.form_id,b.form_name,b.form_type,a.department_id")
                    ->from("form_department a")->join("form b", "b.form_id=a.form_id")
                    ->where("b.form_type='" . $form_type . "' and a.department_id='" . $departcondition . "'")->get()->result();
            }

            if (count($systemic_examination) > 0) {
                $i = 0;
                foreach ($systemic_examination as $value) {
                    $data['para'][$i]['No'] = $i;
                    $data['para'][$i]['form_id'] = $value->form_id;
                    $data['para'][$i]['form_name'] = $value->form_name;
                    $data['para'][$i]['form_type'] = $value->form_type;
                    $data['para'][$i]['department_id'] = $value->department_id;
                    $i++;
                }
            } else {
                $data['para'] = array();
            }
        } else {
            $ak = 0;
            foreach ($gform_data_appointments as $result1) {
                $form_name1 = $this->Generic_model->getSingleRecord('form', array('form_id' => $result1->form_id), '');
                $form_names[] = "'" . $form_name1->form_name . "'";
            }
            if (count($form_names) > 0) {
                $form_names = implode(",", $form_names);
                $systemic_examination = $this->db->select("form_id,form_name,form_type,department_id")
                    ->from("form")
                    ->where("form_type='" . $form_type . "' 
    and department_id='0' and form_name NOT IN (" . $form_names . ")")
                    ->get()
                    ->result();
                // echo $this->db->last_query();
                if (count($systemic_examination) > 0) {
                    $i = 0;
                    foreach ($systemic_examination as $value) {
                        $data['para'][$i]['No'] = $i;
                        $data['para'][$i]['form_id'] = $value->form_id;
                        $data['para'][$i]['form_name'] = $value->form_name;
                        $data['para'][$i]['form_type'] = $value->form_type;
                        $data['para'][$i]['department_id'] = $value->department_id;
                        $i++;
                    }
                } else {
                    $data['para'] = array();
                }
            } else {
                $systemic_examination = $this->db->select("form_id,form_name,
    form_type,department_id")
                    ->from("form")
                    ->where("form_type='" . $form_type . "' and department_id='" . $departcondition . "' ")->get()->result();

                if (count($systemic_examination) > 0) {
                    $i = 0;
                    foreach ($systemic_examination as $value) {
                        $data['para'][$i]['No'] = $i;
                        $data['para'][$i]['form_id'] = $value->form_id;
                        $data['para'][$i]['form_name'] = $value->form_name;
                        $data['para'][$i]['form_type'] = $value->form_type;
                        $data['para'][$i]['department_id'] = $value->department_id;
                        $i++;
                    }
                } else {
                    $data['para'] = array();
                }
            }
        }


        $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));

        //pdf

        //pdf
    }


    public function deletePatienForms($parameters, $method, $user_id)
    {
        $patient_form_id = $parameters['patient_form_id'];
        $ok = $this->Generic_model->deleteRecord('patient_form_line_items', array('patient_form_id' => $patient_form_id));
        if ($ok == 1) {
            $this->Generic_model->deleteRecord('patient_form', array('patient_form_id' => $patient_form_id));
        }
        $data['result'] = 'Successfully Deleted';
        $this->response(array('code' => '200', 'message' => 'Form Deleted Successfully', 'result' => $data, 'requestname' => $method));
    }

    public function getInfo($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];

        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();

        $clinicInfo = $this->db->select("*")->from("clinics")
            ->where("clinic_id= '" . $appointmentList->clinic_id . "'")
            ->get()
            ->row();


        $doctor_details = $this->db->select("*")->from('doctors')
            ->where("doctor_id='" . $appointmentList->doctor_id . "'")
            ->get()->row();

        $patient_details = $this->db->query("select * from patients where patient_id='" . $appointmentList->patient_id . "'")->row();
        $title = ($patient_details->title == "") ? '' : $patient_details->title . ". ";

        $rmp_number = '';
        $rmp_status = '';

        if ($appointmentList->rmp_id == 0) {
            // $rmp_number->rmp_phone = "null";
            $rmp_status = 0;
        } else if ($appointmentList->rmp_id > 0) {

            $rmp_status = 1;

            $rmp_number = $this->db->query("SELECT * FROM `rural_rmp_registration` WHERE rmp_id='" . $appointmentList->rmp_id . "'")->row();
        }

        $packageInfo = $this->db->query("select * from doctor_packages where doctor_id='" . $doctor_details->doctor_id . "'")->row();
        $data['parameters'][0]['appointment_id'] = $parameters['appointment_id'];
        $data['parameters'][0]['patient_id'] = $appointmentList->patient_id;
        $data['parameters'][0]['slot_type'] = $appointmentList->slot_type;
        $data['parameters'][0]['clinic_id'] = $appointmentList->clinic_id;
        $data['parameters'][0]['clinic_name'] = $clinicInfo->clinic_name;
        $data['parameters'][0]['status'] = $appointmentList->status;
        $data['parameters'][0]['umr_no'] = $appointmentList->umr_no;
        $data['parameters'][0]['rmp_status'] = $rmp_status;
        $data['parameters'][0]['rmp_id'] = $appointmentList->rmp_id;
        $data['parameters'][0]['rmp_number'] = $rmp_number->rmp_phone;
        $data['parameters'][0]['patient_name'] = $patient_details->title . ". " . $patient_details->first_name . " " . $patient_details->last_name;
        $data['parameters'][0]['gender'] = $patient_details->gender;
        $data['parameters'][0]['age'] = $patient_details->age;
        $data['parameters'][0]['mobile'] = DataCrypt($patient_details->mobile, 'decrypt');
        $data['parameters'][0]['doctor_number'] = $doctor_details->mobile;
        $data['parameters'][0]['appointment_date'] = date("d/m/Y", strtotime($appointmentList->appointment_date));
        $data['parameters'][0]['doctor_id'] = $doctor_details->doctor_id;
        $data['parameters'][0]['package_id'] = $packageInfo->package_id;
        $data['parameters'][0]['doctor_name'] = $doctor_details->salutation . " " . $doctor_details->first_name . " " . $doctor_details->last_name;
        $data['parameters'][0]['department'] = $doctor_details->department_id;
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $data, 'requestname' => $method));
    }

    public function saveSuggestions($parameters, $method, $user_id)
    {
        $appointment_id = $parameters['appointment_id'];
        $form_type = $parameters['form_type'];
        $suggestion_name = $parameters['suggestion_name'];

        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();
        $doctor_id = $appointmentList->doctor_id;
        $patient_id = $appointmentList->patient_id;

        $insert_data['doctor_id'] = $doctor_id;
        $insert_data['patient_id'] = $patient_id;
        $insert_data['form_type'] = $form_type;
        $insert_data['suggestion_name'] = $suggestion_name;
        $insert_data['created_by'] = $doctor_id;
        $insert_data['modified_by'] = $doctor_id;
        $insert_data['created_date_time'] = date('Y-m-d H:i:s');
        $insert_data['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('suggestions_list', $insert_data);
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $insert_data, 'requestname' => $method));
    }

    public function getSuggestionsList($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];
        $form_type = $parameters['form_type'];

        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();
        $doctor_id = $appointmentList->doctor_id;

        $psInfo = $this->db->query("select *,
    count(*) as count from suggestions_list
    where doctor_id='" . $doctor_id . "' 
    and form_type='" . $form_type . "' 
    group by suggestion_name order by count DESC LIMIT 6")->result();

        if (count($psInfo) > 0) {
            $i = 0;
            foreach ($psInfo as $value) {
                $data['Suggestions'][$i]['id'] = $value->id;
                $data['Suggestions'][$i]['form_type'] = $value->form_type;
                $data['Suggestions'][$i]['doctor_id'] = $value->doctor_id;
                $data['Suggestions'][$i]['suggestion_name'] = $value->suggestion_name;
                $i++;
            }
            $res = sort($data['Suggestions']);
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        } else {
            $data['Suggestions'] = array();
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        }
    }

    public function getAllSuggestions($parameters, $method, $user_id)
    {

        $appointment_id = $parameters['appointment_id'];
        $form_type = $parameters['form_type'];

        $appointmentList = $this->db->select("*")->from("appointments")
            ->where("appointment_id= '" . $appointment_id . "'")
            ->get()
            ->row();
        $doctor_id = $appointmentList->doctor_id;

        $suggestions = $this->db->query("select * from suggestions_list
    where patient_id='" . $appointmentList->patient_id . "' 
    and form_type='" . $form_type . "' ")->result();

        if (count($suggestions) > 0) {
            $i = 0;
            foreach ($suggestions as $value) {
                $data['Suggestions'][$i]['No'] = $i + 1;
                $data['Suggestions'][$i]['id'] = $value->id;
                $data['Suggestions'][$i]['form_type'] = $value->form_type;
                $data['Suggestions'][$i]['doctor_id'] = $value->doctor_id;
                $data['Suggestions'][$i]['suggestion_name'] = $value->suggestion_name;
                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        } else {
            $data['Suggestions'] = array();
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        }
    }

    public function deleteSuggestions($parameters, $method, $user_id)
    {
        $id = $parameters['id'];
        $form_type = $parameters['form_type'];

        $delete = $this->Generic_model->deleteRecord('suggestions_list', array('id' => $id));

        $data['result'] = 'Successfully Deleted';

        $this->response(array('code' => '200', 'message' => 'Form Deleted Successfully', 'result' => $data, 'requestname' => $method));
    }

    public function editSuggestions($parameters, $method, $user_id)
    {
        $id = $parameters['id'];
        $suggestion_name = $parameters['suggestion_name'];
        $check_exist = $this->db->select("*")->from("suggestions_list")
            ->where("id ='" . $id . "'")
            ->get()
            ->row();

        if ($check_exist != null) {
            $suggestion_edit['suggestion_name'] = $suggestion_name;
            $updateRes = $this->Generic_model->updateData('suggestions_list', $suggestion_edit, array('id' => $id));
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'Updated Sucessfully', 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'No Id Exsits', 'requestname' => $method));
        }
    }

    public function testingVideoInfo($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $name = $parameters['name'];

        $check_exist = $this->db->select("*")->from("video_testing")
            ->where("id ='" . $patient_id . "'")
            ->get()
            ->row();

        if ($check_exist != null) {
            if ($patient_id == '2') {
                $suggestion_edit['patient_id'] = '2';
                $updateRes = $this->Generic_model->updateData('video_testing', $suggestion_edit, array('id' => $patient_id));
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'Updated Sucessfully', 'requestname' => $method));
            }
            if ($patient_id == '3') {
                $suggestion_edit['patient_id'] = '3';
                $updateRes = $this->Generic_model->updateData('video_testing', $suggestion_edit, array('id' => $patient_id));
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'Updated Sucessfully', 'requestname' => $method));
            }
        } else {
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'No Id Exsits', 'requestname' => $method));
        }

        // $insert_data['patient_id'] = $patient_id;
        // $insert_data['name'] = $name;
        // $this->Generic_model->insertData('video_testing', $insert_data);
        // $this->response(array('code' => '200', 'message' => 'Success', 'result' => $insert_data, 'requestname' => $method));
    }


    public function testingVideoInfoo($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];

        $check_exist = $this->db->select("*")->from("video_testing")
            ->where("id ='" . $patient_id . "'")
            ->get()
            ->row();

        if ($check_exist != null) {
            if ($patient_id == '2') {
                $suggestion_edit['patient_id'] = '0';
                $updateRes = $this->Generic_model->updateData('video_testing', $suggestion_edit, array('id' => $patient_id));
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'Updated Sucessfully', 'requestname' => $method));
            }
            if ($patient_id == '3') {
                $suggestion_edit['patient_id'] = '0';
                $updateRes = $this->Generic_model->updateData('video_testing', $suggestion_edit, array('id' => $patient_id));
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'Updated Sucessfully', 'requestname' => $method));
            }
        } else {
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => 'No Id Exsits', 'requestname' => $method));
        }

        // $insert_data['patient_id'] = $patient_id;
        // $insert_data['name'] = $name;
        // $this->Generic_model->insertData('video_testing', $insert_data);
        // $this->response(array('code' => '200', 'message' => 'Success', 'result' => $insert_data, 'requestname' => $method));
    }


    public function getpatientTestingInfo($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $suggestions = $this->db->query("select * from video_testing
    where id='" . $patient_id . "' ")->result();

        if (count($suggestions) > 0) {
            $i = 0;
            foreach ($suggestions as $value) {
                $data['Suggestions'][$i]['id'] = $value->id;
                $data['Suggestions'][$i]['name'] = $value->name;
                $data['Suggestions'][$i]['patient_id'] = $value->patient_id;
                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        } else {
            $data['Suggestions'] = array();
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
        }
    }



    //test
    public function sessionUpdate($parameters, $method, $user_id)
    {
        $slots =  $this->db->select("*")
            ->from("clinic_doctor_weekday_slots")
            ->where("session=''  ORDER BY `clinic_doctor_weekday_slot_id` ASC")
            ->get()
            ->result();
        // $updateRes = $this->Generic_model->updateData('prescription_template', $prescription_template_edit, array('prescription_template_id'=>$prescription_template_id));
        if (count($slots) > 0) {
            $j = 0;
            foreach ($slots as $inv_lineitem) {
                $paraa['slots'][$j]['clinic_doctor_weekday_slot_id'] = $inv_lineitem->clinic_doctor_weekday_slot_id;
                $paraa['slots'][$j]['from_time'] = $inv_lineitem->from_time;


                $investigation_template_edit['session'] = 'afternoon';
                $updateRes = $this->Generic_model->updateData(
                        'clinic_doctor_weekday_slots',
                        $investigation_template_edit,
                        array('clinic_doctor_weekday_slot_id' => $inv_lineitem->clinic_doctor_weekday_slot_id)
                    );
                $j++;
            }
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa, 'requestname' => $method));
        }
    }

    //test

    public function paymentCharges($parameters, $method, $user_id)
    {
        $paraa['payment_charges'][0]['internetHandlingFees'] = '8.26';
        $paraa['payment_charges'][0]['bookingFees'] = '7';
        $paraa['payment_charges'][0]['serviceTax'] = '18';
        $paraa['payment_charges'][0]['paymentGatewayCharges'] = '2.4';

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa, 'requestname' => $method));
    }


    public function patient_shared_documents($parameters, $method, $user_id)
    {
        extract($parameters);
        $documents = $this->db->select("*")
            ->from("citizen_records c")
            ->join("doctor_patient_documents dp", "c.citizen_record_id=dp.document_id")
            ->where("dp.patient_id= '" . $patient_id . "' and dp.doctor_id= '" . $doctor_id . "'")
            ->order_by("citizen_record_id", "desc")
            ->get()->result();

        $pv = 0;
        if (count($documents) > 0) {

            foreach ($documents as $pav) {
                $param['patient_documents'][$pv]['citizen_record_id'] = $pav->citizen_record_id;
                $param['patient_documents'][$pv]['current_date'] = $pav->cur_date;
                $param['patient_documents'][$pv]['patient_id'] = $pav->patient_id;
                $param['patient_documents'][$pv]['report_date'] = $pav->report_date;
                $param['patient_documents'][$pv]['document_type'] = $pav->document_type;

                $param['patient_documents'][$pv]['description'] = $pav->description;
                $images = trim($pav->images, ",");
                $picture_explode = explode(",", $images);
                for ($k = 0; $k < count($picture_explode); $k++) {
                    $param['patient_documents'][$pv]['images'][$k]['image'] = base_url('uploads/my_records/' . trim($picture_explode[$k]));
                }
                $pv++;
            }
        } else {
            $param['patient_documents'] = NULL;
        }
        $this->response(array('code' => '200', 'message' => 'Patient Shared Documents', 'result' => $param, 'requestname' => $method));
    }


    public function testing($parameters, $method, $user_id)
    {
        $id = $parameters['patient_id'];
        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $id, 'requestname' => $method));
    }

    public function getDoctors($parameters, $method, $user_id)
    {

        $clinic_id = $parameters['clinic_id'];
        $profile_id = $parameters['profile_id'];
        $role_id = $parameters['role_id'];

        // get all doctors belongs to the clinic '$clinic_id'
        $doctors_list = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join("doctors d", "cd.doctor_id=d.doctor_id")->join("department de", "d.department_id=de.department_id")->where("cd.clinic_id ='" . $clinic_id . "'")->group_by("cd.doctor_id")->order_by("d.doctor_id", "asc")->get()->result();

        $d = 0;
        if (count($doctors_list) > 0) {

            foreach ($doctors_list as $doctor) {
                $param['doctor'][$d]['doctor_id'] = $doctor->doctor_id;
                $param['doctor'][$d]['doctor_name'] = "Dr. " . strtoupper($doctor->first_name . " " . $doctor->last_name);
                $param['doctor'][$d]['designation'] = $doctor->qualification;
                $param['doctor'][$d]['department'] = $doctor->department_name;
                $param['doctor'][$d]['registration_code'] = $doctor->registration_code;
                $param['doctor'][$d]['color_code'] = $doctor->color_code;

                $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c", "cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctor->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id", "asc")->get()->result();

                if (count($clinics_list) > 0) {
                    $c = 0;
                    foreach ($clinics_list as $clinic) {
                        $cdw = 0;
                        $cdww = 0;
                        $param['doctor'][$d]['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
                        $param['doctor'][$d]['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
                        $param['doctor'][$d]['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
                        $param['doctor'][$d]['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
                        $param['doctor'][$d]['clinics'][$c]['online_consulting_fee'] = $clinic->online_consulting_fee;
                        $param['doctor'][$d]['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
                        $param['doctor'][$d]['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
                        $param['doctor'][$d]['clinics'][$c]['lab_discount'] = $clinic->lab_discount;

                        $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='walkin'")->group_by("weekday")->order_by("clinic_doctor_weekday_id", "asc")->get()->result();



                        foreach ($week_day_list as $weekday) {
                            // initiates weekdays to the number
                            if ($weekday->weekday == 1) {
                                $week = "Monday";
                            }
                            if ($weekday->weekday == 2) {
                                $week = "Tuesday";
                            }
                            if ($weekday->weekday == 3) {
                                $week = "Wednesday";
                            }
                            if ($weekday->weekday == 4) {
                                $week = "Thursday";
                            }
                            if ($weekday->weekday == 5) {
                                $week = "Friday";
                            }
                            if ($weekday->weekday == 6) {
                                $week = "Saturday";
                            }
                            if ($weekday->weekday == 7) {
                                $week = "Sunday";
                            }

                            $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id", "asc")->get()->result();

                            $cdws = 0;
                            foreach ($week_day_list_slots as $wdls) {
                                // $day[]=$wdls->session;
                                $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)) . "-" . $wdls->session;
                                $cdws++;
                            }

                            $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['day'] = $week;
                            // $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['session'] = $sessions_list;

                            for ($i = 0; $i < count($sessions_list); $i++) {
                                $sl = explode("-", $sessions_list[$i]);
                                $time = date("H", strtotime($sl[0]));
                                if ($sl[2] == 'morning') {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Morning';
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($sl[2] == 'afternoon') {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Afternoon';
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($sl[2] == 'evening') {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Evening';
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                }

                                // if ($time < 12) {
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                // } else if ($time > 12 && $time < 17) {
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                // } else if ($time >= 17) {
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                // }
                            }
                            unset($sessions_list);
                            $cdw++;
                        }

                        $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='video call'")->group_by("weekday")->order_by("clinic_doctor_weekday_id", "asc")->get()->result();


                        if (count($week_day_list) > 0) {
                            foreach ($week_day_list as $weekday) {
                                // initiates weekdays to the number
                                if ($weekday->weekday == 1) {
                                    $week = "Monday";
                                }
                                if ($weekday->weekday == 2) {
                                    $week = "Tuesday";
                                }
                                if ($weekday->weekday == 3) {
                                    $week = "Wednesday";
                                }
                                if ($weekday->weekday == 4) {
                                    $week = "Thursday";
                                }
                                if ($weekday->weekday == 5) {
                                    $week = "Friday";
                                }
                                if ($weekday->weekday == 6) {
                                    $week = "Saturday";
                                }
                                if ($weekday->weekday == 7) {
                                    $week = "Sunday";
                                }

                                $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id", "asc")->get()->result();

                                $cdws = 0;
                                foreach ($week_day_list_slots as $wdls) {
                                    // $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
                                    $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)) . "-" . $wdls->session;
                                    $cdws++;
                                }

                                $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['day'] = $week;
                                // $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['session'] = $sessions_listt;

                                for ($i = 0; $i < count($sessions_list); $i++) {
                                    $sl = explode("-", $sessions_list[$i]);
                                    $time = date("H", strtotime($sl[0]));

                                    if ($sl[2] == 'morning') {
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Morning';
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'afternoon') {
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Afternoon';
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'evening') {
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Evening';
                                        $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    }
                                }
                                unset($sessions_list);
                                $cdww++;
                            }
                        } else {
                            $param['doctor'][$d]['clinics'][$c]['videocall_working_days'] = array();
                        }

                        // $param['doctor'][$d]['clinics'][$c]['video_slots']=array();


                        $c++;
                    }
                } else {
                    $param['doctor'][$d]['clinics'] = array();
                }


                $d++;
            }
        } else {
            $param['doctor'] = array();
        }

        $this->response(array('code' => '200', 'message' => 'Region Masters', 'result' => $param, 'requestname' => $method));
    }

    public function telecallangularNotifications($parameters, $method, $user_id)
    {
        // $user_id=$parameters['user_id'];
        $mobile_number = $parameters['mobile_number'];
        // $this->Generic_model->angularNotifications($mobile_number,'','','','TelecallNotification');

        $checkMobileNumber = $this->db->select("*")
            ->from("patients_device_info")
            ->where("mobile ='" . $mobile_number . "'")
            ->get()
            ->row();

        if (count($checkMobileNumber) > 0) {
            $fcm_id = $checkMobileNumber->fcm_id;
            $this->Generic_model->angularNotificationsCitizens($mobile_number, '', '', '', 'TelecallNotification');
            $this->response(array('code' => '200', 'message' =>   $mobile_number, 'result' => 'Success', 'requestname' => $method));
        } else {
            $fcm_id = "No Number Registered";
            $this->response(array('code' => '201', 'message' =>   $mobile_number, 'result' => $fcm_id, 'requestname' => $method));
        }
    }


    public function followup_plan($parameters, $method, $user_id)
    {
        $pdf_status = 1;
        $d_allergy['pdf_status'] = 1;
        $this->Generic_model->updateData("appointments", $d_allergy, array('appointment_id' => $parameters['appointment_id']));
        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        // Check if the prescription master exist for the specified appointment id
        $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

        $patient_prescription_id = $check_exist->patient_prescription_id;

        if (count($check_exist) > 0) {

            $patient_prescription_update['plan'] = $parameters['plan'];
            // $patient_prescription_update['general_instructions'] = $parameters['instructions'];

            // Master prescription exist
            // Update it with the instruction and plan provided in the paramaeters    
            $this->Generic_model->updateData('patient_prescription', $patient_prescription_update, array('patient_prescription_id' => $patient_prescription_id));
        } else {
            // No master prescription exist
            // Create new prescription master record and then 
            // Create master prescription and then line items
            $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
            $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
            $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
            $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
            $patient_prescription_insert['plan'] = $parameters['plan'];
            $patient_prescription_insert['general_instructions'] = '';
            $patient_prescription_insert['status'] = 1;
            $patient_prescription_insert['created_by'] = $user_id;
            $patient_prescription_insert['modified_by'] = $user_id;
            $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
            $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

            $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);
        }

        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        $this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $parameters, 'requestname' => $method));
    }

    public function followup_instructions($parameters, $method, $user_id)
    {
        $pdf_status = 1;
        $d_allergy['pdf_status'] = 1;
        $this->Generic_model->updateData("appointments", $d_allergy, array('appointment_id' => $parameters['appointment_id']));
        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        // Check if the prescription master exist for the specified appointment id
        $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

        $patient_prescription_id = $check_exist->patient_prescription_id;

        if (count($check_exist) > 0) {

            // $patient_prescription_update['plan'] = $parameters['plan'];
            $patient_prescription_update['general_instructions'] = $parameters['instructions'];

            // Master prescription exist
            // Update it with the instruction and plan provided in the paramaeters    
            $this->Generic_model->updateData('patient_prescription', $patient_prescription_update, array('patient_prescription_id' => $patient_prescription_id));
        } else {
            // No master prescription exist
            // Create new prescription master record and then 
            // Create master prescription and then line items
            $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
            $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
            $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
            $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
            $patient_prescription_insert['plan'] = '';
            $patient_prescription_insert['general_instructions'] = nl2br($parameters['instructions']);
            $patient_prescription_insert['status'] = 1;
            $patient_prescription_insert['created_by'] = $user_id;
            $patient_prescription_insert['modified_by'] = $user_id;
            $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
            $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

            $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);
        }

        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        $this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $parameters, 'requestname' => $method));
    }

    // public function followup_date($parameters, $method, $user_id) {
    //     $pdf_status = 1;
    //     $d_allergy['pdf_status'] = 1;
    //     $this->Generic_model->updateData("appointments", $d_allergy, array('appointment_id'=>$parameters['appointment_id']));
    //     // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
    //         // Check if the prescription master exist for the specified appointment id
    //     $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

    //     $patient_prescription_id = $check_exist->patient_prescription_id;

    //     if(count($check_exist) > 0){

    //         $patient_prescription_update['plan'] = $parameters['plan'];
    //         $patient_prescription_update['general_instructions'] = $parameters['instructions'];
    //         $this->Generic_model->updateData('patient_prescription',$patient_prescription_update,array('patient_prescription_id'=>$patient_prescription_id));

    //             // Master prescription exist
    //             // Update it with the instruction and plan provided in the paramaeters    
    //         // $this->Generic_model->updateData('patient_prescription',$patient_prescription_update,array('patient_prescription_id'=>$patient_prescription_id));
    //     }
    //     else{
    //             // No master prescription exist
    //             // Create new prescription master record and then 
    //             // Create master prescription and then line items
    //         $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
    //         $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
    //         $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
    //         $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
    //         $patient_prescription_insert['plan'] = $parameters['plan'];
    //         $patient_prescription_insert['general_instructions'] = nl2br($parameters['instructions']);
    //         $patient_prescription_insert['status'] = 1;
    //         $patient_prescription_insert['created_by'] = $user_id;
    //         $patient_prescription_insert['modified_by'] = $user_id;
    //         $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
    //         $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

    //         // $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);
    //     }

    //         // Check if the follow up days are provided in the parameters
    //     if($parameters['days'] != "" || $parameters['days'] != NULL){

    //         $parent_appointment = $this->db->select("appointment_date, appointment_time_slot")->from("appointments")->where("appointment_id='" . $parameters['appointment_id'] . "'")->get()->row();

    //             // Calculate the next appointment date with respect to the days specified
    //         $next_appointment_date = $date = date('Y-m-d', strtotime('+' . $parameters['days'] . ' day', strtotime($parent_appointment->appointment_date)));

    //         if($parameters['days'] != 0){
    //             $followup1['follow_up_date'] = $next_appointment_date;
    //             $this->Generic_model->updateData('patient_prescription',$followup1,array('patient_prescription_id'=>$patient_prescription_id));
    //         }
    //         else{
    //             $followup1['follow_up_date'] = "";
    //             $this->Generic_model->updateData('patient_prescription',$followup1,array('patient_prescription_id'=>$patient_prescription_id));
    //         }

    //         if($parameters['followup_appointment_id'] != '' || $parameters['followup_appointment_id'] != NULL){
    //                 // Update the appointment date & time slot of the existing
    //             $followup_appointment['appointment_date'] = $next_appointment_date;
    //             $followup_appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;

    //             $this->Generic_model->updateData('appointments',$followup_appointment,array('appointment_id'=>$parameters['followup_appointment_id']));
    //         }else{
    //                 // Create the new appointment for the follow days/date provided
    //             $appointment['parent_appointment_id'] = $parameters['appointment_id'];
    //             $appointment['clinic_id'] = $parameters['clinic_id'];
    //             $appointment['patient_id'] = $parameters['patient_id'];
    //             $appointment['umr_no'] = $parameters['umr_no'];
    //             $appointment['doctor_id'] = $parameters['doctor_id'];
    //             $appointment['appointment_type'] = "Follow-up";
    //             $appointment['appointment_date'] = $next_appointment_date;
    //             $appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;
    //             $appointment['priority'] = "none";
    //             $appointment['status'] = "booked";
    //             $appointment['created_by'] = $user_id;
    //             $appointment['modified_by'] = $user_id;
    //             $appointment['created_date_time'] = date('Y-m-d H:i:s');
    //             $appointment['modified_date_time'] = date('Y-m-d H:i:s');

    //                 // Create new appointment with specified days and generated next appointment date
    //             $followup_appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

    //                 // Create a followup_appointment_id item 
    //             $parameters['followup_appointment_id'] = $followup_appointment_id;
    //         }          

    //         $parameters['next_followup_date'] = $next_appointment_date;
    //         $parameters['time_slot'] = $parent_appointment->appointment_time_slot;

    //     }
    //     // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
    //     $this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $parameters, 'requestname' => $method));

    // }


    public function followup_date($parameters, $method, $user_id)
    {
        $pdf_status = 1;
        $d_allergy['pdf_status'] = 1;
        $this->Generic_model->updateData("appointments", $d_allergy, array('appointment_id' => $parameters['appointment_id']));
        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        // Check if the prescription master exist for the specified appointment id
        $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

        $patient_prescription_id = $check_exist->patient_prescription_id;

        // if (count($check_exist) > 0) {

        //     $patient_prescription_update['plan'] = $parameters['plan'];
        //     $patient_prescription_update['general_instructions'] = $parameters['instructions'];
        //     $this->Generic_model->updateData('patient_prescription', $patient_prescription_update, array('patient_prescription_id' => $patient_prescription_id));

        // Master prescription exist
        // Update it with the instruction and plan provided in the paramaeters    
        // $this->Generic_model->updateData('patient_prescription',$patient_prescription_update,array('patient_prescription_id'=>$patient_prescription_id));
        // } else {
        // No master prescription exist
        // Create new prescription master record and then 
        // Create master prescription and then line items
        // $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
        // $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
        // $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
        // $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
        // $patient_prescription_insert['plan'] = $parameters['plan'];
        // $patient_prescription_insert['general_instructions'] = nl2br($parameters['instructions']);
        // $patient_prescription_insert['status'] = 1;
        // $patient_prescription_insert['created_by'] = $user_id;
        // $patient_prescription_insert['modified_by'] = $user_id;
        // $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
        // $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

        // $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);
        // }

        // Check if the follow up days are provided in the parameters
        if ($parameters['days'] != "" || $parameters['days'] != NULL) {

            $parent_appointment = $this->db->select("appointment_date, appointment_time_slot")->from("appointments")->where("appointment_id='" . $parameters['appointment_id'] . "'")->get()->row();

            // Calculate the next appointment date with respect to the days specified
            $next_appointment_date = $date = date('Y-m-d', strtotime('+' . $parameters['days'] . ' day', strtotime($parent_appointment->appointment_date)));

            if ($parameters['days'] != 0) {
                $followup1['follow_up_date'] = $next_appointment_date;
                $this->Generic_model->updateData('patient_prescription', $followup1, array('patient_prescription_id' => $patient_prescription_id));
            } else {
                $followup1['follow_up_date'] = "";
                $this->Generic_model->updateData('patient_prescription', $followup1, array('patient_prescription_id' => $patient_prescription_id));
            }

            if ($parameters['followup_appointment_id'] != '' || $parameters['followup_appointment_id'] != NULL) {
                // Update the appointment date & time slot of the existing
                $followup_appointment['appointment_date'] = $next_appointment_date;
                $followup_appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;

                $this->Generic_model->updateData('appointments', $followup_appointment, array('appointment_id' => $parameters['followup_appointment_id']));
            } else {
                // Create the new appointment for the follow days/date provided
                $appointment['parent_appointment_id'] = $parameters['appointment_id'];
                $appointment['clinic_id'] = $parameters['clinic_id'];
                $appointment['patient_id'] = $parameters['patient_id'];
                $appointment['umr_no'] = $parameters['umr_no'];
                $appointment['doctor_id'] = $parameters['doctor_id'];
                $appointment['appointment_type'] = "Follow-up";
                $appointment['appointment_date'] = $next_appointment_date;
                $appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;
                $appointment['priority'] = "none";
                $appointment['status'] = "booked";
                $appointment['created_by'] = $user_id;
                $appointment['modified_by'] = $user_id;
                $appointment['created_date_time'] = date('Y-m-d H:i:s');
                $appointment['modified_date_time'] = date('Y-m-d H:i:s');

                // Create new appointment with specified days and generated next appointment date
                $followup_appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

                // Create a followup_appointment_id item 
                $parameters['followup_appointment_id'] = $followup_appointment_id;
            }

            $parameters['next_followup_date'] = $next_appointment_date;
            $parameters['time_slot'] = $parent_appointment->appointment_time_slot;
        }
        // $this->Generic_model->updateData('appointments',$pdf_status,array('appointment_id'=>$parameters['appointment_id']));
        $this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $parameters, 'requestname' => $method));
    }

    public function patient_procedure_edit_angular($parameters, $method, $user_id)
    {

        $patient_id = $parameters['patient_id'];
        $doctor_id = $parameters['doctor_id'];
        $appointment_id = $parameters['appointment_id'];
        $medical_procedure_id = $parameters['medical_procedure_id'];
        $clinic_id = $parameters['clinic_id'];

        $checking_patient_procedure = $this->db->select("*")->from("patient_procedure")->where("patient_id ='" . $patient_id . "' and  doctor_id = '" . $doctor_id . "' and appointment_id = '" . $appointment_id . "' and  medical_procedure_id = '" . $medical_procedure_id . "'")->get()->row();
        if (count($checking_patient_procedure) > 0) {
            $data['procedure_description'] = strip_tags($checking_patient_procedure->medical_procedure);
        } else {
            $check_doctor_procedure = $this->db->select("*")->from("doctor_medical_procedures")->where("doctor_id ='" . $doctor_id . "' and medical_procedure_id ='" . $medical_procedure_id . "' and clinic_id ='" . $clinic_id . "'")->get()->row();
            if (count($check_doctor_procedure) > 0) {
                $data['procedure_description'] = "<html><body style='padding:0px; margin:0px;'>" . strip_tags($check_doctor_procedure->medical_procedure) . "/body></html>";
            } else {
                $standard_procedure = $this->db->select("*")->from("medical_procedures")->where("medical_procedure_id ='" . $medical_procedure_id . "'")->get()->row();
                $data['procedure_description'] = "<html><body>" . strip_tags($standard_procedure->procedure_description) . "/body></html>";
            }
        }
        $url_parameters = $patient_id . "/" . $doctor_id . "/" . $appointment_id . "/" . $medical_procedure_id . "/" . $clinic_id;
        $data["procedure_url"] = base_url("Procedure_update/patient_producer/" . $url_parameters);
        $this->response(array('code' => '200', 'message' => 'Patient Procedure', 'result' => $data, 'requestname' => $method));
    }

    public function patient_procedure_save($parameters, $method, $user_id)
    {
        // print_r($this->input->post());
        // exit;

        // extract($parameters);

        $patient_id = $parameters['patient_id'];
        $doctor_id = $parameters['doctor_id'];
        $appointment_id = $parameters['appointment_id'];
        $medical_procedure_id = $parameters['medical_procedure_id'];
        $clinic_id = $parameters['clinic_id'];
        $medical_procedure = $parameters['description'];
        $type = $parameters['type'];

        $checking_patient_procedure = $this->db->query("select * from patient_procedure where 
    patient_id ='" . $patient_id . "' and  doctor_id = '" . $doctor_id . "' and
    appointment_id = '" . $appointment_id . "' and  
    medical_procedure_id = '" . $medical_procedure_id . "'")
            ->row();

        if (count($checking_patient_procedure) > 0) {

            $param_1['patient_id'] = $patient_id;
            $param_1['doctor_id'] = $doctor_id;
            $param_1['appointment_id'] = $appointment_id;
            $param_1['medical_procedure_id'] = $medical_procedure_id;
            if ($type == "Save As Template") {
                $para3['clinic_id'] = $clinic_id;
                $para3['doctor_id'] = $doctor_id;
                $para3['medical_procedure_id'] = $medical_procedure_id;
                $para3['medical_procedure'] = $medical_procedure;
                $this->Generic_model->insertData("doctor_medical_procedures", $para3);
            } elseif ($type == "Save For This Patient") {
                $param_1['template'] = 0;
            }
            //$param_1['clinic_id'] = $clinic_id;
            $param_1['medical_procedure'] = $medical_procedure;

            $this->Generic_model->updateData("patient_procedure", $param_1, array('patient_id' => $patient_id, "doctor_id" => $doctor_id, "appointment_id" => $appointment_id, "medical_procedure_id" => $medical_procedure_id));
            $this->response(array('code' => '200', 'message' => 'Patient Procedure saved sucessfully', 'requestname' => $method));
        } else {

            $param_2['patient_id'] = $patient_id;
            $param_2['doctor_id'] = $doctor_id;
            $param_2['appointment_id'] = $appointment_id;
            $param_2['medical_procedure_id'] = $medical_procedure_id;
            if ($type == "Save As Template") {
                $para3['clinic_id'] = $clinic_id;
                $para3['doctor_id'] = $doctor_id;
                $para3['medical_procedure_id'] = $medical_procedure_id;
                $para3['medical_procedure'] = $medical_procedure;
                $this->Generic_model->insertData("doctor_medical_procedures", $para3);
            } elseif ($type == "Save For This Patient") {
                $param_2['template'] = 0;
            }
            //$param_2['clinic_id'] = $clinic_id;
            $param_2['medical_procedure'] = $medical_procedure;
            $this->Generic_model->insertData('patient_procedure', $param_2);
            $this->response(array('code' => '200', 'message' => 'Patient Procedure saved sucessfully', 'requestname' => $method));
        }
        // if($ok == 1){
        //     $url_parameters = $patient_id."/".$doctor_id."/".$appointment_id."/".$medical_procedure_id."/".$clinic_id;
        //     redirect("procedure_update/patient_producer/".$url_parameters);
        //     //redirect("patient_producer")
        // }


    }

    public function deleteCdImage($parameters, $method, $user_id)
    {
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $clinic_id = $parameters['clinic_id'];
        $appointment_id = $parameters['appointment_id'];
        $index = $parameters['index'];

        $check = $this->db->query("select * from patient_clinical_diagnosis where 
    doctor_id ='" . $doctor_id . "' and clinic_id='" . $clinic_id . "' and 
    patient_id='" . $patient_id . "' and appointment_id='" . $appointment_id . "'")
            ->row();

        if (count($check) > 0) {
            $images = $check->images;
            $array = explode(",", $images);

            if ($index == '2') {
                $data['images'] = $array[0];
                $res = $this->Generic_model->updateData('patient_clinical_diagnosis', $data, array('patient_clinical_diagnosis_id' => $check->patient_clinical_diagnosis_id));
                $this->response(array('code' => '200', 'message' => 'Deleted Successfully', 'result' => 'Deleted Successfully', 'requestname' => $method));
            } else {
                $data['images'] = $array[1];
                $res = $this->Generic_model->updateData('patient_clinical_diagnosis', $data, array('patient_clinical_diagnosis_id' => $check->patient_clinical_diagnosis_id));
                $this->response(array('code' => '200', 'message' => 'Deleted Successfully', 'result' => 'Deleted Successfully', 'requestname' => $method));
            }
        } else {
            $this->response(array('code' => '200', 'message' => 'patient_clinical_diagnosis_id not found', 'result' => 'patient_clinical_diagnosis_id not found', 'requestname' => $method));
        }
    }

    public function get_consentform_details($parameters, $method, $user_id)
    {
        $check_data = $this->db->select("*")->from("patient_consent_forms")
            ->where("patient_consent_form_id='" . $parameters['patient_consent_form_id'] . "'")
            ->get()->row();

        if (count($check_data) > 0) {
            $data['checked_by'] = $check_data->checked_by;
            $data['done_by'] = $check_data->done_by;
            $data['assisted_by'] = $check_data->assisted_by;
            $data['nurse'] = $check_data->nurse;
            $data['anesthetist'] = $check_data->anesthetist;
            // $data['patient_consent_form'] = $check_data->anesthetist;
            $this->response(array('code' => '200', 'message' => 'patient_consent_forms_details', 'result' => $data, 'requestname' => $method));
        } else {
            $this->response(array('code' => '201', 'message' => 'No Id Found', 'result' => 'No Id Found', 'requestname' => $method));
        }
    }

    //general instructions templates save
    public function gen_instructions_templates_save($parameters, $method, $user_id)
    {
        extract($parameters);
        if (!empty($doctor_id) && !empty($clinic_id)) {
            $data['gn_template_name'] = $template_name;
            $data['doctor_id'] = $doctor_id;
            $data['clinic_id'] = $clinic_id;
            $data['gen_instructions'] = $gen_instructions;
            $data['plan'] = $plan;
            $data['followup'] = $followup;
            $data['status'] = 1;
            $data['created_by'] = $doctor_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $doctor_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $gen_instructions_templates_id = $this->Generic_model->insertDataReturnId('gen_instructions_templates', $data);
            $param = "Successfully Added";
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'method' => $method));
        } else {
            $param = "Parameters Not Found";
            $this->response(array('code' => '201', 'message' => 'Error Occured', 'result' => $param, 'method' => $method));
        }
    }


    //general instructions templates update
    public function gen_instructions_templates_update($parameters, $method, $user_id)
    {
        extract($parameters);
        if (!empty($gen_instructions_templates_id)) {
            $data['gn_template_name'] = $template_name;
            // $data['doctor_id'] = $doctor_id;
            // $data['clinic_id'] = $clinic_id;
            // $data['gen_instructions'] = $gen_instructions;
            // $data['plan'] = $plan;
            // $data['followup'] = $followup;
            // $data['status'] = 1;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $gen_instructions_templates_id = $this->Generic_model->updateData('gen_instructions_templates', $data, array('gen_instructions_templates_id' => $gen_instructions_templates_id));
            $param = "Successfully Updated";
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'method' => $method));
        } else {
            $param = "Parameters Not Found";
            $this->response(array('code' => '201', 'message' => 'Error Occured', 'result' => $param, 'method' => $method));
        }
    }

    //general instructions templates list
    public function gen_instructions_templates_list($parameters, $method, $user_id)
    {
        extract($parameters);
        if (!empty($doctor_id) && !empty($clinic_id)) {
            $gen_instructions_templates = $this->Generic_model->getAllRecords('gen_instructions_templates', array('doctor_id' => $doctor_id, 'clinic_id' => $clinic_id));
            if (count($gen_instructions_templates) > 0) {
                $i = 0;
                foreach ($gen_instructions_templates as $value) {
                    $data['templates'][$i]['gen_instructions_templates_id'] = $value->gen_instructions_templates_id;
                    $data['templates'][$i]['template_name'] = $value->gn_template_name;
                    $data['templates'][$i]['doctor_id'] = $value->doctor_id;
                    $data['templates'][$i]['clinic_id'] = $value->clinic_id;
                    $data['templates'][$i]['gen_instructions'] = $value->gen_instructions;
                    $data['templates'][$i]['plan'] = $value->plan;
                    $data['templates'][$i]['followup'] = $value->followup;
                    $data['templates'][$i]['status'] = $value->status;
                    $i++;
                }
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => $data, 'method' => $method));
            } else {
                $param['templates'] = [];
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'method' => $method));
            }
        } else {
            $param['templates'] = [];
            $this->response(array('code' => '201', 'message' => 'Error Occured', 'result' => $param, 'method' => $method));
        }
    }

    //general instructions templates delete
    public function gen_instructions_templates_delete($parameters, $method, $user_id)
    {
        extract($parameters);
        if (!empty($gen_instructions_templates_id)) {
            $this->Generic_model->deleteRecord('gen_instructions_templates', array('gen_instructions_templates_id' => $gen_instructions_templates_id));
            $param = "Templates Deleted Successfully";
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'method' => $method));
        } else {
            $param = "Parameters Not Found";
            $this->response(array('code' => '201', 'message' => 'Error Occured', 'result' => $param, 'method' => $method));
        }
    }



    public function presenting_symptoms_submit_angular($parameters, $method, $user_id)
    {
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $appointment_id = $parameters['appointment_id'];
        $form_id = $parameters['form_id'];

        $symptoms = $parameters['symptoms'];
        if (count($symptoms) > 0) {
            $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")->join(" patient_ps_line_items psl", "ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("patient_id= '" . $parameters['patient_id'] . "' and appointment_id= '" . $parameters['appointment_id'] . "' and doctor_id= '" . $parameters['doctor_id'] . "' and clinic_id= '" . $parameters['clinic_id'] . "'")->get()->row();
            if (count($presenting_symptoms) > 0) {
                $patient_presenting_symptoms_id = $presenting_symptoms->patient_presenting_symptoms_id;
            } else {

                $ps['clinic_id'] = $clinic_id;
                $ps['doctor_id'] = $doctor_id;
                $ps['patient_id'] = $patient_id;
                $ps['umr_no'] = $umr_no;
                $ps['appointment_id'] = $appointment_id;
                $ps['created_by'] = $user_id;
                $ps['modified_by'] = $user_id;
                $ps['created_date_time'] = date('Y-m-d H:i:s');
                $ps['modified_date_time'] = date('Y-m-d H:i:s');
                $patient_presenting_symptoms_id = $this->Generic_model->insertDataReturnId('patient_presenting_symptoms', $ps);
            }

            if ($patient_presenting_symptoms_id != '') {
                for ($s = 0; $s < count($symptoms); $s++) {
                    if ($symptoms[$s]['form_id'] == 0) {
                        $get_generic_form = $this->db->select("form_id")->from("form")->where("form_name='Generic' and form_type='HOPI'")->get()->row();
                        $form_id = $get_generic_form->form_id;
                    } else {
                        $form_id = $symptoms[$s]['form_id'];
                    }
                    $check = $this->db->select("*")
                        ->from("patient_ps_line_items")
                        ->where("symptom_data= '" . $symptoms[$s]['symptom_data'] . "' and patient_presenting_symptoms_id= '" . $patient_presenting_symptoms_id . "' ")->get()->row();
                    if (count($check) > 0) {
                        $this->response(array('code' => '201', 'message' => 'Already exsists', 'result' => 'Already exsists', 'requestname' => $method));
                    } else {
                        $psl['patient_presenting_symptoms_id'] = $patient_presenting_symptoms_id;
                        $psl['symptom_data'] = $symptoms[$s]['symptom_data'];
                        $psl['time_span'] = $symptoms[$s]['time_span'];
                        $psl['form_id'] = $form_id;
                        $psl['span_type'] = $symptoms[$s]['span_type'];
                        $psl['created_by'] = $user_id;
                        $psl['modified_by'] = $user_id;
                        $psl['created_date_time'] = date('Y-m-d H:i:s');
                        $psl['modified_date_time'] = date('Y-m-d H:i:s');
                        $pps_line_item_id =  $this->Generic_model->insertDataReturnId('patient_ps_line_items', $psl);
                        $param['appointment_id'] = $appointment_id;
                        $param['pps_line_item_id'] = $pps_line_item_id;

                        $this->response(array('code' => '200', 'message' => 'Presenting Symptoms Done', 'result' => $param, 'requestname' => $method));
                    }
                }
            }
        } else {
            $param['appointment_id'] = $appointment_id;
            $this->response(array('code' => '200', 'message' => 'Presenting Symptoms Not Done', 'result' => $param, 'requestname' => $method));
        }
    }

    public function telecallPushNotifications($parameters, $method, $user_id)
    {
        // $user_id=$parameters['user_id'];
        $mobile_number = $parameters['mobile_number'];
        // $this->Generic_model->pushNotifications($mobile_number,'','','','TelecallNotification');

        $checkMobileNumber = $this->db->select("*")
            ->from("patients_device_info")
            ->where("mobile ='" . $mobile_number . "'")
            ->order_by('id', 'DESC')
            ->get()
            ->row();

        // echo  $this->db->last_query();
        if (count($checkMobileNumber) > 0) {
            $fcm_id = $checkMobileNumber->fcm_id;
            $this->Generic_model->pushNotificationsCitizens($mobile_number, '', '', '', 'TelecallNotification');
            $this->response(array('code' => '200', 'message' =>   $mobile_number, 'result' => 'Success', 'requestname' => $method));
        } else {
            $fcm_id = "No Number Registered";
            $this->response(array('code' => '201', 'message' =>   $mobile_number, 'result' => $fcm_id, 'requestname' => $method));
        }
    }


    public function getPlan($parameters, $method, $user_id)
    {
        extract($parameters);
        $check = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id));
        if (count($check) > 0) {
            $planInfo = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id));
            if (count($planInfo) > 0) {
                if ($planInfo->follow_up_date == "" || $planInfo->follow_up_date == "0000-00-00") {
                    $fup = "";
                } else {
                    $fup = $planInfo->follow_up_date;
                }
                $data['planInfo'][0]['general_instructions'] = $planInfo->general_instructions;
                $data['planInfo'][0]['plan'] = $planInfo->plan;
                $data['planInfo'][0]['followup_date'] = $fup;
                $this->response(array('code' => '200', 'message' => 'Success', 'result' => $data, 'requestname' => $method));
            } else {
                $data['planInfo'] = [];
                $this->response(array('code' => '201', 'message' => 'Error', 'result' => $data, 'requestname' => $method));
            }
        } else {
            $data['planInfo'] = [];
            $this->response(array('code' => '202', 'message' => 'Appointment ID Not Exists', 'result' => $data, 'requestname' => $method));
        }
    }

    // function for making doctor making online
    public function docOnline($parameters, $method, $user_id)
    {
        extract($parameters);
        if (isset($doctor_id)) {
            $check = $this->Generic_model->getSingleRecord('doctor_availability', array('doctor_id' => $doctor_id, 'date' => $date, 'feature' => $feature));
            if (count($check) > 0) {
                $udata['available'] = 0;
                $udata['status'] = 0;
                $this->Generic_model->updateData('doctor_availability', $udata, array('doctor_id' => $doctor_id, 'date' => $date, 'feature' => $feature));
            }
            $data['doctor_id'] = $doctor_id;
            $data['available'] = $available;
            $data['status'] = 1;
            $data['feature'] = $feature;
            $data['date'] = $date;
            $data['time'] = $time;
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $user_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('doctor_availability', $data);
            $param['docAvail']['time'] = $time;
            $param['docAvail']['online'] = 1;
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param));
        } else {
            $this->response(array('code' => '201', 'message' => 'Error Occured'));
        }
    }

    public function docOffline($parameters, $method, $user_id)
    {
        extract($parameters);
        if (isset($doctor_id)) {
            $check = $this->Generic_model->getSingleRecord('doctor_availability', array('doctor_id' => $doctor_id));
            if (count($check) > 0) {
                $udata['available'] = 0;
                $udata['status'] = 0;
                $this->Generic_model->updateData('doctor_availability', $udata, array('doctor_id' => $doctor_id, 'feature' => $feature));
            }
            $param['docAvail']['online'] = 0;
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param));
        } else {
            $this->response(array('code' => '201', 'message' => 'Error Occured'));
        }
    }

    public function ruraltelecallPushNotifications($parameters, $method, $user_id)
    {
        extract($parameters);
        // $user_id=$parameters['user_id'];
        $mobile_number = $m_number;
        // $this->Generic_model->pushNotifications($mobile_number,'','','','TelecallNotification');

        $checkMobileNumber = $this->db->select("*")
            ->from("rural_rmp_registration")
            ->where("rmp_phone ='" . $mobile_number . "'")
            ->order_by('rmp_id', 'DESC')
            ->get()
            ->row();

        // echo  $this->db->last_query();
        if (count($checkMobileNumber) > 0) {
            $fcm_id = $checkMobileNumber->fcm_id;
            $this->pushNotificationsrural($mobile_number, 'TelecallNotification');
            $this->response(array('code' => '200', 'message' =>   $mobile_number, 'result' => 'Success', 'requestname' => $method));
        } else {
            $fcm_id = "No Number Registered";
            $this->response(array('code' => '201', 'message' =>   $mobile_number, 'result' => $fcm_id, 'requestname' => $method));
        }
    }

    public function pushNotificationsrural($mobile_number, $notification_type)
    {
        $type = "";
        // echo "Notification Type : ".$notification_type;
        if ($notification_type == "TelecallNotification") {
            $checkMobileNumber = $this->db->select("*")
                ->from("rural_rmp_registration")
                ->where("rmp_phone ='" . $mobile_number . "'")
                ->get()
                ->row();
            $user_fcm_id[] = $checkMobileNumber->fcm_id;
            $type = "Call Notification";
            $msg = $checkMobileNumber->rmp_phone;
        }

        $registrationIds = $user_fcm_id;


        $message = array(
            'body' => $msg,
            'title' => ' $clinic->clinic_name',
            'appointment_id' => '$appointment_id',
            'patient_id' => '$patient_id',
            'sound' => 1,
            'type' => $type,
            //    'largeIcon' => 'large_icon',
            //    'smallIcon' => 'small_icon',
            'screen' => '$page',
            'image' => 'base_url("uploads/clinic_logos/" . $clinic->clinic_logo)'
        );


        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $message
        );

        $headers = array(
            //    'Authorization: key=' . API_ACCESS_KEY,
            'Authorization: key=AAAABCNnhe4:APA91bHga4VrGtmZbD3m10qZVzdhtxuE8sW35X6OI8sYnSxtB0pdRTAJtAj6blz8EdLBxlZlillz4gV3iYn39bICdIszkf6HhEEAV24SJoAUSAFW_lYFpKQ4pHprRHHd_FF5bOx-0z0s',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //    echo json_encode($fields);
        //    echo $result;
        //    echo json_encode($headers);
        curl_close($ch);
    }
    public function docAvalibilty($parameters, $method, $doctor_id)
    {
        extract($parameters);
        if (isset($doctor_id)) {
            $getdata = $this->db->query("select available from doctor_availability where doctor_id='" . $doctor_id . "' and feature='" . $feature . "'")->row();
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $getdata));
        } else {
            $this->response(array('code' => '201', 'message' => 'Error Occured'));
        }
    }
}
