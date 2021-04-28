<?php

error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/controllers/Indent.php';
class Pharmacy_orders extends Indent {

    public function __construct() {

        parent::__construct();

        $this->load->library('mail_send', array('mailtype' => 'html'));

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    }

    public function index() {
        $c_date = date('Y-m-d');
        $lt_date = date("Y-m-d", strtotime("+3 month"));
        $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';
        if ($clinic_id != 0)
            $cond = "and b.clinic_id=" . $clinic_id;
        if ($clinic_id == 0) {
            $expired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' group by b.drug_id,b.batch_no")->result_array();
            $sexpired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' and b.expiry_date>'" . $c_date . "' group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();
            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 group by a.drug_id")->result_array();
        } else {
            $expired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no")->result_array();
            $sexpired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' and b.expiry_date>'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();

            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.clinic_id=" . $clinic_id . " and a.archieve=0 and (a.expiry_date<'" . $c_date . "' or a.expiry_date>'" . $c_date . "')  group by a.drug_id order by a.expiry_date ASC")->result_array();
        }
        $data['expired'] = array();
        $data['sexpired'] = array();
        $data['shortage'] = array();
        $ei = 0;
        $sei = 0;
        $shi = 0;

        // $data['permissions'] = $this->db->query("select * from clinic_role_property_permissions where clinic_id='".$clinic_id."' and clinic_role_id IN (".$this->session->userdata('assigned_roles').") and pharmacy_del_access='1'")->row();
        // echo $this->db->last_query();
        // exit;

        foreach ($expired as $eresult) {
            $data['expired'][$ei]['drug_name'] = $eresult['trade_name'];
            $data['expired'][$ei]['batch_no'] = $eresult['batch_no'];
            $data['expired'][$ei]['drug_id'] = $eresult['drug_id'];
            $data['expired'][$ei]['formulation'] = $eresult['formulation'];
            $data['expired'][$ei]['clinic_pharmacy_inventory_inward_id'] = $eresult['clinic_pharmacy_inventory_inward_id'];
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "'")->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
            }
            $data['expired'][$ei]['quantity'] = ($eresult['oqty'] - $outqnt->qty);
            $data['expired'][$ei]['edate'] = $eresult['expiry_date'];
            $ei++;
        }
        foreach ($sexpired as $seresult) {
            $data['sexpired'][$sei]['drug_name'] = $seresult['trade_name'];
            $data['sexpired'][$sei]['batch_no'] = $seresult['batch_no'];
            $data['sexpired'][$sei]['drug_id'] = $seresult['drug_id'];
            $data['sexpired'][$sei]['formulation'] = $seresult['formulation'];
            $data['sexpired'][$sei]['clinic_pharmacy_inventory_inward_id'] = $seresult['clinic_pharmacy_inventory_inward_id'];
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "'")->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
            }

            $data['sexpired'][$sei]['quantity'] = ($seresult['oqty'] - $outqnt->qty);
            $data['sexpired'][$sei]['edate'] = $seresult['expiry_date'];
            $sei++;
        }
        foreach ($shortage as $ssresult) {
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'])->row();
                $shqty = $this->db->query("select * from clinic_pharmacy_inventory where drug_id=" . $ssresult['drug_id'])->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'] . " and clinic_id=" . $clinic_id)->row();
                $shqty = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $ssresult['drug_id'])->row();
            }

            $actual_qty = ($ssresult['oqty'] - $outqnt->qty);

            // echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
            if (($actual_qty <= $shqty->reorder_level) && ($shqty->indent_status == 0)) {
                // echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
                $data['shortage'][$shi]['drug_id'] = $ssresult['drug_id'];
                $data['shortage'][$shi]['drug_name'] = $ssresult['trade_name'];
                $data['shortage'][$shi]['batch_no'] = $ssresult['batch_no'];
                $data['shortage'][$shi]['quantity'] = $actual_qty;
                $data['shortage'][$shi]['edate'] = $ssresult['expiry_date'];
                $data['shortage'][$shi]['vendor_id'] = $shqty->vendor_id;
            }
            $shi++;
        }


        $data['pinfo'] = $this->db->query("select *,sum(quantity) as invSum from clinic_pharmacy_inventory_inward CPI,drug d where d.drug_id=CPI.drug_id  and CPI.archieve=0 and CPI.clinic_id='".$clinic_id."' group by CPI.batch_no,CPI.drug_id")->result();
        // echo $this->db->last_query();
        $pi = 0;
        // echo count($data['pinfo']);
        // exit;

        foreach ($data['pinfo'] as $result) {
            $disinfo = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $result->drug_id)->row();
            $outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=" . $result->drug_id . " and batch_no='" . $result->batch_no . "' and clinic_id=" . $result->clinic_id)->row();
            // echo $this->db->last_query();
            $sch_salt = array();

            if ($result->salt_id == "" || $result->salt_id == NULL) {
                $scheduled_salt = "";
            } else {
                $imp = "'" . implode("','", explode(",", $result->salt_id)) . "'";
                $salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
                $scheduled_salt = trim($salt_id->scheduled_salt, ",");
                $explode = explode(",", $scheduled_salt);
                foreach ($explode as $key => $svalue) {
                    $sch_salt[] = "<span id=" . trim($svalue) . ">" . trim($svalue) . "</span>";
                }
            }
            if(($result->invSum - $outward->ouqty) <= 0)
                $qty = 0;
            else
                $qty = $result->invSum - $outward->ouqty;

            if($qty <= 0){
                continue;
            }
            $data['parinfo'][$pi]['clinic_pharmacy_inventory_inward_id'] = $result->clinic_pharmacy_inventory_inward_id;
            $data['parinfo'][$pi]['trade_name'] = $result->trade_name . " " . implode(" ", $sch_salt);
            $data['parinfo'][$pi]['drug_id'] = $result->drug_id;
            $data['parinfo'][$pi]['formulation'] = $result->formulation;
            $data['parinfo'][$pi]['composition'] = $result->composition;
            $data['parinfo'][$pi]['batch_no'] = $result->batch_no;
            $data['parinfo'][$pi]['oqty'] = $qty;
            $data['parinfo'][$pi]['mrp'] = round($result->mrp, 2);
            $data['parinfo'][$pi]['reorder_level'] = $disinfo->reorder_level;
            $data['parinfo'][$pi]['hsn_code'] = $result->hsn_code;
            $data['parinfo'][$pi]['igst'] = $disinfo->igst;
            $data['parinfo'][$pi]['cgst'] = $disinfo->cgst;
            $data['parinfo'][$pi]['sgst'] = $disinfo->sgst;
            $data['parinfo'][$pi]['disc'] = $disinfo->max_discount_percentage;
            $data['parinfo'][$pi]['vendor_id'] = $disinfo->vendor_id;
            $data['parinfo'][$pi]['pack_size'] = $result->pack_size;
            $data['parinfo'][$pi]['expiry_date'] = $result->expiry_date;
            $pi++;
        }
        // echo "<pre>";print_r($data['parinfo']);echo "</pre>";
        // exit;

        $data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='" . $clinic_id . "'")->result();
        $data['view'] = 'Pharmacy_orders/Pharmacy_orders';

        $this->load->view('layout', $data);
    }


    //Returns of pharmacy
    public function Returns(){
        if(isset($_POST['rsubmit'])){
            extract($_POST);
            // exit;
            if(count($total) > 0){
                $billInfo = $this->Generic_model->getSingleRecord('billing', array('billing_id'=>$billing_id));
                $prdata['billing_id'] = $billing_id;
                $prdata['status'] = 1;
                $prdata['created_by'] = $this->session->userdata('user_id');
                $prdata['created_date_time'] = date('Y-m-d H:i:s');
                $prdata['modified_by'] = $this->session->userdata('user_id');
                $prdata['modified_date_time'] = date('Y-m-d H:i:s');
                $pharmacy_return_id = $this->Generic_model->insertDataReturnId('pharmacy_returns', $prdata);
                $totalAmt = 0;
                for($i = 0;$i < count($qty);$i++){
                    $disc = 0;
                    $price = 0;
                    $drugInfo = $this->db->query("select unit_price,discount,discount_unit from billing_line_items where billing_line_item_id='".$billing_line_item_id[$i]."'")->row();
                    
                    $price = $drugInfo->unit_price * $qty[$i];
                    if($drugInfo->discount_unit == "%"){
                        $disc = $price - (($price * $drugInfo->discount)/100);
                    }
                    elseif($drugInfo->discount_unit == "INR"){
                        $disc = $price-$drugInfo->discount;
                    }
                    $totalAmt += $disc;
                    $data['pharmacy_returns_id'] = $pharmacy_return_id;
                    $data['billing_line_item_id'] = $billing_line_item_id[$i];
                    $data['total_qty'] = $total[$i];
                    $data['return_qty'] = $qty[$i];
                    $data['drug_id'] = $drug_id[$i];
                    $data['batch_no'] = $batch_no[$i];
                    $data['status'] = 1;
                    $data['created_by'] = $this->session->userdata('user_id');
                    $data['created_date_time'] = date('Y-m-d H:i:s');
                    $data['modified_by'] = $this->session->userdata('user_id');
                    $data['modified_date_time'] = date('Y-m-d H:i:s');
                    $this->Generic_model->insertData('pharmacy_returns_line_items', $data);

                    // $inventory = $this->Generic_model->getSingleRecord('clinic_pharmacy_inventory_inward', array('clinic_id'=>$billInfo->clinic_id,'drug_id'=>$drug_id[$i],'batch_no'=>$batch_no[$i]));
                    // echo "<br>".$inventory->quantity;
                    // $udata['quantity'] = $inventory->quantity+$qty[$i];
                    $outwardInfo = $this->Generic_model->getSingleRecord('clinic_pharmacy_inventory_outward', array('clinic_id'=>$billInfo->clinic_id,'drug_id'=>$drug_id[$i],'batch_no'=>$batch_no[$i],'billing_id'=>$billing_id));
                    // echo $this->db->last_query();
                    $odata['quantity'] = $outwardInfo->quantity-$qty[$i];


                    // $inwardUpdate = $this->Generic_model->updateData('clinic_pharmacy_inventory_inward', $udata, array('clinic_pharmacy_inventory_inward_id'=>$inventory->clinic_pharmacy_inventory_inward_id));    
                    $outwardUpdate = $this->Generic_model->updateData('clinic_pharmacy_inventory_outward', $odata, array('clinic_pharmacy_inventory_outward_id'=>$outwardInfo->clinic_pharmacy_inventory_outward_id));    
                    // $outward = $this->Generic_model->deleteRecord('clinic_pharmacy_inventory_outward', array('billing_id'=>$billing_id));
                }
                // exit;

                $bdata['return_status'] = 1;
                $this->Generic_model->updateData('billing', $bdata, array('billing_id'=>$billing_id));
                $this->session->set_flashdata('showBill', number_format($totalAmt, 2));
                redirect('Pharmacy_orders/returns');

                // exit;


            }
        }
        else{

        }
        $data['view'] = "Pharmacy_orders/returns";
        $this->load->view('layout', $data);
    }

    public function ReturnsData(){
        $clinic_id = $this->session->userdata('clinic_id');
        if(isset($_POST)){
            extract($_POST);
            $returnInfo = $this->db->query("select *,pr.created_date_time as return_date from pharmacy_returns pr, billing b where pr.billing_id=b.billing_id and b.clinic_id='".$clinic_id."' and b.billing_type='Pharmacy' and DATE(pr.created_date_time) >='".date("Y-m-d",strtotime($date_from)) ."' and DATE(pr.created_date_time) <='".date("Y-m-d",strtotime($date_to)) ."' order by pr.pharmacy_returns_id DESC")->result();
            // echo $this->db->last_query();
            if(count($returnInfo) > 0){
                $i = 1;
                foreach($returnInfo as $value){
                    $billLineItems = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id'=>$value->billing_id));
                    $returnLineItems = $this->Generic_model->getAllRecords('pharmacy_returns_line_items', array('pharmacy_returns_id'=>$value->pharmacy_returns_id));
                    ?>
                    <tr>
                        <td style="width:5%"><?=$i?></td>
                        <td style="width:10%"><span><?=$value->invoice_no?></span>
                            <p class="p-0 m-0"><?=date('Y-m-d', strtotime($value->return_date))?></p>
                        </td>
                        <td style="width:20%">
                            <?php
                            if($value->patient_id!="")
                            {
                                if($value->title == "")
                                {
                                    $pname = $value->pname." ".$value->lname;
                                }
                                else
                                {
                                    $pname = $value->title.". ".$value->pname." ".$value->lname;
                                }
                                echo '<span class="trade_name">'.$pname.'</span><span class="formulation">'.$value->umr_no.'</span><br>'.DataCrypt($value->pmob,'decrypt');
                            }
                            else
                            {
                                echo '<span class="trade_name">'.$value->guest_name.'</span><br>'.$value->guest_mobile;
                            }
                            ?>
                        </td> 
                        <td style="padding:0px !important;">
                        <?php 
                        if(count($returnLineItems) > 0){
                            ?>
                            <table class="customTable w-100" style="margin:0px !important">
                                <tr>
                                    <th style="width:65%">Trade Name</th>
                                    <th style="width:35%">Quantity Taken - Return</th>
                                </tr>
                                <?php 
                                foreach($returnLineItems as $val){
                                    $drugInfo = getDrugInfo($val->drug_id);
                                    ?>
                                    <tr>
                                        <td><span><?=$drugInfo->trade_name?></span>
                                        <p class="m-0 p-0"><span class="code m-0">Batch : <?=$val->batch_no?></span></p>
                                        </td>
                                        <td><span><?=$val->total_qty." - ".$val->return_qty?></span></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                        }
                        else{
                            ?>
                            <span>No Line Items Found.</span>
                            <?php
                        }
                        ?>
                        </td>
                        <td style="width:10%" class="text-center">
                            <a href="<?=base_url('Pharmacy_orders/printReturn/'.$value->pharmacy_returns_id)?>" >
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            else{
                ?>
                <span>No Items Found.</span>
                <?php
            }
            

        }
    }

    public function getBillInfo(){
        extract($_POST);
        $billInfo = $this->Generic_model->getSingleRecord('billing', array('invoice_no' => $invoice_no));
        if(count($billInfo) > 0){
            if($billInfo->return_status == 1){
                ?>
                    <p class="text-center text-primary font-weight-bold" style="padding:0px !important">Already Returned.</p>
                <?php
            }
            else{
                ?>
                <table class="customTable">
                    <tr>
                        <th>INvoice No & Date</th>
                        <th>Customer Info</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>
                            <span><?=$billInfo->invoice_no?></span>
                            <p style="margin:0px !important;padding:0px !important"><?=date('d-m-Y', strtotime($billInfo->created_date_time))?></p>
                        </td>
                        <td>
                        <?php 
                        if($billInfo->patient_id!="")
                        {
                            if($billInfo->title == "")
                            {
                                $pname = $billInfo->pname." ".$billInfo->lname;
                            }
                            else
                            {
                                $pname = $billInfo->title.". ".$billInfo->pname." ".$billInfo->lname;
                            }
                            echo '<span class="trade_name">'.$pname.'</span><span class="formulation">'.$billInfo->umr_no.'</span><br>'.DataCrypt($billInfo->pmob,'decrypt');
                        }
                        else
                        {
                            echo '<span class="trade_name">'.$billInfo->guest_name.'</span><br>'.$billInfo->guest_mobile;
                        }
                        ?>
                        </td>
                        <td>
                            <button class="btn btn-app getBillInfo" data-toggle="modal" data-target="#returnsModal" data-id="<?=$billInfo->billing_id?>">View</button>
                        </td>
                    </tr>
                </table>
                <?php
            }
            
        }
        else{
            ?>
                <p class="text-center text-primary font-weight-bold" style="padding:0px !important">No Records Found.</p>
            <?php
        }
    }

    
public function Pharmacy_Billings(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if(count($this->input->post())>0){
		$data['from'] = $this->input->post('date_from');
		$data['to'] = $this->input->post('date_to');
		
	if($clinic_id!=0)
		$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
	else
		$cond = "where b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
		}
		else{
			if($clinic_id!=0)
		$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and b.created_date_time like'".date('Y-m-d')."%'";
	else
		$cond = "where b.billing_type='Pharmacy'  and b.created_date_time like'".date('Y-m-d')."%'";
		}

	$billings = $this->db->query("SELECT b.*,bi.discount as disc,bi.quantity,bi.unit_price,bi.discount_unit as disc_unit,p.first_name as pname,p.mobile as pmob,p.umr_no,p.title,p.last_name as lname,c.clinic_name,sum(bi.amount) as bamount,sum(bi.total_amount) as tamount
							FROM `billing` b
							left join billing_line_items bi on b.billing_id=bi.billing_id
							left join patients p on p.patient_id=b.patient_id
							left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
							group by bi.billing_id order by b.billing_id desc")->result();

							// echo  $this->db->last_query();
			  $i=1; 
			  if(count($billings)>0)
			  {

			  foreach ($billings as $value) { 
			  	// if($value->status==2)
			  	// 	continue;
		  		$discAmount = $value->tamount-$value->bamount;
		  		$discTotal = $discTotal+$discAmount;
			  	$total = $total + $value->bamount;
			  	$last =$i++;

			  	$price = number_format($value->quantity * $value->unit_price,2);
                
                // Accountable price if any discounts applying
                $accountablePrice =  number_format($price - ($price * ($value->disc / 100)),2);
                $totalPrice = $totalPrice + $price;
                $totalDiscount = number_format($totalDiscount + ($price * ($value->disc / 100)),2);
               
                // Taxation
                // Value inclding GST = mrp ($accountablePrice)
                // TaxValue = (mrp * 100)/(100 + CGST + SGST)
				$taxValue = number_format(($accountablePrice * 100)/(100 + $value->cgst + $value->sgst + $value->igst),2);
				if($value->status == 2)
					$payStatus = "Dropped";
				elseif($value->status == 1)
					$payStatus = "Payment Completed";	
			  ?> 
			  <tr>
			    <td class="text-center"><?php echo $last;?></td>
			    <td><span><?=$value->invoice_no?></span></td>
                <td><?=date("d-m-Y",strtotime($value->created_date_time))?></td>
			    <td>
			    	<?php
			    	if($value->patient_id!="")
			    	{
			    		if($value->title == "")
			    		{
			    			$pname = $value->pname." ".$value->lname;
			    		}
			    		else
			    		{
			    			$pname = $value->title.". ".$value->pname." ".$value->lname;
			    		}
			    		echo '<span class="trade_name">'.$pname.'</span><span class="formulation">'.$value->umr_no.'</span><br>'.DataCrypt($value->pmob,'decrypt');
			    	}
			    	else
			    	{
			    		echo '<span class="trade_name">'.$value->guest_name.'</span><br>'.$value->guest_mobile;
			    	}
			    	?>
			   	</td> 
			    <!-- <td>&nbsp;</td> -->
			    <td><span><?php echo number_format($value->bamount+$discAmount,2); ?></span></td> 
			    <!-- <td class="text-center"><span><?php echo number_format($discAmount,2); ?></span></td>  -->
			    <!-- <td class="text-right"><span><?php echo number_format($taxValue,2); ?></span></td>  -->
			    <td><span><?php echo number_format($value->bamount,2); ?></span></td> 
				<!-- <td class="text-center"><span><?=$payStatus?></span></td>
				<td class="text-center"><span><?php echo $value->payment_mode;?></span></td> -->
			  	<td class="text-center">
                  <a data-toggle="modal" data-target="#returnsModal" data-id="<?=$value->billing_id?>" class="getBillInfo"><i class="fa fa-eye"></i></a>
                  <!-- <a href="<?php echo base_url('new_order/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a> -->
                </td>
			  </tr>
            <?php }
            
			  }

}

public function printReturn($return_id){
    $clinic_id = $this->session->userdata('clinic_id');
    if($return_id != ""){

        $data['pharmacy_returns'] = $this->db->query("select * from pharmacy_returns pr, pharmacy_returns_line_items prl where pr.pharmacy_returns_id=prl.pharmacy_returns_id and pr.pharmacy_returns_id='".$return_id."'")->result();
        $data['billing'] = $billing = $this->db->query("select * from billing where billing_id='".$data['pharmacy_returns'][0]->billing_id."'")->row();
        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['billing']->clinic_id."'")->row();
        $data['doctorInfo'] = doctorDetails($billing->doctor_id);
        
        $checkPharmaInfo = $this->db->query("select * from clinic_pharmacy where clinic_id='".$clinic_id."'")->row();
        if(count($checkPharmaInfo) > 0){
            $data['clinicname'] = $checkPharmaInfo->name;
            $data['address'] = $checkPharmaInfo->address;
            $data['contact'] = $checkPharmaInfo->mobile;
            $data['logo'] = "uploads/pharmacy_logos/".$checkPharmaInfo->logo;
        }
        else{
            $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
            $data['clinicname'] = $clinicInfo->clinic_name;
            $data['address'] = $clinicInfo->address;
            $data['contact'] = $clinicInfo->clinic_phone;
            $data['logo'] = "uploads/clinic_logos/".$clinicInfo->clinic_logo;
        }

        if($billing->patient_id != ""){
            $data['pinfo'] = $pinfo = getPatientDetails($billing->patient_id);
            $data['patient_name'] = getPatientName($billing->patient_id);
            $data['age'] = $pinfo->age;
            $data['age_unit'] = $pinfo->age_unit;
            $data['gender'] = $pinfo->gender;
            $data['paddress'] = $pinfo->address;
            $data['pmobile'] = DataCrypt($pinfo->mobile, 'decrypt');
        }
        else{
            $data['patient_name'] = $billing->guest_name;
            $data['pmobile'] = DataCrypt($billing->guest_mobile, 'decrypt');
        }

        $this->load->library('M_pdf');
        $html = $this->load->view('Pharmacy_orders/returnsPrint',$data, true);
        $pdfFilePath = "TI-".$clinic_id.rand(1000,9999).date("dmY").".pdf";
        $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css");
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;

        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/returns/".$pdfFilePath, "F");
        $this->m_pdf->pdf->Output("./uploads/returns/".$pdfFilePath, "F");
        redirect('uploads/returns/'.$pdfFilePath);
    }
    else{
        redirect('Pharmacy_orders/Returns');
    }
}

public function getBillDetails(){
    extract($_POST);
    if(isset($billing_id)){
        $billInfo = $this->Generic_model->getSingleRecord('billing', array('billing_id'=>$billing_id));
        if(count($billInfo) > 0){
            if($billInfo->patient_id == "0" || $billInfo->patient_id == ""){
                // echo "hello";
                $name = $billInfo->guest_name;
                $mobile = $billInfo->guest_mobile;
            }
            else{
                // echo "hi";
                $name = getPatientName($billInfo->patient_id);
                $mobile = DataCrypt($billInfo->guest_mobile, 'decrypt');
            }
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!-- <h4 class="page-title">Billing Info </h4> -->
                        <p class="font-weight-bold" style="padding: 0px !important;margin-bottom:5px !important">Customer Name: <?=$name?>
                            <span class="pull-right">Date: <?=date('d/m/Y h:i A', strtotime($billInfo->created_date_time))?></span>
                        </p>
                        <p class="font-weight-bold" style="padding: 0px !important;margin-bottom:5px !important">Mobile Number: <?=$mobile?>
                            <span class="pull-right font-weight-bold">#Invoice No: <?=$billInfo->invoice_no?></span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 p-0 mt-4">
                    <form method="post" action="<?=base_url('Pharmacy_orders/returns')?>">
                        <table class="customTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="checkall"></th>
                                    <th>Item Information</th>
                                    <th style="width:15%">QTY</th>
                                    <th style="width:20%">Returning</th>
                                    <th>Batch No</th>
                                    <!-- <th>Unit Price</th> -->
                                    <th>Total AMT</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $billLineItems = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id'=>$billing_id));
                                if(count($billLineItems) > 0){
                                    $i = 1;
                                    $total = 0;
                                    foreach($billLineItems as $value){
                                        $total += $value->amount;
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" class="checkall_sub" value="<?=$value->billing_line_item_id?>"></td>
                                            <td><span class="font-weight-bold"><?=$value->item_information?></span></td>
                                            <td>
                                                <span class="font-weight-bold pull-right "><?=$value->quantity?></span>
                                                <input type="hidden" name="total[]" value="<?=$value->quantity?>">
                                                <input type="hidden" name="drug_id[]" value="<?=$value->drug_id?>">
                                                <input type="hidden" name="batch_no[]" value="<?=$value->batch_no?>">
                                                <input type="hidden" name="billing_line_item_id[]" value="<?=$value->billing_line_item_id?>">
                                                <input type="hidden" name="billing_id" value="<?=$value->billing_id?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control qtyInput" readonly name="qty[]" id="txt_<?=$value->billing_line_item_id?>" max="<?=$value->quantity?>" data-value="<?=$value->quantity?>"  value="0" onkeypress="return numeric()">
                                            </td>
                                            <td><span class="font-weight-bold"><?=$value->batch_no?></span></td>
                                            <!-- <td><span class="font-weight-bold pull-right"><?=number_format($value->unit_price, 2)?></span></td> -->
                                            <td><span class="font-weight-bold pull-right"><?=number_format($value->total_amount, 2)?></span></td>
                                            <td><span class="font-weight-bold pull-right"><?=$value->discount?> %</span></td>
                                            <td><span class="font-weight-bold pull-right"><?=number_format($value->amount, 2)?></span></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-right">Total Amount</th>
                                    <td class="text-right"><span><?=number_format($total, 2)?></span></td>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- <p class="pull-right" style="padding: 0px !important;margin-bottom:5px !important"><button class="btn btn-xs btn-app return" type="button">Return</button></p> -->
                        <p class="pull-right" style="padding: 0px !important;margin-bottom:5px !important"><button class="btn btn-xs btn-app rsubmit" name="rsubmit" type="submit">Submit</button></p>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            
        }
    }
}


    public function delete_order($cinvid) {
        //$this->db->query("delete from clinic_pharmacy_inventory_inward where clinic_pharmacy_inventory_inward_id=".$cinvid);
        $this->db->query("update clinic_pharmacy_inventory_inward set archieve=1 where clinic_pharmacy_inventory_inward_id=" . $cinvid);

        redirect('Pharmacy_orders');
    }

    public function edit_order($drug_id, $batch_no) {

        $clinic_id = $this->session->userdata('clinic_id');

        // $data['permissions'] = $this->db->query("select * from clinic_role_property_permissions where clinic_id='".$clinic_id."' and clinic_role_id IN (".$this->session->userdata('assigned_roles').") and pharmacy_edit_access ='1'")->row();
        // echo $this->db->last_query();
        // exit;
        $data['info'] = $this->db->select('INW.clinic_pharmacy_inventory_inward_id, INW.drug_id, INW.batch_no, INW.quantity, INW.mrp, INW.pack_size, INW.expiry_date, INW.supplied_date, CPI.clinic_pharmacy_inventory_id, CPI.reorder_level, CPI.igst, CPI.cgst, CPI.sgst, CPI.max_discount_percentage, CPI.vendor_id, D.trade_name, D.hsn_code')->from('clinic_pharmacy_inventory_inward INW')->join('clinic_pharmacy_inventory CPI', 'INW.drug_id = CPI.drug_id', 'inner')->join('drug D', 'INW.drug_id = D.drug_id', 'inner')->where('INW.drug_id =', $drug_id)->where('INW.batch_no =', $batch_no)->where('INW.clinic_id =', $clinic_id)->where('INW.status =', 1)->where('INW.archieve=', 0)->group_by('INW.supplied_date')->get()->result_array();

        $data['view'] = 'Pharmacy_orders/edit_order';

        $this->load->view('layout', $data);
    }

// add CPI if not exists 
public function addCPI($clinic_id){
    $array = array("78679","87786","160530","164902","172866","254973","331196","337888","344466","358494","366151","371506","398802");
    // echo count($array);
    foreach($array as $value)
    {
        $check = $this->db->query("select * from clinic_pharmacy_inventory where drug_id='".$value."' and clinic_id='".$clinic_id."'")->row();
        if(count($check)<=0)
        {
            $data['drug_id'] = $value;
            $data['clinic_id'] = $clinic_id;
            $data['reorder_level'] = 20;
            $data['max_discount_percentage'] = 10;
            $data['igst'] = 0;
            $data['cgst'] = 6;
            $data['sgst'] = 6;
            $data['created_by'] = 216;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_by'] = 216;
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            echo $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory", $data);
        }
        else
        {
            echo "Added Already";
        }
    }
    
}



public function generateinventorypdf()
{
    $clinic_id = $this->session->userdata('clinic_id');
    $cond = '';
    $data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
    $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
    $data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.clinic_id='".$clinic_id."' and b.status=1 and b.archieve=0 " . $cond . " and b.expiry_date > CURRENT_DATE group by b.drug_id,b.batch_no")->result();

    $pi = 0;

    foreach ($data['pinfo'] as $result) {
        $disinfo = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $result->drug_id)->row();
        $outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=" . $result->drug_id . " and batch_no='" . $result->batch_no . "' and clinic_id=" . $result->clinic_id)->row();

        $sch_salt = array();

        if ($result->salt_id == "" || $result->salt_id == NULL) {
            $scheduled_salt = "";
        } else {
            $imp = "'" . implode("','", explode(",", $result->salt_id)) . "'";
            $salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
            $scheduled_salt = trim($salt_id->scheduled_salt, ",");
            $explode = explode(",", $scheduled_salt);
            foreach ($explode as $key => $svalue) {
                $sch_salt[] = "<span id=" . trim($svalue) . ">" . trim($svalue) . "</span>";
            }
        }

        $data['parinfo'][$pi]['clinic_pharmacy_inventory_inward_id'] = $result->clinic_pharmacy_inventory_inward_id;
        $data['parinfo'][$pi]['trade_name'] = $result->trade_name . " " . implode(" ", $sch_salt);
        $data['parinfo'][$pi]['drug_id'] = $result->drug_id;
        $data['parinfo'][$pi]['formulation'] = $result->formulation;
        $data['parinfo'][$pi]['composition'] = $result->composition;
        $data['parinfo'][$pi]['batch_no'] = $result->batch_no;
        $data['parinfo'][$pi]['oqty'] = ($result->oqty - $outward->ouqty);
        $data['parinfo'][$pi]['mrp'] = round($result->mrp, 2);
        $data['parinfo'][$pi]['reorder_level'] = $disinfo->reorder_level;
        $data['parinfo'][$pi]['hsn_code'] = $result->hsn_code;
        $data['parinfo'][$pi]['igst'] = $disinfo->igst;
        $data['parinfo'][$pi]['cgst'] = $disinfo->cgst;
        $data['parinfo'][$pi]['sgst'] = $disinfo->sgst;
        $data['parinfo'][$pi]['disc'] = $disinfo->max_discount_percentage;
        $data['parinfo'][$pi]['vendor_id'] = $disinfo->vendor_id;
        $data['parinfo'][$pi]['pack_size'] = $result->pack_size;
        $data['parinfo'][$pi]['expiry_date'] = $result->expiry_date;
        $pi++;
    }
    // echo "<pre>";print_r($data);exit;
    $this->load->library('M_pdf');
    $html = $this->load->view('Pharmacy_orders/drugInventoryPrint',$data,true);
    $pdfFilePath = "TI-".$clinic_id.rand(1000,9999).date("dmY").".pdf";
    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    echo $pdfFilePath;
}

public function generateshortagepdf()
{
    $clinic_id = $this->session->userdata('clinic_id');
    $cond = '';
    $data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
    $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
    $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.clinic_id=" . $clinic_id . " and a.archieve=0 and (a.expiry_date<'" . $c_date . "' or a.expiry_date>'" . $c_date . "')  group by a.drug_id order by a.expiry_date ASC")->result_array();
    $shi = 0;
    foreach ($shortage as $ssresult) {
        if ($clinic_id == 0) {
            $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'])->row();
            $shqty = $this->db->query("select * from clinic_pharmacy_inventory where drug_id=" . $ssresult['drug_id'])->row();
        } else {
            $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'] . " and clinic_id=" . $clinic_id)->row();
            $shqty = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $ssresult['drug_id'])->row();
        }

        $actual_qty = ($ssresult['oqty'] - $outqnt->qty);

        // echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
        if (($actual_qty <= $shqty->reorder_level) && ($shqty->indent_status == 0)) {
            // echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
            $data['shortage'][$shi]['drug_id'] = $ssresult['drug_id'];
            $data['shortage'][$shi]['drug_name'] = $ssresult['trade_name'];
            $data['shortage'][$shi]['batch_no'] = $ssresult['batch_no'];
            $data['shortage'][$shi]['quantity'] = $actual_qty;
            $data['shortage'][$shi]['edate'] = $ssresult['expiry_date'];
            $data['shortage'][$shi]['vendor_id'] = $shqty->vendor_id;
        }
        $shi++;
    }

    // echo "<pre>";print_r($data);exit;
    $this->load->library('M_pdf');
    $html = $this->load->view('Pharmacy_orders/shortageInventoryprint',$data,true);
    $pdfFilePath = "SI-".$clinic_id.date("dmY").".pdf";
    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    echo $pdfFilePath;
}

public function generateexpsoonpdf()
{
    $c_date = date('Y-m-d');
    $lt_date = date("Y-m-d", strtotime("+3 month"));
    $clinic_id = $this->session->userdata('clinic_id');
    $cond = '';
    $data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
    $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
    $sexpired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' and b.expiry_date>'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();
    $sei = 0;
    foreach ($sexpired as $seresult) {
            $data['sexpired'][$sei]['drug_name'] = $seresult['trade_name'];
            $data['sexpired'][$sei]['batch_no'] = $seresult['batch_no'];
            $data['sexpired'][$sei]['drug_id'] = $seresult['drug_id'];
            $data['sexpired'][$sei]['formulation'] = $seresult['formulation'];
            $data['sexpired'][$sei]['clinic_pharmacy_inventory_inward_id'] = $seresult['clinic_pharmacy_inventory_inward_id'];
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "'")->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
            }

            $data['sexpired'][$sei]['quantity'] = ($seresult['oqty'] - $outqnt->qty);
            $data['sexpired'][$sei]['edate'] = $seresult['expiry_date'];
            $sei++;
    }

    // echo "<pre>";print_r($data);exit;
    $this->load->library('M_pdf');
    $html = $this->load->view('Pharmacy_orders/expirysoonInventoryprint',$data,true);
    $pdfFilePath = "ES-".$clinic_id.date("dmY").".pdf";
    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    echo $pdfFilePath;
}

public function generateexpiredpdf()
{
    $c_date = date('Y-m-d');
    $clinic_id = $this->session->userdata('clinic_id');
    $cond = '';
    $data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
    $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
    $expired = $this->db->query("select *,b.clinic_pharmacy_inventory_inward_id,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no")->result_array();
    $ei = 0;

    foreach ($expired as $eresult) {
        $data['expired'][$ei]['drug_name'] = $eresult['trade_name'];
        $data['expired'][$ei]['batch_no'] = $eresult['batch_no'];
        $data['expired'][$ei]['drug_id'] = $eresult['drug_id'];
        $data['expired'][$ei]['formulation'] = $eresult['formulation'];
        $data['expired'][$ei]['clinic_pharmacy_inventory_inward_id'] = $eresult['clinic_pharmacy_inventory_inward_id'];
        if ($clinic_id == 0) {
            $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "'")->row();
        } else {
            $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
        }
        $data['expired'][$ei]['quantity'] = ($eresult['oqty'] - $outqnt->qty);
        $data['expired'][$ei]['edate'] = $eresult['expiry_date'];
        $ei++;
    }

    // echo "<pre>";print_r($data);exit;
    $this->load->library('M_pdf');
    $html = $this->load->view('Pharmacy_orders/expiredInventoryprint',$data,true);
    $pdfFilePath = "EX-".$clinic_id.date("dmY").".pdf";
    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/inventoryPdf/".$pdfFilePath, "F");
    echo $pdfFilePath;
}

