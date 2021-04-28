<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mail_send', array(
            'mailtype' => 'html'
        ));
    }

    public function index()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        
        $this->db->select("*");
        $this->db->from("doctors Doc");
        $this->db->join("department Dep", "Doc.department_id = Dep.department_id");

        if($this->session->userdata('clinic_id') != 0){
            $this->db->where("CD.clinic_id", $clinic_id);
        }

        $this->db->order_by('doctor_id', 'DESC');

        $data['doctor_list'] = $this->db->get()->result();
        
        $data['view'] = 'doctor/doctor_list';
        $this->load->view('layout', $data);
    }

    public function Slots($id = ''){
        $data['clinicsInfo'] = $this->db->query("select * from clinic_doctor cd, clinics c where cd.clinic_id=c.clinic_id and cd.clinic_doctor_id='".$id."'")->row();
        $data['clinic_doctor_id'] = $id;
        $data['doctor_id'] = $data['clinicsInfo']->doctor_id;
        $data['view'] = "doctor/slots";
        $this->load->view('layout', $data);
    }

    public function packagesInfo(){
        if(isset($_POST)){
            extract($_POST);
            $packagesInfo= $this->db->query("select * from doctor_packages dp, packages p where dp.package_id=p.package_id and dp.doctor_id='".$doctor_id."'")->row();
            // echo $this->db->last_query();
            $packages = $this->Generic_model->getAllRecords('packages');
            ?>
            <h3 class="ml-1 p-0 pl-2"><?=$packagesInfo->package_name?></h3>
            <?php
            if($packagesInfo->package_subscription_date != ""){
                ?>
                <p style="padding: 0px 10px !important">Subscription Date: <?=date("d/M/Y", strtotime($packagesInfo->package_subscription_date))?></p>
                <p style="padding: 0px 10px !important">Subscription Valid Upto: <?=date("d/M/Y", strtotime($packagesInfo->package_subscription_date . ' +1 year '))?></p>
                <?php
            }
            ?>
            <div class="row mt-2">
                <div class="col-12 ml-1 pl-4">
                <?php
                if(count($packages) > 0){
                    foreach($packages as $val){
                        ?>
                        <a href="<?=base_url('Doctor/ChangePackage/'.$doctor_id.'/'.$val->package_id)?>" class="btn btn-app">CHANGE TO <?=$val->package_name?></a>
                        <?php
                    }
                }
                ?>
                </div>
            </div>
            <?php
        }
    }

    public function ChangePackage($doctor_id, $package_id){
        $check = $this->Generic_model->getSingleRecord('packages', array('package_id'=>$package_id));
        if(count($check) > 0){
            $docPackCheck = $this->Generic_model->getSingleRecord('doctor_packages', array('doctor_id'=>$doctor_id));
            if(count($docPackCheck) > 0){
                $data['package_id'] = $package_id;
                $this->Generic_model->updateData('doctor_packages', $data, array('doctor_id'=>$doctor_id));
                $this->session->set_flashdata('msg', 'Package Changed Successfully');
            }
            else{
                $docInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."' order by clinic_doctor_id ASC")->row();
                $data['package_id'] = $package_id;
                $data['doctor_id'] = $doctor_id;
                $data['clinic_id'] = $docInfo->clinic_id;
                $data['package_subscription_date'] = date("Y-m-d H:i:s");
                $data['package_validity'] = "1";
                $data['status'] = 1;
                $data['created_by'] = $this->session->userdata('user_id');
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_by'] = $this->session->userdata('user_id');
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('doctor_packages', $data);
                $this->session->set_flashdata('msg', 'Package Added Successfully');
            }
            redirect('Doctor');
        }
        else{
            $this->session->set_flashdata('msg', 'Invalid Package');
            redirect('Doctor');
        }
    }

    public function Profile($id = ''){
        $data['doctor_info'] = $this->db->query("select * from doctors where doctor_id='".$id."'")->row();
        $data['departments'] = $this->db->query("select * from department order by department_name ASC")->result();
        $data['clinicsInfo'] = $this->db->query("select * from clinic_doctor cd, clinics c where cd.clinic_id=c.clinic_id and cd.doctor_id='".$id."'")->result();
        $data['degrees'] = $this->db->query("select * from doctor_degrees where doctor_id='".$id."'")->result();
        $data['view'] = "doctor/profile";
        $this->load->view('layout', $data);
    }

    public function Clinics($id = ''){
        $data['clinicsInfo'] = $this->db->query("select * from clinic_doctor cd, clinics c where cd.clinic_id=c.clinic_id and cd.doctor_id='".$id."'")->result();
        $data['clinics'] = $this->db->query("select CONCAT(clinic_name,', ',location) as label, clinic_id as value from clinics")->result();
        $data['view'] = "doctor/clinics";
        $this->load->view('layout', $data);

    }

    public function PairClinic(){
        // echo "<pre>";print_r($_POST);echo "</pre>";
        if(isset($_POST)){
            extract($_POST);
            $data['consulting_fee'] = $walkin_consulting_fee;
            $data['online_consulting_fee'] = $tele_consulting_fee;
            $data['review_times'] = $review_times;
            $data['review_days'] = $review_days;
            $data['clinic_id'] = $clinic_id;
            $data['doctor_id'] = $doctor_id;
            $data['status'] = 1;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('clinic_doctor', $data);
            $this->session->set_flashdata('msg', 'Successfully Clinic Added');
            redirect('Doctor/Clinics/'.$doctor_id);
        }
        else{
            redirect('Doctor/Clinics/'.$doctor_id);
        }
    }

    public function RemovePair($cd_id,$docId){
        $check = $this->db->query("select * from clinic_doctor where clinic_doctor_id='".$cd_id."'")->row();
        if(count($check) > 0){
            $this->Generic_model->deleteRecord('clinic_doctor', array('clinic_doctor_id'=>$cd_id));
            $this->session->set_flashdata('msg', 'Successfully Pairing Removed');
            redirect('Doctor/Clinics/'.$docId);
        }
        else{
            redirect('Doctor/Clinics/'.$docId);
        }
    }

    public function NewClinic(){
        // echo "<pre>";print_r($_POST);echo "</pre>";
        // exit;
        if(isset($_POST)){
            extract($_POST);
            $clinicData['clinic_name'] = $clinic_name;
            $clinicData['location'] = $location;
            $clinicData['registration_fee'] = $clinic_reg_fee;

            $clinicData['clinic_id'] = $this->Generic_model->insertDataReturnId('clinics', $clinicData);
            // $primary_clinic = 1;
            if($FO==1)
            {
                $inchargeUname = "FO_".strtoupper(substr(str_replace(' ', '', $clinic_name),0,3))."_".$clinicData['clinic_id'];
                $inchargePwd = $this->Generic_model->getrandomstring(8);
                /* creating incharge*/        
                $inchargeData['username'] = $inchargeUname;
                $inchargeData['password'] = md5('1234');
                $inchargeData['clinic_id'] = $clinicData['clinic_id'];
                $inchargeData['user_type'] = 'employee';
                // Get role id for Front Office
                $adroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Front Office')->get()->row();
                $inchargeData['role_id'] = $adroleInfo->role_id;
                // Get profile id for Front Office
                $adprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Front Office')->get()->row();
                $inchargeData['profile_id'] = $adprofileInfo->profile_id;
                $inchargeData['status'] = 1;
                $inchargeData['created_date_time'] = date('Y-m-d H:i:s');
                $inchargeData['modified_date_time'] = date('Y-m-d H:i:s');
                $incharge_id = $this->Generic_model->insertDataReturnId('users',$inchargeData);
                $empcode = 'EMP-'.date('Ymd').$incharge_id;
                $emp['employee_id']=$incharge_id;
                $emp['employee_code']=$empcode;
                $emp['first_name']=$inchargeUname;
                $emp['clinic_id']=$clinicData['clinic_id'];
                $emp['status']=1;
                $emp['created_date_time']=date('Y-m-d H:i:s');
                $emp['modified_date_time']=date('Y-m-d H:i:s');
                $this->Generic_model->insertData('employees',$emp);
                $forced = '<p class="font-weight-bold text-danger">Front Office Credentials</p><p class="font-weight-bold">Username : '.$inchargeUname.'<br>Password : '.$inchargePwd.'</p>';
            }
            if($PH==1)
            {
                $phUname = "PH_".strtoupper(substr(str_replace(' ', '', $clinic_name),0,3))."_".$clinicData['clinic_id'];
                $phPwd = $this->Generic_model->getrandomstring(8);
                /* creating incharge*/        
                $phData['username'] = $phUname;
                $phData['password'] = md5('1234');
                $phData['clinic_id'] = $clinicData['clinic_id'];
                $phData['user_type'] = 'employee';
                // Get role id for Pharmacy
                $adroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Pharmacy')->get()->row();
                $phData['role_id'] = $adroleInfo->role_id;
                // Get profile id for Pharmacy
                $adprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Pharmacy')->get()->row();
                $phData['profile_id'] = $adprofileInfo->profile_id;
                $phData['status'] = 1;
                $phData['created_date_time'] = date('Y-m-d H:i:s');
                $phData['modified_date_time'] = date('Y-m-d H:i:s');
                $ph_id = $this->Generic_model->insertDataReturnId('users',$phData);
                $empcode = 'EMP-'.date('Ymd').$ph_id;
                $emp['employee_id']=$ph_id;
                $emp['employee_code']=$empcode;
                $emp['first_name']=$phUname;
                $emp['clinic_id']=$clinicData['clinic_id'];
                $emp['status']=1;
                $emp['created_date_time']=date('Y-m-d H:i:s');
                $emp['modified_date_time']=date('Y-m-d H:i:s');
                $this->Generic_model->insertData('employees',$emp);
                $phcred = '<p class="font-weight-bold text-danger">Pharmacy Credentials</p><p class="font-weight-bold">Username : '.$phUname.'<br>Password : '.$phPwd.'</p>';
            }
            if($LAB==1)
            {
                $labUname = "LAB_".strtoupper(substr(str_replace(' ', '', $clinic_name),0,3))."_".$clinicData['clinic_id'];
                $labPwd = $this->Generic_model->getrandomstring(8);
                /* creating incharge*/        
                $labData['username'] = $labUname;
                $labData['password'] = md5('1234');
                $labData['clinic_id'] = $clinicData['clinic_id'];
                $labData['user_type'] = 'employee';
                // Get role id for Lab Technician
                $adroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Lab Technician')->get()->row();
                $labData['role_id'] = $adroleInfo->role_id;
                // Get profile id for Lab Technician
                $adprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Lab Technician')->get()->row();
                $labData['profile_id'] = $adprofileInfo->profile_id;
                $labData['status'] = 1;
                $labData['created_date_time'] = date('Y-m-d H:i:s');
                $labData['modified_date_time'] = date('Y-m-d H:i:s');
                $lab_id = $this->Generic_model->insertDataReturnId('users',$labData);
                $empcode = 'EMP-'.date('Ymd').$lab_id;
                $emp['employee_id']=$lab_id;
                $emp['employee_code']=$empcode;
                $emp['first_name']=$labUname;
                $emp['clinic_id']=$clinicData['clinic_id'];
                $emp['status']=1;
                $emp['created_date_time']=date('Y-m-d H:i:s');
                $emp['modified_date_time']=date('Y-m-d H:i:s');
                $this->Generic_model->insertData('employees',$emp);
                $labcred = '<p class="font-weight-bold text-danger">Lab Technician Credentials</p><p class="font-weight-bold">Username : '.$labUname.'<br>Password : '.$labPwd.'</p>';
            }
            if($NU==1)
            {
                $nuUname = "NU_".strtoupper(substr(str_replace(' ', '', $clinic_name),0,3))."_".$clinicData['clinic_id'];
                $nuPwd = $this->Generic_model->getrandomstring(8);
                /* creating incharge*/        
                $nuData['username'] = $nuUname;
                $nuData['password'] = md5('1234');
                $nuData['clinic_id'] = $clinicData['clinic_id'];
                $nuData['user_type'] = 'employee';
                // Get role id for Nurse
                $adroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Nurse')->get()->row();
                $nuData['role_id'] = $adroleInfo->role_id;
                // Get profile id for Nurse
                $adprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Nurse')->get()->row();
                $nuData['profile_id'] = $adprofileInfo->profile_id;
                $nuData['status'] = 1;
                $nuData['created_date_time'] = date('Y-m-d H:i:s');
                $nuData['modified_date_time'] = date('Y-m-d H:i:s');
                $nu_id = $this->Generic_model->insertDataReturnId('users',$nuData);
                $empcode = 'EMP-'.date('Ymd').$nu_id;
                $emp['employee_id']=$nu_id;
                $emp['employee_code']=$empcode;
                $emp['first_name']=$nuUname;
                $emp['clinic_id']=$clinicData['clinic_id'];
                $emp['status']=1;
                $emp['created_date_time']=date('Y-m-d H:i:s');
                $emp['modified_date_time']=date('Y-m-d H:i:s');
                $this->Generic_model->insertData('employees',$emp);
                $nucred = '<p class="font-weight-bold text-danger">Nurse Credentials</p><p class="font-weight-bold">Username : '.$nuUname.'<br>Password : '.$nuPwd.'</p>';
            }
            $data['consulting_fee'] = $walkin_consulting_fee;
            $data['online_consulting_fee'] = $tele_consulting_fee;
            $data['review_times'] = $review_times;
            $data['review_days'] = $review_days;
            $data['clinic_id'] = $clinicData['clinic_id'];
            $data['doctor_id'] = $doctor_id;
            $data['status'] = 1;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('clinic_doctor', $data);
            $this->session->set_flashdata('msg', 'Successfully Clinic Added');
            redirect('Doctor/Clinics/'.$doctor_id);
        }
        else{
            redirect('Doctor/Clinics/'.$doctor_id);
        }
    }

    public function SearchClinic(){
        extract($_POST);
        $check = $this->db->query("select * from clinics where clinic_name LIKE '%".urldecode($search)."%'")->result();
        ?>
        <div class="row my-3">
            <div class="col-12 text-center">
                <a href="" data-toggle="modal" data-target="#newClinicModal" data-id="<?=$doctor_id?>" class="btn btn-outline-primary newClinic">ADD NEW CLINIC</a>
            </div>
        </div>
        <?php
        if(count($check) > 0)
        {
            ?>
            <ul class="docListWindow mt-3 p-0">
            <?php
            foreach($check as $value){
                $clinicCheck = $this->db->query("select * from clinic_doctor where clinic_id='".$value->clinic_id."' and doctor_id='".$doctor_id."'")->row();
                if(count($clinicCheck) > 0)
                {
                    continue;
                }
                if($value->clinic_logo != "")
                {
                    $img = base_url('uploads/clinic_logos/').$value->clinic_logo;
                }
                else
                {
                    $img = base_url('uploads/departments/dummyDEPT.png');
                }
                ?>
                <li>
                    <div class="prog-avatar">
                        <img src="<?=$img?>" alt="" width="40" height="40">
                    </div>
                    <div class="details">
                        <div class="title">
                            <a href="#"><?=$value->clinic_name?></a>
                            <!-- <?=base_url('Doctor/PairClinic/'.$value->clinic_id.'/'.$doctor_id)?> -->
                            <a href="" class="btn pull-right btn-xs btn-outline-primary pair" data-toggle="modal" data-target="#pairModal" data-values="<?=$value->clinic_id."*".$doctor_id?>">Add this Clinic</a>
                        </div>
                        <div>
                            <span class=""><?=$value->location?></span>
                        </div>
                    </div>
                </li>
                <?php
            }
            ?>
            </ul>
            <?php
        }
    }
    
    public function addSlots(){
        if(isset($_POST))
        {
          extract($_POST);
          // echo "<pre>";print_r($_POST);echo "</pre>";
          // $doctor_id='24';
          $slots = $this->Generic_model->getAllRecords('clinic_doctor_weekdays',array('clinic_doctor_id'=>$clinic_doctor_id,'slot'=>$slot_type));
          // echo $this->db->last_query();
          // echo "<pre>";print_r($slots);echo "</pre>";
          // exit;
          if(count($slots) > 0){
            foreach($slots as $value){
              $this->Generic_model->deleteRecord('clinic_doctor_weekday_slots', array('clinic_doctor_weekday_id' => $value->clinic_doctor_weekday_id));
            }
            $this->Generic_model->deleteRecord('clinic_doctor_weekdays', array('clinic_doctor_id' => $clinic_doctor_id,'slot'=>$slot_type));
          }
  
          for($i = 0;$i <= 6;$i++)
          {
            $data['weekday'] = $i+1;
            $data['clinic_doctor_id'] = $clinic_doctor_id;
            $data['status'] = 0;
            $data['slot'] = $slot_type;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $cdw_id = $this->Generic_model->insertDataReturnId('clinic_doctor_weekdays', $data);
  
            // Morning Slots
            if($mr_start[$i] != "" && $mr_end[$i] != "")
            {
                $morning['clinic_doctor_weekday_id'] = $cdw_id;
                $morning['from_time'] = ($mr_start[$i]!="")?$mr_start[$i]:'';
                $morning['to_time'] = ($mr_end[$i]!="")?$mr_end[$i]:'';
                $morning['session'] = "morning";
                $morning['created_by'] = $this->session->userdata('user_id');
                $morning['modified_by'] = $this->session->userdata('user_id');
                $morning['created_date_time'] = date("Y-m-d H:i:s");
                $morning['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('clinic_doctor_weekday_slots', $morning);
            }
  
            // Afternoon Slots
            if($af_start[$i] != "" && $af_end[$i] != "")
            {
                $afternoon['clinic_doctor_weekday_id'] = $cdw_id;
                $afternoon['from_time'] = ($af_start[$i]!="")?$af_start[$i]:'';
                $afternoon['to_time'] = ($af_end[$i]!="")?$af_end[$i]:'';
                $afternoon['session'] = "afternoon";
                $afternoon['created_by'] = $this->session->userdata('user_id');
                $afternoon['modified_by'] = $this->session->userdata('user_id');
                $afternoon['created_date_time'] = date("Y-m-d H:i:s");
                $afternoon['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('clinic_doctor_weekday_slots', $afternoon);
            }
  
            // Evening Slots
            if($ev_start[$i] != "" && $ev_end[$i] != "")
            {
                $evening['clinic_doctor_weekday_id'] = $cdw_id;
                $evening['from_time'] = ($ev_start[$i]!="")?$ev_start[$i]:'';
                $evening['to_time'] = ($ev_end[$i]!="")?$ev_end[$i]:'';
                $evening['session'] = "evening";
                $evening['created_by'] = $this->session->userdata('user_id');
                $evening['modified_by'] = $this->session->userdata('user_id');
                $evening['created_date_time'] = date("Y-m-d H:i:s");
                $evening['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('clinic_doctor_weekday_slots', $evening);
            }
            
  
          }
          $this->session->set_flashdata('msg', 'Slots Saved Successfully.');
          redirect('Doctor/Profile/'.$doctor_id);
        }
      }


    // Update Doctor Profile
  public function update_profile($id){
    $config['upload_path']="./uploads/doctors/";
    $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
    $this->load->library('upload');    
    $this->upload->initialize($config);
    $this->upload->do_upload('profile_image');
    $fileData=$this->upload->data('file_name');
    if($fileData==""){
      $doctor_img=$this->db->query('select profile_image from doctors where doctor_id='.$id)->row();
      $img=$doctor_img->profile_image;
    }else{
      $img = $fileData;
    }


    $languages = implode(",",$this->input->post("language")); 
    $achievements = implode(",",$this->input->post("achievements")); 
    $membership = implode(",",$this->input->post("membership")); 
    $data['first_name'] = $this->input->post("first_name");
    $data['profile_image'] = $img;
    $data['last_name'] = $this->input->post("last_name");
    $data['about'] = $this->input->post("about");
    $data['registration_code'] = $this->input->post("reg_number");
    $data['speciality'] = $this->input->post("speciality");
    $data['department_id'] = $this->input->post("department");
    $data['experience'] = $this->input->post("experience");
    $data['diseases_dealt'] = $this->input->post("diseases_dealt");
    $data['google_review_link'] = $this->input->post("google_review_link");
    $data['languages'] = $languages;
    $data['acheivements'] = $achievements;
    $data['membership_in'] = $membership;
    // $data['last_modified_by'] = $this->session->has_userdata('user_id'); 
    $this->Generic_model->updateData('doctors', $data, array('doctor_id' => $id));
    $this->session->set_flashdata('msg', 'Successfully Added');

    $degrees = $this->input->post("Degree");

    for($i=0;$i<count($degrees); $i++) {
      $idd = $degrees[$i]['doctor_degree_id'];

      if(count($idd) == 0)
      {
        $dd['doctor_id'] = $id; 
        $dd['degree_name'] = $degrees[$i]['degree'];
        $dd['university'] = $degrees[$i]['college'];
        $dd['year'] = $degrees[$i]['year'];
        $this->Generic_model->insertData('doctor_degrees',$dd);
        $this->session->set_flashdata('msg', 'Successfully Added');
      }
      else{
      }
    }
    redirect("Doctor");
 }


    //Get Doc Details
    public function getDocDetails(){
        extract($_POST);
        $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$id."'")->row();
        $clinicsInfo = $this->db->query("select * from clinic_doctor cd, clinics c where c.clinic_id=cd.clinic_id and cd.doctor_id='".$id."'")->result();
        if(count($clinicsInfo)>0)
        {
            ?>
            <div class="row">
                <div class="col-md-12">
                <p class="p-2 font-italic ml-4 rounded-bottom rounded-top my-2 bg-danger">
                By choosing below clinics, You can erase enrolled doctors information of choosen clinics (Appointments and its related data, Patients and Doctor Relation. Including Billings.). Once deleted can't be reverted back.</p>
                <h5 class="ml-2 font-weight-bold">Select Clinics To Delete</h5>
                <input type="hidden" class="docId" value="<?=$docInfo->doctor_id?>">
                <!-- <form> -->
                    <?php 
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
                    ?>
                    <!-- 68,69,86,104,106,112,116,119,123,273,281,334 -->
                    <div class="form-group ml-4" id="submitLoad" >
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

    //Delete doctor with his respective clinics
    public function deleteDoc($doctor_id){
        if(isset($doctor_id))
        {
            $docInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();
            if(count($docInfo)>0)
            {
                foreach($docInfo as $val)
                {
                    $clinic_id = $val->clinic_id;
                    $appInfo = $this->db->query("select * from appointments where clinic_id='".$val->clinic_id."' and doctor_id='".$doctor_id."'")->result();
                    foreach($appInfo as $value)
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
                    
                        $this->Generic_model->deleteRecord('clinic_doctor_patient',array('clinic_id'=>$value->clinic_id,'doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));    
                        $this->Generic_model->deleteRecord('doctor_patient',array('doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));
                        $this->Generic_model->deleteRecord('appointments',array('appointment_id'=>$value->appointment_id));
                    }                
                    $this->Generic_model->deleteRecord('clinic_doctor',array('clinic_id'=>$clinic_id,'doctor_id'=>$doctor_id));
                }
            }
            $this->Generic_model->deleteRecord('doctors',array('doctor_id'=>$doctor_id));
            $this->Generic_model->deleteRecord('users',array('user_id'=>$doctor_id));
            $this->session->set_flashdata('msg', 'Doctor Deleted Successfully.');
            redirect('Doctor');
        }
    }

    // Session Data 
    public function SessionData(){
        echo "<pre>";print_r($this->session);echo "</pre>";
    }

    // Delete Data 
    public function DeleteData(){
        extract($_POST);
        $clinics = explode(",", $clinics);
        foreach($clinics as $clinic)
        {
            $appInfo = $this->db->query("select * from appointments where clinic_id='".$clinic."' and doctor_id='".$id."'")->result();
            foreach($appInfo as $value)
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
            
                $this->Generic_model->deleteRecord('clinic_doctor_patient',array('clinic_id'=>$value->clinic_id,'doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));    
                $this->Generic_model->deleteRecord('doctor_patient',array('doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id));
                $this->Generic_model->deleteRecord('appointments',array('appointment_id'=>$value->appointment_id));
            }
        
            $this->Generic_model->deleteRecord('clinic_doctor',array('clinic_id'=>$clinic,'doctor_id'=>$id));
            echo "1";
        }
        
    }

    public function generateRandomString($length = 8)
    {
        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        $charactersLength = strlen($characters);
        
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            
            $randomString .= $characters[rand(0, $charactersLength - 1)];
            
        }
        
        return $randomString;
        
    }

    public function doctor_add()
    {

        if($_POST){
            $param = $_POST;

             // Get Type
            if($_POST['clinic_id'] == 0){
                $_POST['type'] = "In House";
            }else{
                $_POST['type'] = "Customer";            
            }

            $cond = '';
            $condition = '';

            // Get role and profile id data
            $session_user_id = $this->session->has_userdata('user_id');
            $check_profile = $this->db->select("profile_id")->from("profiles")->where("profile_name =","Doctor")->get()->row();
            $check_role    = $this->db->select("role_id")->from("std_uac_roles")->where("role_name =","Doctor")->get()->row();
            
            // Create User and get the user id
            $rand = str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
            $colorcode = '#'.$rand;
            $type = $this->input->post('type');
            $pwd = $this->Generic_model->getrandomString(8);
            $userData['username'] = $this->input->post("reg_code");
            $userData['email_id'] = $this->input->post("email");
            $userData['clinic_id'] = $this->input->post("clinic_id");
            $userData['mobile'] = $this->input->post("mobile");
            $userData['password'] = md5($pwd);
            $userData['user_type'] = "doctor";
            $userData['role_id'] = $check_role->role_id;
            $userData['profile_id'] = $check_profile->profile_id;
            $userData['status'] = '1';
            $userData['last_logged_in_date_time'] = date("Y-m-d H:i:s");
            $userData['created_by'] = $session_user_id;
            $userData['created_date_time'] = date("Y-m-d H:i:s");
            $userData['modified_by'] = $session_user_id;
            $userData['modified_date_time'] = date("Y-m-d H:i:s");

            // Create user and get the user id
            $user_id = $this->Generic_model->insertDataReturnId('users', $userData);

            if ($user_id != "" || $user_id != null) {
                $data['doctor_id'] = $user_id;
                $data['doctor_type'] = $this->input->post('type');
                $data['salutation'] = 'Dr';
                $data['first_name'] = $this->input->post('first_name');
                $data['last_name'] = $this->input->post('last_name');
                $data['registration_code'] = $this->input->post('reg_code');
                $data['department_id'] = $this->input->post('department');
                $data['experience'] = $this->input->post('experience');
                $data['gender'] = $this->input->post('gender');
                $data['qualification'] = $this->input->post('qualification');
                $data['address'] = $this->input->post('address');
                $data['state_id'] = $this->input->post('state');
                $data['pincode'] = $this->input->post('pincode');
                $data['color_code'] = $colorcode;
                $data['mobile'] = $this->input->post('mobile');
                $data['email'] = $this->input->post('email');
                $data['status'] = "1";
                $data['created_by'] = $session_user_id;
                
                $okay = $this->Generic_model->insertData('doctors', $data);
                
                if($okay) {  
                    // Subscription Package Details
                    $package_price_info = $this->db->select("*")->from("subscription_price")->where("package_id='".$this->input->post('package_id')."'")->order_by("package_price_id", "desc")->get()->row();

                    $renewalDate = ($package_price_info->no_days - 30);

                    $pdata['clinic_id'] = $this->input->post('clinic_id');
                    $pdata['doctor_id'] = $user_id;
                    $pdata['package_id'] = $this->input->post('package_id');
                    $pdata['package_price_id'] = $package_price_info->package_price_id;
                    
                    if ($this->input->post('package_subscription_date') != '') {
                        $pdata['package_subscription_date'] = $this->input->post('package_subscription_date');
                        $pdata['package_expiry_date']       = date('Y-m-d', strtotime("+" . $package_price_info->no_days . " days", strtotime($this->input->post('package_subscription_date'))));
                        $pdata['package_renewal_date']      = date('Y-m-d', strtotime("+" . $rdate . " days", strtotime($this->input->post('package_subscription_date'))));
                    }
                    
                    $pdata['status']             = 1;
                    $pdata['created_by']         = $session_user_id;
                    $pdata['modified_by']        = $session_user_id;
                    $pdata['created_date_time']  = date("Y-m-d H:i:s");
                    $pdata['modified_date_time'] = date("Y-m-d H:i:s");
                    
                    $this->Generic_model->insertData('doctor_package', $pdata);
                    
                    $clinic_doctor['clinic_id']       = $this->input->post('clinic_id');
                    $clinic_doctor['doctor_id']       = $user_id;
                    $clinic_doctor['consulting_fee']  = $this->input->post('consulting_fee');
                    $clinic_doctor['consulting_time'] = $this->input->post('consulting_time');
                    $clinic_doctor['review_days']     = $this->input->post('review_days');
                    
                    $ok = $this->Generic_model->insertData('clinic_doctor', $clinic_doctor);
                    
                    
                    $from = 'UMDAA';
                    $to = $this->input->post('email');
                    $subject = "Login Credentials ";
                    $message = "<html><body><h3>Hi " . $this->input->post("title") . " " . ucwords($this->input->post('first_name')) . ",</h3><br/><b>UserName :</b>" . $this->input->post('email') . "," . $this->input->post('mobile') . "," . $this->input->post('reg_code') . " <br/> <b> password :</b>" . $pwd . "<br/><b>URL :</b>" . base_url("Authentication/login") . "</p><br/><br/><h3>Thanks and Regards</h3><b>UMDAA TEAM</b>";
                    
                    $ok = $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '',$message);
                    
                    $this->session->set_flashdata('suscess', 'Successfully Inserted');
                    redirect('Doctor');
                } else {
                    $this->session->set_flashdata('error', 'Not  Inserted Your Record');
                    redirect('Doctor');
                }
            } else {
                $this->session->set_flashdata('error', 'Not  Inserted Your Record');
                redirect('Doctor');
            }
        }else{
            $data['department_list'] = $this->Generic_model->getAllRecords("department", $condition = '', $order = '');
            $data['state_list'] = $this->Generic_model->getAllRecords("states", $condition = '', $order = '');
            $data['clinic_list'] = $this->Generic_model->getAllRecords("clinics", $condition = '', $order = '');
            $data['packages_list'] = $this->Generic_model->getAllRecords("subscription", $condition = '', $order = '');
            $data['view'] = 'doctor/doctor_add';
            $this->load->view('layout', $data);
        }
        
    }
    

    public function doctor_profile($id = '')
    {
        
        $clinic_id = $this->session->userdata('clinic_id');
        
        $data['doctor_info'] = $this->db->select("*")->from("clinic_doctor a")->join("doctors b", "a.doctor_id=b.doctor_id")->join("department d", "d.department_id = b.department_id")->join("clinics c", "c.clinic_id = a.clinic_id")->where("clinic_id", $clinic_id)->get()->row();
        $data['view']        = 'doctor/doctor_profile';
        $this->load->view('layout', $data);
        
    }
    public function doctor_update($id = '')
    {
        $sclinic_id = $this->session->userdata('clinic_id');
        $cond       = '';
        $condition  = '';
        if ($sclinic_id != 0) {
            $cond = "clinic_id=" . $sclinic_id;
        }
        
        $suser_id = $this->session->has_userdata('user_id');
        
        if ($this->input->post('submit')) {
            
            $data['salutation']        = $this->input->post("salutation");
            $data['doctor_type']       = $this->input->post('type');
            $data['first_name']        = $this->input->post('first_name');
            $data['last_name']         = $this->input->post('last_name');
            $data['registration_code'] = $this->input->post('reg_code');
            $data['gender']            = $this->input->post('gender');
            $data['qualification']     = $this->input->post('qualification');
            $data['address']           = $this->input->post('address');
            $data['state_id']          = $this->input->post('state');
            $data['pincode']           = $this->input->post('pincode');
            $data['mobile']            = $this->input->post('mobile');
            $data['email']             = $this->input->post('email');
            
            $data['status']           = "1";
            $data['created_by']       = "1";
            $data['last_modified_by'] = date("Y-m-d H:i:s");
            $type                     = $this->input->post('type');
            if ($type == "clinic") {
                $data['year_of_passing'] = $this->input->post('year_pass');
                $data['university']      = $this->input->post('university');
            } else if ($type == "inhouse") {
                $data['working_hospital'] = $this->input->post('working_hospital');
                $data['department_id']    = $this->input->post('department');
                $data['experience']       = $this->input->post('experience');
            } else if ($type == "consultant") {
                $data['working_hospital'] = $this->input->post('working_hospital');
                $data['department_id']    = $this->input->post('department');
                $data['experience']       = $this->input->post('experience');
            } else {
                echo "fail";
            }
            $this->Generic_model->updateData('doctors', $data, array(
                'doctor_id' => $id
            ));
            
            $param_1['email_id']                 = $this->input->post("email");
            $param_1['mobile']                   = $this->input->post("mobile");
            $param_1['status']                   = '1';
            $param_1['last_logged_in_date_time'] = date("Y-m-d H:i:s");
            $param_1['modified_by']              = "1";
            $param_1['modified_date_time']       = date("Y-m-d H:i:s");
            
            $this->Generic_model->updateData('users', $param_1, array(
                'user_id' => $id
            ));
            
            $package_price_info = $this->db->select("*")->from("subscription_price")->where("package_id='" . $this->input->post('package_id') . "'")->order_by("package_price_id", "desc")->get()->row();
            $rdate              = ($package_price_info->no_days - 30);
            $pdata['clinic_id'] = $sclinic_id;
            
            $pdata['package_id']       = $this->input->post('package_id');
            $pdata['package_price_id'] = $package_price_info->package_price_id;
            if ($this->input->post('package_subscription_date') != '') {
                $pdata['package_subscription_date'] = $this->input->post('package_subscription_date');
                $pdata['package_expiry_date']       = date('Y-m-d', strtotime("+" . $package_price_info->no_days . " days", strtotime($this->input->post('package_subscription_date'))));
                $pdata['package_renewal_date']      = date('Y-m-d', strtotime("+" . $rdate . " days", strtotime($this->input->post('package_subscription_date'))));
            }
            
            
            $pdata['modified_by'] = $suser_id;
            
            $pdata['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->updateData('doctor_package', $pdata, array(
                'package_price_id' => $this->input->post('package_price_id')
            ));
            
            $clinic_doctor['consulting_fee']  = $this->input->post('consulting_fee');
            $clinic_doctor['consulting_time'] = $this->input->post('consulting_time');
            $clinic_doctor['review_days']     = $this->input->post('review_days');
            
            $this->Generic_model->updateData('clinic_doctor', $clinic_doctor, array(
                'doctor_id' => $id,
                'clinic_id' => $this->input->post('clinic_id')
            ));
            
            
            redirect('doctors');
        } else {
            $data['department_list'] = $this->Generic_model->getAllRecords("department", $condition = '', $order = '');
            $data['state_list']      = $this->Generic_model->getAllRecords("states", $condition = '', $order = '');
            $data['doctor_list']     = $this->db->select("*")->from("doctors a")->join("doctor_package b", "a.doctor_id=b.doctor_id")->where("a.doctor_id='" . $id . "'")->get()->row();
            if ($sclinic_id != 0) {
                $data['clinic_list'] = $this->db->select("*")->from("clinic_doctor cd")->join("clinics c", "cd.clinic_id = c.clinic_id")->where("cd.doctor_id= '" . $id . "'")->get()->result();
            } else {
                $data['clinic_list'] = $this->db->select("*")->from("clinic_doctor cd")->join("clinics c", "cd.clinic_id = c.clinic_id")->where("cd.doctor_id= '" . $id . "' and cd.clinic_id='" . $sclinic_id . "'")->get()->result();
            }
            
            $data['packages_list'] = $this->Generic_model->getAllRecords("subscription", $condition = '', $order = '');
            
            if ($sclinic_id != 0) {
                $data['clinic_doctor'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id='" . $sclinic_id . "' and doctor_id='" . $id . "'")->get()->row();
            } else {
                $data['clinic_doctor'] = $this->db->select("*")->from("clinic_doctor")->where("doctor_id='" . $id . "'")->get()->row();
            }
            //echo "<pre>";print_r($data['doctor_list']);
            $data['view'] = 'doctor/doctor_info';
            $this->load->view('layout', $data);
        }
    }
    
    public function profile_info($doctor_id, $clinic_id)
    {
        $data['doctor_info'] = $this->db->select('doc.*, dep.department_name')->from('doctors doc')->join("department dep", "doc.department_id = dep.department_id")->where("doctor_id ='" . $doctor_id . "'")->get()->row();
        
        $data['weekdays'] = $this->db->select('*')->from("clinic_doctor_weekdays cd")->join("clinic_doctor_weekday_slots cs", "cd.clinic_doctor_weekday_id = cs.clinic_doctor_weekday_id")->join("clinic_doctor cdd", "cdd.clinic_doctor_id = cd.clinic_doctor_id")->where('cdd.clinic_id = "' . $clinic_id . '" and cdd.doctor_id = "' . $doctor_id . '" group by cd.clinic_doctor_weekday_id,cd.weekday')->get()->result();
        
        $data['education_info'] = $this->db->select("*")->from("doctor_degrees")->where("doctor_id='" . $doctor_id . "'")->get()->result();
        
        $data['clinic_info'] = $doctor_id = $this->db->select('*')->from("clinic_doctor cd")->join("clinics c", "cd.clinic_id= c.clinic_id")->where('cd.clinic_id = "' . $clinic_id . '" and cd.doctor_id = "' . $doctor_id . '"')->get()->row();
        
        $data['clinic_doctor_id'] = $doctor_id->clinic_doctor_id;
        
        $data['view'] = 'doctor/profile_info';
        $this->load->view('layout', $data);
    }
    
    
    public function doctor_delete($id)
    {
        $doctor_info['archieve'] = 1;
        $this->Generic_model->deleteRecord('doctors', $doctor_info, array(
            'doctor_id' => $id
        ));
        redirect('Doctor');
    }
    
    
    
}
?>