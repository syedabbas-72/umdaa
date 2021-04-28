<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class AndroidVersion extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))
    {
        redirect('Authentication/login');
    }      
 }

 public function index(){
     $data['versionInfo'] = $this->db->select("*")->from("app_version")->where("app_category='Doctors'")->get()->row();
     $data['view'] = "androidVersion/androidVersion";
     $this->load->view('layout',$data);
 }

//  Change Version
public function changeVersion(){
    extract($_POST);
    $data['app_version_name'] = $version;
    $data['app_version_id'] = $versionID;
    $res = $this->Generic_model->updateData("app_version",$data,array('app_id'=>$app_id));
    if($res)
        echo "1";
    else
        echo "0";
}

}
