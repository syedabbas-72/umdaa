<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
  $data['pharmacy_list']=$this->Generic_model->getAllRecords('pharmacy_master',array('archieve'=>0),$order='');
	$data['view'] = 'pharmacy/pharmacy_list';
    $this->load->view('layout', $data);
}
public function pharmacy_clinic($id){
  if($this->input->post('submit')){
       $data['clinic_id']=$this->input->post('clinic_id');
    $data['pharmacy_id']=$this->input->post('pharmacy_id');
    $data['status']=1;
    $data['created_by']=$this->session->userdata('user_id');
    $data['modified_by']=$this->session->userdata('user_id');
    $data['created_date_time']=date('Y-m-d H:i:s');
    $data['modified_date_time']=date('Y-m-d H:i:s');
    
    $this->Generic_model->insertData('pharmacy_clinic',$data);
    
     redirect('pharmacy');
  }else{
$data['pharmacy_list']=$this->Generic_model->getAllRecords('pharmacy_master',array('archieve'=>0),$order='');
$data['clinics']=$this->db->query('select clinic_id,clinic_name from clinics order by clinic_id asc')->result();

$data['pharmacy_master']=$this->db->query('select pharmacy_id,pharmacy_name from pharmacy_master where pharmacy_id='.$id)->row();
$data['pharmacy_clinic']=$this->db->query('select * from pharmacy_clinic where pharmacy_id='.$id)->result();
//echo $this->db->last_query();
  $data['view'] = 'pharmacy/pharmacy_clinic';
    $this->load->view('layout', $data);
  }
}
public function pharmacy_add(){	
  if($this->input->post('submit')){
    $data['pharmacy_name']=$this->input->post('pharmacy_name');
    $data['pharmacy_code']=$this->input->post('pharmacy_code');
    $data['location']=$this->input->post('location');
    $data['address']=$this->input->post('address');
    $data['phone']=$this->input->post('phone');
    $data['contact_person']=$this->input->post('contact_person');
    $data['status']=$this->input->post('status');
    $data['created_by']=$this->session->userdata('user_id');
    $data['modified_by']=$this->session->userdata('user_id');
    $data['created_date_time']=date('Y-m-d H:i:s');
    $data['modified_date_time']=date('Y-m-d H:i:s');
    $this->Generic_model->insertData('pharmacy_master',$data);
    redirect('pharmacy');
  }else{
		$data['view'] = 'pharmacy/pharmacy_add';
    $this->load->view('layout', $data);
  }
 }
 public function pharmacy_update($id){
    if($this->input->post('submit')){
      $data['pharmacy_name']=$this->input->post('pharmacy_name');
      $data['pharmacy_code']=$this->input->post('pharmacy_code');
      $data['location']=$this->input->post('location');
      $data['address']=$this->input->post('address');
      $data['phone']=$this->input->post('phone');
      $data['contact_person']=$this->input->post('contact_person');
      $data['status']=$this->input->post('status');
      $data['modified_by']=$this->session->userdata('user_id');
      $data['modified_date_time']=date('Y-m-d H:i:s');
      $this->Generic_model->updateData('pharmacy_master', $data, array('pharmacy_id'=>$id));
      redirect('pharmacy');
    }else{
      $data['pharmacy_list']=$this->db->query('select * from pharmacy_master where pharmacy_id='.$id)->row();
	    $data['view'] = 'pharmacy/pharmacy_edit';
	    $this->load->view('layout', $data);
    }
 }
 public function pharmacy_delete($id){
    $pharmacy_info['archieve'] = 1;
    $this->Generic_model->deleteRecord('pharmacy_master',$pharmacy_info, array('pharmacy_id' => $id));
     redirect('pharmacy');
 }

 public function save(){
  $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/pharmacy_bulk/';
            $config['upload_path'] = './uploads/pharmacy_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
           
        //echo $_FILES['userfile']['name']=$_FILES['userfile']['name'];exit;
                  
             $this->load->library('upload');
            $this->upload->initialize($config);
             $this->upload->do_upload('userfile'); //uploading file to server
      $fileData=$this->upload->data('file_name');
      $inputFileName = $path . $fileData;
            
      if(move_uploaded_file($fileData,$path))
           { 
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
            $createArray = array('pharmacy_name','pharmacy_code', 'location', 'address', 'phone', 'contact_person');
          $makeArray = array('pharmacy_name' =>'pharmacy_name', 'pharmacy_code' => 'pharmacy_code', 'location' =>'location', 'address' => 'address', 'phone' => 'phone', 'contact_person' => 'contact_person');
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
                    $addresses = array();
                    $pname = $SheetDataKey['pharmacy_name'];
                    $pcode = $SheetDataKey['pharmacy_code'];
                    $plocation = $SheetDataKey['location'];
                    $paddress = $SheetDataKey['address'];
                    $pphone = $SheetDataKey['phone'];
                    $pcontact_person = $SheetDataKey['contact_person'];
                  

                    $p_name = filter_var(trim($allDataInSheet[$i][$pname]), FILTER_SANITIZE_STRING);
                    $p_code = filter_var(trim($allDataInSheet[$i][$pcode]), FILTER_SANITIZE_STRING);
                    $p_location = filter_var(trim($allDataInSheet[$i][$plocation]), FILTER_SANITIZE_STRING);
                    $p_address = filter_var(trim($allDataInSheet[$i][$paddress]), FILTER_SANITIZE_STRING);
                    $p_phone= filter_var(trim($allDataInSheet[$i][$pphone]), FILTER_SANITIZE_STRING);
                    $p_contact_person = filter_var(trim($allDataInSheet[$i][$pcontact_person]), FILTER_SANITIZE_STRING);
                  
                    
                     

                    $fetchData = array('pharmacy_name' => $p_name, 'pharmacy_code' => $p_code, 'location' => $p_location, 'address' => $p_address, 'phone' => $p_phone, 'contact_person' => $p_contact_person);
                    $this->Generic_model->insertData('pharmacy_master',$fetchData);
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
        
        redirect('pharmacy');
       // $this->load->view('import/display', $data);
        
     }
}