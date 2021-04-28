<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '1024M');
//library for generating code
include "phpqrcode/qrlib.php";
error_reporting(0);
class ModuleEntities extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->has_userdata('is_logged_in')) {
            redirect('Authentication/login');
        }
    }

    public function index(){
        $data['modules'] = $this->Generic_model->getAllRecords('modules');
        $data['view'] = "modules/modules_list";
        $this->load->view('layout', $data);
    }

    public function module_entities($module_id){
        $data['moduleInfo'] = $this->Generic_model->getSingleRecord('modules', array('module_id'=>$module_id));
        $data['user_entities'] = $this->Generic_model->getAllRecords('user_entities');
        $data['module_entities'] = $this->db->query("select * from module_entities me,user_entities ue where ue.user_entity_id=me.entity_id and me.module_id='".$module_id."'")->result();
        $data['selected_entities'] = $this->db->query("select GROUP_CONCAT(ue.user_entity_id) as entities from module_entities me,user_entities ue where ue.user_entity_id=me.entity_id and me.module_id='".$module_id."'")->row();
        $data['module_id'] = $module_id;

        $data['view'] = "modules/module_entities";
        $this->load->view('layout', $data);

    }

    public function delMap($mapId){
        $check = $this->Generic_model->getSingleRecord('module_entities', array('module_entity_id'=>$mapId));
        if(count($check) > 0){
            $this->Generic_model->deleteRecord('module_entities', array('module_entity_id'=>$check->module_entity_id));
            redirect('ModuleEntities/module_entities/'.$check->module_id);
        }
    }

    public function getEntities(){
        extract($_POST);
        $info = $this->Generic_model->getAllRecords('user_entities');
        if(count($info) > 0){
            $i = 0;
            $selected_entities = $this->db->query("select GROUP_CONCAT(ue.user_entity_id) as entities from module_entities me,user_entities ue where ue.user_entity_id=me.entity_id and me.module_id='".$module_id."'")->row();
            $entities = explode(",", $selected_entities->entities);
            foreach($info as $value){
                $parentInfo = userEntityInfo($value->parent_id);
                if(in_array($value->user_entity_id, $entities)){
                    continue;
                }
                if($value->parent != 0){
                    $parent = '('. $parentInfo->user_entity_name .')';
                }
                else{
                    $parent = '';
                }
                $data[$i]['id'] = $value->user_entity_id;
                $data[$i]['text'] = $value->user_entity_name;
                $data[$i]['html'] = $value->user_entity_name. $parent . ' <span class="badge badge-danger pull-right">' . $value->category . '</span>';
                $data[$i]['title'] = $value->user_entity_name;
                $i++;
            }
            echo json_encode($data);
        }
    }

    public function addNewModule(){
        extract($_POST);
        if(isset($_POST['submitModule']))
        {
            $data['module_name'] = $module_name;
            $data['status'] = 1;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_Date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('modules', $data);
            $this->session->set_flashdata('msg', 'Successfully Added');
        }
        redirect('ModuleEntities');
    }

    public function addEntities(){
        if(isset($_POST['add'])){
            extract($_POST);
            foreach($entities as $val){
                $data['module_id'] = $module_id;
                $data['entity_id'] = $val;
                $data['status'] = 1;
                $data['created_by'] = $this->session->userdata('user_id');
                $data['modified_by'] = $this->session->userdata('user_id');
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_Date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData("module_entities", $data);
                unset($data);
            }
            redirect('ModuleEntities');
        }
    }
}
?>