<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Features extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }
    public function index(){
        $data['features_list'] = $this->Generic_model->getAllRecords('features');
        $data['modules'] = $this->Generic_model->getAllRecords('modules');
        $data['view'] = 'features/features_list';
        $this->load->view('layout', $data);
    }

    public function features_add(){
        $user_id = $this->session->userdata('user_id');
        extract($_POST);
        if(isset($_POST['feature_add'])){
            $data['feature_name'] = $feature_name;
            if($feature_type == "Module"){
                $data['module_id'] = $module;
            }
            $data['feature_type'] = $feature_type;
            $data['status'] = 1;
            $data['created_by'] = $user_id;
            $data['modified_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('features', $data);
        }
        redirect('Features');
    }

    public function feature_delete($feature_id){
        $check = $this->Generic_model->getSingleRecord('features', array('feature_id'=>$feature_id));
        if(count($check) > 0){
            $this->Generic_model->deleteRecord('features', array('feature_id'=>$feature_id));
        }
        redirect('Features');
    }
 
    public function feature_edit(){
        extract($_POST);
        if(isset($_POST['feature_edit'])){
            $data['feature_name'] = $feature_name;
            if($features_type == "Module"){
                $data['module_id'] = $umodule;
            }
            $data['feature_type'] = $features_type;
            $data['status'] = 1;
            $data['modified_by'] = $user_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData('features', $data, array('feature_id'=>$feature_id));
        }
        redirect('Features');
    }
}