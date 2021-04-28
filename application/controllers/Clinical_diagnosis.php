<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Clinical_diagnosis extends MY_Controller {
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->library('zip');
    }

    public function index(){
        $data['diseases']=$this->db->select('clinical_diagnosis_id, code, disease_name, status')->from('clinical_diagnosis')->where("archieve =",0)->limit("100","0")->get()->result();
        echo $this->db->last_query();
        $data['view'] = 'clinical_diagnosis/disease_list';
        $this->load->view('layout', $data);
    }

    public function disease_add(){	
        if($this->input->post('submit')){
            $data['disease_name']=$this->input->post('disease_name');
            $data['code']=$this->input->post('code');
            
            $data['status']=1;
            $data['created_by']=$this->session->userdata('user_id');
            $data['modified_by']=$this->session->userdata('user_id');
            $data['created_date_time']=date('Y-m-d H:i:s');
            $data['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->insertData('clinical_diagnosis',$data);
            redirect('Clinical_diagnosis');
        }else{
            //$data['clinical_diagnosis'] = $this->db->select('*')->from('clinical_diagnosis')->get()->result();

            $data['view'] = 'clinical_diagnosis/disease_add';
            $this->load->view('layout', $data);
        }
    }

    public function disease_update($id){
        if($this->input->post('submit')){
            $data['clinical_diagnosis_id']=$this->input->post('clinical_diagnosis_id');
            $data['disease_name'] = $this->input->post('disease_name');
            $data['code']=$this->input->post('code');
            
            $data['status']=1;
            $data['created_by']=$this->session->userdata('user_id');
            $data['modified_by']=$this->session->userdata('user_id');
            $data['created_date_time']=date('Y-m-d H:i:s');
            $data['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->updateData('clinical_diagnosis', $data, array('clinical_diagnosis_id'=>$id));
            redirect('Clinical_diagnosis');
        }else{
            $data['clinical_diagnosis']=$this->db->select('*')->from('clinical_diagnosis')->where('clinical_diagnosis_id='.$id)->get()->row();

            $data['view'] = 'clinical_diagnosis/disease_edit';
            $this->load->view('layout', $data);
        }
    }

    public function disease_delete($id){
        $disease_info['archieve'] = 1;
        $this->Generic_model->deleteRecord('clinical_diagnosis',$disease_info, array('clinical_diagnosis_id' => $id));
        redirect('Clinical_diagnosis');
    }

    public function bulk_save(){
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/clinical_diagnosis_bulk/';
            $config['upload_path'] = './uploads/clinical_diagnosis_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
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
            $createArray = array('code','disease_name');
            $makeArray = array('code' =>'code','disease_name' =>'disease_name');
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

                    $code = $SheetDataKey['code'];
                    $disease_name = $SheetDataKey['disease_name'];

                    $cd_code = filter_var(trim($allDataInSheet[$i][$code]), FILTER_SANITIZE_STRING);
                    $cd_disease_name = filter_var(trim($allDataInSheet[$i][$disease_name]), FILTER_SANITIZE_STRING);

                    $fetchData = array('code' => $cd_code, 'disease_name' => $cd_disease_name, 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));

                    $last_id = $this->Generic_model->insertDataReturnId('clinical_diagnosis',$fetchData);

                    echo $this->db->last_query();
                }
            }  
        } else {
            echo "Please import correct file";
        }
        redirect('Clinical_diagnosis');
    }
    	 
         
    public function clinical_diagnosis_master_json_create(){
        $clinical_diagnosis_master_data = $this->db->select('*')->from('clinical_diagnosis')->get()->result_array();
        echo "<pre>";
        print_r($clinical_diagnosis_master_data);
        echo '</pre>';
    }

}