//Vendor Master
    public function Pharmacy_vendors() {
        $clinic_id = $this->session->userdata("clinic_id");

        $data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='" . $clinic_id . "'")->result();

        $data['view'] = 'Pharmacy_orders/vendor_list';
        $this->load->view('layout', $data);
    }

//Vendor Add
    public function addVendor() {
        $clinic_id = $this->session->userdata("clinic_id");

        $data['vendor_storeName'] = $this->input->post("storeName");
        $data['vendor_name'] = $this->input->post("vendorName");
        $data['vendor_mobile'] = $this->input->post("mobile");
        $data['vendor_email'] = $this->input->post("email");
        $data['vendor_address'] = $this->input->post("address");
        // $data['vendor_location'] = $this->input->post("location");
        $data['clinic_id'] = $clinic_id;
        $this->Generic_model->insertData('vendor_master', $data);
        $this->session->set_flashdata('msg','Vendor Added.');
        redirect("Pharmacy_orders/Pharmacy_vendors");
    }

//Edit Vendor
    public function editVendor() {
        $clinic_id = $this->session->userdata("clinic_id");

        echo $vendor_id = $this->input->post("vendor_id");
        $data['vendor_storeName'] = $this->input->post("storeName");
        $data['vendor_name'] = $this->input->post("vendorName");
        $data['vendor_mobile'] = $this->input->post("mobile");
        $data['vendor_email'] = $this->input->post("email");
        $data['vendor_address'] = $this->input->post("address");
        $data['vendor_location'] = $this->input->post("location");
        $this->Generic_model->updateData('vendor_master', $data, array('vendor_id' => $vendor_id));
        redirect("Pharmacy_orders/Pharmacy_vendors?usuccess");
    }

