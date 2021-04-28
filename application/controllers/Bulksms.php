<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulksms extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->library('phpqrcode/qrlib');
		$is_logged_in = $this->session->has_userdata('is_logged_in');
		if($is_logged_in == 0)
		{
            redirect('Authentication');
        }
    }

	public function index()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$sess_role_id = $this->session->userdata('role_id');
		$user_id = $this->session->userdata('user_id');

		$roles = $this->db->select("role_id")->from("roles")->where("role_name='Doctor'")->get()->row();


		//If Doctor
		if($roles->role_id==$sess_role_id)
		{
			$data['templates'] = $this->db->select("template_id,message")->from("sms_templates")->where("doctor_id='".$user_id."'")->get()->result();
			$data['patientsData'] = $this->db->query("select p.first_name,p.mobile from patients p,appointments a where a.patient_id=p.patient_id and a.doctor_id='".$user_id."' and a.clinic_id='".$clinic_id."' group by a.patient_id ")->result();
		}
		else
		{
			$data['templates'] = $this->db->select("template_id,message")->from("sms_templates")->where("clinic_id='".$clinic_id."'")->get()->result();
			$data['patientsData'] = $this->db->query("select p.first_name,p.mobile from patients p,appointments a where a.patient_id=p.patient_id and  a.clinic_id='".$clinic_id."' group by p.patient_id ")->result();
			$data['doctors'] = $this->db->query("select d.first_name,d.last_name,d.doctor_id from doctors d,clinic_doctor cd where cd.doctor_id=d.doctor_id and cd.clinic_id='".$clinic_id."'")->result();
		}
		
		$data['view'] = 'bulk_sms/sendSMS';

	    $this->load->view('layout', $data);
	}	

	//Save Template
	public function addTemplate()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$sess_role_id = $this->session->userdata('role_id');
		$user_id = $this->session->userdata('user_id');

		$roles = $this->db->select("role_id")->from("roles")->where("role_name='Doctor'")->get()->row();

		//if doctor
		if($roles->role_id==$sess_role_id)
		{
			$data['message'] = $this->input->post("message");
			$data['clinic_id'] = $clinic_id;
			$data['doctor_id'] = $user_id;
 			$this->Generic_model->insertData('sms_templates',$data);
		}
		else
		{
			$data['message'] = $this->input->post("message");
			$data['clinic_id'] = $clinic_id;
 			$this->Generic_model->insertData('sms_templates',$data);
		}
		redirect("Bulksms?success");
	}

	//Save Template
	public function editTemplate()
	{

		$data['message'] = $this->input->post("edit_message");
		$this->Generic_model->updateData('sms_templates', $data, array('template_id' => $this->input->post("edit_template_id")));
		
		redirect("Bulksms?success");
	}

	//Delete Template
	public function deleteTemplate($id)
	{

		$template_id = $id;
		$this->Generic_model->deleteRecord('sms_templates',array('template_id'=>$id));
		
		redirect("Bulksms?success");
	}

	//sendsms
	public function sendbulkSMS()
	{
		$clinic_id = $this->session->userdata("clinic_id");
		$mobilenumbers = $_POST['mobile'];
		$mobileCount = count(explode(",", $mobilenumbers));
		$message = $_POST['message'];
		$credits = $_POST['credits']*$mobileCount;
		$doctor_id = $_POST['doctor'];

		$bks_key = $_POST['bks_key'];
		// print_r($_POST);exit;
		// if(isset($_POST['bks_key']))
		// {

		// }
		$numberData['user_id'] = $doctor_id;
		$this->Generic_model->updateData('bulksms_numbers',$numberData,array('bks_key'=>$bks_key));

		$ch = sendBulkSMS($mobilenumbers, $message);
		$year = date("Y");	
		$month  = date("F"); 
		
		$bulkSMSNumbers = $this->db->select("*")->from("bulksms_numbers")->where("bks_key='".$_POST['bks_key']."'")->get()->result();
		if(count($bulkSMSNumbers)>0)
		{
			foreach ($bulkSMSNumbers as $value) {
				$patientInfo = $this->db->select("*")->from("patients")->where("mobile='".DataCrypt($value->mobile,'encrypt')."'")->get()->row();
				if(count($patientInfo)>0)
				{
					$CDP['clinic_id'] = $value->clinic_id;
					$CDP['doctor_id'] = $value->user_id;
					$CDP['patient_id'] = $patientInfo->patient_id;
					$CDP['status'] = 1;
					$this->Generic_model->insertData("clinic_doctor_patient",$CDP);

					$DP['doctor_id'] = $value->user_id;
					$DP['patient_id'] = $patientInfo->patient_id;
					$DP['status'] = 1;
					$this->Generic_model->insertData("doctor_patient",$DP);
				}
				else
				{
					$mob = explode(",", $mobilenumbers);
					foreach ($mob as $value) {
							$last_umr_no = $this->db->select("*")->from("patients")->order_by("patient_id","desc")->get()->row();

				            if($last_umr_no->umr_no == NULL || $last_umr_no->umr_no == ""){
				                $umr = "P".date("my")."1";
				            }else{
				                $trim_umr = trim($last_umr_no->umr_no);
				                $check_umr = substr($trim_umr,1,4);
				                if($check_umr == date("my")){
				                    $umr = (++$trim_umr);
				                }else{
				                    $umr = "P".date("my")."1";
				                }   
				            }
							
							$tempDir = './uploads/qrcodes/patients/';
							$codeContents = $umr;
							$qrname = $umr.md5($codeContents).'.png';
							$pngAbsoluteFilePath = $tempDir . $qrname;
							$urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;
	
							if (!file_exists($pngAbsoluteFilePath)) {
								QRcode::png($codeContents, $pngAbsoluteFilePath);
							}
							
				            $patient_rec['umr_no'] = $umr;
				            $patient_rec['qrcode'] = $qrname;
				            $patient_rec['mobile'] = DataCrypt($value,'encrypt');
				            $patient_rec['username'] = $umr;
				            $patient_rec['password'] = md5($umr);
				            $patient_rec['clinic_id'] = 0;
				            $patient_rec['payment_status'] = 0;
				            $patient_rec['status'] = 1;
				            $patient_rec['created_date_time'] = date('Y-m-d H:i:s');
				            $patient_rec['modified_date_time'] = date('Y-m-d H:i:s');

				            $patient_id = $this->Generic_model->insertDataReturnId('patients',$patient_rec);

				            $patientUpdate['created_by'] = $patient_id;
				            $patientUpdate['modified_by'] = $patient_id;

				            $res = $this->Generic_model->updateData('patients',$patientUpdate,array('patient_id' => $patient_id));


					    	$CDP['clinic_id'] = $clinic_id;
							$CDP['doctor_id'] = $doctor_id;
							$CDP['patient_id'] = $patient_id;
							$CDP['status'] = 1;
							$this->Generic_model->insertData("clinic_doctor_patient",$CDP);

							$DP['doctor_id'] = $doctor_id;
							$DP['patient_id'] = $patient_id;
							$DP['status'] = 1;
							$this->Generic_model->insertData("doctor_patient",$DP);
						}
					}
			}	
		}
		else
		{


			$key = DataCrypt($clinic_id.time(),"encrypt");
			$mob = explode(",", $mobilenumbers);
			foreach ($mob as $value) {
				$patientInfo = $this->db->select("*")->from("patients")->where("mobile='".DataCrypt($value,'encrypt')."'")->get()->row();
				// echo $this->db->last_query();exit;
				if(count($patientInfo)>0)
				{
					$CDP['clinic_id'] = $clinic_id;
					$CDP['doctor_id'] = $doctor_id;
					$CDP['patient_id'] = $patientInfo->patient_id;
					$CDP['status'] = 1;
					$this->Generic_model->insertData("clinic_doctor_patient",$CDP);

					$DP['doctor_id'] = $doctor_id;
					$DP['patient_id'] = $patientInfo->patient_id;
					$DP['status'] = 1;
					$this->Generic_model->insertData("doctor_patient",$DP);
				}
				else
				{
					
							$last_umr_no = $this->db->select("*")->from("patients")->order_by("patient_id","desc")->get()->row();

				            if($last_umr_no->umr_no == NULL || $last_umr_no->umr_no == ""){
				                $umr = "P".date("my")."1";
				            }else{
				                $trim_umr = trim($last_umr_no->umr_no);
				                $check_umr = substr($trim_umr,1,4);
				                if($check_umr == date("my")){
				                    $umr = (++$trim_umr);
				                }else{
				                    $umr = "P".date("my")."1";
				                }   
							}
							
							$tempDir = './uploads/qrcodes/patients/';
							$codeContents = $umr;
							$qrname = $umr.md5($codeContents).'.png';
							$pngAbsoluteFilePath = $tempDir . $qrname;
							$urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;
	
							if (!file_exists($pngAbsoluteFilePath)) {
								QRcode::png($codeContents, $pngAbsoluteFilePath);
							}
							
				            $patient_rec['umr_no'] = $umr;
				            $patient_rec['qrcode'] = $qrname;
				            $patient_rec['mobile'] = DataCrypt($value,'encrypt');
				            $patient_rec['username'] = $umr;
				            $patient_rec['password'] = md5($umr);
				            $patient_rec['clinic_id'] = 0;
				            $patient_rec['payment_status'] = 0;
				            $patient_rec['status'] = 1;
				            $patient_rec['created_date_time'] = date('Y-m-d H:i:s');
				            $patient_rec['modified_date_time'] = date('Y-m-d H:i:s');

				            $patient_id = $this->Generic_model->insertDataReturnId('patients',$patient_rec);

				            $patientUpdate['created_by'] = $patient_id;
				            $patientUpdate['modified_by'] = $patient_id;

				            $res = $this->Generic_model->updateData('patients',$patientUpdate,array('patient_id' => $patient_id));

// echo "ikkada";
					    	$CDP['clinic_id'] = $clinic_id;
							$CDP['doctor_id'] = $doctor_id;
							$CDP['patient_id'] = $patient_id;
							$CDP['status'] = 1;
							$this->Generic_model->insertData("clinic_doctor_patient",$CDP);

							$DP['doctor_id'] = $doctor_id;
							$DP['patient_id'] = $patient_id;
							$DP['status'] = 1;
							$this->Generic_model->insertData("doctor_patient",$DP);

							$BKS['mobile'] = $value;
					    	$BKS['clinic_id'] = $clinic_id;
					    	$BKS['user_id'] = $doctor_id;
					    	$BKS['bks_key'] = $key;	
					    	$this->Generic_model->insertData("bulksms_numbers",$BKS);	

					}
		    	
			}
	    			
		}
		

		// Commented This Code for All Doctors. If required all doctors uncomment This
		// if($doctor_id=="all")
		// {
		// 	$doctors = $this->db->query("select d.doctor_id from doctors d,clinic_doctor cd where cd.doctor_id=d.doctor_id and cd.clinic_id='".$clinic_id."'")->result();
		// 	foreach($doctors as $value)
		// 	{
		// 		$sms_counter = $this->db->query("select count(*) as count,".$month.",sms_counter_id from sms_counter where `doctor_id`='".$value->doctor_id."' and `year`='".$year."'")->row();
		// 		if($sms_counter->count>0)
		// 		{
		// 			$counter = $sms_counter->$month+$credits; 
		// 			$data[$month] = $counter;
		// 			$this->Generic_model->updateData('sms_counter', $data, array('sms_counter_id' => $sms_counter->sms_counter_id));
		// 		}
		// 		else
		// 		{
		// 			$data[$month] = $credits;
		// 			$data['year'] = $year;
		// 			$data['doctor_id'] = $value->doctor_id;
		// 			$this->Generic_model->insertData('sms_counter',$data);				
		// 		}
		// 	}
		// }
		// else
		// {
		$sms_counter = $this->db->query("select count(*) as count,".$month.",sms_counter_id from sms_counter where `doctor_id`='".$doctor_id."' and `year`='".$year."'")->row();
		if($sms_counter->count>0)
		{
			$counter = $sms_counter->$month+$credits; 
			$data[$month] = $counter;
			$this->Generic_model->updateData('sms_counter', $data, array('sms_counter_id' => $sms_counter->sms_counter_id));
		}
		else
		{
			$data[$month] = $credits;
			$data['year'] = $year;
			$data['doctor_id'] = $doctor_id;
			$this->Generic_model->insertData('sms_counter',$data);				
		}
		// }
		

		
		redirect("Bulksms?success");
	}


