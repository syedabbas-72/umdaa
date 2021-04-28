<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_department extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
    $data['form_department']=$this->db->query("select * from form_department a inner join department b on a.department_id = b.department_id group by a.department_id")->result();
	$data['view'] = 'form_department/form_department_list';
    $this->load->view('layout', $data);
}
public function add(){
    if($this->input->post('submit')){
        $consent_form=$this->input->post('consent');       
        for($i=0;$i<count($consent_form);$i++){
            $data['department_id']=$this->input->post('department');
            $data['form_id'] = $consent_form[$i];    
            $this->Generic_model->insertData('form_department',$data);
            
        }
        redirect('form_department/add');  
    }else{
        $data['department_list']=$this->Generic_model->getAllRecords('department',$condition='',$order='');

        $data['form_list']=$this->db->query("select * from form where form_type='Systemic Examination'");

        $data['view'] = 'form_department/form_department_add';
        $this->load->view('layout', $data);
    }
}

public function upload_symptoms(){
     $data['view'] = 'form_department/symptoms_add';
        $this->load->view('layout', $data);
}

public function delt_form($id){
    $this->db->query("delete from form_department where form_id=".$id);
    redirect('form_department');
}

public function getforms(){
    $department_id = $this->input->post('department_id');
    $query = $this->db->query("select * from form_department where department_id='".$department_id."'")->result();
    $form_arry = array();
    foreach ($query as $value) {
        $form_arry[] = $value->form_id;
    }
    $form_ids = implode(',', $form_arry);
    if($form_ids==NULL || $form_ids=="" ){
        $forms=$this->db->query('select * from form where form_type="Systemic Examination"')->result();
    }else{
        $forms=$this->db->query('select * from form where form_type="Systemic Examination" and  form_id NOT IN ('.$form_ids.')')->result();
    }
    $output ='';
    $output .='<option>--Select--</option>';
    foreach ($forms as $value) {
        $output .='<option value="'.$value->form_id.'">'.$value->form_name.'</option>';
    }
    echo $output;


 
 
}

public function mapped_forms(){
  $dept_id = $this->input->post('department_id');
  $output ='';
  $query = $this->db->query("select * from form_department a inner join form b on(a.form_id = b.form_id) where b.form_type='Systemic Examination' and a.department_id=".$dept_id)->result();
 if(count($query)>0){
$output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">MAPPED FORMS</th>';
   foreach ($query as  $value) { 
    $output .='<tr id="'.$value->form_department_id.'"><td style="padding: 15px;">'.$value->form_name.'</td><td style="padding: 15px;"><a href="javascript:;" id="'.$value->form_department_id.'" class="btn btn-danger btn-xs delete-followup"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>';
  }
$output .= '</tbody></table>'; 
 }
 else{
  $output .= '<table id="prescription" class="table table-bordered items"><tbody><tr id="cparams"><th colspan=2  style="padding: 15px;">NO FORMS MAPPED</th></tbody></table>';
 }
 
  echo $output;
  
 }
 

  public function delete_mapped_form(){
    
                $this->db->query("DELETE from form_department where form_department_id='".$this->input->post('pid')."'");

    }

    public function save(){
    $this->load->library('excel');

         if ($this->input->post('importfile')) {
            $path = './uploads/drugs_bulk/';
            $config['upload_path'] = './uploads/drugs_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
           
        //echo $_FILES['userfile']['name']=$_FILES['userfile']['name'];exit;
                    
             $this->load->library('upload');
            $this->upload->initialize($config);
             $this->upload->do_upload('userfile'); //uploading file to server
            $fileData=$this->upload->data('file_name');
            $inputFileName = $path . $fileData;
            
            if(file_exists($inputFileName)){
                $inputFileName = $path . $fileData;
            }
            else{
                move_uploaded_file($fileData,$path);
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
            $createArray = array('Symptoms','Synonyms');
            $makeArray = array('Symptoms' =>'Symptoms','Synonyms' =>'Synonyms',);
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

                    $Symptoms = $SheetDataKey['Symptoms'];
                    $Synonyms = $SheetDataKey['Synonyms'];

                    $symptom_name = filter_var(trim($allDataInSheet[$i][$Symptoms]), FILTER_SANITIZE_STRING);
                    $synonym_name = filter_var(trim($allDataInSheet[$i][$Synonyms]), FILTER_SANITIZE_STRING);

                    $getforms = $this->db->query("select count(*) as num_rows,form_id from form where form_name = '".$symptom_name."'")->row();
                    if($getforms->num_rows > 0 && $synonym_name!=""){
                        $explode_synonyms = explode(",",$synonym_name);
                    foreach ($explode_synonyms as $key => $syn) {
                        $datas['form_id'] = $getforms->form_id; 
                            $datas['synonym'] = $syn;  
                            $datas['status']=1;
                            $datas['created_by']=$this->session->userdata('user_id');
                            $datas['modified_by']=$this->session->userdata('user_id');
                            $datas['created_date_time']=date('Y-m-d H:i:s');
                            $datas['modified_date_time']=date('Y-m-d H:i:s');
                         $ok = $this->Generic_model->insertData('hopi_synonyms',$datas);
                    }
                    }
                    

                            
 
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
 
        redirect('form_department/upload_symptoms');
       // $this->load->view('import/display', $data);
        
     }

}