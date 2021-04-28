<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Drug extends MY_Controller {
public function __construct() 
{
    parent::__construct();
	$this->load->library('zip');
}
public function index(){
  $data['drug']=$this->db->query("SELECT * FROM drug WHERE archieve = 0 LIMIT 10000  ")->result();
	$data['view'] = 'drug/drug_list';
    $this->load->view('layout', $data);
}

public function drug_add(){	
  if($this->input->post('submit')){
    $data['salt_id']=$this->input->post('salt_id');
    //$data['drug_notify_quantity']=$this->input->post('drug_notify_quantity');
    $data['trade_name']=$this->input->post('trade_name');
    $data['formulation']=$this->input->post('formulation');
    $data['composition']=$this->input->post('composition');
    
    $data['status']=1;
    $data['created_by']=$this->session->userdata('user_id');
    $data['modified_by']=$this->session->userdata('user_id');
    $data['created_date_time']=date('Y-m-d H:i:s');
    $data['modified_date_time']=date('Y-m-d H:i:s');
    $this->Generic_model->insertData('drug',$data);
    redirect('Drug');
  }else{
    $data['salt']=$this->db->query('select * from salt')->result();

		$data['view'] = 'drug/drug_add';
    $this->load->view('layout', $data);
  }
 }
 public function drug_update($id){
    if($this->input->post('submit')){
      $data['salt_id']=$this->input->post('salt_id');
  //  $data['drug_notify_quantity']=$this->input->post('drug_notify_quantity');
    $data['trade_name']=$this->input->post('trade_name');
    $data['formulation']=$this->input->post('formulation');
    $data['composition']=$this->input->post('composition');
    
    $data['status']=1;
    $data['created_by']=$this->session->userdata('user_id');
    $data['modified_by']=$this->session->userdata('user_id');
    $data['created_date_time']=date('Y-m-d H:i:s');
    $data['modified_date_time']=date('Y-m-d H:i:s');
      $this->Generic_model->updateData('drug', $data, array('drug_id'=>$id));
      redirect('Drug');
    }else{
      $data['drug']=$this->db->query('select * from drug where drug_id='.$id)->row();
    $data['salt']=$this->db->query('select * from salt')->result();

	    $data['view'] = 'drug/drug_edit';
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
            $createArray = array('Form','Name','Composition','Salts','Manufacturer','HSN_Code','GST');
            $makeArray = array('Form' =>'Form','Name' =>'Name','Composition' =>'Composition','Salts' =>'Salts','Manufacturer' =>'Manufacturer','HSN_Code' =>'HSN_Code','GST' =>'GST');
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

                	
                    // $addresses = array();
                    $Type = $SheetDataKey['Form'];
                    $Name = $SheetDataKey['Name'];
                    $GST = $SheetDataKey['GST'];
                    $Composition = $SheetDataKey['Composition'];
                    $Salt = $SheetDataKey['Salts'];
                    $Manufacturer = $SheetDataKey['Manufacturer'];
                    $hsn_code = $SheetDataKey['HSN_Code'];

                  

                    $d_type = filter_var(trim($allDataInSheet[$i][$Type]), FILTER_SANITIZE_STRING);
                    $d_name = filter_var(trim($allDataInSheet[$i][$Name]), FILTER_SANITIZE_STRING);
                    $d_composition = filter_var(trim($allDataInSheet[$i][$Composition]), FILTER_SANITIZE_STRING);
                    $d_salt = filter_var(trim($allDataInSheet[$i][$Salt]), FILTER_SANITIZE_STRING);
                    $d_manufacturer= filter_var(trim($allDataInSheet[$i][$Manufacturer]), FILTER_SANITIZE_STRING);
                    $d_hsn = filter_var(trim($allDataInSheet[$i][$hsn_code]), FILTER_SANITIZE_STRING);
                    $d_gst = filter_var(trim($allDataInSheet[$i][$GST]), FILTER_SANITIZE_STRING);
                 
                	if($d_gst == ""){
                		$cgst =0;
                		$sgst = 0;
                		$igst = 0;
                	} 
                	else{
                		$divide_gst = $d_gst/2;
                		$cgst = $divide_gst;
                		$sgst = $divide_gst;
                		$igst = $d_gst;
                	}
                   
                		if($d_salt == "" || $d_salt == NULL){
                			$saltid = "";
                		}
                		else{
                			$imp =  "'".implode("','", explode(",", $d_salt))."'";
                    	$salt_id = $this->db->query("SELECT GROUP_CONCAT(salt_id) salt_id FROM `salt` where salt_name in ($imp)")->row();
                    	$saltid = $salt_id->salt_id;
                		}
               			
                    	$fetchData = array('formulation' => $d_type, 'trade_name' => $d_name, 'composition' => $d_composition, 'manufacturer' => $d_manufacturer,'hsn_code'=>$d_hsn ,'salt_id' =>$saltid,'category'=>'Drug','cgst'=>$cgst, 'sgst'=>$sgst, 'igst'=>$igst, 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));

      

                    	$last_id=$this->Generic_model->insertDataReturnId('drug',$fetchData);
                       
                       
 
                    
                }
                }  
                   
                 
            } else {
                echo "Please import correct file";
            }
       
 
        redirect('drug');
       // $this->load->view('import/display', $data);
        
     }
	 
	 
	 
	 public function drug_master_json_create(){
		 $drugs_count=$this->db->query("SELECT * FROM drug")->num_rows();
		$json_file_count=ceil($drugs_count/30000)+1;	
for($i=1;$i<=$json_file_count;$i++){
$limit=$i*30000-30000;
$offset=$i*30000;
echo "limit".$limit."offset".$offset."<br>";
$data['result']=$this->db->query("SELECT * FROM drug  LIMIT ".$limit.','.$offset)->result();
echo $this->db->last_query();

		

}
echo "<pre>";print_r($data['result']);
		 
		
	 }



}