//Upload Excel File
public function save()
{
	$this->load->library('excel');
	$clinic_id = $this->session->userdata("clinic_id");

	// if($this->input->post('importfile')) 
	// {
		$path = './uploads/bulksms/';
		$config['upload_path'] = './uploads/bulksms/';
		$config['allowed_types'] = 'xlsx|xls';
		$config['remove_spaces'] = TRUE;

		//echo $_FILES['userfile']['name']=$_FILES['userfile']['name'];exit;
		    	
		$this->load->library('upload');
		$this->upload->initialize($config);
		$this->upload->do_upload('userfile'); //uploading file to server
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
		    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME). '": ' . $e->getMessage());
		}
		$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

		$arrayCount = count($allDataInSheet);

		$flag = 0;
		$createArray = array('Name','Mobile');
		$makeArray = array('Name' =>'Name','Mobile' =>'Mobile');
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
		if ($flag == 1) 
		{
			$key = DataCrypt($clinic_id.time(),"encrypt");
		    for ($i = 2; $i <= $arrayCount; $i++) 
		    {
		        // $addresses = array();
		        $Name = $SheetDataKey['Name'];
		        $Mobile = $SheetDataKey['Mobile'];

		        $d_name = filter_var(trim($allDataInSheet[$i][$Name]), FILTER_SANITIZE_STRING);
		        $d_mobile = filter_var(trim($allDataInSheet[$i][$Mobile]), FILTER_SANITIZE_STRING);
		   			
		    	$data['name'] = $d_name;
		    	$data['mobile'] = $d_mobile;
		    	$data['clinic_id'] = $clinic_id;
		    	$data['bks_key'] = $key;	
		    	$this->Generic_model->insertData("bulksms_numbers",$data);
		    }
	    	redirect("Bulksms?q=".$key);   
		}  
	     
	// } 
	else 
	{
	    redirect("Bulksms?failed");   
	}
       
}

}