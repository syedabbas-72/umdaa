<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Clinic extends MY_Controller {
	public function __construct(){      
		parent::__construct();
		if(!$this->session->has_userdata('is_logged_in'))
		{
			redirect('Authentication/login');
		}       
	}

	public function index(){
		$clinic_id = $this->session->userdata('clinic_id');
		$cond = '';$condition='';
		if($clinic_id!=0){
			$cond = "clinic_id=".$clinic_id." and archieve=0";		
		}else{

			$cond = "archieve=0";
		}

		$data['clinic_list']=$this->Generic_model->getAllRecords('clinics',$cond,$order='');
		$data['view'] = 'clinics/clinic_list';
		$this->load->view('layout', $data);
	}

    public function addClinicPackages(){
        $docInfo = $this->db->query("select * from doctors")->result();
        foreach($docInfo as $value){
            $docClinicInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$value->doctor_id."' order by clinic_doctor_id ASC")->row();

        }
    }


    public function addAdmin(){
        if(isset($_POST)){
            extract($_POST);
            $clinicInfo = $this->Generic_model->getSingleRecord('clinics', array('clinic_id'=>$clinic_id));
            
            $adminPwd = strtoupper(substr(str_replace(" ","",$clinicInfo->clinic_name), 0, 5))."123";
            
            /* creating admin*/
            // $adminData['username'] = "";
            $adminData['password'] = md5($adminPwd);
            $adminData['clinic_id'] = $clinic_id;
            $adminData['user_type'] = 'employee';
            // Get role id for Clinic Head
            $admroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =', 'Clinic Head')->get()->row();
            $adminData['role_id'] = $admroleInfo->role_id;
            // Get profile id for Clinic Head
            $admprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =', 'Clinic Head')->get()->row();
            $adminData['profile_id'] = $admprofileInfo->profile_id;
            $adminData['status'] = 1;
            $adminData['created_date_time'] = date('Y-m-d H:i:s');
            $adminData['modified_date_time'] = date('Y-m-d H:i:s');
            $admin_id = $this->Generic_model->insertDataReturnId('users', $adminData);

            $empcode = strtoupper(substr($clinicInfo->clinic_name,0,3)).'-' . date('mY') . $admin_id;
            $adminUname = $empcode;

            $emp1['employee_id'] = $admin_id;
            $emp1['employee_code'] = $empcode;
            $emp1['first_name'] = $adminUname;
            $emp1['clinic_id'] = $clinic_id;
            $emp1['status'] = 1;
            $emp1['created_date_time'] = date('Y-m-d H:i:s');
            $emp1['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('employees', $emp1);
            $udata['username'] = $empcode;
            $this->Generic_model->updateData('users', $udata, array('user_id' => $admin_id));
            echo "1";
        }
    }


	public function clinic_add(){

		$user_id = $this->session->userdata('user_id');

		if($this->input->post('submit')){
			$config['upload_path']="./uploads/clinic_logos/";
			$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
			$this->load->library('upload');    
			$this->upload->initialize($config);
			$this->upload->do_upload('clinic_logo');
			$fileData=$this->upload->data('file_name');

			$config['upload_path']="./uploads/clinic_qrcode/";
			$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
			$this->load->library('upload');    
			$this->upload->initialize($config);
			$this->upload->do_upload('clinic_qrcode');
			$fileData1=$this->upload->data('file_name');

			$config1['upload_path']="./uploads/clinic_logos/";
			$config1['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
			$this->load->library('upload');    
			$this->upload->initialize($config1);
			$this->upload->do_upload('clinic_emblem');
			$fileDataemblem=$this->upload->data('file_name');

			$alias_count=$this->Generic_model->getNumberOfRecords('clinics',array("clinic_alias"=>$this->input->post('clinic_alias')));

			if($alias_count<=0){
				$data['clinic_name']=$this->input->post('clinic_name');
				$data['clinic_type']='CUSTOMER';
				$data['clinic_phone']=$this->input->post('clinic_phone');
				$data['clinic_logo']=$fileData;
				$data['clinic_emblem']=$fileDataemblem;

				$data['email'] = $this->input->post('clinic_email');
				$data['clinic_alias']=$this->input->post('clinic_alias');
				$data['address']=$this->input->post('address');

		 		$data['location']=$this->input->post('location');
		 		$data['district_id']=$this->input->post('district_id');
		 		$data['state_id']=$this->input->post('state');
		 		$data['pincode']=$this->input->post('pincode');
		 		$data['lab']=$this->input->post('lab');
		 		$data['lift']=$this->input->post('lift');
		 		$data['parking']=$this->input->post('parking');
		 		$data['pharmacy']=$this->input->post('pharmacy');
		 		$data['incharge_name']=$this->input->post('clinic_incharge');
		 		$data['incharge_mobile']=$this->input->post('incharge_mobile');
		 		$data['incharge_email']=$this->input->post('incharge_email');

				$data['created_date_time']=date('Y-m-d H:i:s');
				$data['modified_date_time']=date('Y-m-d H:i:s');

				$clinic_id = $this->Generic_model->insertDataReturnId('clinics',$data);
				
				$pwd = $this->Generic_model->getrandomstring(8);

				/* creating user*/        
				$email = $this->input->post('incharge_email');

				$user['username'] = $this->input->post('incharge_email');
				$user['password'] = md5($pwd);
				$user['clinic_id'] = $clinic_id;
				$user['email_id'] = $this->input->post('incharge_email');
				$user['mobile'] = $this->input->post('incharge_mobile');
				$user['user_type'] = 'employee';

            	// Get role id for admin
				$roleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Admin')->get()->row();
				$user['role_id'] = $roleInfo->role_id;

            	// Get profile id for admin
				$profileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Admin')->get()->row();
				$user['profile_id'] = $profileInfo->profile_id;

				$user['status'] = 1;
				$user['created_by'] = $user_id;
				$user['modified_by'] = $user_id;
				$user['created_date_time'] = date('Y-m-d H:i:s');
				$user['modified_date_time'] = date('Y-m-d H:i:s');

				$user_id = $this->Generic_model->insertDataReturnId("users", $user);		

				/* end creating user */		
				// Creating Employee 
				$emp['employee_id'] = $user_id;
				$emp['clinic_id'] = $clinic_id;

				// Generate employee code
				// Get last employee code with clinic id
				// Get last generated UMR No.                
				$empRes = $this->db->select("employee_code")->from("employees")->where('clinic_id =',$clinic_id)->order_by("employee_id DESC")->get()->row();

		    	// Generate UMR No.
				if(count($empRes) > 0){
					$empCode_str   = trim($empRes->employee_code);
					$lastCode = substr($empCode_str, 1, 3);
					$currentCode = $lastCode++;
					$currentCode = sprintf("%04d", $currentCode);
					$employee_code = "emp".$currentCode;
				}else{
		        // No records found. Generate New UMR#
					$employee_code = "emp0001";
				}

				$emp['employee_code'] = $employee_code;
				$emp['first_name'] = $this->input->post('clinic_incharge');
				$emp['mobile'] = $this->input->post('incharge_mobile');
				$emp['email_id'] = $this->input->post('incharge_email');

				$this->Generic_model->insertData("employees", $emp);

				// Shoot an email
				$from = 'UMDAA Heath Care Pvt Ltd';
				$to = $this->input->post('incharge_email');
				$subject = ucwords($this->input->post('clinic_name'))." - Admin Credentials";
				$message = "<h4>Congratulations!<br><br>Your account has been successfully created.<h4><br><br>You can log in to your portal using below password. For username you can use your mobile no. or your email. <br><br>Below are your credentials.<br><br><b>Password:<b> ".$pwd;

				$ok = $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $message);   

				$this->session->set_flashdata('msg','Record added successfully');
				redirect('clinic');
			}else{
				$this->session->set_flashdata('msg','Alias shoud be unique');
				redirect('clinic');
			}
		}else{
			$data['state_list']=$this->Generic_model->getAllRecords('states', $condition='', $order='');
			$data['view'] = 'clinics/clinic_add';
			$this->load->view('layout', $data);
		}
	}

	public function testemail(){
		$to = "uday@beaut.in";
		$subject = "Test email";
		$message = "Hello there";
		$this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $body);	
	}


 	/**
    * used to get the district details  based on the state id 
    * @name getDistricts
    * @access public
    * @author Rajesh
    */
	public function getDistricts() {
	 	$id = $_POST['id'];

	 	$districts = $this->db->select('district_id,district_name')->from('districts')->where('state_id',$id)->get()->result();
	 	$res = '<option value="">Select District</option>';
	 	foreach ($districts as $key => $value) {
	 		$res .= '<option value="' . $value->district_id . '">' . $value->district_name . '</option>';
	 	}

	 	echo $res;
	}

 
	public function clinic_update($id){

	 	if($this->input->post('submit')){

	 		unset($_POST['submit']);

	 		$config['upload_path']="./uploads/clinic_logos/";
	 		$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
	 		$this->load->library('upload');    
	 		$this->upload->initialize($config);
	 		$this->upload->do_upload('clinic_logo');
	 		if ($this->upload->do_upload('clinic_logo')) {
	 			$logo_data = $this->upload->data('file_name');
	 		}
	 		if ($this->upload->do_upload('clinic_emblem')) {
	 			$emblem_data = $this->upload->data('file_name');
	 		}

	 		if($logo_data==""){
	 			$clinic_img=$this->db->query('select clinic_logo from clinics where clinic_id='.$id)->row();
	 			$logo_img=$clinic_img->clinic_logo;
	 		}else{
	 			$logo_img = $logo_data;
	 		}

	 		$data = $_POST;

	 		$facilities = implode(",",$this->input->post("facilities"));

	 		$data['clinic_logo']=$logo_img;
	 		$data['modified_date_time']=date('Y-m-d H:i:s');

	 		$result = $this->Generic_model->updateData('clinics', $data, array('clinic_id'=>$id));

	 		if($result==1){
	 			$this->session->set_flashdata('msg','Record Updated successfully');
	 			redirect('settings');
	 		}else{

	 			$this->session->set_flashdata('msg','Failed to Insert');
	 			redirect('settings');
	 		}
	 	}else{
	 		$data['state_list']=$this->Generic_model->getAllRecords('states', $condition='', $order='');
	 		$data['clinic_list']=$this->db->query('select * from clinics where clinic_id='.$id)->row();
	 		$data['view'] = 'clinics/clinic_edit';
	 		$this->load->view('layout', $data);
	 	}
	}


	public function clinic_view($id){
	 	$data['clinic_list']=$this->db->query('select * from clinics where clinic_id='.$id)->row();
	 	$state = $data['clinic_list']->state_id;
	 	$clinic_type = $data['clinic_list']->clinic_type; 
	 	$data['state_list']=$this->Generic_model->getSingleRecord('states', array('state_id'=>$state), $order='');
	 	$data['clinic_type']=$this->Generic_model->getSingleRecord('clinic_type', array('clinic_type_id'=>$clinic_type), $order='');

	 	$data['clinic_doctor'] = $this->db->query('select * from clinic_doctor c inner join doctors d on(c.doctor_id = d.doctor_id)  inner join department dep on(d.department_id = dep.department_id) where c.clinic_id ='.$id)->result();
	 	$data['staff'] = $this->db->query('select * from employees e inner join users u on(e.employee_id = u.user_id) inner join roles r on(r.role_id = u.role_id) where e.archieve=0 and e.clinic_id ='.$id)->result();

	 	$data['view'] = 'clinics/clinic_view';
	 	$this->load->view('layout', $data);
	}


	public function clinic_delete($id){
		$result = $this->Generic_model->deleteRecord('clinics', array('clinic_id'=>$id));
		$this->Generic_model->deleteRecord('clinic_doctor', array('clinic_id'=>$id));
		$this->Generic_model->deleteRecord('clinic_doctor_patient', array('clinic_id'=>$id));
		if($result==1){
			$this->session->set_flashdata('msg', "Record deleted successfully");
			redirect('clinic');
		}else{
			$this->db->last_query();
			echo "failed to delete";
		}
	}

	 //Get Doc Details
	 public function getClinicDetails(){
        extract($_POST);
        // $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$id."'")->row();
        $clinicsInfo = $this->db->query("select * from clinic_doctor cd, clinics c where c.clinic_id=cd.clinic_id and c.clinic_id='".$id."'")->result();
        if(count($clinicsInfo)>0)
        {
            ?>
            <div class="row">
                <div class="col-md-12">
                <p class="p-2 font-italic ml-4 rounded-bottom rounded-top my-2 bg-danger">
                By choosing This clinics, You can erase enrolled doctors information of this clinics (Appointments and its related data, Patients and Doctor Relation. Including Billings.). Once deleted can't be reverted back.</p>
                <!-- <h5 class="ml-2 font-weight-bold">Select Clinics To Delete</h5> -->
                <!-- <input type="hidden" class="docId" value="<?=$docInfo->doctor_id?>"> -->
                <!-- <form> -->
                    <!-- <?php 
                    foreach($clinicsInfo as $clinic_list)
                    {
                        ?>
                        <div class="form-group">
                            <div class='checkbox checkbox-success checkbox-inline'>
                                <input class="icheckbox_flat-green clinics" id='clinic_<?=$clinic_list->clinic_id?>' type='checkbox'  name='clinics' value='<?=$clinic_list->clinic_id?>' />
                                <label for='clinic_<?=$clinic_list->clinic_id?>' class='font-weight-bold'><?=$clinic_list->clinic_name?> </label>
                            </div>  
                        </div>
                        <?php
                    }
                    ?> -->
                    <!-- 68,69,86,104,106,112,116,119,123,273,281,334 -->
                    <div class="form-group ml-4" id="submitLoad" style="text-align:center;" >
				    	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-app submitDel">Submit</button>
					
                    </div>
                <!-- </form> -->
                </div>
            </div>
            <div class="row hidden" id="delLoading">
                <div class="col-md-12 text-center">
                    <h4 class="">Deleting Data... <i class="fa fa-spinner fa-spin"></i></h4>
                </div>
            </div>
            <?php
        }
        else
        {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <h4>No Clinics Present For This Doctor.</h4>
                </div>
            </div>
            <?php
        }
        ?>
        
        <?php
	}
	
	public function deleteClinicData()
	{
		extract($_POST);
		$id =  $this->input->post('id');

		$appInfo = $this->db->query("select * from clinic_doctor 
		where clinic_id='".$id."'")->result();

		// $i=0;
		foreach($appInfo as $doc)
		{
			$this->Generic_model->deleteRecord("doctors", array('doctor_id'=>$doc->doctor_id));
			$this->Generic_model->deleteRecord('users',array('user_id'=>$doc->doctor_id));

                    $appInfoo = $this->db->query("select * from appointments where clinic_id='".$id."' and doctor_id='".$doc->doctor_id."'")->result();
                    foreach($appInfoo as $value)
                    {
                        $vitals = $this->Generic_model->deleteRecord("patient_vital_sign", array('appointment_id'=>$value->appointment_id));

                        $symptoms = $this->db->query("select * from patient_presenting_symptoms where appointment_id='".$value->appointment_id."'")->result();
                        foreach($symptoms as $symptom)
                        {
                            $this->Generic_model->deleteRecord("patient_ps_line_items",array('patient_presenting_symptoms_id'=>$symptom->patient_presenting_symptoms_id));
                        }
                        $this->Generic_model->deleteRecord('patient_presenting_symptoms',array('appointment_id'=>$value->appointment_id));

                        $billings = $this->db->query("select * from billing where appointment_id='".$value->appointment_id."'")->result();
                        foreach($billings as $bill)
                        {
                            $this->Generic_model->deleteRecord('billing_line_items',array('billing_id'=>$bill->billing_id));
                        }
                        $this->Generic_model->deleteRecord('billing',array('appointment_id'=>$value->appointment_id));

                        $clinicalDiagnosis = $this->db->query("select * from patient_clinical_diagnosis where appointment_id='".$value->appointment_id."'")->result();
                        foreach($clinicalDiagnosis as $cd)
                        {
                            $this->Generic_model->deleteRecord("patient_cd_line_items",array('patient_clinical_diagnosis_id'=>$cd->patient_clinical_diagnosis_id));
                        }
                        $this->Generic_model->deleteRecord('patient_clinical_diagnosis',array('appointment_id'=>$value->appointment_id));


                        $investigations = $this->db->query("select * from patient_investigation where appointment_id='".$value->appointment_id."'")->result();
                        foreach($investigations as $inv)
                        {
                            $this->Generic_model->deleteRecord("patient_investigation_line_items",array('patient_investigation_id'=>$inv->patient_investigation_id));
                        }
                        $this->Generic_model->deleteRecord('patient_investigation',array('appointment_id'=>$value->appointment_id));

                        $prescription = $this->db->query("select * from patient_prescription where appointment_id='".$value->appointment_id."'")->result();
                        foreach($prescription as $pres)
                        {
                            $this->Generic_model->deleteRecord("patient_prescription_drug",array('patient_prescription_id'=>$pres->patient_prescription_id));
                        }
                        $this->Generic_model->deleteRecord('patient_prescription',array('appointment_id'=>$value->appointment_id));
                        
                        $patient_form = $this->db->query("select * from patient_form where appointment_id='".$value->appointment_id."'")->result();
                        foreach($patient_form as $pform)
                        {
                            $this->Generic_model->deleteRecord("patient_form_line_items",array('patient_form_id'=>$pform->patient_form_id));
                        }
                        $this->Generic_model->deleteRecord('patient_form',array('appointment_id'=>$value->appointment_id));
                    
                        // $this->Generic_model->deleteRecord('clinic_doctor_patient',array('clinic_id'=>$value->clinic_id,'doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));    
                        // $this->Generic_model->deleteRecord('doctor_patient',array('doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));
                        // $this->Generic_model->deleteRecord('appointments',array('appointment_id'=>$value->appointment_id));
                    }                
		}
		$result = $this->Generic_model->deleteRecord('clinics', array('clinic_id'=>$id));
		$this->Generic_model->deleteRecord('clinic_doctor', array('clinic_id'=>$id));
		$this->Generic_model->deleteRecord('clinic_doctor_patient', array('clinic_id'=>$id));
		// redirect('clinic');
		// if($result==1){
		// 	$this->session->set_flashdata('msg', "Record deleted successfully");
		// 	redirect('clinic');
		// }
		// else{
		// 	$this->db->last_query();
		// 	echo "failed to delete";
		// }
		echo "1";
		// redirect('clinic');
	}
}

?>