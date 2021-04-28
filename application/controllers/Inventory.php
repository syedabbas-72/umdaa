<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends MY_Controller {
public function __construct() 
{
    parent::__construct();
     $this->load->model("Ajx_model"); 
}
public function index(){  
  $clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and b.clinic_id=".$clinic_id;
  /*$data['shortage'] = $this->db->query("select *,(pi.quantity - pd.issued_quantity) as total_qty,pi.drug_id as drugs_id   from pharmacy_inventory pi inner join inventory_outward pd on(pi.drug_id = pd.drug_id) inner join drug d on(d.drug_id = pi.drug_id) HAVING  total_qty <= d.durg_reorder_level ")->result();
  $data['expiry'] = $this->db->query("select * from pharmacy_inventory p  inner join drug d on(d.drug_id = p.drug_id) where p.expiry_date <='".date('Y-m-d')."' ")->result();
  $data['indent_list'] = $this->db->query("select * from pharmacy_indent pi inner join pharmacy_indent_line_items pil on(pi.pharmacy_indent_id = pil.pharmacy_indent_id) group by pi.pharmacy_indent_id")->result();
  $data['ytExpire'] = $this->db->query("select * from pharmacy_inventory p inner join drug d on(d.drug_id = p.drug_id) where p.expiry_date <='".$this->checkexpiry()."' ")->result();*/
  $data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 ".$cond." group by b.drug_id,b.batch_no")->result();
	$data['view'] = 'inventory/inventory_list';
    $this->load->view('layout', $data);
}

public function autocomplete(){

  $query = $this->input->post('query');
        $data = $this->db->query("select * from pharmacy_inventory a inner join drug b on (a.drug_id = b.drug_id) where b.trade_name like '".$query."%' group by a.drug_id")->result_array();
        $result = array();
        foreach ($data as $key => $value) {
          $result[] = $value;
        }

        echo json_encode( $result);
	   
    }  



public function checkexpiry(){
$month = date('n');
$year = date('Y');
$IsLeapYear = date('L');
$NextYear = $year + 1;
$IsNextYearLeap = date('L', mktime(0, 0, 0, 1, 1, $NextYear));
$TodaysDate = date('j');
if (strlen($month+3) < 10)
{
$UpdateMonth = "0".($month+3);
}
if ($month > 9) {
if ($month == 10)
{
$UpdateMonth = "01";
}
else if ($month == 11)
{
$UpdateMonth = "02";
}
else
{
$UpdateMonth = "03";
}
}

if (($month != 10) && ($month != 11) && ($month != 12))
{
if(($month&1) && ($TodaysDate != 31))
{
$DateAfterThreeMonths = $year."-".$UpdateMonth."-".$TodaysDate;
}
else if (($month&1) && ($TodaysDate == 31))
{
$DateAfterThreeMonths = $year."-".$UpdateMonth."-30";
} 
else {
$DateAfterThreeMonths = $year."-".$UpdateMonth."-".$TodaysDate;
}
}
else if ($month == 11)
{
if (($TodaysDate == 28) || ($TodaysDate == 29) || ($TodaysDate == 30))
{
if ($IsLeapYear == 1)
{
$DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-28";
}
else if ($IsNextYearLeap == 1)
{
$DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-29";
}
else
{
$DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-28";
}
}
else
{
$DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-".$TodaysDate;
}
}
else
{
$DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-".$TodaysDate;
}
return $DateAfterThreeMonths;
}

public function pharmacy_inventory_values(){
  $drug_id = $this->input->post("id");
  $pharmacy_inventory_data = $this->db->query("select * from pharmacy_inventory a inner join drug b on (a.drug_id = b.drug_id) where a.drug_id ='".$drug_id."'")->result();
 // $data = "";
  foreach ($pharmacy_inventory_data as  $value) {
    
  
      echo '<tr><td>'.$value->trade_name.'</td><td>'.$value->composition.'</td><td>'.$value->quantity.'</td><td>'.$value->batch_no.'</td><td>'.date("d-m-Y",strtotime($value->expiry_date)).'</td><td> <a class="btn btn-success btn-xs" id="'.$value->pharmacy_inventory_id.'" onclick = "pharmacy_inventory_edit(\''.$value->pharmacy_inventory_id.'\');"><i class="fa fa-edit"></i></a></td></tr>';
    }
}

public function pharmacy_inventory_edit(){
  $pharmacy_inventory_id = $this->input->post("id");
  $pharmacy_inventory_data = $this->db->query("select * from pharmacy_inventory a inner join drug b on (a.drug_id = b.drug_id) where a.pharmacy_inventory_id ='".$pharmacy_inventory_id."'")->row();
      echo '<tr><td><input type="text" name="trade_name"  id="trade_name" value="'.$pharmacy_inventory_data->trade_name.'" readonly=""></td><td><input type="text" name="composition" id="composition"  value="'.$pharmacy_inventory_data->composition.'" readonly=""></td><td><input type="text" name="quantity" id="quantity" value="'.$pharmacy_inventory_data->quantity.'"></td><td><input type="text" name="batch_no" id="batch_no"  value="'.$pharmacy_inventory_data->batch_no.'"></td><td><input type="text" name="expiry_date" id="expiry_date" value="'.date("d-m-Y",strtotime($pharmacy_inventory_data->expiry_date)).'"></td><td> <a class="btn btn-success btn-xs" id="'.$pharmacy_inventory_id.'" onclick = "pharmacy_inventory_save(\''.$pharmacy_inventory_id.'\');">save</a></td></tr>';
}

public function pharmacy_inventory_save(){
   $pharmacy_inventory_id = $this->input->post("id");
   // $param_1['trade_name'] = $this->input->post("trade_name");
   // $param_1['composition'] = $this->input->post("composition");
   $param_1['quantity'] = $this->input->post("quantity");
   $param_1['batch_no'] = $this->input->post("batch_no");
   $param_1['expiry_date'] = date("Y-m-d",strtotime($this->input->post("expiry_date")));
   $ok=$this->Generic_model->updateData('pharmacy_inventory', $param_1, array('pharmacy_inventory_id' => $pharmacy_inventory_id));

   $pharmacy_inventory_data_val = $this->db->query("select * from pharmacy_inventory a inner join drug b on (a.drug_id = b.drug_id) where a.pharmacy_inventory_id ='".$pharmacy_inventory_id."'")->row();

   $drug_id = $pharmacy_inventory_data_val->drug_id;

     //$drug_id = $this->input->post("id");
  $pharmacy_inventory_data = $this->db->query("select * from pharmacy_inventory a inner join drug b on (a.drug_id = b.drug_id) where a.drug_id ='".$drug_id."'")->result();
 // $data = "";
  foreach ($pharmacy_inventory_data as  $value) {
    
  
      echo '<tr><td>'.$value->trade_name.'</td><td>'.$value->composition.'</td><td>'.$value->quantity.'</td><td>'.$value->batch_no.'</td><td>'.date("d-m-Y",strtotime($value->expiry_date)).'</td><td> <a class="btn btn-success btn-xs" id="'.$value->pharmacy_inventory_id.'" onclick = "pharmacy_inventory_edit(\''.$value->pharmacy_inventory_id.'\');"><i class="fa fa-edit"></i></a></td></tr>';
    }


     // echo '<tr><td>'.$pharmacy_inventory_data->trade_name.'</td><td>'.$pharmacy_inventory_data->composition.'</td><td>'.$pharmacy_inventory_data->quantity.'</td><td>'.$pharmacy_inventory_data->batch_no.'</td><td>'.date("d-m-Y",strtotime($pharmacy_inventory_data->expiry_date)).'</td><td> <a class="btn btn-success btn-xs" id="'.$pharmacy_inventory_id.'" onclick = "pharmacy_inventory_edit(\''.$pharmacy_inventory_id.'\');"><i class="fa fa-edit"></i></a></td></tr>';
  
}

function bulk_save(){
  $this->load->library('excel');
$clinic_id = $this->session->userdata('clinic_id');
         if ($this->input->post('importfile')) {
            $path = './uploads/Inventory_bulk/';
            $config['upload_path'] = './uploads/Inventory_bulk/';
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
            $createArray = array('drug','batch_no','quantity','expiry_date');
            $makeArray = array('drug' =>'drug','batch_no' =>'batch_no','quantity' =>'quantity','expiry_date' =>'expiry_date');
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
                  $drug = $SheetDataKey['drug'];
                  $batch_no = $SheetDataKey['batch_no'];
                  $quantity = $SheetDataKey['quantity'];
                  $expiry_date = $SheetDataKey['expiry_date'];

                  $d_drug = filter_var(trim($allDataInSheet[$i][$drug]), FILTER_SANITIZE_STRING);
                  $d_batch_no = filter_var(trim($allDataInSheet[$i][$batch_no]), FILTER_SANITIZE_STRING);
                  $d_quantity = filter_var(trim($allDataInSheet[$i][$quantity]), FILTER_SANITIZE_STRING);
                  $d_expiry_date = filter_var(trim($allDataInSheet[$i][$expiry_date]), FILTER_SANITIZE_STRING);
                
                   $checking_drug  = $this->db->query("select * from drug where trade_name like '".$d_drug."%'")->row();
                   $drug_id = $checking_drug->drug_id;
                   $clinic_id = $clinic_id;
                   $supplied_date = date("Y-m-d");

                   $fetchdata2 = array('drug_id'=>$drug_id, 'clinic_id'=>$clinic_id, 'batch_no'=> $d_batch_no, 'quantity'=>$d_quantity, 'supplied_date'=>$supplied_date, 'expiry_date'=>date("Y-m-d",strtotime($d_expiry_date)), 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));
                        $this->Generic_model->insertData('pharmacy_inventory', $fetchdata2);
                }
              }else{
                 echo "Please import correct file";
              }
              redirect('Inventory');

            }
    }

}
?>