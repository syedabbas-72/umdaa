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
        
        $data['doctor_list'] = $this->db->select("*")->from("clinic_doctor a")->join("doctors b", "a.doctor_id=b.doctor_id")->join("department d", "d.department_id = b.department_id")->where("clinic_id", $clinic_id)->get()->result();
        
        $data['view'] = 'doctor/doctor_list';
        $this->load->view('layout', $data);
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
        $param      = $_POST;
        $sclinic_id = $this->session->userdata('clinic_id');
        $cond       = '';
        $condition  = '';
        if ($sclinic_id != 0) {
            $cond = "where clinic_id=" . $sclinic_id . "";
        }
        $suser_id      = $this->session->has_userdata('user_id');
        $check_profile = $this->db->select("profile_id")->from("profiles")->where("clinic_id='" . $this->input->post("clinic_id") . "' and profile_name='Doctor'")->get()->row();
        $check_role    = $this->db->select("role_id")->from("std_uac_roles")->where("role_name='Doctor'")->get()->row();
        
        
        if (count($param) > 0) {
            
            // echo "<pre>";print_r($param);exit;
            $rand                                = str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
            $colorcode                           = '#' . $rand;
            $type                                = $this->input->post('type');
            //$pwd = $this->generateRandomString($length = 8);
            $pwd                                 = '1234';
            $param_1['username']                 = $this->input->post("reg_code");
            $param_1['email_id']                 = $this->input->post("email");
            $param_1['clinic_id']                = $this->input->post("clinic_id");
            $param_1['mobile']                   = $this->input->post("mobile");
            $param_1['password']                 = md5($pwd);
            $param_1['user_type']                = "doctor";
            $param_1['role_id']                  = $check_role->role_id;
            $param_1['profile_id']               = $check_profile->profile_id;
            $param_1['status']                   = '1';
            $param_1['last_logged_in_date_time'] = date("Y-m-d H:i:s");
            $param_1['created_by']               = "1";
            $param_1['created_date_time']        = date("Y-m-d H:i:s");
            $param_1['modified_by']              = "1";
            $param_1['modified_date_time']       = date("Y-m-d H:i:s");
            $user_id                             = $this->Generic_model->insertDataReturnId('users', $param_1);
            if ($user_id != "" || $user_id != null) {
                $data['doctor_id']         = $user_id;
                $data['doctor_type']       = $this->input->post('type');
                //$data['consulting_time']=$this->input->post('consulting_time');
                $data['salutation']        = 'Dr'; //$this->input->post("salutation");
                $data['first_name']        = $this->input->post('first_name');
                $data['last_name']         = $this->input->post('last_name');
                $data['registration_code'] = $this->input->post('reg_code');
                $data['gender']            = $this->input->post('gender');
                $data['qualification']     = $this->input->post('qualification');
                $data['address']           = $this->input->post('address');
                $data['state_id']          = $this->input->post('state');
                $data['pincode']           = $this->input->post('pincode');
                $data['color_code']        = $colorcode;
                $data['mobile']            = $this->input->post('mobile');
                $data['email']             = $this->input->post('email');
                
                $data['status']           = "1";
                $data['created_by']       = "1";
                // $data['last_modified_by'] = date("Y-m-d H:i:s");
                //$data['clinic_id'] = $clinic_id;
                if ($type == "clinic") {
                    $data['year_of_passing'] = $this->input->post('year_pass');
                    $data['university']      = $this->input->post('university');
                } else if ($type == "inhouse") {
                    //$data['working_hospital']=$this->input->post('working_hospital');
                    $data['department_id'] = $this->input->post('department');
                    $data['experience']    = $this->input->post('experience');
                } else if ($type == "consultant") {
                    //$data['working_hospital']=$this->input->post('working_hospital');
                    $data['department_id'] = $this->input->post('department');
                    $data['experience']    = $this->input->post('experience');
                }
                
                $ok = $this->Generic_model->insertData('doctors', $data);
                
                
                
                if ($ok) {
                    
                    $package_price_info        = $this->db->select("*")->from("subscription_price")->where("package_id='" . $this->input->post('package_id') . "'")->order_by("package_price_id", "desc")->get()->row();
                    $rdate                     = ($package_price_info->no_days - 30);
                    $pdata['clinic_id']        = $this->input->post('clinic_id');
                    $pdata['doctor_id']        = $user_id;
                    $pdata['package_id']       = $this->input->post('package_id');
                    $pdata['package_price_id'] = $package_price_info->package_price_id;
                    if ($this->input->post('package_subscription_date') != '') {
                        $pdata['package_subscription_date'] = $this->input->post('package_subscription_date');
                        $pdata['package_expiry_date']       = date('Y-m-d', strtotime("+" . $package_price_info->no_days . " days", strtotime($this->input->post('package_subscription_date'))));
                        $pdata['package_renewal_date']      = date('Y-m-d', strtotime("+" . $rdate . " days", strtotime($this->input->post('package_subscription_date'))));
                    }
                    $pdata['status']             = 1;
                    $pdata['created_by']         = $suser_id;
                    $pdata['modified_by']        = $suser_id;
                    $pdata['created_date_time']  = date("Y-m-d H:i:s");
                    $pdata['modified_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->insertData('doctor_package', $pdata);
                    $clinic_doctor['clinic_id']       = $this->input->post('clinic_id');
                    $clinic_doctor['doctor_id']       = $user_id;
                    $clinic_doctor['consulting_fee']  = $this->input->post('consulting_fee');
                    $clinic_doctor['consulting_time'] = $this->input->post('consulting_time');
                    $clinic_doctor['review_days']     = $this->input->post('review_days');
                    $clinic_doctor['from_date']       = date("Y-m-d");
                    $clinic_doctor['from_date']       = date("Y-m-d");
                    
                    $ok = $this->Generic_model->insertData('clinic_doctor', $clinic_doctor);
                    
                    
                    $from    = 'UMDAA';
                    $to      = $this->input->post('email');
                    $subject = "Login Credentials ";
                    $header  = "<html><body><h3>Hi " . $this->input->post("title") . " " . ucwords($this->input->post('first_name')) . ",</h3><br/><b>UserName :</b>" . $this->input->post('email') . "," . $this->input->post('mobile') . "," . $this->input->post('reg_code') . " <br/> <b> password :</b>" . $pwd . "<br/><b>URL :</b>" . base_url("Authentication/login") . "</p><br/><br/><h3>Thanks and Regards</h3><b>UMDAA TEAM</b>";
                    //$message = $message;
                    
                    //$ok = $this->mail_send->content_mail_ncl_all($from, $to, $subject, '', '',$header);
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
        } else {
            $data['department_list'] = $this->Generic_model->getAllRecords("department", $condition = '', $order = '');
            $data['state_list']      = $this->Generic_model->getAllRecords("states", $condition = '', $order = '');
            $data['clinic_list']     = $this->Generic_model->getAllRecords("clinics", $condition = '', $order = '');
            $data['packages_list']   = $this->Generic_model->getAllRecords("subscription", $condition = '', $order = '');
            $data['view']            = 'doctor/doctor_add';
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