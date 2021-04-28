<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Drug extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
    }

    //Drug Search
    // public function index_get($clinic_id,$search)
    // {
    //     // echo $clinic_id;
    //     if(!empty(isset($_GET)))
    //     {
    //         // extract($_POST);
    //         $pharmaInfo = $this->db->select("*")->from("clinic_pharmacy_inventory")->where("clinic_id",$clinic_id)->get()->row();
    //     //   print_r( $pharmaInfo);
    //         if(count($pharmaInfo)>0)
    //         {
    //             $drugInfo = $this->db->query("select drug_id,trade_name,
    //             formulation,hsn_code,category from drug where
    //             trade_name LIKE '".urldecode($search)."%' LIMIT 20")
    //             ->result();

    //             $i = 0;
    //             foreach($drugInfo as $value)
    //             {
    //                 $stock = getStockInfo($clinic_id,$value->drug_id);
    //                 if(count($stock[$i])>0)
    //                 {
    //                     $stockInfo = $stock;
    //                 }
    //                 else
    //                 {
    //                     $stockInfo[$i]->clinic_id = $clinic_id;
    //                     $stockInfo[$i]->batch_no = '';
    //                     $stockInfo[$i]->drug_id = $value->drug_id;
    //                     $stockInfo[$i]->quantity_supplied = '';
    //                     $stockInfo[$i]->expiry_date = '';
    //                     $stockInfo[$i]->status = 1;
    //                     $stockInfo[$i]->available_quantity = 0;
    //                     $stockInfo[$i]->trade_name = $value->trade_name;
    //                     $stockInfo[$i]->formulation = $value->formulation;
    //                     $stockInfo[$i]->category = $value->category; 
    //                 }
    //                 $i++;
    //             }
    //             // echo "<pre>".print_r($stockInfo)."</pre>";
    //             $data = $stockInfo;
    //             $this->response($data);
    //         }
    //         else
    //         {
    //             $i = 0;
    //             $drugInfo = $this->db->query("select drug_id,trade_name,formulation,
    //             hsn_code from drug where trade_name LIKE '".urldecode($search)."%' LIMIT 20")
    //             ->result();
    //             foreach($drugInfo as $value)
    //             {
    //                 $stockInfo[$i]->clinic_id = $clinic_id;
    //                 $stockInfo[$i]->batch_no = '';
    //                 $stockInfo[$i]->drug_id = $value->drug_id;
    //                 $stockInfo[$i]->quantity_supplied = '';
    //                 $stockInfo[$i]->expiry_date = '';
    //                 $stockInfo[$i]->status = 1;
    //                 $stockInfo[$i]->available_quantity = 0;
    //                 $stockInfo[$i]->trade_name = $value->trade_name;
    //                 $stockInfo[$i]->formulation = $value->formulation;
    //                 $stockInfo[$i]->category = $value->category; 
    //                 $i++;
    //             }
                
    //             $this->response($stockInfo);
    //         }
    //     }
    //     else
    //     {
    //         $data = "UnAuthorized Access";
    //         $this->response($data);
    //     }
    // }

    //Drug Search Android
    public function index_get($clinic_id,$search)
    {
        // echo $clinic_id;
        if(!empty(isset($_GET)))
        {
            // extract($_POST);
            $pharmaInfo = $this->db->select("*")->from("clinic_pharmacy_inventory")->where("clinic_id",$clinic_id)->get()->row();
        //   print_r( $pharmaInfo);
            if(count($pharmaInfo)>0)
            {
                $drugInfo = $this->db->query("select drug_id,trade_name,formulation,hsn_code,category from drug where trade_name LIKE '".urldecode($search)."%' LIMIT 20")->result();

                $i = 0;
                foreach($drugInfo as $value)
                {
                    $stock = getStockInfo($clinic_id,$value->drug_id);
                    // print_r($stock);
                    if(count($stock[$i])>0)
                    {
                        $stockInfo = $stock;
                    }
                    else
                    {
                        $stockInfo[$i]->clinic_id = $clinic_id;
                        $stockInfo[$i]->batch_no = '';
                        $stockInfo[$i]->drug_id = $value->drug_id;
                        $stockInfo[$i]->quantity_supplied = '';
                        $stockInfo[$i]->expiry_date = '';
                        $stockInfo[$i]->status = 1;
                        $stockInfo[$i]->available_quantity = 0;
                        $stockInfo[$i]->trade_name = $value->trade_name;
                        $stockInfo[$i]->formulation = $value->formulation;
                        $stockInfo[$i]->category = $value->category; 
                    }
                    $i++;
                }
                // echo "<pre>".print_r($stockInfo)."</pre>";
                $data = $stockInfo;
                $this->response($data);
                // $this->response(array('code' => '200', 'message' => 'Drugs Info', 'result' => $data, 'requestname' => $method));
            }
            else
            {
                $i = 0;
                $drugInfo = $this->db->query("select drug_id,trade_name,formulation,
                hsn_code from drug where trade_name LIKE '".urldecode($search)."%' LIMIT 20")
                ->result();
                foreach($drugInfo as $value)
                {
                    $stockInfo[$i]->clinic_id = $clinic_id;
                    $stockInfo[$i]->batch_no = '';
                    $stockInfo[$i]->drug_id = $value->drug_id;
                    $stockInfo[$i]->quantity_supplied = '';
                    $stockInfo[$i]->expiry_date = '';
                    $stockInfo[$i]->status = 1;
                    $stockInfo[$i]->available_quantity = 0;
                    $stockInfo[$i]->trade_name = $value->trade_name;
                    $stockInfo[$i]->formulation = $value->formulation;
                    $stockInfo[$i]->category = $value->category; 
                    $i++;
                }
                
                $this->response($stockInfo);
            }
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response($data);
        }
    }

    //New drug Search
    public function DrugSearch_get($clinic_id,$search)
    {
        extract($_GET);
        $check = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id='".$clinic_id."'")->num_rows();
        // echo $check;
        if($check > 0)
        {
            $clinicCheck = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward CPI,drug d where d.drug_id=CPI.drug_id and CPI.clinic_id='".$clinic_id."' and CPI.archieve=0 and d.trade_name LIKE '%".urldecode($search)."%' group by CPI.batch_no,CPI.drug_id")->result();
            if(count($clinicCheck)>0)
            {
                $i = 0;
                foreach($clinicCheck as $result)
                {
                    $outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=" . $result->drug_id . " and batch_no='" . $result->batch_no . "' and clinic_id=" . $result->clinic_id)->row();
                    if(($result->oqty - $outward->ouqty) <= 0)
                    {
                        $qty = 0;
                    }
                    else
                    {
                        $qty = $result->oqty - $outward->ouqty;
                        $qty = ($qty<0)?'0':$qty;
                    }
                    if($qty == 0)
                        continue;
                    $stockInfo[$i]->clinic_id = $clinic_id;
                    $stockInfo[$i]->batch_no = $result->batch_no;
                    $stockInfo[$i]->drug_id = $result->drug_id;
                    $stockInfo[$i]->quantity_supplied = $result->quantity;
                    $stockInfo[$i]->expiry_date = $result->expiry_date;
                    $stockInfo[$i]->status = 1;
                    $stockInfo[$i]->available_quantity = $qty;
                    $stockInfo[$i]->trade_name = $result->trade_name;
                    $stockInfo[$i]->formulation = $result->formulation;
                    $stockInfo[$i]->category = $result->category;
                    $ids[$i] = $result->drug_id;
                    $i++; 
                }
            }
            else
            {
                $drugInfo = $this->db->query("select * from drug where trade_name LIKE '%".urldecode($search)."%' order by trade_name ASC LIMIT 20")->result();
                if(count($drugInfo)>0)
                {
                    $i = 0;
                    foreach($drugInfo as $value)
                    {
                        $stockInfo[$i]->clinic_id = $clinic_id;
                        $stockInfo[$i]->batch_no = '';
                        $stockInfo[$i]->drug_id = $value->drug_id;
                        $stockInfo[$i]->quantity_supplied = '';
                        $stockInfo[$i]->expiry_date = '';
                        $stockInfo[$i]->status = 0;
                        $stockInfo[$i]->available_quantity = 0;
                        $stockInfo[$i]->trade_name = $value->trade_name;
                        $stockInfo[$i]->formulation = $value->formulation;
                        $stockInfo[$i]->category = $value->category;
                        $ids[$i] = $value->drug_id;
                        $i++;
                    }
                }
                else
                {
                    $stockInfo = [];
                }
            }
        }
        else
        {
            $drugInfo = $this->db->query("select * from drug where trade_name LIKE '%".urldecode($search)."%' order by trade_name ASC LIMIT 20")->result();
            if(count($drugInfo)>0)
            {
                $i = 0;
                foreach($drugInfo as $value)
                {
                    $stockInfo[$i]->clinic_id = $clinic_id;
                    $stockInfo[$i]->batch_no = '';
                    $stockInfo[$i]->drug_id = $value->drug_id;
                    $stockInfo[$i]->quantity_supplied = '';
                    $stockInfo[$i]->expiry_date = '';
                    $stockInfo[$i]->status = 0;
                    $stockInfo[$i]->available_quantity = 0;
                    $stockInfo[$i]->trade_name = $value->trade_name;
                    $stockInfo[$i]->formulation = $value->formulation;
                    $stockInfo[$i]->category = $value->category;
                    $ids[$i] = $value->drug_id;
                    $i++;
                }
            }
            else
            {
                $stockInfo = [];
            }
        }
        // echo "<pre>";
        // print_r($ids);
        // echo "</pre>";
        if(count($stockInfo)<20)
        {
            $count = count($stockInfo);
            // echo "<br>";
            $twenty = 20;
            $limit = $twenty-$count;
            $drugInfo = $this->db->query("select * from drug where trade_name LIKE '%".urldecode($search)."%' order by trade_name ASC LIMIT ".$limit)->result();
            if(count($drugInfo)>0)
            {
                $i = $count;
                foreach($drugInfo as $value)
                {
                    // echo "<br>".$value->drug_id."<br>";
                    if(in_array($value->drug_id,$ids))
                    {
                        continue;
                    }
                    else
                    {
                        $stockInfo[$i]->clinic_id = $clinic_id;
                        $stockInfo[$i]->batch_no = '';
                        $stockInfo[$i]->drug_id = $value->drug_id;
                        $stockInfo[$i]->quantity_supplied = '';
                        $stockInfo[$i]->expiry_date = '';
                        $stockInfo[$i]->status = 0;
                        $stockInfo[$i]->available_quantity = 0;
                        $stockInfo[$i]->trade_name = $value->trade_name;
                        $stockInfo[$i]->formulation = $value->formulation;
                        $stockInfo[$i]->category = $value->category;
                    }
                    $i++;
                }
            }
            else
            {
                $stockInfo = [];
            }
        }
        // echo count($stockInfo);
        ksort($stockInfo);
        $data['medicine_object']['drugs'] = $stockInfo;
        ksort($data['medicine_object']['drugs']);
        $this->response(array('code'=>'200','message'=>'success','result'=>$data));
    }

    // Delete Prescription Drug
    public function delPresDrug_get($id)
    {
        $this->Generic_model->deleteRecord('patient_prescription_drug',array('patient_prescription_drug_id'=>$id));
        $this->response(array('code'=>'200','message'=>'Success','result'=>'Successfully Deleted'));
    }

    public function androidDrug_get($clinic_id,$search)
    {
        // echo $clinic_id;
        if(!empty(isset($_GET)))
        {
            // extract($_POST);
            $pharmaInfo = $this->db->select("*")->from("clinic_pharmacy_inventory")
            ->where("clinic_id",$clinic_id)
            ->get()->row();

            if(count($pharmaInfo)>0)
            {

                $drugInfo = $this->db->query("select * from clinic_pharmacy_inventory m JOIN drug p ON p.drug_id=m.drug_id where
                p.trade_name LIKE '".urldecode($search)."%' and clinic_id='".$clinic_id."' ORDER BY m.drug_id DESC LIMIT 20")
                ->result();

            
                if(count($drugInfo)>0)
                {
                    $i = 0;
                    foreach($drugInfo as $value)
                    {
                   
                            $today = date('Y-m-d');	

                            $nxt_date = strtotime("+3 month");
                
                            $inward =  $this->db->select("clinic_id, batch_no, drug_id, sum(quantity) as quantity_supplied, expiry_date, status")
                            ->from("clinic_pharmacy_inventory_inward")
                            ->where("clinic_id = '" . $clinic_id . "' AND drug_id='".$value->drug_id."' AND expiry_date > '".$today."' 
                            and status = 1 AND archieve=0")
                            ->group_by("drug_id")->get()->result();
           
                            if (count($inward) > 0) {
                  
                                foreach ($inward as $pharmacy_inventory) {
                                    
                                    $drugInfoo = $this->db->query("select * from drug where drug_id = '".$pharmacy_inventory->drug_id."' ")->row();
                    
                                    $outward = $this->db->select("batch_no, drug_id, sum(quantity) as quantity_sold")
                                    ->from("clinic_pharmacy_inventory_outward")
                                    ->where("batch_no = '" . $pharmacy_inventory->batch_no . "' 
                                    and drug_id = '" . $pharmacy_inventory->drug_id . "'
                                    AND clinic_id = '" . $clinic_id . "' ")
                                     ->group_by("drug_id")->get()->row();
                             
                                    if (count($outward)) {
                                        $qty_supplied = $pharmacy_inventory->quantity_supplied;
                                        $qty_sold = $outward->quantity_sold;
                    
                                        // available quantity
                                        $pharmacy_inventory->available_quantity = (string)($qty_supplied - $qty_sold);
                                    } else {
                                        // available quantity
                                        $pharmacy_inventory->available_quantity = (string) ($pharmacy_inventory->quantity_supplied);
                                    }
                    
                                    // 
                                    
                                    $stockInfo[$i]['clinic_id'] = $clinic_id;
                                    $stockInfo[$i]['batch_no'] = $pharmacy_inventory->batch_no;
                                    $stockInfo[$i]['drug_id'] = $pharmacy_inventory->drug_id;
                                    $stockInfo[$i]['quantity_supplied'] = $pharmacy_inventory->available_quantity;
                                    $stockInfo[$i]['expiry_date'] = $pharmacy_inventory->expiry_date;
                                    $stockInfo[$i]['status'] = 1;
                                    $stockInfo[$i]['trade_name'] = $drugInfoo->trade_name;
                                    // $stockInfo[$i]['available_quantity'] = $pharmacy_inventory->available_quantity;
                                    // $para[$i][] = $drugInfo->trade_name;
                                    // $para[$i][]->trade_name = htmlspecialchars_decode("<strong>".$drugInfo->trade_name."</strong>&emsp;<label class='badge badge-primary'>".$pharmacy_inventory->expiry_date."</label>&emsp;<label class='badge badge-success'>QTY : ".$pharmacy_inventory->available_quantity."</label>");
                                    $stockInfo[$i]['formulation'] = $drugInfoo->formulation;
                                    $stockInfo[$i]['category'] = $drugInfoo->category;
                                    
                                    // $i++;
                                }
                            } 

                        $i++;
                    }
                    if(count($drugInfo)<21)
                    {
                        $limit = 20-count($drugInfo);
                        $drugInfoMaster = $this->db->query("select * from drug  where
                        trade_name LIKE '".urldecode($search)."%' and drug_id NOT IN(SELECT drug_id FROM clinic_pharmacy_inventory_inward)
                         LIMIT $limit")
                        ->result();
                        $a = 0;
                        foreach($drugInfoMaster as $valuee)
                        {
                            $masterInfo[$a]->clinic_id = $clinic_id;
                            $masterInfo[$a]->batch_no = '';
                            $masterInfo[$a]->drug_id = $valuee->drug_id;
                            $masterInfo[$a]->quantity_supplied = '';
                            $masterInfo[$a]->expiry_date = '';
                            $masterInfo[$a]->status = 1;
                            $masterInfo[$a]->available_quantity = 0;
                            $masterInfo[$a]->trade_name = $valuee->trade_name;
                            $masterInfo[$a]->formulation = $valuee->formulation;
                            $masterInfo[$a]->category = $valuee->category; 
                            $a++;
                        }
                    }
                    $data['medicine_object']['drugs'] = (array_merge($stockInfo,$masterInfo));
                    // $data['medicine_object']['drugs'] = $stockInfo;
                    $this->response(array('code' => '200', 'message' => 'Drugs Info', 'result' => $data, 'requestname' => 'drug'));
                }
                else
                {
                    $i = 0;
                    $drugInfo = $this->db->query("select drug_id,trade_name,formulation,
                    hsn_code from drug where trade_name LIKE '".urldecode($search)."%' LIMIT 20")
                    ->result();
                    foreach($drugInfo as $value)
                    {
                        $stockInfo[$i]->clinic_id = $clinic_id;
                        $stockInfo[$i]->batch_no = '';
                        $stockInfo[$i]->drug_id = $value->drug_id;
                        $stockInfo[$i]->quantity_supplied = '';
                        $stockInfo[$i]->expiry_date = '';
                        $stockInfo[$i]->status = 1;
                        $stockInfo[$i]->available_quantity = 0;
                        $stockInfo[$i]->trade_name = $value->trade_name;
                        $stockInfo[$i]->formulation = $value->formulation;
                        $stockInfo[$i]->category = $value->category; 
                        $i++;
                    }
                    $data['medicine_object']['drugs'] = $stockInfo;
                    $this->response(array('code' => '200', 'message' => 'Drugs Info', 'result' => $data, 'requestname' => 'drug'));
                }
     
            }
            else
            {
                $i = 0;
                $drugInfo = $this->db->query("select drug_id,trade_name,formulation,
                hsn_code from drug where trade_name LIKE '".urldecode($search)."%' LIMIT 20")
                ->result();
                foreach($drugInfo as $value)
                {
                    $stockInfo[$i]->clinic_id = $clinic_id;
                    $stockInfo[$i]->batch_no = '';
                    $stockInfo[$i]->drug_id = $value->drug_id;
                    $stockInfo[$i]->quantity_supplied = '';
                    $stockInfo[$i]->expiry_date = '';
                    $stockInfo[$i]->status = 1;
                    $stockInfo[$i]->available_quantity = 0;
                    $stockInfo[$i]->trade_name = $value->trade_name;
                    $stockInfo[$i]->formulation = $value->formulation;
                    $stockInfo[$i]->category = $value->category; 
                    $i++;
                }
                $data['medicine_object']['drugs'] = $stockInfo;
                $this->response(array('code' => '200', 'message' => 'Drugs Info', 'result' => $data, 'requestname' => 'drug'));
            }
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response(array('code' => '201', 'message' => 'Drugs Info', 'result' => $data, 'requestname' => 'drug'));
            // $this->response($data);
        }
    }

}
?>