
<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller 
{

    public function __construct()
    {        
        parent::__construct();

    }
    public function index()
    {
        $is_logged_in = $this->session->userdata('logged_in')['is_logged_in'];
        if(!isset($is_logged_in)){
            $this->load->view('auth/login');
        }
    }

    public function unauthorised(){
        $data['view'] = "errors/unauthorised";
        $this->load->view('layout', $data);
    }



    public function login()
    {
        if($this->input->post('submit')){

            $email=$this->input->post('email');
            $password=md5($this->input->post('password'));

            $this->db->select('*');
            $this->db->from('users');
            $this->db->where("(email_id = BINARY '".$email."' or username= BINARY '".$email."' or mobile='".$email."') and password = BINARY '".$password."'");
            $result =  $this->db->get()->row();
            
            $user_entities = $this->db->query("select * from user_entities uc,profiles p where uc.user_entity_id=p.user_entity_id and p.profile_id='".$result->profile_id."'")->row();
             
            
            $clinic_details =  $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $result->clinic_id));
            
            if($result==TRUE){
                
                $employeeInfo = $this->db->select('employee_id, employee_code, first_name, last_name,assigned_roles')->from('employees')->where('employee_id =',$result->user_id)->get()->row();
                $profile = $this->Generic_model->getFieldValue('profiles','profile_name',array('profile_id' => $result->profile_id));
                $home = getHome($result->profile_id);
                $clinic_id = $result->clinic_id;
                // print_r($employeeInfo);
                // $packageInfo = $this->db->query("select *,max(dp.package_id) as pack_id from doctor_packages dp,packages p where p.package_id=dp.package_id and dp.clinic_id='" . $clinic_id . "'")->row();
                // if($employeeInfo->assigned_roles != ""){
                //     $clinic_setup = "1";
                //     $entities = $this->db->query("select *,group_concat(ue.user_entity_id) as ues from clinic_role_permissions crp,user_entities ue where ue.user_entity_id=crp.entity_id and crp.clinic_role_id IN (".$employeeInfo->assigned_roles.") and crp.clinic_id='".$clinic_id."' group by ue.parent_id")->row();
                // }
                // else{
                //     $clinic_setup = "0";
                //     $entities = $this->db->query("select * FROM `package_features` pf,modules m,user_entities ue where m.module_id=pf.module_id and ue.user_entity_id=pf.entity_id and pf.feature_type='Module' and pf.package_id='".$packageInfo->package_id."' and m.role_id='".$result->role_id."' and ue.parent_id='0' order by ue.position ASC")->row();
                // }
                // echo $this->db->last_query();
                // exit();
                $sess_data=array(
                    'user_id'  =>$result->user_id,
                    'user_name'=>$result->username,
                    'employee_name' => $employeeInfo->first_name." ".$employeeInfo->last_name,
                    'home' => $home,
                    'setup' => $clinic_setup,
                    // 'package_name' => $packageInfo->package_name,
                    // 'package_id' => $packageInfo->pack_id,
                    'clinic_id'=>$result->clinic_id,
                    'clinic_logo'=>$clinic_details->clinic_logo,
                    'clinic_emblem'=>$clinic_details->clinic_emblem,
                    'clinic_name'=>$clinic_details->clinic_name,
                    // 'clinic_role_id'=>$employeeInfo->clinic_role_id,
                    // 'assigned_roles'=>$employeeInfo->assigned_roles,
                    'pharmacy_discount'=>$clinic_details->pharmacy_discount,
                    'lab_discount'=>$clinic_details->lab_discount,
                    'email' =>$result->email_id,
                    'role_id'  =>$result->role_id,
                    'profile_id'  =>$result->profile_id,
                    'profile'  =>$profile,
                    'user_type'=>$result->user_type,
                    'is_logged_in'=>TRUE,
                    'home_page'=>base_url($entities->entity_url)
                );
                // if($result->clinic_id == "0"){
                //     redirect(base_url('Articles'));
                // }
                // else{
                //     redirect(base_url('Dashboard'));
                // }
                $this->session->set_userdata($sess_data);
                // if($result->clinic_id == "0"){
                //     redirect(base_url('Pricing'));
                // }
                // else{
                //     redirect(base_url($entities->entity_url),'refresh');
                // }
                 
                redirect(base_url('Dashboard'));
            }else{
                $data['msg'] = 'Invalid Email or Password!';
                $this->load->view('auth/login',$data);
            }

        }else{

            $this->load->view('auth/login');
        }

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('Authentication/login');
    }

}
?>