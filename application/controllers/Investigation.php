<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Investigation extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }

    public function index(){
        $data['investigations']=$this->db->query('select * from investigations order by investigation_id')->result();
        $data['view'] = 'investigation/investigation_list';
        $this->load->view('layout', $data);
    }

    public function investigation_delete($id){
        // Check any lab templates exists with investigation id
        $labTemplatesRes = $this->db->query("SELECT * FROM lab_templates WHERE investigation_id = '".$id."'")->row();

        if(count($labTemplatesRes) > 0){ // Lab template Exists
            // Check if any lab template line items exists with lab template id
            $labTemplateLineItemsRes = $this->db->query("SELECT * FROM  lab_template_line_items WHERE lab_template_id = '".$labTemplatesRes->lab_template_id."'")->row();

            if(count($labTemplateLineItemsRes) > 0){ // lab template line items exists
                // Delete lab template line items which belongs to lab template id
                $res = $this->db->delete('lab_template_line_items', array("lab_template_id"=>$labTemplateLineItemsRes->lab_template_id));         
            }

            // Delete lab templates which belongs to investigation id
            $this->db->delete("lab_templates", array('investigation_id'=>$id));

        }  

        // Delete investigation with investigation id
        $this->db->delete("investigations", array('investigation_id'=>$id));

        $this->investigation_json();
    }


    public function investigation_add(){

        $user_id = $this->session->has_userdata('user_id');
        $clinic_id = $this->session->userdata('clinic_id');

        if($this->input->post('submit')){

            $param = $this->input->post();

            // Check invetsigation to avoid duplications
            $invetsigationCount = $this->db->select('investigation_id')->from('investigations')->where('investigation =',$param['investigation']['investigation'])->get()->row();

            if(count($invetsigationCount) == 0){
                // Get the last item code of investigations
                $get_item_code = $this->db->select("item_code")->from('investigations')->order_by('item_code','DESC')->limit(1)->get()->row();
                $param['investigation']['item_code']= ++$get_item_code->item_code;
                $param['investigation']['admin_review'] = 0;
                $param['investigation']['created_by'] = $param['investigation']['modified_by'] = $user_id;
                $param['investigation']['created_date_time'] = $param['investigation']['modified_date_time'] = date('Y-m-d H:i:s');

                // Create new Investigation and get investigation_id
                $param['clinic_investigation']['investigation_id'] = $this->Generic_model->insertDataReturnId('investigations',$param['investigation']);

                $param['clinic_investigation']['clinic_id']= $clinic_id;
                $param['clinic_investigation']['short_form']= $param['investigation']['short_form'];
                $param['clinic_investigation']['created_by'] = $param['clinic_investigation']['modified_by'] = $user_id;
                $param['clinic_investigation']['created_date_time'] = $param['clinic_investigation']['modified_date_time'] = date('Y-m-d H:i:s');

                // Create new clinic_investigation
                $this->Generic_model->insertData('clinic_investigations',$param['clinic_investigation']);          

                // Params : master_table_name, field_name, master_name, clinic_id
                update_master_version('investigations','investigation','investigation',0);

            }
            
            redirect("Lab/investigations");

        }else{
            $data['view'] = 'investigation/investigation_add';
            $this->load->view('layout', $data);        
        }

        

        // $user_id = $this->session->has_userdata('user_id');

        // if($this->input->post('submit')){

        //     $param = $this->input->post('submit');

        //     echo '<pre>';
        //     print_r($param);
        //     exit();            

        //     // Get last item_code and increment by 1            
        //     $get_item_code = $this->db->query("select item_code from investigations order by item_code desc")->row();
        //     $data['item_code']= ++$get_item_code->item_code;
        //     $data['investigation'] = $this->input->post('investigation');
        //     $data['short_form'] = $this->input->post('short_form');
        //     $data['sample_type'] = $this->input->post('sample_type');
        //     $data['category'] = $this->input->post('category');
        //     $data['status'] = 1;
        //     $data['admin_review'] = 0;
        //     $data['created_by'] = $user_id;
        //     $data['modified_by'] = $user_id;
        //     $data['created_date_time'] = date('Y-m-d H:i:s');
        //     $data['modified_date_time'] = date('Y-m-d H:i:s');

        //     $this->Generic_model->insertData('investigations',$data);

        //     // Params : master_table_name, field_name, master_name, clinic_id
        //     update_master_version('investigations','investigation','investigation',0);
        //     // $this->investigation_json();
        // }else{
        //     $data['view'] = 'investigation/investigation_add';
        //     $this->load->view('layout', $data);
        // }

        // $data['view'] = 'Lab/investigations';
        // $this->load->view('layout', $data);

    }


    public function investigation_update($id=''){
        $user_id = $this->session->has_userdata('user_id');
        if($this->input->post('submit')){
            $data['short_form']=$this->input->post('short_form');
            $data['investigation']=$this->input->post('investigation');
            $data['category']=$this->input->post('category');
            $data['status']=1;
            $data['modified_by']=$user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_date_time'] = date('Y-m-d H:i:s');

            $this->Generic_model->updateData('investigations',$data,array('investigation_id'=>$id));
            $this->investigation_json();
        }else{
            $data['investigations']=$this->db->query('select * from investigations where investigation_id='.$id)->row();

            $data['view'] = 'investigation/investigation_edit';
            $this->load->view('layout', $data);
        }
    }

    // Creating json with investigation masters
    public function investigation_json()
    {
        $investigation_list = $this->db->query("select investigation from investigations")->result();

        $prefix = '';
        $prefix .= '[';
        foreach ($investigation_list as $row) {
            $prefix .= json_encode($row->investigation);
            $prefix .= ',';
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        $path_user = './uploads/investigation.json';

        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/investigation.json', 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/investigation.json', 'w');
            fwrite($fp, $json_file);
        }
        redirect('Investigation');
    }


    public function save(){
        $user_id = $this->session->has_userdata('user_id');
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/investigation_bulk/';
            $config['upload_path'] = './uploads/investigation_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
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
            $createArray = array('item_code','short_form', 'category', 'investigation');
            $makeArray = array('item_code' =>'item_code', 'short_form' => 'short_form', 'category' =>'category', 'investigation' => 'investigation');
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
                    $icode = $SheetDataKey['item_code'];
                    $iinvestigation = $SheetDataKey['investigation'];
                    $icategory = $SheetDataKey['category'];
                    $isform = $SheetDataKey['short_form'];

                    $i_code = filter_var(trim($allDataInSheet[$i][$icode]), FILTER_SANITIZE_STRING);
                    $i_investigation = filter_var(trim($allDataInSheet[$i][$iinvestigation]), FILTER_SANITIZE_STRING);
                    $i_category = filter_var(trim($allDataInSheet[$i][$icategory]), FILTER_SANITIZE_STRING);
                    $i_short_form = filter_var(trim($allDataInSheet[$i][$isform]), FILTER_SANITIZE_STRING);

                    $fetchData = array('item_code' => $i_code, 'investigation' => $i_investigation, 'category' => $i_category, 'short_form' => $i_short_form,'status'=>1, 'created_by'=>$user_id,'modified_by'=>$user_id,'created_date_time'=>date('Y-m-d H:i:s'), 'modified_date_time'=>date('Y-m-d H:i:s'));
                    $this->Generic_model->insertData('investigations',$fetchData);
                }
            }  

        } else {
            echo "Please import correct file";
        }

        redirect('Investigation');

    }


    public function lab_template_save(){
        $user_id = $this->session->has_userdata('user_id');
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/investigation_bulk/';
            $config['upload_path'] = './uploads/investigation_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
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
            $createArray = array('investigation','type');
            $makeArray = array('investigation' => 'investigation','type'=>'type');
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
                    $iinvestigation = $SheetDataKey['investigation'];
                    $itype = $SheetDataKey['type'];

                    $i_investigation = filter_var(trim($allDataInSheet[$i][$iinvestigation]), FILTER_SANITIZE_STRING);
                    $i_type = filter_var(trim($allDataInSheet[$i][$itype]), FILTER_SANITIZE_STRING);

                    $inv_info = $this->db->query("select investigation_id from investigations where investigation='".$i_investigation."'")->row();

                    $fetchData = array('investigation_id' => $inv_info->investigation_id, 'template_name' => $i_investigation, 'template_type' => $i_type, 'status'=>1, 'created_by'=>$user_id,'modified_by'=>$user_id,'created_date_time'=>date('Y-m-d H:i:s'), 'modified_date_time'=>date('Y-m-d H:i:s'));
                    $this->Generic_model->insertData('lab_templates',$fetchData);
                }
            }  

        } else {
            echo "Please import correct file";
        }

        redirect('Investigation');

    }


    public function template_lineitems_save(){
        $user_id = $this->session->has_userdata('user_id');
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/investigation_bulk/';
            $config['upload_path'] = './uploads/investigation_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
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
            $createArray = array('template','parameter');
            $makeArray = array('template' => 'template','parameter'=>'parameter');
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
                    $itemplate = $SheetDataKey['template'];
                    $iparameter = $SheetDataKey['parameter'];

                    $i_template = filter_var(trim($allDataInSheet[$i][$itemplate]), FILTER_SANITIZE_STRING);
                    $i_parameter = filter_var(trim($allDataInSheet[$i][$iparameter]), FILTER_SANITIZE_STRING);
                    if($i_parameter!=""){

                        $template_info = $this->db->query("select investigation_id,lab_template_id from lab_templates where template_name='".$i_template."'")->row();

                        $fetchData = array('investigation_id' => $template_info->investigation_id, 'lab_template_id' => $template_info->lab_template_id, 'parameter' => $i_parameter, 'status'=>1, 'created_by'=>$user_id,'modified_by'=>$user_id,'created_date_time'=>date('Y-m-d H:i:s'), 'modified_date_time'=>date('Y-m-d H:i:s'));
                        $this->Generic_model->insertData('lab_template_line_items',$fetchData);
                    }
                }
            }  
        } else {
            echo "Please import correct file";
        }

        redirect('Investigation');
    }
}

?>