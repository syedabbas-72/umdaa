<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '1024M');
//library for generating code
include "phpqrcode/qrlib.php";
error_reporting(0);
class ClinicRoles extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->has_userdata('is_logged_in')) {
            redirect('Authentication/login');
        }
    }


    public function DivideModules(){
        
    }

    public function SaveRolePermissions(){
        extract($_POST);
        $clinic_id = $this->session->userdata('clinic_id');
        $this->Generic_model->deleteRecord('clinic_role_permissions', array('clinic_id'=>$clinic_id,'clinic_role_id'=>$clinic_role_id));
        foreach($main as $value){
            // echo $value."<br>";
            $data['clinic_id'] = $clinic_id;
            $data['clinic_role_id'] = $clinic_role_id;
            $data['entity_id'] = $value;
            $data['status'] = 1;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('clinic_role_permissions', $data);
            unset($data);
        }
        redirect('ClinicRoles/role_setup/'.$clinic_role_id);
    }

    public function index(){
        $clinic_id = $this->session->userdata('clinic_id');
        $check = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(m.role_id)) as roles FROM `package_features` pf,modules m where m.module_id=pf.module_id and pf.package_id='".$this->session->userdata('package_id')."' and pf.feature_type='Module'")->row();
        if($clinic_id == 0){
            $data['clinicRoles'] = $this->db->query("select * from clinic_roles")->result();
        }
        else{
            $data['clinicRoles'] = $this->db->query("select * from clinic_roles where belongs_to_role IN (".$check->roles.")")->result();
        }
        

        // echo $this->db->last_query();
        // exit;        
        $data['view'] = "clinic_setup/clinic_roles";
        $this->load->view('layout', $data);
    }

    public function storePermissions(){
        if(isset($_POST)){
            extract($_POST);
            $clinic_id = $this->session->userdata('clinic_id');
            // echo "<pre>";print_r($_POST);echo "</pre>";
            // exit;
            $check = $this->Generic_model->getAllRecords("clinic_role_permissions", array('clinic_role_id'=>$clinic_role_id,'clinic_id'=>$clinic_id));
            if($role_id == "7" || $role_id == "2"){
                $propertyCheck = $this->Generic_model->getSingleRecord("clinic_role_property_permissions", array('clinic_id'=>$clinic_id,'clinic_role_id'=>$clinic_role_id));
                if(count($propertyCheck) > 0){
                    $prodata['pharmacy_edit_access'] = $pharmacy_edit_access;
                    $prodata['pharmacy_del_access'] = $pharmacy_delete_access;
                    $this->Generic_model->updateData('clinic_role_property_permissions', $prodata, array('clinic_role_property_permission_id'=>$propertyCheck->clinic_role_property_permission_id));
                }
                else{
                    $prodata['pharmacy_edit_access'] = $pharmacy_edit_access;
                    $prodata['pharmacy_del_access'] = $pharmacy_delete_access;
                    $prodata['clinic_id'] = $clinic_id;
                    $prodata['clinic_role_id'] = $clinic_role_id;
                    $prodata['created_by'] = $this->session->userdata('user_id');
                    $prodata['created_date_time'] = date("Y-m-d H:i:s");
                    $prodata['modified_by'] = $this->session->userdata('user_id');
                    $prodata['modified_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->insertData('clinic_role_property_permissions', $prodata);
                }
            }
            
            if(count($check) > 0){
                $this->Generic_model->deleteRecord('clinic_role_permissions', array('clinic_role_id'=>$clinic_role_id,'clinic_id'=>$clinic_id));
            }
            if(count($entities) > 0){
                foreach($entities as $value){
                    unset($data);
                    $data['clinic_role_id'] = $clinic_role_id;
                    $data['role_id'] = $role_id;
                    $data['clinic_id'] = $clinic_id;
                    $data['entity_id'] = $value;
                    $data['status'] = 1;
                    $data['created_by'] = $this->session->userdata('user_id');
                    $data['created_date_time'] = date("Y-m-d H:i:s");
                    $data['modified_by'] = $this->session->userdata('user_id');
                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->insertData('clinic_role_permissions', $data);
                }
            }
            redirect('ClinicRoles/role_setup/'. $clinic_role_id);
        }
    }

    public function role_setup($role_id){
        $check = $this->Generic_model->getSingleRecord('clinic_roles', array('clinic_role_id'=>$role_id));
        $belongs_to_role = $check->belongs_to_role;

        $clinic_id = $this->session->userdata('clinic_id');
        if($belongs_to_role == 2){
            $ent = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(m.role_id)) as roles FROM `package_features` pf,modules m where m.module_id=pf.module_id and pf.package_id='".$this->session->userdata('package_id')."' and pf.feature_type='Module'")->row();
            $clRoles =  $this->db->query("select group_concat(DISTINCT(belongs_to_role)) as role from clinic_roles where belongs_to_role IN (".$ent->roles.")")->row();
            $cond = " and m.role_id IN (".$clRoles->role.")";
        }
        else{
            $cond = " and m.role_id='".$belongs_to_role."'";
        }
        
        
        $data['entities'] = $this->db->query("select * from modules m,package_features pf where m.module_id=pf.module_id  and pf.feature_type='Module' and pf.package_id='".$this->session->userdata('package_id')."'".$cond)->result();
        // echo $this->db->last_query();
        // exit;
        $data['propertyPermissions'] = $this->db->query("select * from clinic_role_property_permissions where clinic_id='".$clinic_id."' and clinic_role_id='".$role_id."'")->row();
        $data['clinicRolesCheck'] = $this->db->query("select * from clinic_role_permissions where clinic_id='".$clinic_id."' and clinic_role_id='".$role_id."'")->row();
        $data['clinic_role_name'] = $check->clinic_role_name;
        $data['clinic_role_id'] = $check->clinic_role_id;
        $data['role_id'] = $belongs_to_role;
        $data['clinic_id'] = $this->session->userdata('clinic_id');
        $data['view'] = "clinic_setup/clinic_roles_setup";
        $this->load->view('layout', $data);
    }

    public function setup(){
        $clinic_id = $this->session->userdata('clinic_id');
        $data['roles'] = $this->Generic_model->getAllRecords('clinic_roles', array('clinic_id'=>$clinic_id));
        $data['view'] = "clinic_setup/clinic_setup";
        $this->load->view('layout', $data);
    }

    public function addRole(){
        $clinic_id = $this->session->userdata('clinic_id');
        extract($_POST);
        $check = $this->db->query("select * from clinic_roles where clinic_role_name = '".$role_name."'")->row();
        if(count($check) > 0){
            echo "0";
        }
        else{
            // $data['clinic_id'] = $clinic_id;
            $data['clinic_role_name'] = $role_name;
            $data['status'] = 1;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('clinic_roles', $data);
            echo "1";
        }
    }

}
?>