//Delete Vendor
    public function deleteVendor($id) {
        $vendor_id = $id;
        $this->Generic_model->deleteRecord('vendor_master', array('vendor_id' => $vendor_id));
        redirect("Pharmacy_orders/Pharmacy_vendors?dsuccess");
    }

    public function pharmacy_edit() {
        date_default_timezone_set('Asia/Kolkata');

        $user_id = $this->session->userdata('user_id');
        $clinic_id = $this->session->userdata('clinic_id');

        $recCount = count($_POST['cpi_inw_id']);

        // $igst=0;$cgst=0;$sgst=0;$rlevel=0;$mdiscount=0;$cid=0;$did=0;
        // echo "<pre>";print_r($_POST);exit;

        for ($i = 0; $i < $recCount; $i++) {

            $replace_date_slash_with_hyphen = str_replace("/", "-", trim($_POST['expiry_date'][$i]));

            // Clinic Pharmacy Inventory Inward data
            $inwardInfo['batch_no'] = $_POST['batchno'][$i];
            if(isset($_POST['quantity'])){
                $inwardInfo['quantity'] = $_POST['quantity'][$i];
            }
            
            $inwardInfo['mrp'] = $_POST['mrp'][$i];
            $inwardInfo['pack_size'] = $_POST['pack_size'][$i];
            $expiryDay = 01;
            $inwardInfo['expiry_date'] = $_POST['expiryYear'][$i] . "-" . $_POST['expiryMonth'][$i] . "-" . $expiryDay;
            $inwardInfo['modified_by'] = $user_id;
            $inwardInfo['modified_date_time'] = date("Y-m-d H:i:s");

            // Update clinic pharmacy inventory inward DB
            $this->Generic_model->updateData('clinic_pharmacy_inventory_inward', $inwardInfo, array('clinic_pharmacy_inventory_inward_id' => $_POST['cpi_inw_id'][$i]));


            // Clinic Pharmacy Inventory data
            $cpiInfo['reorder_level'] = $_POST['reorder_level'][$i];
            $cpiInfo['igst'] = $_POST['igst'][$i];
            $cpiInfo['cgst'] = $_POST['cgst'][$i];
            $cpiInfo['sgst'] = $_POST['sgst'][$i];
            $cpiInfo['max_discount_percentage'] = $_POST['max_discount_percentage'][$i];
            $cpiInfo['vendor_id'] = $_POST['vendor'][$i];
            $cpiInfo['modified_by'] = $user_id;
            $cpiInfo['modified_date_time'] = date("Y-m-d H:i:s");

            // Update clinic pharmacy inventory DB
            $this->Generic_model->updateData('clinic_pharmacy_inventory', $cpiInfo, array('clinic_pharmacy_inventory_id' => $_POST['cpi_id'][$i]));

            // Drug with HSN Code
            $drugInfo['hsn_code'] = $_POST['hsn_code'][$i];

            // Update drug db with HSN Code
            $this->Generic_model->updateData("drug", $drugInfo, array('drug_id' => $_POST['drug_id'][$i]));

            // $this->Generic_model->updateData("clinic_pharmacy_inventory_inward",$iinfo,array('	clinic_pharmacy_inventory_inward_id'=>$_POST['iid'][$i]));
        }

        // $this->db->query("update clinic_pharmacy_inventory set reorder_level='".$rlevel."',igst='".$igst."',cgst='".$cgst."',sgst='".$sgst."',max_discount_percentage='".$mdiscount."',`vendor_id`='".$vendor_id."' where clinic_id=".$cid." and drug_id=".$did);
        // Navigate to Pharmacy Inventory listing 


        $this->Generic_model->pushNotifications('','','',$clinic_id,'PharmacyCurrentStock');
        redirect('Pharmacy_orders');
    }

  //   public function get_dashboard_details() {
  //       $cid = $_POST['c_id'];
  //       $did = $_POST['d_id'];
  //       $start = $_POST['startDate'];
  //       $start = date('Y-m-d', strtotime($start));

  //       $end = $_POST['endDate'];
  //       $end = date('Y-m-d', strtotime($end));

  //       if ($start == $end) {
  //           $regCond = "created_date_time LIKE '%" . $start . "%'";
  //           $conCond = "b.created_date_time LIKE '%" . $start . "%' and item_information='Consultation'";
  //       } else {
  //           $regCond = "created_date_time between '" . $start . "%' and '" . $end . "%'";
  //           $conCond = "(b.created_date_time between '" . $start . "%' and '" . $end . "%') and item_information='Consultation'";
  //       }

  //       $tdate = date('Y-m-d');

  //       if ($did == 'all') {
  //           $registrations = $this->db->query("select count(patient_id) as pcnt from patients where " . $regCond)->row();
  //           $consultations = $this->db->query("select sum(amount) as camt from billing_line_items a inner join billing b on a.billing_id=b.billing_id where clinic_id=" . $cid . " and " . $conCond)->row();
  //       } else {
  //           $registrations = $this->db->query("select count(patient_id) as pcnt from patients where " . $regCond)->row();
  //           $consultations = $this->db->query("select sum(amount) as camt from billing_line_items a inner join billing b on a.billing_id=b.billing_id where clinic_id=" . $cid . " and doctor_id=" . $did . " and " . $conCond)->row();
  //       }


  //       echo '<table cellspacing="0" cellpadding="0" class="table finances">
		// 	<tr>
		// 		<td class="noBdr btmBdr"><span class="amt consultationsAmount">' . ($consultations->camt != '' ? $consultations->camt : 0) . '</span><br />CONSULTATIONS</td>
  // 			</tr>
		// 	<tr>
		// 		<td id="reg_td" class="noBdr btmBdr"><span class="amt registrationsData">' . ($registrations->pcnt * 100) . '</span><br />REGISTRATIONS</td>
  // 			</tr>
  // 			<tr>
  // 				<td class="noBdr btmBdr"><span class="amt proceduresData">0</span><br />PROCEDURES</td>
		// 	</tr>
		// 	<tr>
  // 				<td class="noBdr"><span class="amt investigationData">0</span><br />INVESTIGATION</td>
  // 			</tr>
		// </table>';
  //   }

    public function pharmacy_dashboard() {
        $clinic_id = $this->session->userdata('clinic_id');

        $data['clinic_id'] = $clinic_id;

        $cond = '';

        if ($clinic_id != 0)
            $cond = "where clinic_id=" . $clinic_id;

        $tdate = date('Y-m-d');

        if ($this->session->userdata('role_id') == 4) {
            $data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id, 'doctor_id' => $this->session->userdata('user_id')), $order = '');
        } else {
            $this->db->select('distinct(doctor_id)');
            $this->db->from('clinic_doctor');

            if ($clinic_id != 0) {
                $this->db->where("clinic_id = ", $clinic_id);
            }

            $data['doctors_list'] = $this->db->get()->result();
        }
        // $patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.created_date_time like '%".$tdate."%' group by a.patient_id")->result();
        // Get the list of patient prescriptions
        $patientPrescriptions = $this->db->select('PP.patient_id, PP.patient_prescription_id, PP.doctor_id, PP.clinic_id, PP.appointment_id')->from('patient_prescription as PP')->where('PP.clinic_id =', $clinic_id)->like('PP.created_date_time', $tdate)->group_by('PP.patient_id', 'ASC')->get()->result_array();

        $expectedRevenue = 0;
        $convertedRevenue = 0;
        $outPeopleRevenue = 0;
        $discounts = 0;

        if (count($patientPrescriptions) > 0) {

            $data['prescriptions_count'] = count($patientPrescriptions);

            // Get the drugs prescribed in each prescription
            foreach ($patientPrescriptions as $prescription) {

                $drugsPrescribed = $this->db->query('select PPD.drug_id, PPD.quantity, INW.mrp/INW.pack_size as amount_per_unit, PPD.quantity * INW.mrp/INW.pack_size as amount from patient_prescription_drug PPD INNER JOIN clinic_pharmacy_inventory_inward INW ON PPD.drug_id = INW.drug_id WHERE clinic_id = ' . $clinic_id . ' AND PPD.patient_prescription_id = ' . $prescription['patient_prescription_id'] . ' AND INW.status = 1 AND INW.archieve = 0 GROUP BY INW.drug_id order by INW.clinic_pharmacy_inventory_inward_id ASC')->result();

                if (count($drugsPrescribed) > 0) {
                    foreach ($drugsPrescribed as $drugAmount) {
                        $expectedRevenue = (float) $expectedRevenue + (float) $drugAmount->amount;
                    }
                }
                
                // Check whether this prescription converted as a bill or no
                // If converted get the amount of the billing
                $convertedPrecription = $this->db->select('billing_id')->from('billing')->where('patient_prescription_id =', $prescription['patient_prescription_id'])->get()->result_array();

                if (count($convertedPrecription) > 0) {

                    $data['billing_count'] = count($convertedPrecription);

                    foreach ($convertedPrecription as $billing) {

                        // Get the line items of the billing and sum of the amounts
                        $billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =', $billing['billing_id'])->get()->row();
                        $convertedRevenue = (float) $convertedRevenue + (float) $billingInfo->amount;
                    }
                }
            }

            $data['expected_revenue'] = number_format(round($expectedRevenue), 2);
            $data['converted_revenue'] = number_format(round($convertedRevenue), 2);
        }

        // Get the list of customers purchaseddrugs from the pharmacy who are outsiders w.r.to the date
        $outPeople = $this->db->select('billing_id, guest_name, guest_mobile')->from('billing')->where('patient_prescription_id =', 0)->where('clinic_id =', $clinic_id)->where('billing_type =', 'Pharmacy')->like('billing_date_time', $tdate)->get()->result_array();

        if (count($outPeople) > 0) {

            $data['out_people_count'] = count($outPeople);

            foreach ($outPeople as $person) {

                // Get the billing line items info with amount summation
                $personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =' . $person['billing_id'])->get()->row();
                $outPeopleRevenue = (float) $outPeopleRevenue + (float) $personBillingInfo->amount;
            }
        }

        $data['out_people_revenue'] = number_format(round($outPeopleRevenue), 2);
        $lost_revenue = number_format(round($expectedRevenue) - round($convertedRevenue), 2);
        if($lost_revenue>0)
            $data['lost_revenue'] = $lost_revenue;
        elseif($lost_revenue<0)
            $data['lost_revenue'] = 0;
        else
            $data['lost_revenue'] = 0;

        /*
          echo '<pre>';
          print_r($data);
          echo '</pre>';

          exit();

          $i=0; $expected_revenue = 0; $tcamount = 0;

          foreach($patients as $presult)
          {
          $data['patients'][$i]['pname'] = $presult->first_name." ". $presult->last_name;
          $data['patients'][$i]['pid'] = $presult->patient_id;
          $data['patients'][$i]['age'] = $presult->age;
          $data['patients'][$i]['gender'] = $presult->gender;
          $data['patients'][$i]['pdid'] = $presult->patient_prescription_id;
          $data['patients'][$i]['dcstatus'] = $presult->dc_status;
          $data['patients'][$i]['doctor'] = $presult->salutation." ".$presult->first_name." ".$presult->last_name;

          $pdrugs = $this->db->query("select * from patient_prescription_drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where a.patient_prescription_id=".$presult->patient_prescription_id." and clinic_id=".$clinic_id." group by b.drug_id")->result();

          foreach($pdrugs as $pdresult)
          {
          $mrp = ($pdresult->mrp/$pdresult->pack_size);
          $expected_revenue = $expected_revenue+($pdresult->quantity*$mrp);
          }

          $camount = $this->db->query("select sum(amount) as camt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.patient_prescription_id=".$presult->patient_prescription_id)->row();

          if(count($camount)>0){
          $tcamount = $tcamount+$camount->camt;
          }else{
          $tcamount = $tcamount+0;
          }

          $i++;
          }

          $data['erevenue'] = $expected_revenue;
          $data['crevenue'] = $tcamount;

          $data['drug'] = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.created_date_time like '".$tdate."%' and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
         */

        $data['view'] = 'Pharmacy_orders/pharmacy_dashboard';
        $this->load->view('layout', $data);
    }

    public function getFinances() {

        $clinic_id = $this->session->userdata('clinic_id');
        $start = $_POST['startDate'];
        $end = date('Y-m-d', strtotime($_POST['endDate'] . ' +1 day'));
        $d_id = $_POST['d_id'];
        $expected = 0;$converted = 0;$converted_discounts = 0;$out_discounts = 0;$outrevenue = 0;

        if($start == $end)
        {
            $exDateCond = "pp.created_date_time LIKE '".$start."%'";
            $billDateCond = "created_date_time LIKE '".$start."%'";
        }
        else
        {
            $exDateCond = "pp.created_date_time BETWEEN '".$start."%' AND '".$end."%'";
            $billDateCond = "created_date_time BETWEEN '".$start."%' AND '".$end."%'";
        }

        if($d_id == "all")
        {
            $exCon = 'and '.$exDateCond;
            $billCon = 'and '.$billDateCond;
            $outBillCon = "and ".$billDateCond;
        }
        else
        {
            $exCon = "and pp.doctor_id = '".$d_id."' and ".$exDateCond;
            $billCon = "and doctor_id = '".$d_id."' and ".$billDateCond;
            $outBillCon = "and ".$billDateCond;
        }
        // Expected Revenue
        $expectedInfo = $this->db->query("select ppd.drug_id,ppd.quantity from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.clinic_id='".$clinic_id."' ".$exCon)->result();
        $expectedCount = $this->db->query("select count(*) as expectedCount from patient_prescription pp where EXISTS (SELECT * from patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id) and pp.clinic_id='".$clinic_id."' ".$exCon)->row();
        
        foreach($expectedInfo as $value)
        {
            $expected += getDrugPrice($clinic_id,$value->drug_id,$value->quantity);
        }
        // Converted Revenue
        $billing_master = $this->db->query("select * from billing where clinic_id = '".$clinic_id."' and billing_type='Pharmacy' and (status='0' or status='1') and patient_prescription_id!='0' ".$billCon)->result();
        foreach($billing_master as $value)
        {
            $billing_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
            $converted += $billing_line_info->amount;
            $converted_discounts += $billing_line_info->discount;
        }
        // Out Patients Revenue
        $outBills = $this->db->query("select * from billing where clinic_id = '".$clinic_id."' and billing_type='Pharmacy' and (status='0' or status='1') and patient_prescription_id='0' ".$outBillCon)->result();
        foreach($outBills as $value)
        {
            $OutBill_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
            $outrevenue += $OutBill_line_info->amount;
            $out_discounts += $OutBill_line_info->discount;
        }


        $revenue['expected_revenue'] = number_format(round($expected,2),2);
        $revenue['expected_prescriptions_count'] = $expectedCount->expectedCount;
        $revenue['converted_revenue'] = number_format(round($converted,2),2);
        $revenue['indiscounts'] = number_format(round($converted_discounts,2),2);
        $revenue['converted_prescriptions_count'] = count($billing_master);
        $revenue['out_people_revenue'] = number_format(round($outrevenue,2),2);
        $revenue['out_people_count'] = count($outBills);
        $revenue['outdiscounts'] = number_format(round($out_discounts,2),2);
        $revenue['lost_revenue'] = (($revenue['expected_revenue']-$revenue['converted_revenue']) <= 0) ? '0.00' : number_format(round(($expected-$converted),2),2);
        
        echo json_encode($revenue);


    }

    public function search_pharmacy() {
        $pharmacy_main = $_POST['search_pharmacy'];
        //echo $pharmacy_main;exit;
        $data['pharmacy'] = $pharmacy_main;
        //$pharmacy = substr($pharmacy, strpos($pharmacy_main, " ")+1);
        $pharmacy = explode(' ', $pharmacy_main);
        //echo $pharmacy[1];
        $c_date = date('Y-m-d');
        $lt_date = date("Y-m-d", strtotime("+3 month"));
        $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';
        if ($clinic_id != 0)
            $cond = "and b.clinic_id=" . $clinic_id;
        if ($clinic_id == 0) {
            $expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' group by b.drug_id,b.batch_no")->result_array();
            $sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' group by b.drug_id,b.batch_no")->result_array();
            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 group by a.drug_id,a.batch_no")->result_array();
            //echo $this->db->last_query();
        } else {
            $expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.expiry_date<'" . $c_date . "' and b.clinic_id=" . $clinic_id . " and b.archieve=0 group by b.drug_id,b.batch_no")->result_array();
            $sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.expiry_date<'" . $lt_date . "' and b.clinic_id=" . $clinic_id . " and b.archieve=0 group by b.drug_id,b.batch_no")->result_array();
            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 and clinic_id=" . $clinic_id . " group by a.drug_id,a.batch_no")->result_array();
        }
        $data['expired'] = array();
        $data['sexpired'] = array();
        $data['shortage'] = array();
        $ei = 0;
        $sei = 0;
        $shi = 0;
        foreach ($expired as $eresult) {
            $data['expired'][$ei]['drug_name'] = $eresult['trade_name'];
            $data['expired'][$ei]['batch_no'] = $eresult['batch_no'];
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "'")->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $eresult['drug_id'] . " and batch_no='" . $eresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
            }
            $data['expired'][$ei]['quantity'] = ($eresult['oqty'] - $outqnt->qty);
            $data['expired'][$ei]['edate'] = $eresult['expiry_date'];
            $ei++;
        }
        foreach ($sexpired as $seresult) {
            $data['sexpired'][$sei]['drug_name'] = $seresult['trade_name'];
            $data['sexpired'][$sei]['batch_no'] = $seresult['batch_no'];
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "'")->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $seresult['drug_id'] . " and batch_no='" . $seresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
            }

            $data['sexpired'][$sei]['quantity'] = ($seresult['oqty'] - $outqnt->qty);
            $data['sexpired'][$sei]['edate'] = $seresult['expiry_date'];
            $sei++;
        }
        $drug_info = $this->db->query("SELECT * FROM drug where trade_name like '" . $pharmacy[1] . "%'")->row();
        //echo $this->db->last_query();exit;
        if (count($drug_info) > 0) {
            if ($clinic_id == 0) {
                $inventray = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=" . $drug_info->drug_id . " and archieve=0 group by drug_id,batch_no")->result_array();
            } else {
                $inventray = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=" . $drug_info->drug_id . " and archieve=0 and clinic_id=" . $clinic_id . " group by drug_id,batch_no")->result_array();
            }

            $data['presult'] = array();
            $pi = 0;
            foreach ($inventray as $result) {
                $data['presult'][$pi]['drug_name'] = $drug_info->trade_name;
                $data['presult'][$pi]['batch_no'] = $result['batch_no'];
                if ($clinic_id == 0) {
                    $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $result['drug_id'] . " and batch_no='" . $result['batch_no'] . "'")->row();
                } else {
                    $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $result['drug_id'] . " and batch_no='" . $result['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
                }

                $data['presult'][$pi]['quantity'] = ($result['oqty'] - $outqnt->qty);
                $data['presult'][$pi]['edate'] = $result['expiry_date'];
                $pi++;
            }
        } else {
            $data['presult'] = array();
        }
        foreach ($shortage as $ssresult) {
            if ($clinic_id == 0) {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'] . " and batch_no='" . $ssresult['batch_no'] . "'")->row();
                $shqty = $this->db->query("select * from clinic_pharmacy_inventory where drug_id=" . $ssresult['drug_id'])->row();
            } else {
                $outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=" . $ssresult['drug_id'] . " and batch_no='" . $ssresult['batch_no'] . "' and clinic_id=" . $clinic_id)->row();
                $shqty = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $ssresult['drug_id'])->row();
            }
            $actual_qty = ($ssresult['oqty'] - $outqnt->qty);
            if ($actual_qty <= $shqty->reorder_level) {
                $data['shortage'][$shi]['drug_name'] = $ssresult['trade_name'];
                $data['shortage'][$shi]['batch_no'] = $ssresult['batch_no'];
                $data['shortage'][$shi]['quantity'] = $actual_qty;
                $data['shortage'][$shi]['edate'] = $ssresult['expiry_date'];
                $shi++;
            }
        }
        $trade_names = $this->db->query("select trade_name,formulation from drug")->result_array();
        $data['tname'] = '';
        foreach ($trade_names as $tresult) {
            if ($tresult['trade_name'] != '') {
                if ($data['tname'] == '')
                    $data['tname'] = $data['tname'] . '"' . $tresult['formulation'] . ' ' . $tresult['trade_name'] . '"';
                else
                    $data['tname'] = $data['tname'] . ',"' . $tresult['formulation'] . ' ' . $tresult['trade_name'] . '"';
            }
        }
        $data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 " . $cond . " group by b.drug_id,b.batch_no")->result();
        $data['view'] = 'Pharmacy_orders/Pharmacy_orders';

        $this->load->view('layout', $data);
    }

    public function pharmacy_add() {
        $clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');
        $param = $this->input->post();
        $insertFlag = 0;

        if (count($param) > 0) {

            $drug_ids = count($_POST['drgid']);

            for ($i = 0; $i < $drug_ids; $i++) {

                $replace_date_slash_with_hyphen = str_replace("/", "-", trim($_POST['expiredate'][$i]));

                $cpiInwardInfo['drug_id'] = $_POST['drgid'][$i];
                $cpiInwardInfo['clinic_id'] = $clinic_id;
                $cpiInwardInfo['batch_no'] = preg_replace('/\s+/', '', $_POST['batchno'][$i]);
                $cpiInwardInfo['quantity'] = $_POST['qty'][$i];
                $cpiInwardInfo['mrp'] = $_POST['mrp'][$i];
                $cpiInwardInfo['pack_size'] = $_POST['pack_size'][$i];

                $expiryDate = "01";
                $expiryMonth = $_POST['expiryMonth'][$i];
                $expiryYear = $_POST['expiryYear'][$i];
                $cpiInwardInfo['expiry_date'] = $expiryYear . "-" . sprintf('%02d', $expiryMonth) . "-" . sprintf('%02d', $expiryDate);

                $cpiInwardInfo['supplied_date'] = date('Y-m-d');
                $cpiInwardInfo['status'] = 1;
                $cpiInwardInfo['created_by'] = $user_id;
                $cpiInwardInfo['modified_by'] = $user_id;
                $cpiInwardInfo['created_date_time'] = date("Y-m-d H:i:s");
                $cpiInwardInfo['modified_date_time'] = date("Y-m-d H:i:s");

                // Create a record in the Clinic Pharmacy Inventory Inward w.r.to the clinic id & drug id
                $res = $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_inward", $cpiInwardInfo);
                if($res)
                {
                    $insertFlag++;
                }

                $cpiInfo['reorder_level'] = $_POST['rlevel'][$i];
                $cpiInfo['igst'] = $_POST['igst'][$i];
                $cpiInfo['cgst'] = $_POST['cgst'][$i];
                $cpiInfo['sgst'] = $_POST['sgst'][$i];
                $cpiInfo['vendor_id'] = $_POST['vendor'][$i];
                $cpiInfo['max_discount_percentage'] = $_POST['disc'][$i];
                $cpiInfo['status'] = 1;
                $cpiInfo['modified_by'] = $user_id;
                $cpiInfo['modified_date_time'] = date("Y-m-d H:i:s");

                $cpiDrugCount = $this->db->select('clinic_pharmacy_inventory_id, drug_id, clinic_id')->from('clinic_pharmacy_inventory')->where('drug_id =', $_POST['drgid'][$i])->where('clinic_id =', $clinic_id)->get()->num_rows();

                if ($cpiDrugCount > 0) {
                    // Drug Exists :: Just update the record with required
                    $this->Generic_model->updateData("clinic_pharmacy_inventory", $cpiInfo, array('clinic_id' => $clinic_id, 'drug_id' => $_POST['drgid'][$i]));
                } else {
                    $cpiInfo['clinic_id'] = $clinic_id;
                    $cpiInfo['drug_id'] = $_POST['drgid'][$i];
                    $cpiInfo['created_by'] = $user_id;
                    $cpiInfo['created_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory", $cpiInfo);
                }

                $hsnCodeInfo['hsn_code'] = $_POST['hsn_code'][$i];

                // Update if the Drug master with hsn code for the concern drug
                if ($hsnCodeInfo['hsn_code'] != '' || $hsnCodeInfo['hsn_code'] != null) {
                    $hsnCodeRes = $this->Generic_model->updateData("drug", $hsnCodeInfo, array('drug_id' => $_POST['drgid'][$i]));
                }
            }
            if($insertFlag > 0)
            {
                $this->session->set_flashdata("msg","Drug Added Successfully");
            }
            else
            {
                $this->session->set_flashdata("msg","Drug Not Added");
            }

            $this->Generic_model->pushNotifications('','','',$clinic_id,'PharmacyCurrentStock');

            $this->clinic_inventory_json();
        }

        $data['drug_master_json_file'] = $this->Generic_model->getFieldValue('master_version', 'json_file_name', array('master_name' => 'drug'));
        


        $data['view'] = 'Pharmacy_orders/new_orders';
        $this->load->view('layout', $data);
    }
    
    // Get Drugs From Masters
    public function getDrugs(){
        extract($_POST);
        $druginfo = $this->db->query("select CONCAT(formulation,' ',trade_name) as label,drug_id as value from drug where trade_name LIKE '".urldecode($searchParam)."%' order by trade_name ASC LIMIT 10")->result();
        $i = 0;
        foreach($druginfo as $value)
        {
            $data[$i]['label'] = $value->label;
            $data[$i]['value'] = $value->value;
            $i++;
        }
        echo json_encode($data);

    }

//	Creating inventory json w.r.t to clinic
    public function clinic_inventory_json() {
        $clinic_id = $this->session->userdata('clinic_id');
        $master_version = $this->db->query("select * from master_version where clinic_id='" . $clinic_id . "' and master_name='clinic_inventory'")->row();
        @$path = base_url('uploads/'.$master_version->json_file_name);
        @unlink($path);
        if (sizeof($master_version) == 0) {
            $json_file_name = $clinic_id . '_clinic_inventory_v1.json';
            $data['clinic_id'] = $clinic_id;
            $data['master_name'] = 'clinic_inventory';
            $data['version_code'] = '1';
            $data['json_file_name'] = $json_file_name;
            $this->Generic_model->insertData('master_version', $data);
        } else {
        //     // chmod()
            
            $version_code = $master_version->version_code + 1;
            $json_file_name = $clinic_id . "_clinic_inventory_v" . $version_code . ".json";
            $data['clinic_id'] = $clinic_id;
            $data['master_name'] = 'clinic_inventory';
            $data['version_code'] = $version_code;
            $data['json_file_name'] = $json_file_name;
            $this->Generic_model->updateData("master_version", $data, array('master_version_id' => $master_version->master_version_id));
        }

        $drugs_list = $this->db->query("select CONCAT(d.formulation,' ',d.trade_name) as drug_name from drug d inner join clinic_pharmacy_inventory cp on(d.drug_id = cp.drug_id) where cp.clinic_id='" . $clinic_id . "'")->result();

        $prefix = '';
        $prefix .= '[';
        foreach ($drugs_list as $row) {
            $prefix .= json_encode($row->drug_name);
            $prefix .= ',';
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        $path_user = './uploads/clinic_inventory_json/' . $json_file_name;

        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/clinic_inventory_json/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/clinic_inventory_json/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        }
        redirect('Pharmacy_orders');
    }

    public function get_drug_info() {
        $clinic_id = $this->session->userdata('clinic_id');
        $drg_name = $_POST['drug'];

        $drugInfo = $this->db->select('drug_id, hsn_code, trade_name, formulation, composition')->from('drug')->where('trade_name =', $drg_name)->get()->row();

        if (count($drugInfo) > 0) {

            $clinicDrugInfo = $this->db->select("clinic_pharmacy_inventory_id, clinic_id, drug_id, reorder_level, igst, cgst, sgst, max_discount_percentage as discount, vendor_id")->from("clinic_pharmacy_inventory")->where("clinic_id =", $clinic_id)->where("drug_id =", $drugInfo->drug_id)->where("status =", 1)->where("archieve =", 0)->get()->row();

            $drugInfo = (object) array_merge((array) $drugInfo, (array) $clinicDrugInfo);

            // Get vendor information to populate the vendor list
            $vendorInfo = $this->db->select('vendor_id, vendor_name, vendor_storeName, vendor_location')->from('vendor_master')->where('clinic_id =', $clinic_id)->get()->result_array();

            $drugInfo->vendor_list = $vendorInfo;

            echo json_encode($drugInfo);
        } else {
            echo '';
        }
    }

    public function getDrugInfo() {
        $clinic_id = $this->session->userdata('clinic_id');
        $drg = $_POST['drug'];

        $drugInfo = $this->db->select('drug_id, hsn_code, trade_name, formulation, composition')->from('drug')->where('drug_id =', $drg)->get()->row();

        if (count($drugInfo) > 0) {

            $clinicDrugInfo = $this->db->select("clinic_pharmacy_inventory_id, clinic_id, drug_id, reorder_level, igst, cgst, sgst, max_discount_percentage as discount, vendor_id")->from("clinic_pharmacy_inventory")->where("clinic_id =", $clinic_id)->where("drug_id =", $drugInfo->drug_id)->where("status =", 1)->where("archieve =", 0)->get()->row();

            $drugInfo = (object) array_merge((array) $drugInfo, (array) $clinicDrugInfo);

            // Get vendor information to populate the vendor list
            $vendorInfo = $this->db->select('vendor_id, vendor_name, vendor_storeName, vendor_location')->from('vendor_master')->where('clinic_id =', $clinic_id)->get()->result_array();

            $drugInfo->vendor_list = $vendorInfo;

            echo json_encode($drugInfo);
        } else {
            echo '';
        }
    }

    function bulk_save() {
        $clinic_id = $this->session->userdata('clinic_id');
        $this->load->library('excel');

        if ($this->input->post('importfile')) {
            $path = './uploads/pharmacy_inventory_bulk/';
            $config['upload_path'] = './uploads/pharmacy_inventory_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;

            //echo $_FILES['userfile']['name']=$_FILES['userfile']['name'];exit;

            $this->load->library('upload');
            $this->upload->initialize($config);
            $this->upload->do_upload('userfile'); //uploading file to server
            $fileData = $this->upload->data('file_name');
            $inputFileName = $path . $fileData;

            if (file_exists($inputFileName)) {
                $inputFileName = $path . $fileData;
            } else {
                move_uploaded_file($fileData, $path);
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
            $createArray = array('drug', 'batch_no', 'quantity', 'expiry_date');
            $makeArray = array('drug' => 'drug', 'batch_no' => 'batch_no', 'quantity' => 'quantity', 'expiry_date' => 'expiry_date');
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

                    $checking_drug = $this->db->query("select * from drug where trade_name like '" . $d_drug . "%'")->row();
                    $drug_id = $checking_drug->drug_id;
                    $clinic_id = $clinic_id;
                    $supplied_date = date("Y-m-d");

                    $fetchdata2 = array('drug_id' => $drug_id, 'clinic_id' => $clinic_id, 'batch_no' => $d_batch_no, 'quantity' => $d_quantity, 'supplied_date' => $supplied_date, 'expiry_date' => date("Y-m-d", strtotime($d_expiry_date)), 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));
                    $this->Generic_model->insertData('clinic_pharmacy_inventory_inward', $fetchdata2);
                }
            } else {
                echo "Please import correct file";
            }
            redirect('Pharmacy_orders');
        }
    }

    /*
      Get the drugs which match with the trade name
     */

    public function searchDrugs() {

        $trade_name = $_POST['trade_name'];

        $this->db->select('drug_id, trade_name, formulation');
        $this->db->from('drug');
        $this->db->like('trade_name', $trade_name, 'before');

        $drugs = $this->db->get()->result_array();

        echo $this->db->last_query();

        // if(count($drugs) > 0){
        // 	echo $drugs;	
        // }else{
        // 	echo 0;
        // }
    }

    public function raise_shortage_indent() {
        extract($_POST);
        // echo "<pre>";
        // print_r($_POST);
        // echo "<pre>";
        // exit();
        $clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');
        $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
        $ind_cnt = $this->db->query("select * from pharmacy_indent where indent_no like '%IND-" . $clinic_id . "-%'")->result();
        // echo $this->db->last_query();
        $icnt = (count($ind_cnt) + 1);
        // exit;
        $indentinfo['indent_no'] = 'IND-' . $clinic_id . "-" . $icnt;
        $indentinfo['user_id'] = $user_id;
        $indentinfo['clinic_id'] = $clinic_id;
        $indentinfo['status'] = 1;
        $indentinfo['indent_date'] = date("Y-m-d");
        $indentinfo['created_by'] = $user_id;
        $indentinfo['modified_by'] = $user_id;
        $indentinfo['created_date_time'] = date("Y-m-d H:i:s");
        $indentinfo['modified_date_time'] = date("Y-m-d H:i:s");
        $last_inserted_id = $this->Generic_model->insertDataReturnId("pharmacy_indent", $indentinfo);
        $drug_ids = count($_POST['drgid']);
            // echo "<pre>";
            // print_r($drug_ids);
            // echo "<pre>";
            // exit();
        for ($i = 0; $i < $drug_ids; $i++) {
            if ($_POST['rqty'][$i] == "")
                continue;
            echo $vendorName = 'vendor_'.$inventory_id[$i];
            $lineinfo['pharmacy_indent_id'] = $last_inserted_id;
            $lineinfo['drug_id'] = $_POST['drgid'][$i];
            $lineinfo['quantity'] = $_POST['rqty'][$i];
            $lineinfo['vendor_id'] = $_POST[$vendorName];
            // echo "<pre>";
            // print_r($vendor[$i]);
            // echo "<pre>";
            // exit();
            $lineinfo['status'] = 1;
            $lineinfo['created_by'] = $user_id;
            $lineinfo['modified_by'] = $user_id;
            $lineinfo['created_date_time'] = date("Y-m-d H:i:s");
            $lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertDataReturnId("pharmacy_indent_line_items", $lineinfo);
            $this->db->query("update clinic_pharmacy_inventory set status=1 where drug_id=" . $_POST['drgid'][$i] . " and clinic_id=" . $clinic_id);
            $details[$i]['drug_id'] = $lineinfo['drug_id'];
            $details[$i]['vendor_id'] = $lineinfo['vendor_id'];
            $details[$i]['quantity'] = $lineinfo['quantity'];
            
        }
        // exit(); 
        $j = 0;
        $ph_indent = $this->db->query("select * from pharmacy_indent_line_items ph,vendor_master vm where vm.vendor_id=ph.vendor_id and ph.pharmacy_indent_id='$last_inserted_id' group by ph.vendor_id")->result();
        foreach ($ph_indent as $value) 
        {
            $message = "Indent Raised From ".$clinicInfo->clinic_name.". Check Your Mail For Order Details.";
            sendsms($value->vendor_mobile,$message);
            $ph_indent_line = $this->db->query("select * from pharmacy_indent_line_items pil,drug d where d.drug_id=pil.drug_id and pil.pharmacy_indent_id='$last_inserted_id' and pil.vendor_id='".$value->vendor_id."'")->result();
            foreach ($ph_indent_line as $mail) 
            {
                $mailMessage = $mailMessage."<br>".$mail->formulation." ".$mail->trade_name.". Required Quantity - ".$value->quantity."<br>"; 
            }
            // Shoot an email
            $from = 'UMDAA Heath Care Pvt Ltd';
            $to = $value->vendor_email;
            $subject = ucwords($clinicInfo->clinic_name)." - Indent Raised";
            $mail_body = $mailMessage;           

            $ok = $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $mail_body);   
        }

        redirect('Indent');
    }

    public function drug_add() {

        $clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');
        $insertFlag = 0;
        $data['salt_info'] = $this->db->query("select * from salt")->result_array();
        $data['sname'] = '';

        foreach ($data['salt_info'] as $sresult) {
            if ($sresult['salt_name'] != '') {
                if ($data['sname'] == '')
                    $data['sname'] = $data['sname'] . '"' . $sresult['salt_name'] . '"';
                else
                    $data['sname'] = $data['sname'] . ',"' . $sresult['salt_name'] . '"';
            }
        }

        $data['vendors'] = $this->db->select('vendor_id, vendor_storeName, vendor_location, clinic_id')->from('vendor_master')->where('clinic_id =', $clinic_id)->get()->result_array();

        $param = $this->input->post();

        if (count($param) > 0) {

            // echo '<pre>';
            // print_r($param);
            // echo '</pre>';
            // // exit();
            // From added salts get the compsition for the drug
            $salt_array = $param['salt'];
            $sids = '';
            $composition = '';

            foreach ($salt_array as $sresult) {
                if ($sresult['salt_id'] == 0) {
                    $sinfo['salt_name'] = $sresult['salt_name'];
                    $sinfo['scheduled_salt'] = $sresult['schedule'];
                    $sinfo['admin_review'] = 0;
                    $sinfo['created_by'] = $user_id;
                    $sinfo['modified_by'] = $user_id;
                    $sinfo['created_date_time'] = date("Y-m-d H:i:s");
                    $sinfo['modified_date_time'] = date("Y-m-d H:i:s");
                    $salt_id = $this->Generic_model->insertDataReturnId("salt", $sinfo);
                } else {
                    $salt_id = $sresult['salt_id'];
                }

                if ($composition == '')
                    $composition = $sresult['salt_name'] . " " . $sresult['dossage'] . $sresult['unit'];
                else
                    $composition = $composition . " + " . $sresult['salt_name'] . " " . $sresult['dossage'] . $sresult['unit'];

                if ($sids == '')
                    $sids = $salt_id;
                else
                    $sids = $sids . "," . $salt_id;
            }

            // Insert new drug into the drug master db
            $drugInfo = $param['drug'];
            $drugInfo['salt_id'] = $sids;
            $drugInfo['admin_review'] = 0;
            $drugInfo['created_by'] = $user_id;
            $drugInfo['modified_by'] = $user_id;
            $drugInfo['created_date_time'] = date("Y-m-d H:i:s");
            $drugInfo['modified_date_time'] = date("Y-m-d H:i:s");

            // echo '<pre>';
            // print_r($drugInfo);
            // echo '</pre>';

           $drug_id = $this->Generic_model->insertDataReturnId("drug", $drugInfo);
            if($drug_id)
            {
                $insertFlag++;
            }

            // Check if inventory inward is checked
            if ($param['inventoryInward'] == 1) {

                // Insert new drug into clinic pharmacy inventory
                $inventory = $param['clinic_pharmacy'];
                $inventory['clinic_id'] = $clinic_id;
                $inventory['drug_id'] = $drug_id;
                $inventory['created_by'] = $user_id;
                $inventory['modified_by'] = $user_id;
                $inventory['created_date_time'] = date("Y-m-d H:i:s");
                $inventory['modified_date_time'] = date("Y-m-d H:i:s");

                // echo '<pre>';
                // print_r($inventory);
                // echo '</pre>';

                $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory", $inventory);

                // Drug inventory inward entry details
                $drugInventoryInward = $param['cp_inward'];


                if (count($drugInventoryInward) > 0) {

                    foreach ($drugInventoryInward as $drugInward) {

                        $drugInward['drug_id'] = $drug_id;
                        $drugInward['clinic_id'] = $clinic_id;

                        $expiryDate = "01";
                        $expiryMonth = $drugInward['expiry_month'];
                        $expiryYear = $drugInward['expiry_year'];
                        $drugInward['expiry_date'] = $expiryYear . "-" . sprintf('%02d', $expiryMonth) . "-" . sprintf('%02d', $expiryDate);

                        unset($drugInward['expiry_month']);
                        unset($drugInward['expiry_year']);

                        $drugInward['created_by'] = $drugInward['modified_by'] = $user_id;
                        $drugInward['created_date_time'] = $drugInward['modified_date_time'] = date('Y-m-d H:i:s');

                        // echo '<pre>';
                        // print_r($drugInward);
                        // echo '</pre>';

                       $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_inward", $drugInward);
                        
                    }
                }
            }
            // echo "<pre>";
            // print_r($this->session);
            // echo "</pre>";
            // echo $insertFlag;
            // // exit;
            if($insertFlag >= 1)
            {
                $this->session->set_flashdata('msg','Drug Added Succcessfully');
            }
            else
            {
                $this->session->set_flashdata('msg','Drug Not Added');
            }

            $this->drug_json();
            $this->Generic_model->pushNotifications('','','',$clinic_id,'PharmacyCurrentStock');
            $this->clinic_inventory_json();
            exit;

            $this->Generic_model->pushNotifications('','','','','NewDrugs');
            // redirect('Pharmacy_orders');
        }

        $data['view'] = 'Pharmacy_orders/add_drug';
        $this->load->view('layout', $data);
    }

    public function getsalt_details() {
        $saltName = $_POST['saltName'];
        $sinfo = $this->db->query("select * from salt where salt_name='" . $saltName . "'")->row();
        if (count($sinfo) > 0)
            echo $sinfo->salt_id . ":" . $sinfo->scheduled_salt;
        else
            echo "0" . ":" . "";
    }

    

    public function drug_json() {

        // Get the version from master_version db
        // If no record exists, then insert a record with verison 1
        $masterVersion = $this->db->select('master_version_id, master_name, version_code, json_file_name')->from('master_version')->where('master_name =', 'drug')->get()->row();

        if (count($masterVersion) > 0) {
            $version = (int) $masterVersion->version_code + 1;
            $master_version['version_code'] = $version;
            $master_version['modified_date_time'] = date('Y-m-d H:i:s');
            $old_json_file = $masterVersion->json_file_name;
            $new_json_file = "drugs_v" . $version . ".json";
            $master_version['json_file_name'] = $new_json_file;

            // Update record with the new version drug master
            $this->Generic_model->updateData('master_version', $master_version, array('master_version_id' => $masterVersion->master_version_id));
        } else {
            $version = 1;
            $master_version['clinic_id'] = 0;
            $master_version['master_name'] = 'drug';
            $master_version['version_code'] = 1;
            $master_version['json_file_name'] = "drugs_v" . $version . ".json";
            $master_version['created_date_time'] = date('Y-m-d H:i:s');
            $master_version['modified_date_time'] = date('Y-m-d H:i:s');

            // Create record
            $this->Generic_model->insertData('master_version', $master_version);
        }

        $drugs_list = $this->db->query("select CONCAT(formulation,' ',trade_name) as drug_name from drug")->result();

        $prefix = '';
        $prefix .= '[';
        foreach ($drugs_list as $row) {
            $prefix .= json_encode($row->drug_name);
            $prefix .= ',';
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        $old_file = './uploads/' . $old_json_file;

        if (!file_exists($old_file)) {
            $fp = fopen('./uploads/drugs_v' . $version . '.json', 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($old_file);
            $fp = fopen('./uploads/drugs_v' . $version . '.json', 'w');
            fwrite($fp, $json_file);
        }
    }


    public function pharmacySettings()
    {
       
        $clinic_id = $this->session->userdata('clinic_id'); 

        // Check if the data is submitting
        if($this->input->post('submit')){

            $clinic_pharmacy_id = $this->input->post('clinic_pharmacy_id');

            unset($_POST['submit']);

            $pharmacyData = $this->input->post();

            $config['upload_path']="./uploads/pharmacy_logos/";
            $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
            $this->load->library('upload');    
            $this->upload->initialize($config); 
            $this->upload->do_upload('pharmacy_logo');
            $fileData=$this->upload->data('file_name');

            $createdDateInfo = get_CM_by_dates();
            
            if($fileData!="")
            {
                $pharmacyData['logo'] = $fileData;
            }

            // Perform Insert if New data Or Edit if existing data
            // Check if the clinic_pharmacy_id has got a value. If not It is a new Phamracy data otherwise its an existing pharmacy data
            if($clinic_pharmacy_id != ''){
                // Update Pharmacy Data
                // $pharmacyData = $clinic_id;
                $pharmacyData['clinic_id'] = $clinic_id;
                $this->Generic_model->updateData('clinic_pharmacy', $pharmacyData, array('clinic_pharmacy_id'=>$clinic_pharmacy_id));
                redirect('Pharmacy_orders/pharmacySettings'); 
            }else{
                // Unset the clinic_pharmacy_id object
                $pharmacyData = $_POST;
                $pharmacyData['clinic_id'] = $clinic_id;
                unset($_POST['clinic_pharmacy_id']);
                // Insert New Pharmacy Data
                $this->Generic_model->insertData('clinic_pharmacy',$pharmacyData);
                redirect('Pharmacy_orders/pharmacySettings'); 
            }
        }else{
            // Fetch Data for showing up the Pharmacy Information
            $data['pharmacy_info'] = $this->db->select('clinic_pharmacy_id, name, email, mobile, logo, gst_number, max_discount, address')->from('clinic_pharmacy')->where('clinic_id', $clinic_id)->get()->row();
        }

        // $test= $this->db->query("select * from pharmacy_settings where clinic_id='".$clinic_id."'")->result();

       
        // if(count($test)== 0)
        // {           
        //     if($this->input->post('submit')){
        //         $config['upload_path']="./uploads/pharmacy_logos/";
        //         $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
        //         $this->load->library('upload');    
        //         $this->upload->initialize($config); 
        //         $this->upload->do_upload('pharmacy_logo');
        //         $fileData=$this->upload->data('file_name');
        
        //         $data['clinic_id']= $this->session->userdata('clinic_id');
        //         $data['pharmacy_name']=$this->input->post('pharmacy_name');
        //         $data['pharmacy_email']=$this->input->post('pharmacy_email');
        //         $data['pharmacy_mobile']=$this->input->post('pharmacy_mobile');  
        //         $data['pharmacy_logo']= $fileData;
        //         $data['pharmacy_discount']=$this->input->post('pharmacy_discount');
        //         $data['pharmacy_address']=$this->input->post('pharmacy_address');
        //         $data['created_by']=$this->session->has_userdata('user_id'); 
        //         $data['modified_by']=$this->session->has_userdata('user_id'); 
        //         $data['created_date_time']=$this->created_datetime = date('Y-m-d H:i:s');
        //         $data['modified_date_time']=$this->modified_datetime = date('Y-m-d H:i:s'); 
        
 
        //         $this->Generic_model->insertData('pharmacy_settings',$data);
        //         redirect('Pharmacy_orders/pharmacySettings'); 
        
        //          }
        // }
        // else{
            // if($this->input->post('submit')){

            //     $config['upload_path']="./uploads/pharmacy_logos/";
            //     $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
            //     $this->load->library('upload');    
            //     $this->upload->initialize($config); 
            //     $this->upload->do_upload('pharmacy_logo');
            //     $fileData=$this->upload->data('file_name');
                
            //     $id=$this->input->post('pharmacy_id');
            //     $data['clinic_id']= $this->session->userdata('clinic_id');
            //     $data['pharmacy_name']=$this->input->post('pharmacy_name');
            //     $data['pharmacy_email']=$this->input->post('pharmacy_email');
            //     $data['pharmacy_mobile']=$this->input->post('pharmacy_mobile');  
            //     if($fileData!="")
            //     {
            //         $data['pharmacy_logo']= $fileData;
            //     }
                    
            //     $data['pharmacy_discount']=$this->input->post('pharmacy_discount');
            //     $data['pharmacy_address']=$this->input->post('pharmacy_address');
            //     $data['created_by']=$this->session->has_userdata('user_id'); 
            //     $data['modified_by']=$this->session->has_userdata('user_id'); 
            //     $data['created_date_time']=$this->created_datetime = date('Y-m-d H:i:s');
            //     $data['modified_date_time']=$this->modified_datetime = date('Y-m-d H:i:s'); 
        
 
            //     $this->Generic_model->updateData('pharmacy_settings',$data,array('pharmacy_id'=>$id));
            //     redirect('Pharmacy_orders/pharmacySettings'); 
        
            //      }
            //  $data['clinic_list']= $this->db->query("select * from pharmacy_settings where clinic_id='".$clinic_id."'")->row();
        // }

        $data['clinic_name'] = $this->session->userdata('clinic_name');
        $data['view'] = 'Pharmacy_orders/settings';
        $this->load->view('layout', $data);

    }

    
    // public function pharmacySettings($id)
    // {

    //     $clinic_id = $this->session->userdata('clinic_id'); 
  
     
    //         if($this->input->post('submit')){
    //             $config['upload_path']="./uploads/pharmacy_logos/";
    //             $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
    //             $this->load->library('upload');    
    //             $this->upload->initialize($config); 
    //             $this->upload->do_upload('pharmacy_logo');
    //             $fileData=$this->upload->data('file_name');
        
    //             $data['clinic_id']= $this->session->userdata('clinic_id');
    //             $data['pharmacy_name']=$this->input->post('pharmacy_name');
    //             $data['pharmacy_email']=$this->input->post('pharmacy_email');
    //             $data['pharmacy_mobile']=$this->input->post('pharmacy_mobile');  
    //             $data['pharmacy_logo']= $fileData;
    //             $data['pharmacy_discount']=$this->input->post('pharmacy_discount');
    //             $data['pharmacy_address']=$this->input->post('pharmacy_address');
    //             $data['created_by']=$this->session->has_userdata('user_id'); 
    //             $data['modified_by']=$this->session->has_userdata('user_id'); 
    //             $data['created_date_time']=$this->created_datetime = date('Y-m-d H:i:s');
    //             $data['modified_date_time']=$this->modified_datetime = date('Y-m-d H:i:s'); 
        
 
    //             $this->Generic_model->updateData('pharmacy_settings',$data,array('pharmacy_id'=>$id));
    //             redirect('Pharmacy_orders/pharmacySettings'); 
        
    //              }
    //     //      $data['clinic_list']= $this->db->query("select * from pharmacy_settings where clinic_id='".$clinic_id."'")->row();
    //     // }
    
    //      $data['clinic_name'] = $this->session->userdata('clinic_name');
    //      $data['view'] = 'Pharmacy_orders/settings';
    //      $this->load->view('layout', $data);
    // }

}
