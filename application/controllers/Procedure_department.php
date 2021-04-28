<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Procedure_department extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
    $data['procedure_department']=$this->db->query("select * from procedure_department a inner join department b on a.department_id = b.department_id group by a.department_id")->result();
	$data['view'] = 'procedure_department/procedure_department_list';
    $this->load->view('layout', $data);
}
public function procedure_department_add(){
    if($this->input->post('submit')){
        $procedure=$this->input->post('procedure');       
        for($i=0;$i<count($procedure);$i++){
            $data['department_id']=$this->input->post('department');
            $data['medical_procedure_id'] = $procedure[$i];    
            // $data['status']=$this->input->post('status');
            $data['created_by']=$this->session->userdata('user_id');
            $data['modified_by']=$this->session->userdata('user_id');
            $data['created_date_time']=date('Y-m-d H:i:s');
            $data['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->insertData('procedure_department',$data);
            
        }
        redirect('Procedure_department/procedure_department_add');  
    }else{
        $data['department_list']=$this->Generic_model->getAllRecords('department',$condition='',$order='');
        $data['procedure_list']=$this->Generic_model->getAllRecords('medical_procedures',$condition='',$order='');

        $data['view'] = 'procedure_department/procedure_department_add';
        $this->load->view('layout', $data);
    }
}

public function procedure_department_view($id){
    $data['procedure_department']=$this->db->query("select * from procedure_department a inner join department b on a.department_id = b.department_id where a.department_id='".$id."' group by a.department_id")->row();

     $data['procedures']=$this->db->query("select * from procedure_department a inner join medical_procedures c on a.medical_procedure_id = c.medical_procedure_id where a.department_id='".$data['procedure_department']->department_id."'")->result();
   
    $data['view'] = 'procedure_department/procedure_department_view';
    $this->load->view('layout', $data);

}
public function delt_procedure($id){
    $this->db->query("delete from procedure_department where medical_procedure_id=".$id);
    redirect('Procedure_department');
}

public function getprocedure(){
    $department_id = $this->input->post('department_id');
    $query = $this->db->query("select * from procedure_department where department_id='".$department_id."'")->result();
    $procedure_arry = array();
    foreach ($query as $value) {
        $procedure_arry[] = $value->medical_procedure_id;
    }
    $procedure_ids = implode(',', $procedure_arry);
    if($procedure_ids==NULL || $procedure_ids=="" ){
        $procedures=$this->db->query('select * from medical_procedures')->result();
    }else{
        $procedures=$this->db->query('select * from medical_procedures where medical_procedure_id NOT IN ('.$procedure_ids.')')->result();
    }
    $output ='';
    $output .='<option>--Select--</option>';
    foreach ($procedures as $value) {
        $output .='<option value="'.$value->medical_procedure_id.'">'.$value->medical_procedure.'</option>';
    }
    echo $output; 
}

public function mapped_procedures(){
  $dept_id = $this->input->post('department_id');
  $output ='';
  $query = $this->db->query("select * from procedure_department a inner join medical_procedures b on(a.medical_procedure_id = b.medical_procedure_id) where a.department_id=".$dept_id)->result();
 if(count($query)>0){
$output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">MAPPED PROCEDURES <span class="pull-right">'.count($query).'</span></th>';
   foreach ($query as  $value) { 
    $output .='<tr id="'.$value->procedure_department_id.'"><td style="padding: 15px;">'.$value->medical_procedure.'</td><td style="padding: 15px;"><a href="javascript:;" id="'.$value->procedure_department_id.'" class="btn btn-danger btn-xs delete-procedure"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>';
  }
$output .= '</tbody></table>'; 
 }
 else{
  $output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">NO PROCEDURES MAPPED</th></tbody></table>';
 }
 
  echo $output;
  
 }

  public function delete_mapped_procedure(){
    
                $this->db->query("DELETE from procedure_department where procedure_department_id='".$this->input->post('pid')."'");

    }
 

}