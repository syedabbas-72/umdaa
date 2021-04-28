<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller {


    public function __construct() {      
        parent::__construct();
        if(!$this->session->has_userdata('is_logged_in'))
        {
            redirect('Authentication/login');
        }
        $this->load->library('mail_send', array('mailtype'=>'html'));        
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');  
    }


    public function index() {
        $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';
        if($clinic_id!=0)
            $cond = "e.clinic_id=".$clinic_id." and ";
        $data['employee_list']=$this->db->query('select e.*,u.password,c.clinic_name,u.username from employees e inner join users u on (e.employee_id=u.user_id)  inner join clinics c on (e.clinic_id=c.clinic_id) where '.$cond.' e.archieve=0')->result();
        $data['adminInfo'] = $this->db->query("select user_id from users where clinic_id='".$clinic_id."' and role_id='2' order by user_id ASC")->row();
        $data['view'] = 'employees/employee_list';
        $this->load->view('layout', $data);
    }

    public function setEmp(){
        $employees = $this->db->query("select * from employees")->result();
        if(count($employees) > 0){
            foreach($employees as $value){
                $username = "";
                $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$value->clinic_id."'")->row();
                if($value->created_date_time == "" || $value->created_date_time == "0000-00-00 00:00:00"){
                    $rand = date("mY", strtotime($clinicInfo->created_date_time));
                }
                else{
                    $rand = date("mY", strtotime($value->created_date_time));
                }
                $username = strtoupper(substr($clinicInfo->clinic_name, 0, 3));
                if($username == ""){
                    $username = "EMP";
                }
                else{
                    $username = $username;
                }
                $empData['employee_code'] = $username."-".$rand.$value->employee_id;
                $userData['username'] = $username."-".$rand.$value->employee_id;
                $this->Generic_model->updateData('employees', $empData, array('employee_id' => $value->employee_id));
                $this->Generic_model->updateData('users', $userData, array('user_id' => $value->employee_id));
            }
        }
    }

    public function MobileCheck(){
        if(isset($_POST)){
            extract($_POST);
            $checkMobile = $this->db->query("select * from mobile ")->num_rows();
        }
    }

    public function employee_list() {
    	$clinic_id = $this->session->userdata('clinic_id');
    	$cond = '';
    	if($clinic_id!=0)
    		$cond = "e.clinic_id=".$clinic_id." and ";
        $data['employee_list']=$this->db->query('select e.*,e.firstname as efirstname,e.lastname as elastname,u.password,r.role_name,p.profile_name,c.clinic_name from employees e inner join users u on (e.employee_id=u.user_id)  inner join roles r on (u.role_id=r.role_id) inner join profiles p on (u.profile_id=p.profile_id) inner join clinics c on (e.clinic_id=c.clinic_id) where '.$cond.' e.archieve=0')->result();
        $data['view'] = 'employees/employee_list';
        $this->load->view('layout', $data);
    }


    public function nurses() {
    	$clinic_id = $this->session->userdata('clinic_id');
    	$cond = '';
    	if($clinic_id!=0)
    		$cond = "e.clinic_id=".$clinic_id." and ";
        $data['employee_list']=$this->db->query('select e.*,u.password,r.role_name,p.profile_name,c.clinic_name from employees e inner join users u on (e.employee_id=u.user_id)  inner join roles r on (u.role_id=r.role_id) inner join profiles p on (u.profile_id=p.profile_id) inner join clinics c on (e.clinic_id=c.clinic_id) where '.$cond.' e.archieve=0')->result();
        $data['view'] = 'employees/employee_list';
        $this->load->view('layout', $data);
    }


    public function generateRandomString($length = 8) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;

    }


    public function employee_add() {

        $user_id = $this->session->has_userdata('user_id');

        if($this->input->post('submit')){
    		//$pwd = $this->generateRandomString($length = 8);		
            $pwd = '12345678';
            $user['password']=md5($pwd);
            $user['email_id']=$this->input->post('email_id');
            $user['mobile']=$this->input->post('mobile');
            $user['clinic_id']= $this->session->userdata('clinic_id');
            $user['user_type']='employee';
            $user['role_id']=$this->input->post('role_id');
            // $user['profile_id']=$this->input->post('profile_id');
            $user['created_by'] = $user_id;
            $user['modified_by'] = $user_id;
            $user['created_date_time'] = date('Y-m-d H:i:s');
            $user['modified_date_time'] = date('Y-m-d H:i:s');

            $emp_id = $this->Generic_model->insertDataReturnId("users",$user);	
            $username = "";
            $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$this->session->userdata('clinic_id')."'")->row();
            $username = strtoupper(substr($clinicInfo->clinic_name, 0, 3));
            if($username == ""){
                $username = "EMP";
            }
            else{
                $username = $username;
            }
            $empcode = $username.'-'.date('mY').$emp_id;
            $emp['employee_id']=$emp_id;
            $emp['employee_code']=$empcode;
            $emp['first_name']=$this->input->post('first_name');
            $emp['last_name']=$this->input->post('last_name');
            $emp['gender']=$this->input->post('gender');
            $emp['date_of_birth']= date('Y-m-d',strtotime($this->input->post('date_of_birth')));
            $emp['date_of_joining']=date('Y-m-d',strtotime($this->input->post('date_of_joining')));
            $emp['qualification']=$this->input->post('qualification');
            $emp['mobile']=$this->input->post('mobile');
            $emp['email_id']=$this->input->post('email_id');
            $emp['assigned_roles']=implode(",", $this->input->post('assigned_roles'));
            $emp['adhaar_no']=$this->input->post('adhaar_no');
            $emp['pan_no']=$this->input->post('pan_no');
            $emp['bank_account_no']=$this->input->post('bank_account_no');
            $emp['clinic_id']=$this->session->userdata('clinic_id');
            $emp['address']=$this->input->post('address');
            $emp['status']=1;
            $emp['created_by']=$user_id;
            $emp['modified_by']=$user_id;
            $emp['created_date_time']=date('Y-m-d H:i:s');
            $emp['modified_date_time']=date('Y-m-d H:i:s');

            $this->Generic_model->insertData('employees', $emp);

            
            // Send email if the email address is specified
            if($user['email_id'] != '' || $user['email_id'] != NULL){
                $from='UMDAA';
                $to = $user['email_id'];  
                $url = base_url();
                $subject = "Your Credentials to use UMDAA portal for ".$clinicInfo->clinic_name;
                
                $message = "<h4>Credentials to Login In To UMDAA Portal</h4>";
                $message .= "<p><span style='font-weight: bold'>URL : </span>".$url."</p>";
                $message .= "<p><span style='font-weight: bold'>Username : </span>".$empcode."</p><p><span style='font-weight: bold'>Password : </span>".$pwd."</p>";  
                $ok = $this->mail_send->Content_send_all_mail($from,$to,$subject,'','',$message);
            }

            $empCu['username']=$empcode;
            $emp_id = $this->Generic_model->updateData("users",$empCu,array('user_id'=>$emp_id));
            $this->session->set_flashdata('msg', 'Employee Created Successfully. Credentials has been sent to email');
            redirect('Employee');
        }else{
            $check = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(m.role_id)) as roles FROM `package_features` pf,modules m where m.module_id=pf.module_id and pf.package_id='".$this->session->userdata('package_id')."' and pf.feature_type='Module'")->row();
            $data['clinic_roles'] = $this->db->query("select * from clinic_roles where belongs_to_role IN (".$check->roles.")")->result();
            $data['roles']=$this->Generic_model->getAllRecords('roles', $condition='', $order='');
            $data['profiles']=$this->Generic_model->getAllRecords('profiles', $condition='', $order='');
            $data['view'] = 'employees/employee_add';
            $this->load->view('layout', $data);
        }

    }

    public function sendMail(){
        $from='UMDAA';
        $to = "naveenreddy@umdaa.co";  
        $url = base_url();
        $subject = "Your Credentials to use UMDAA portal";
        
        $message = '<html><head><link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" media="all"></head><body><div class="container"><div class="row"><div class="col-6"><div class="card"><div class="card-body"><h4>Credentials</h4></div></div></div></div></div></body></html>';
        $ok = $this->mail_send->Content_send_all_mail($from,$to,$subject,'','',$message);
    }


    public function employee_update($id) {

        if($this->input->post('submit')){

            // Digital Sign Upload
            $config['upload_path']="./uploads/digital_sign/";
            $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
            $this->load->library('upload');    
            $this->upload->initialize($config); 
            $this->upload->do_upload('digital_sign');
            $fileData=$this->upload->data('file_name');

            if($fileData != "")
            {
                $emp['digital_sign'] = $fileData;
            }

            $emp['first_name']=$this->input->post('first_name');
            $emp['last_name']=$this->input->post('last_name');
            $emp['gender']=$this->input->post('gender');
            $emp['date_of_birth']= date('Y-m-d',strtotime($this->input->post('date_of_birth')));
            $emp['date_of_joining']=date('Y-m-d',strtotime($this->input->post('date_of_joining')));
            $emp['qualification']=$this->input->post('qualification');
            $emp['mobile']=$this->input->post('mobile');
            $emp['adhaar_no']=$this->input->post('adhaar_no');
            $emp['assigned_roles']=implode(",", $this->input->post('assigned_roles'));
            $emp['pan_no']=$this->input->post('pan_no');
            $emp['bank_account_no']=$this->input->post('bank_account_no');
            $emp['email_id']=$this->input->post('email_id');
            $emp['clinic_id']= $this->session->userdata('clinic_id');
            $emp['address']=$this->input->post('address');
            $emp['modified_by']= $this->session->has_userdata('user_id');
            $emp['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->updateData('employees',$emp,array('employee_id'=>$id));
            
            $user['email_id']=$this->input->post('email_id');
            $user['mobile']=$this->input->post('mobile');
            $user['clinic_id']= $this->session->userdata('clinic_id');
            $user['modified_by'] =  $this->session->has_userdata('user_id');
            $user['modified_date_time'] = date('Y-m-d H:i:s');
            // $user['role_id']=$this->input->post('role_id');
            // $user['profile_id']=$this->input->post('profile_id');
            $this->Generic_model->updateData('users',$user,array('user_id'=>$id));

            $pageURL = $this->input->post('back_url');
            redirect($pageURL);

        }else{
            $check = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(m.role_id)) as roles FROM `package_features` pf,modules m where m.module_id=pf.module_id and pf.package_id='".$this->session->userdata('package_id')."' and pf.feature_type='Module'")->row();
            $data['clinic_roles'] = $this->db->query("select * from clinic_roles where belongs_to_role IN (".$check->roles.")")->result();
            $data['roles']=$this->Generic_model->getAllRecords('roles', $condition='', $order='');
            $data['profiles']=$this->Generic_model->getAllRecords('profiles', $condition='', $order='');
            $data['department_list']=$this->db->select('department_id, department_name')->from('department')->get()->result_array();
            $data['state_list']=$this->db->select('state_id, state_name')->from('states')->get()->result_array();
            $data['employee_info']=$this->db->query('select EMP.assigned_roles,EMP.employee_id, EMP.first_name, EMP.last_name, EMP.gender, EMP.date_of_birth, EMP.date_of_joining, EMP.qualification, EMP.employee_code, EMP.email_id, EMP.mobile, EMP.address, EMP.adhaar_no, EMP.pan_no, EMP.bank_account_no, EMP.digital_sign, U.role_id, U.profile_id,U.username from employees EMP, users U where U.user_id = EMP.employee_id and EMP.employee_id='.$id)->row();
            
            $data['view'] = 'employees/employee_edit';
            $this->load->view('layout', $data);
            
        }
    }

    // delete employee details
    public function delEmployee($id){
        $check = $this->db->select("*")->from("users")->where("user_id", $id)->get()->num_rows();
        if($check > 0)
        {
            $this->Generic_model->deleteRecord('users', array('user_id' => $id));
            $this->Generic_model->deleteRecord('employees', array('employee_id' => $id));
            $this->session->set_flashdata('msg','Record Deleted Successfully.');
            redirect('Settings/staff');
        }
        else{
            redirect('Settings/staff');
        }
    }


    public function employee_delete($id) {
        $employee_info['archieve']=1;
        $this->Generic_model->deleteRecord('employees', $employee_info, array('employee_id'=>$id));
        redirect('settings/staff');
    }


    public function save() {

        $this->load->library('excel');
        $clinic_id = $this->session->userdata('clinic_id');
        if ($this->input->post('importfile')) {
            $path = './public/uploads/employee_bulk/';
            $config['upload_path'] = './public/uploads/employee_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
            $this->load->library('upload');
            $this->upload->initialize($config);
            $this->upload->do_upload('userfile'); //uploading file to server
            $fileData=$this->upload->data('file_name');
            $inputFileName = $path . $fileData;

            if(move_uploaded_file($fileData,$path))
            { 
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
            $createArray = array('title', 'first_name', 'last_name', 'gender', 'mobile','email_id', 'date_of_birth', 'date_of_joining', 'qualification', 'phone', 'address', 'user_type', 'status');
            $makeArray = array('title' => 'title', 'first_name' =>'first_name', 'last_name' => 'last_name', 'gender' => 'gender', 'mobile' => 'mobile', 'email_id'=>'email_id', 'date_of_birth'=>'date_of_birth', 'date_of_joining'=>'date_of_joining', 'qualification'=>'qualification', 'phone'=>'phone', 'address'=>'address', 'user_type'=>'user_type', 'status'=>'status');
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
                    $addresses = array();
                    $etitle = $SheetDataKey['title'];
                    $efirst = $SheetDataKey['first_name'];
                    $elast = $SheetDataKey['last_name'];
                    $egender = $SheetDataKey['gender'];
                    $emobile = $SheetDataKey['mobile'];
                    $eemail = $SheetDataKey['email_id'];
                    $edob = $SheetDataKey['date_of_birth'];
                    $edoj = $SheetDataKey['date_of_joining'];
                    $qualification = $SheetDataKey['qualification'];
                    $phone = $SheetDataKey['phone'];
                    $address = $SheetDataKey['address'];
                    $user_type = $SheetDataKey['user_type'];
                    $estatus = $SheetDataKey['status']; 


                    $e_title = filter_var(trim($allDataInSheet[$i][$etitle]), FILTER_SANITIZE_STRING);
                    $e_first = filter_var(trim($allDataInSheet[$i][$efirst]), FILTER_SANITIZE_STRING);
                    $e_last = filter_var(trim($allDataInSheet[$i][$elast]), FILTER_SANITIZE_STRING);
                    $e_gender= filter_var(trim($allDataInSheet[$i][$egender]), FILTER_SANITIZE_STRING);
                    $e_mobile = filter_var(trim($allDataInSheet[$i][$emobile]), FILTER_SANITIZE_STRING);
                    $e_email = filter_var(trim($allDataInSheet[$i][$eemail]), FILTER_SANITIZE_STRING);
                    $e_dob = filter_var(trim($allDataInSheet[$i][$edob]), FILTER_SANITIZE_STRING);
                    $e_dob2 = date('Y-m-d', strtotime($e_dob));
                    $e_doj = filter_var(trim($allDataInSheet[$i][$edoj]), FILTER_SANITIZE_STRING);
                    $e_doj2 = date('Y-m-d', strtotime($e_doj));
                    $e_qualification = filter_var(trim($allDataInSheet[$i][$qualification]), FILTER_SANITIZE_STRING);
                    $e_phone = filter_var(trim($allDataInSheet[$i][$phone]), FILTER_SANITIZE_STRING);
                    $e_address = filter_var(trim($allDataInSheet[$i][$address]), FILTER_SANITIZE_STRING);
                    $e_user_type = filter_var(trim($allDataInSheet[$i][$user_type]), FILTER_SANITIZE_STRING);
                    $e_status = filter_var(trim($allDataInSheet[$i][$estatus]), FILTER_SANITIZE_STRING);

                    $pwd = $this->generateRandomString($length = 8);		
                    $user_password=md5($pwd);
                    $created_by= $this->session->has_userdata('user_id');
                    $modified_by= $this->session->has_userdata('user_id');
                    $created_date_time= date('Y-m-d H:i:s');
                    $modified_date_time = date('Y-m-d H:i:s');
                    $clinic_id=$clinic_id;
                    $role_id=7;
                    $profile_id=9;

                    if($e_status=='Active'||$e_status=='active'){
                        $status=1;
                    }else{
                        $status=0;
                    }

                    $fetchData_user=array('clinic_id'=>$clinic_id,  'user_type'=>$e_user_type, 'email_id'=>$e_email,'password'=>$user_password, 'mobile'=>$e_mobile, 'status'=>$status, 'role_id'=>$role_id, 'profile_id'=>$profile_id, 'status'=>$status, 'created_by'=>$created_by,'modified_by'=>$modified_by, 'created_date_time'=>$created_date_time, 'modified_date_time'=>$modified_date_time);

                    $u_id=$this->Generic_model->insertDataReturnId('users',$fetchData_user);

                    $empcode = 'EMP-'.date('Ymd').$u_id;

                    if($e_user_type=='patient'||$e_user_type=='Patient'){
                        $usr_name['username']=$umr_no;
                    }elseif($e_user_type=='Employee'|| $e_user_type=='employee'){
                        $usr_name['username']=$empcode;
                    }elseif($e_user_type=='Doctor'|| $e_user_type=='doctor'){
                        $usr_name['username']="doctor";
                    }else{
                        $usr_name['username']="null";
                    }

                    $this->Generic_model->updateData('users',$usr_name,array('user_id'=>$u_id));

                    $fetchData_emp = array('employee_id'=>$u_id, 'clinic_id'=>$clinic_id, 'employee_code'=>$empcode,'title' => $e_title, 'first_name' => $e_first, 'last_name' => $e_last, 'gender' => $e_gender, 'mobile' => $e_mobile, 'email_id'=>$e_email, 'date_of_birth'=>$e_dob2, 'date_of_joining'=>$e_doj2, 'qualification'=>$e_qualification, 'phone'=>$e_phone, 'address'=>$e_address, 'status'=>$status, 'created_by'=>$created_by,'modified_by'=>$modified_by, 'created_date_time'=>$created_date_time, 'modified_date_time'=>$modified_date_time);
                    $this->Generic_model->insertData('employees',$fetchData_emp);

                }
            }  
        } else {
            echo "Please import correct file";
        }

        redirect('Employee/employee_list');

    }


}