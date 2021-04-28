<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Followup_templates extends MY_Controller {
public function __construct() 
{
    parent::__construct();
     if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
}
 public function index(){
 	$data['templates_list'] = $this->db->query("select * from  followup")->result();
 	$data['view'] = 'followup_templates/template_list';
 	$this->load->view('layout', $data);
 }
 public function insert(){
 	$param = $this->input->post();
 	if(count($param) >0){
 		$user_id = $this->session->has_userdata('user_id');
 		$param_1['followup_name'] = $this->input->post("name");
 		//$param_1['department_id'] = $this->input->post("dept");
 		$param_1['created_by'] = $user_id;
 		$param_1['modified_by'] = $user_id;
 		$param_1['created_date_time'] = date("Y-m-d H:i:s");
 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
 		$ok = $this->Generic_model->insertData('followup',$param_1);
 		if($ok ==1){
 			redirect('followup_templates');
 		}else{
 			redirect('followup_templates');
 		}
 	}else{
 		$data['dept_list'] = $this->db->query("select * from  department")->result();
	 	$data['view'] = 'followup_templates/template_insert';
	 	$this->load->view('layout', $data);
	}
 }
 public function followup_department(){
 	$param = $this->input->post();

 	if(count($param) >0){
    $followup_id=$this->input->post('followup_id');       
        for($i=0;$i<count($followup_id);$i++){
 		$user_id = $this->session->has_userdata('user_id');
 		$param_1['followup_id'] = $followup_id[$i];
 		$param_1['department_id'] = $this->input->post("dept");
 		$param_1['created_by'] = $user_id;
 		$param_1['modified_by'] = $user_id;
 		$param_1['created_date_time'] = date("Y-m-d H:i:s");
 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
 		$ok = $this->Generic_model->insertData('followup_department',$param_1);
 	
 	
 		
  }
      redirect('followup_templates/followup_department');
 	}else{
 		$data['dept_list'] = $this->db->query("select * from  department")->result();
 		$data['followup_list'] = $this->db->query("select * from  followup")->result();
	 	$data['view'] = 'followup_templates/followup_department';
	 	$this->load->view('layout', $data);
	}
 }
  public function delete_parameter(){
    
                $this->db->query("UPDATE followup_parameter
SET
  parameter_id =
    TRIM(BOTH ',' FROM REPLACE(CONCAT(',', parameter_id, ','), ',".$this->input->post('pid').",', ','))
WHERE
  FIND_IN_SET(".$this->input->post('pid').", parameter_id)");


    }

    public function update_parameter(){
    	$this->db->query("update parameters set parameter_name = '".trim($this->input->post('pname'))."' WHERE parameter_id  = '".$this->input->post("pid")."'");

    }
    public function create_clinical_parameter(){
     	$user_id = $this->session->has_userdata('user_id');

    $param['parameter_name'] = $this->input->post("pname");
    $param['parameter_type'] = $this->input->post("ptype");
    $param['created_by'] = $user_id;
    $param['modified_by'] = $user_id;
    $param['created_date_time'] = date("Y-m-d H:i:s");
    $param['modified_date_time'] = date("Y-m-d H:i:s");
    $ok = $this->Generic_model->insertData('parameters',$param);
    $insert_id = $this->db->insert_id();

    $check_fp = $this->db->query("select * from followup_parameter where followup_id = '".$this->input->post('fid')."'")->row();
    if($check_fp){
    	$this->db->query("update followup_parameter set parameter_id = concat(parameter_id, ',','$insert_id') WHERE followup_id  = '".$this->input->post("fid")."'");
    }
    else{
    	 $user_id = $this->session->has_userdata('user_id');
    $fp['parameter_id'] = $insert_id;
    $fp['followup_id'] = $this->input->post("fid");
    $fp['created_by'] = $user_id;
    $fp['modified_by'] = $user_id;
    $fp['created_date_time'] = date("Y-m-d H:i:s");
    $fp['modified_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('followup_parameter',$fp);
    }
    
    
          if($ok == 1){
          	echo "<tr class='clinical' id='".$insert_id."'><td style='padding: 15px;'>".$this->input->post("pname")."</td><td style='padding: 15px;'><a id='".$insert_id."' class='btn btn-danger btn-xs  delete-parameter'><i class='fa fa-times' aria-hidden='true'></i></a><a style='margin-left:10px' href='javascript:; id='".$insert_id."' class='btn btn-info btn-xs edit-parameter'><i class='fa fa-edit' aria-hidden='true'></i></a><a style='margin-left:10px;display: none' href='javascript:;'' id='".$insert_id."' class='btn btn-success btn-xs update-parameter'><i class='fa fa-check' aria-hidden='true'></i></a></td></tr>";
          } else{
          	echo "error";
          } 
          $this->update_clinical_param_json();   
    }

      public function update_clinical_parameter(){
     	$user_id = $this->session->has_userdata('user_id');

    $get_param_id = $this->db->query("select * from parameters where parameter_name='".$this->input->post('param')."'")->row();

    $check_fp = $this->db->query("select * from followup_parameter where followup_id = '".$this->input->post("fid")."'")->row();
    if(count($check_fp)>0){
    	$ok = $this->db->query("update followup_parameter set parameter_id = concat(parameter_id, ',','$get_param_id->parameter_id') WHERE followup_id  = '".$this->input->post("fid")."'");
    	$param_id = $get_param_id->parameter_id;
    }
    else{
    $user_id = $this->session->has_userdata('user_id');
    $param['parameter_id'] = $get_param_id->parameter_id;
    $param['followup_id'] = $this->input->post("fid");
    $param['created_by'] = $user_id;
    $param['modified_by'] = $user_id;
    $param['created_date_time'] = date("Y-m-d H:i:s");
    $param['modified_date_time'] = date("Y-m-d H:i:s");
    $ok = $this->Generic_model->insertData('followup_parameter',$param);
    $param_id = $this->db->insert_id();
    }
    
     if($ok == 1){
          	echo "<tr class='clinical' id='".$get_param_id->parameter_id."'><td style='padding: 15px;'>".$this->input->post("param")."</td><td style='padding: 15px;'><a id='".$get_param_id->parameter_id."' class='btn btn-danger btn-xs  delete-parameter'><i class='fa fa-times' aria-hidden='true'></i></a><a style='margin-left:10px' href='javascript:; id='".$insert_id."' class='btn btn-info btn-xs edit-parameter'><i class='fa fa-edit' aria-hidden='true'></i></a><a style='margin-left:10px;display: none' href='javascript:;'' id='".$insert_id."' class='btn btn-success btn-xs update-parameter'><i class='fa fa-check' aria-hidden='true'></i></a></td></tr>";
          } else{
          	echo "error";
          } 
    
            
    }
     public function update_lab_parameter(){
     	    	$user_id = $this->session->has_userdata('user_id');

    $get_param_id = $this->db->query("select * from parameters where parameter_name='".$this->input->post('param')."'")->row();

    $check_fp = $this->db->query("select * from followup_parameter where followup_id = '".$this->input->post("fid")."'")->row();
    if(count($check_fp)>0){
    	$ok = $this->db->query("update followup_parameter set parameter_id = concat(parameter_id, ',','$get_param_id->parameter_id') WHERE followup_id  = '".$check_fp->followup_id."'");
    	$param_id = $get_param_id->parameter_id;
    }
    else{
    $user_id = $this->session->has_userdata('user_id');
    $param['parameter_id'] = $get_param_id->parameter_id;
    $param['followup_id'] = $this->input->post("fid");
    $param['created_by'] = $user_id;
    $param['modified_by'] = $user_id;
    $param['created_date_time'] = date("Y-m-d H:i:s");
    $param['modified_date_time'] = date("Y-m-d H:i:s");
    $ok = $this->Generic_model->insertData('followup_parameter',$param);
    $param_id = $this->db->insert_id();
    }
    
     if($ok == 1){
          	echo "<tr class='lab' id='".$get_param_id->parameter_id."'><td style='padding: 15px;'>".$this->input->post("param")."</td><td style='padding: 15px;'><a id='".$get_param_id->parameter_id."' class='btn btn-danger btn-xs  delete-parameter'><i class='fa fa-times' aria-hidden='true'></i></a><a style='margin-left:10px' href='javascript:; id='".$insert_id."' class='btn btn-info btn-xs edit-parameter'><i class='fa fa-edit' aria-hidden='true'></i></a><a style='margin-left:10px;display: none' href='javascript:;'' id='".$insert_id."' class='btn btn-success btn-xs update-parameter'><i class='fa fa-check' aria-hidden='true'></i></a></td></tr>";
          } else{
          	echo "error";
          } 
    
    }
     public function create_lab_parameter(){
     	$user_id = $this->session->has_userdata('user_id');

    $param['parameter_name'] = $this->input->post("pname");
    $param['parameter_type'] = $this->input->post("ptype");
    $param['created_by'] = $user_id;
    $param['modified_by'] = $user_id;
    $param['created_date_time'] = date("Y-m-d H:i:s");
    $param['modified_date_time'] = date("Y-m-d H:i:s");
    $ok = $this->Generic_model->insertData('parameters',$param);
    $insert_id = $this->db->insert_id();

    $check_fp = $this->db->query("select * from followup_parameter where followup_id = '".$this->input->post('fid')."'")->row();
   
    if($check_fp){
    	$this->db->query("update followup_parameter set parameter_id = concat(parameter_id, ',','$insert_id') WHERE followup_id  = '".$check_fp->followup_id."'");
    }
    else{
    	 $user_id = $this->session->has_userdata('user_id');
    $fp['parameter_id'] = $insert_id;
    $fp['followup_id'] = $this->input->post("fid");
    $fp['created_by'] = $user_id;
    $fp['modified_by'] = $user_id;
    $fp['created_date_time'] = date("Y-m-d H:i:s");
    $fp['modified_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('followup_parameter',$fp);
    }

    
          if($ok == 1){
          	echo "<tr class='lab' id='".$insert_id."'><td style='padding: 15px;'>".$this->input->post("pname")."</td><td style='padding: 15px;'><a id='".$insert_id."' class='btn btn-danger btn-xs  delete-parameter'><i class='fa fa-times' aria-hidden='true'></i></a><a style='margin-left:10px' href='javascript:; id='".$insert_id."' class='btn btn-info btn-xs edit-parameter'><i class='fa fa-edit' aria-hidden='true'></i></a><a style='margin-left:10px;display: none' href='javascript:;'' id='".$insert_id."' class='btn btn-success btn-xs update-parameter'><i class='fa fa-check' aria-hidden='true'></i></a></td></tr>";
          } else{
          	echo "error";
          } 
          $this->update_lab_param_json();   
    }
    public function update_clinical_param_json(){
        $param_list = $this->db->query("select parameter_name from parameters where parameter_type='Clinical'")->result(); 

        $prefix = '';
        $prefix .= '[';
        foreach($param_list as $row) {
            $prefix .= json_encode($row->parameter_name);
            $prefix .= ',';
        }
        $prefix .= ']';

        $json_file = str_replace(",]","]",trim($prefix,","));

        $path_user = './uploads/clinical_parameters.json';

        if (!file_exists($path_user)) {                   
            $fp = fopen('./uploads/clinical_parameters.json', 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/clinical_parameters.json', 'w');
            fwrite($fp, $json_file);
        }
    }
    public function update_lab_param_json(){
        $param_list = $this->db->query("select parameter_name from parameters where parameter_type='Lab'")->result(); 

        $prefix = '';
        $prefix .= '[';
        foreach($param_list as $row) {
            $prefix .= json_encode($row->parameter_name);
            $prefix .= ',';
        }
        $prefix .= ']';

        $json_file = str_replace(",]","]",trim($prefix,","));

        $path_user = './uploads/lab_parameters.json';

        if (!file_exists($path_user)) {                   
            $fp = fopen('./uploads/lab_parameters.json', 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/lab_parameters.json', 'w');
            fwrite($fp, $json_file);
        }
    }
 public function edit($id){
 	$param = $this->input->post();

	 	if(count($param)>0){

	 		$user_id = $this->session->has_userdata('user_id');
	 		$param_1['followup_id'] = $this->input->post("fp_id");
	 		$implode = implode(",", $this->input->post('param'));
      $implode = trim($implode,",");
	 		 $exists = $this->db->query('select * from followup_parameter where followup_id = '.$id)->row();
	 		 if(count($exists)>0)
             	{
             		$this->db->query("update followup_parameter set parameter_id = concat(parameter_id, ',','$implode') WHERE followup_id  = '".$id."'");

             	}
             	else{
	 		$param_1['parameter_id'] = $implode;
	 		//$param_1['department_id'] = $this->input->post("dept");
	 		$param_1['created_by'] = $user_id;
	 		$param_1['modified_by'] = $user_id;
	 		$param_1['created_date_time'] = date("Y-m-d H:i:s");
	 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
	 		$ok = $this->Generic_model->insertData('followup_parameter',$param_1);
	 	}
	 		if($ok ==1){
	 			redirect('followup_templates');
	 		}else{
	 			redirect('followup_templates');
	 		}
	 	}else{
	 		

	 		$f_params = $this->db->query("select * from followup_parameter where followup_id ='".$id."'")->row();
	 		$data['followup_id'] = $id;
	 		if(count($f_params)>0){
	 		$data['template_val'] = $this->db->query("select * from followup where archieve != 1 and followup_id ='".$id."'")->row();
	 		$p_id = str_replace(",,", ",", $f_params->parameter_id);
	 		$p_id= trim($p_id,",");
	 		$data['c_params'] = $this->db->query("select * from parameters  where parameter_id IN(".$p_id.") and parameter_type='Clinical'")->result();
		 	//$data['parameters_list'] = $this->db->query("select * from parameters")->result();
	 		$data['l_params'] = $this->db->query("select * from parameters  where parameter_id IN(".$p_id.") and parameter_type='Lab'")->result();
			$data['followup_id']=$id;
		 	$data['parameters_list'] = $this->db->query("select * from parameters")->result();
		 }
		 else{
		 	$data['template_val'] = $this->db->query("select * from followup where archieve != 1 and followup_id ='".$id."'")->row();
		 	$data['followup_id'] = $id;
		 	$data['parameters_list'] = $this->db->query("select * from parameters")->result();
		 }
		 	$data['view'] = 'followup_templates/template_edit';
		 	$this->load->view('layout', $data);
	 	}
 	
 	}

 	public function delete($id){
		$user_id = $this->session->has_userdata('user_id');
		$param_1['archieve'] = "1";
		$param_1['modified_by'] = $user_id;
 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
 		$ok = $this->Generic_model->updateData('followup',$param_1,array('followup_id'=>$id));
 		if($ok ==1){
 			redirect('followup_templates');
 		}else{
 			redirect('followup_templates');
 		}
 	}


  public function getFollowups(){
  $dept_id = $this->input->post('department_id');
  $output ='';
  $output .= '<option value="">--select--<option>';
  $query = $this->db->query("select * from followup_department where department_id=".$dept_id)->result();
  $followup_arry = array();
  foreach($query as $value){
    $followup_arry[] = $value->followup_id;
  }
  $followup_ids = implode(',', $followup_arry);
  if($followup_ids!=NULL || $followup_ids!=""){
    $followups = $this->db->query("select * from followup where followup_id NOT IN(".$followup_ids.")")->result();
  }else{
    $followups = $this->db->query("select * from followup")->result();
  }
  foreach ($followups as $key => $value) {
    $output .='<option value="'.$value->followup_id.'">'.$value->followup_name.'</option>';
  }
  echo $output;
  
 }
 public function mapped_followups(){
  $dept_id = $this->input->post('department_id');
  $output ='';
  $query = $this->db->query("select * from followup_department a inner join followup b on(a.followup_id = b.followup_id) where a.department_id=".$dept_id)->result();
 if(count($query)>0){
$output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">MAPPED FOLLOWUPS <span class="pull-right">'.count($query).'</span></th>';
   foreach ($query as  $value) { 
    $output .='<tr id="'.$value->followup_department_id.'"><td style="padding: 15px;">'.$value->followup_name.'</td><td style="padding: 15px;"><a href="javascript:;" id="'.$value->followup_department_id.'" class="btn btn-danger btn-xs delete-followup"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>';
  }
$output .= '</tbody></table>'; 
 }
 else{
  $output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">NO FOLLOWUP MAPPED</th></tbody></table>';
 }
 
  echo $output;
  
 }

  public function delete_mapped_followup(){
    
                $this->db->query("DELETE from followup_department where followup_department_id='".$this->input->post('pid')."'");

    }
 	
}
?>