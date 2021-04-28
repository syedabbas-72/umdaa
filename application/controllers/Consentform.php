<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Consentform extends MY_Controller {

	public function __construct() 
	{
	    parent::__construct();
	}

	public function index(){
		$condition="`archieve`='0'";
	 $data['Consentform_list']=$this->Generic_model->getAllRecords("consent_form", $condition , $order = '');  
	 $data['view'] = 'consentform/consentform_list';
	    	$this->load->view('layout', $data);
	}

    /**
    * Used to get the patient's all open appointment/specific appointment complete information based on the Patient id & Appointment id
    * @name getPatientAppointmentInfo
    * @access public
    * @author Uday Kanth Rapalli
    */
    public function getPatientAppointmentInfo($patient_id, $appointment_id = null) {

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        // $this->db->where_not_in('A.status',$status);
        
        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        return $this->db->get()->result();
    }


	public function Consentform_insert(){
		$param = $this->input->post();
		if(count($param) > 0){
			$user_id = $this->session->userdata('user_id');
			$param_1['department_id'] = $this->input->post("department_id");
			$param_1['consent_form_title'] = $this->input->post("consent_form_title");
			$param_1['brief'] = $this->input->post("brief");
			$param_1['alternative'] = $this->input->post("alternative");
			$param_1['benefits'] = $this->input->post("benefits");
			$param_1['complications'] = $this->input->post("complications");
			$param_1['anesthesia'] = $this->input->post("anesthesia");
			$param_1['status'] = $this->input->post("status");
			$param_1['created_by'] = $user_id;
			$param_1['modified_by'] = $user_id;
			$param_1['created_date_time'] = date("Y-m-d H:i:s");
			$param_1['modified_date_time'] = date("Y-m-d H:i:s");
			$ok =$this->Generic_model->insertData('consent_form',$param_1);
			if($ok == 1){
				redirect("Consentform");
			}else{
				redirect("Consentform");
			}
		}else{
			$data['departments_list'] = $this->Generic_model->getAllRecords("department", $condition = '', $order = '');
			$data['view'] = 'consentform/consentform_insert';
		    $this->load->view('layout', $data);
		}
		
	}

	public function Consentform_edit($id=''){
		$param = $this->input->post();
		if(count($param) >0 ){
			$user_id = $this->session->userdata('user_id');
			$param_1['consent_form_title'] = $this->input->post("consent_form_title");
			$param_1['brief'] = $this->input->post("brief");
			$param_1['alternative'] = $this->input->post("alternative");
			$param_1['benefits'] = $this->input->post("benefits");
			$param_1['complications'] = $this->input->post("complications");
			$param_1['anesthesia'] = $this->input->post("anesthesia");
			$param_1['modified_by'] = $user_id;
			$param_1['modified_date_time'] = date("Y-m-d H:i:s");
			$ok =$this->Generic_model->updateData('consent_form', $param_1, array('consent_form_id'=>$id));
			if($ok == 1){
				redirect("Consentform");
			}else{
				redirect("Consentform");
			}
		}else{
			$data['Consentform_val'] = $this->db->select("*")->from("consent_form a")->where("consent_form_id ='".$id."'")->get()->row();
			$data['cf_id'] = $id;
			$data['departments_list'] = $this->Generic_model->getAllRecords("department", $condition = '', $order = '');
			$data['view'] = 'consentform/consentform_edit';
		    $this->load->view('layout', $data);
		}
		
	}

	public function Consentform_delete($id){
		$user_id = $this->session->userdata('user_id');
		$param_1['archieve'] = "1";
		$param_1['modified_by'] = $user_id;
		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
		$ok =$this->Generic_model->updateData('consent_form', $param_1, array('consent_form_id'=>$id));
		if($ok == 1){
			redirect("Consentform");
		}else{
			redirect("Consentform");
		}
	}

	public function Consentform_view($id){
        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->where("a.archieve != 1 and consent_form_id ='".$id."'")->get()->row();
        $data['consent_form_id']=$id;
        $data['view'] = 'consentform/consentform_view';
        if($this->input->post('getpdf')){
            $this->load->library('M_pdf');
            $html=$this->load->view('consentform/consentform_pdf',$data,true);
            $pdfFilePath = strtolower(str_replace(" ","_",$data['Consentform_val']->consent_form_title)).".pdf";
            $this->load->library('M_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output("./uploads/consentforms/".$pdfFilePath, "F");
            $this->m_pdf->pdf->Output("./uploads/consentforms/".$pdfFilePath, "D");     
        }
		
	    $this->load->view('layout', $data);
	}
	

	public function download_consent()
	{
        $id=explode(",",$_POST['consent']);
        $data['appointment']=$this->db->select("a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id,p.patient_id,p.title,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.umr_no,p.attendant,p.age,p.age_unit,p.gender as p_gender,c.clinic_id,c.clinic_name")->from("appointments a")->join("doctors d","a.doctor_id = d.doctor_id","left")->join("department de","d.department_id=de.department_id","left")->join("patients p","a.patient_id=p.patient_id","left")->join("clinics c","a.clinic_id=c.clinic_id","left")->where("a.appointment_id='".$id[1]."'")->order_by("a.appointment_id desc")->get()->row();

        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->join("department b","a.department_id = b.department_id")->where("a.archieve != 1 and consent_form_id ='".$id[0]."'")->get()->row();
        $data['consent_form_id']=$id[0];
        $this->load->library('M_pdf');
        $html=$this->load->view('consentform/consentform_patient_pdf',$data,true);
        $pdfFilePath = strtolower(str_replace(" ","_",$data['Consentform_val']->consent_form_title.$data['appointment']->appointment_id)).".pdf";
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/".$pdfFilePath, "F");	
	}


    public function reports($patient_id = '', $appointment_id = NULL){
        $data['appointmentInfo'] = $this->getPatientAppointmentInfo($patient_id, $appointment_id);
        $data['patient_consent_form'] = $this->db->select("pc.*,cf.consent_form_id as cf_id,cf.consent_form_title,d.doctor_id,d.salutation,d.first_name,d.last_name")->from("patient_consent_forms pc")->join("consent_form cf","pc.consent_form_id=cf.consent_form_id","left")->join("doctors d","pc.doctor_id=d.doctor_id","left")->where("pc.patient_id = '" . $patient_id . "'")->get()->result();
        $data['patient_id']=$patient_id;
        if($appointment_id != null){
            $data['appointment_id'] = $appointment_id;    
        }        
        $data['view'] = 'profile/consent_list';
        $this->load->view('layout', $data);
    }


    public function save(){
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/consentforms/';
            $config['upload_path'] = './uploads/consentforms/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload');
            $this->upload->initialize($config);
            $this->upload->do_upload('userfile'); 
            $fileData=$this->upload->data('file_name');
            $inputFileName = $path . $fileData;

            if(file_exists($inputFileName)){
                $inputFileName = $path . $fileData;
            }
            else{
                move_uploaded_file($fileData,$path);
                $inputFileName = $path . $fileData;
            }

            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                    . '": ' . $e->getMessage());
            }
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            $arrayCount = count($allDataInSheet);

            $flag = 0;
            $createArray = array('consent','category','checklist','type');
            $makeArray = array('consent' =>'consent','category' =>'category','type' =>'type','checklist' =>'checklist');
            $SheetDataKey = array();
            foreach ($allDataInSheet as $dataInSheet) {
                foreach ($dataInSheet as $key => $value) {
                    if (in_array(trim($value), $createArray)) {
                        $value = preg_replace('/\s+/', '', $value);
                        $SheetDataKey[trim($value)] = $key;

                    } 
                }
            }

            $data = array_diff_key($makeArray, $SheetDataKey);

            if (isset($data)) {
                $flag = 1;
            }
            if ($flag == 1) {
                for ($i = 2; $i <= $arrayCount; $i++) {
                    $cfname = $SheetDataKey['consent'];
                    $category = $SheetDataKey['category'];
                    $type = $SheetDataKey['type'];
                    $checklist = $SheetDataKey['checklist'];

                    $cf_name = filter_var(trim($allDataInSheet[$i][$cfname]), FILTER_SANITIZE_STRING);
                    $c_category = filter_var(trim($allDataInSheet[$i][$category]), FILTER_SANITIZE_STRING);
                    $c_type = filter_var(trim($allDataInSheet[$i][$type]), FILTER_SANITIZE_STRING);
                    $c_checklist = filter_var(trim($allDataInSheet[$i][$checklist]), FILTER_SANITIZE_STRING);


                    $insert_checklist['description'] = $c_checklist;
                    $insert_checklist['status'] = $c_checklist;
                    $insert_checklist['created_by'] = $this->session->userdata('user_id');
                    $insert_checklist['modified_by'] = $this->session->userdata('user_id');
                    $insert_checklist['created_date_time'] = date('Y-m-d H:i:s');
                    $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');

                    $checklist_id = $this->Generic_model->insertDataReturnId('checklist_master',$insert_checklist);

                    $get_consent = $this->db->select("*")->from("consent_form")->where("consent_form_title='".$cf_name."'")->get()->row();


                    $consent_form_id = $get_consent->consent_form_id;


                    $fetchData = array('checklist_id' => $checklist_id, 'patient_consent_form_id' => $consent_form_id,  'category' => $c_category,'type'=>$c_type, 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));



                    $this->Generic_model->insertDataReturnId('checklist_consent_form',$fetchData);
                }
            }  

        } else {
            echo "Please import correct file";
        }

        redirect('Consentform');
    }


     public function bulk_save(){
        $this->load->library('excel');

        echo '<pre>';
        print_r($this->input->post());
        echo '</pre>';

        if ($this->input->post('importfile')) {
            // echo "1, ";
            $path = './uploads/consent_form_bulk/';
            // $config['upload_path'] = './uploads/cd_bulk/';
            // $config['allowed_types'] = 'xlsx|xls|jpg|png';
            // $config['remove_spaces'] = TRUE;
            // echo "2, ";
            // $this->load->library('upload');
            // echo "3, ";
            // $this->upload->initialize($config);
            // echo "90, ";
            // $this->upload->do_upload('userfile'); //uploading file to server
            // echo "100, ";
            // $fileData=$this->upload->data('file_name');
            // echo 'File Date: '.$fileData;
            // //$inputFileName = $path . $fileData;
            // $inputFileName = $path . 'clinical_diagnosis.xlsx';
            // echo 'Input File Name: '.$inputFileName.'<br>';
            // echo "110, ";
            // if(file_exists($inputFileName)){
            //     $inputFileName = $path . $fileData;
            //     echo "4, ";
            // }
            // else{
            //     echo "5, ";
            //     move_uploaded_file($fileData,$path);
            //     $inputFileName = $path . $fileData;
            // }

            $inputFileName = $path . 'Forms.xlsx';
            $inputFileType = 'xlsx';
            try {
                echo '100, ';
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                    . '": ' . $e->getMessage());
            }
            echo "6, ";
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            $arrayCount = count($allDataInSheet);
            echo "7, ";
            $flag = 0;
            $createArray = array('department','consent_form_title','brief','benefits','complications','anesthesia','alternative');
            $makeArray = array('department' =>'department','consent_form_title' =>'consent_form_title','brief' =>'brief','benefits' =>'benefits','complications' =>'complications','anesthesia' =>'anesthesia','alternative' =>'alternative');
            echo "8, ";
            $SheetDataKey = array();
            echo "9, ";
            foreach ($allDataInSheet as $dataInSheet) {
                foreach ($dataInSheet as $key => $value) {
                    if (in_array(trim($value), $createArray)) {
                        $value = preg_replace('/\s+/', '', $value);
                        $SheetDataKey[trim($value)] = $key;
                    } 
                }
            }

            $data = array_diff_key($makeArray, $SheetDataKey);

            if (isset($data)) {
                $flag = 1;
            }

            if ($flag == 1) {
                for ($i = 2; $i <= $arrayCount; $i++) {

                    $department = $SheetDataKey['department'];
                    $consent_form_title = $SheetDataKey['consent_form_title'];
                    $brief = $SheetDataKey['brief'];
                    $benefits = $SheetDataKey['benefits'];
                    $complications = $SheetDataKey['complications'];
                    $anesthesia = $SheetDataKey['anesthesia'];
                    $alternative = $SheetDataKey['alternative'];

                    $form_department= filter_var(trim($allDataInSheet[$i][$department]), FILTER_SANITIZE_STRING);
                    $form_consent_form_title = filter_var(trim($allDataInSheet[$i][$consent_form_title]), FILTER_SANITIZE_STRING);
                    $form_brief = filter_var(trim($allDataInSheet[$i][$brief]), FILTER_SANITIZE_STRING);
                    $form_benefits = $allDataInSheet[$i][$benefits];
                    $form_complications = $allDataInSheet[$i][$complications];
                    $form_anesthesia = filter_var(trim($allDataInSheet[$i][$anesthesia]), FILTER_SANITIZE_STRING);
                    $form_alternative = filter_var(trim($allDataInSheet[$i][$alternative]), FILTER_SANITIZE_STRING);

                 	$depInfo = $this->db->select('department_id')->from('department')->where('department_name =',$form_department)->get()->row();

                    $form_department_id = $depInfo->department_id;

                    $fetchData = array('department_id' => $form_department_id, 'consent_form_title' => $form_consent_form_title, 'brief' => $form_brief,'benefits' => $form_benefits,'complications' => $form_complications,'anesthesia' => $form_anesthesia, 'alternative'=> $form_alternative,'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));

                    $last_id = $this->Generic_model->insertDataReturnId('consent_form',$fetchData);

                }

            }  
        } else {
            echo "Please import correct file";
        }
        redirect('Consentform');
    }
}
?>