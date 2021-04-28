<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Salt extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
  $data['salt_list']=$this->db->query('select * from salt')->result();
	$data['view'] = 'salt/salt_list';
    $this->load->view('layout', $data);
}

public function add()
{
    if(!$this->input->post('submit'))
    {
      $data['view'] = 'salt/salt_add';
      $this->load->view('layout',$data);  
    }else{
        $data['salt_name'] = $this->input->post('name');
        $data['status'] = $this->input->post('status');
        $data['created_by'] = $this->session->userdata('user_id');
        $data['modified_by'] = $this->session->session('user_id');
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['modified_by'] = date('Y-m-d H:i:s');
         $ok = $this->Generic_model->insertData('salt',$data);
         if($ok)
         {
            $this->session->set_flashdata('suscess', 'Successfully Inserted');
            redirect('salt');
         }
         else
         {
            $this->session->set_flashdata('error', 'Successfully Inserted');
            redirect('salt');
         }
    }
    
}

public function edit($id)
{
    if(!$this->input->post('submit'))
    {
        $data['salt'] = $this->db->query('select * from salt where salt_id ='.$id)->row();
        $data['view'] = 'salt/salt_edit';
        $this->load->view('layout',$data);
    }
    else
    {
        $data1['salt_name'] = $this->input->post('name');
        $data1['status'] = $this->input->post('status');
        $data1['modified_by'] = $this->session->session('user_id');
        $data1['modified_by'] = date('Y-m-d H:i:s');
         $ok = $this->Generic_model->updateData('salt',$data1);
         if($ok)
         {
            $this->session->set_flashdata('suscess', 'Successfully Inserted');
            redirect('salt');
         }
         else
         {
            $this->session->set_flashdata('error', 'Successfully Inserted');
            redirect('salt');
         }

    }
}

public function delete($id)
{
     $ok = $this->db->query('delete from salt where salt_id = '.$id);
     if($ok)
     {
        $this->session->set_flashdata('suscess', 'Successfully Inserted');
        redirect('salt');
     }
     else
     {
        $this->session->set_flashdata('error', 'Successfully Inserted');
            redirect('salt');
     }

}

//bulk uploading salt master
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
            $createArray = array('Salt','HSN','Drugs');
            $makeArray = array('Salt' =>'Salt','HSN' =>'HSN','Drugs' =>'Drugs');
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

                    $Salt = $SheetDataKey['Salt'];
                    //$Hsn = $SheetDataKey['HSN'];
                    $drugs = $SheetDataKey['Drugs'];

                    $salt_name = filter_var(trim($allDataInSheet[$i][$Salt]), FILTER_SANITIZE_STRING);
                   // $hsn_code = filter_var(trim($allDataInSheet[$i][$Hsn]), FILTER_SANITIZE_STRING);
                    $scheduled_drug = filter_var(trim($allDataInSheet[$i][$drugs]), FILTER_SANITIZE_STRING);
                 

                 
                    
                            $datas['salt_name'] = $salt_name; 
                           // $datas['hsn_code'] = $hsn_code; 
                            $datas['scheduled_salt'] = $scheduled_drug; 
                            $datas['status']=1;
                            $datas['created_by']=$this->session->userdata('user_id');
                            $datas['modified_by']=$this->session->userdata('user_id');
                            $datas['created_date_time']=date('Y-m-d H:i:s');
                            $datas['modified_date_time']=date('Y-m-d H:i:s');
                         $ok = $this->Generic_model->insertData('salt',$datas);
                            
                        

                    

                       
 
                    
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
 
        redirect('salt');
       // $this->load->view('import/display', $data);
        
     }

     //updating contraindications with respect to salt
     public function update_contraindications(){
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
            $createArray = array('salt','contraindication');
            $makeArray = array('salt' =>'salt','contraindication' =>'contraindication');
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

                    $salt = $SheetDataKey['salt'];
                    $cind = $SheetDataKey['contraindication'];
                   

                    $salt_name = filter_var(trim($allDataInSheet[$i][$salt]), FILTER_SANITIZE_STRING);
                    $c_ind = filter_var(trim($allDataInSheet[$i][$cind]), FILTER_SANITIZE_STRING);
                   
                   $get_salt = $this->db->query("select salt_id from salt where salt_name='".$salt_name."'")->row();
                    if($get_salt){
                        $s_cind['contraindication'] = $c_ind;
                        $condition['salt_id'] = $get_salt->salt_id;
                        $this->Generic_model->updateData("salt", $s_cind, $condition);
                    }
                    else{
                        echo $salt_name."<br>";
                    }
                                
                    
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
 
       // redirect('salt');
       // $this->load->view('import/display', $data);
        
     }

//uploading condition contraindications masters
        public function condition_contraindications(){
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
            $createArray = array('kidney_disorders');
            $makeArray = array('kidney_disorders' =>'kidney_disorders');
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
               $cond['condition'] = "kidney disorders";
               $cond['created_date_time'] = date("Y-m-d H:i:s");
               $cond_id = $this->Generic_model->insertDataReturnId('condition_contraindication',$cond);
                for ($i = 2; $i <= $arrayCount; $i++) {

                    $kidneyDisorders = $SheetDataKey['kidney_disorders'];
                   
                    
                    $kidney_disorders = filter_var(trim($allDataInSheet[$i][$kidneyDisorders]), FILTER_SANITIZE_STRING);
                   
                   
                   $get_salt_id = $this->db->query("SELECT GROUP_CONCAT(salt_id) as salt_id FROM `salt` where contraindication like '%".$kidney_disorders."%'")->row();
                   $this->db->query("update condition_contraindication set contraindication = concat(contraindication, ',','$kidney_disorders') WHERE condition_contraindication_id = '" . $cond_id . "'");
                   if($get_salt_id->salt_id!=""){
                   
                   $get_salt = $this->db->query("select contraindication from condition_contraindication where condition_contraindication_id='".$cond_id."'")->row();
                   if($get_salt->salt_id!=""){
                    $this->db->query("update condition_contraindication set salt_id = concat(salt_id, ',','$get_salt_id->salt_id') WHERE condition_contraindication_id = '" . $cond_id . "'");
                    
                   }
                   else{
                    $this->db->query("update condition_contraindication set salt_id = concat(salt_id, '','$get_salt_id->salt_id') WHERE condition_contraindication_id = '" . $cond_id . "'");
                    
                   }
                   

                   }
                      
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
 
       // redirect('salt');
       // $this->load->view('import/display', $data);
        
     }



}