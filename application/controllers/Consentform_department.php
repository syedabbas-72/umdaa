<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Consentform_department extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
    $data['consent_department']=$this->db->select("*")->from("consent_form_department a")->join("department b","a.department_id = b.department_id")->group_by("a.department_id")->result();
	$data['view'] = 'consent_department/consent_department_list';
    $this->load->view('layout', $data);
}
public function consent_department_add(){
    if($this->input->post('submit')){
        $consent_form=$this->input->post('consent');       
        for($i=0;$i<count($consent_form);$i++){
            $data['department_id']=$this->input->post('department');
            $data['consent_form_id'] = $consent_form[$i];    
            $data['created_by']=$this->session->userdata('user_id');
            $data['modified_by']=$this->session->userdata('user_id');
            $data['created_date_time']=date('Y-m-d H:i:s');
            $data['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->insertData('consent_form_department',$data);
        }
        redirect('Consentform_department/consent_department_add');  
    }else{
        $data['department_list'] = $this->db->query("select * from department order by department_name ASC")->result();
        $data['consent_form_list'] = $this->Generic_model->getAllRecords('consent_form',$condition='',$order='');
        $data['view'] = 'consent_department/consent_department_add';
        $this->load->view('layout', $data);
    }
}

public function consent_department_view($id){
    $data['consent_department']=$this->db->select("*")->from("consent_form_department a")->join("department b","a.department_id = b.department_id")->where("a.department_id='".$id."'")->group_by("a.department_id")->get()->row();

     $data['consent_forms']=$this->db->select("*")->from("consent_form_department a")->join("consent_form c","a.consent_form_id = c.consent_form_id")->where("a.department_id='".$data['consent_department']->department_id."'")->get()->result();
   
    $data['view'] = 'consent_department/consent_department_view';
    $this->load->view('layout', $data);

}
public function delt_consent_form($id){
    $this->db->query("delete from consent_form_department where consent_form_id=".$id);
    redirect('Consentform_department');
}

public function getConsentforms(){

    $department_id = $this->input->post('department_id');
    $query = $this->db->select("*")->from("consent_form_department")->where("department_id='".$department_id."'")->get()->result();
    $consent_arry = array();
    
    $count = 0;

    foreach ($query as $value) {
        $consent_arry[] = $value->consent_form_id;
    }

    $count = count($consent_arry);
    
    $consent_ids = implode(',', $consent_arry);

    if((int)$count > 0){
         $consent_forms = $this->db->query('select * from consent_form where consent_form_id NOT IN ('.$consent_ids.')')->result();
    }else{
        $consent_forms = $this->db->query('select * from consent_form')->result();
    }

    $output ='';
    $output .='<option> -- Choose Consent Forms -- </option>';
    
    foreach ($consent_forms as $value) {
        $output .='<option value="'.$value->consent_form_id.'">'.$value->consent_form_title.'</option>';
    }
    
    echo $output;

}

public function mapped_consentforms(){
    $dept_id = $this->input->post('department_id');
    $output ='';
    // select("*")->from("consent_form_department a")->join("consent_form b","a.consent_form_id = b.consent_form_id")->where("a.department_id=".$dept_id)->get()
    
    $query = $this->db->query("select * from consent_form_department a, consent_form b where a.consent_form_id = b.consent_form_id and a.department_id='".$dept_id."' order by b.consent_form_title ASC")->result();
    if(count($query)>0){
        $output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">MAPPED CONSENTFORMS <span class="pull-right">'. sizeof($query).'</span></th>';
        foreach ($query as  $value) { 
            $output .='<tr id="'.$value->consent_form_department_id.'"><td style="padding: 15px;">'.$value->consent_form_title.'</td><td style="padding: 15px;"><a href="javascript:;" id="'.$value->consent_form_department_id.'" class="btn btn-danger btn-xs delete-consentform"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>';
        }
        $output .= '</tbody></table>'; 
    }
    else{
        $output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">NO CONSENTFORMS MAPPED</th></tbody></table>';
    }
    echo $output;
}

  public function delete_mapped_consentform(){
    
                $this->db->query("DELETE from consent_form_department where consent_form_department_id='".$this->input->post('pid')."'");

    }

 

}