<?php

error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_orders extends MY_Controller {

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
            $expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' group by b.drug_id,b.batch_no")->result_array();
            $sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' and b.expiry_date>'" . $c_date . "' group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();
            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 group by a.drug_id")->result_array();
        } else {
            $expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no")->result_array();

            $sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'" . $lt_date . "' and b.expiry_date>'" . $c_date . "' and b.clinic_id=" . $clinic_id . " group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();

            $shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.clinic_id=" . $clinic_id . " and a.archieve=0 and (a.expiry_date<'" . $c_date . "' or a.expiry_date>'" . $c_date . "')  group by a.drug_id order by a.expiry_date ASC")->result_array();
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


        $data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 " . $cond . " and expiry_date > CURRENT_DATE group by b.drug_id,b.batch_no")->result();

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

        $data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='" . $clinic_id . "'")->result();

        $data['view'] = 'Pharmacy_orders/Pharmacy_orders';

        $this->load->view('layout', $data);
    }

    public function delete_order($cinvid) {
        //$this->db->query("delete from clinic_pharmacy_inventory_inward where clinic_pharmacy_inventory_inward_id=".$cinvid);
        $this->db->query("update clinic_pharmacy_inventory_inward set archieve=1 where clinic_pharmacy_inventory_inward_id=" . $cinvid);

        redirect('Pharmacy_orders');
    }

    public function edit_order($drug_id, $batch_no) {

        $clinic_id = $this->session->userdata('clinic_id');

        $data['info'] = $this->db->select('INW.clinic_pharmacy_inventory_inward_id, INW.drug_id, INW.batch_no, INW.quantity, INW.mrp, INW.pack_size, INW.expiry_date, INW.supplied_date, CPI.clinic_pharmacy_inventory_id, CPI.reorder_level, CPI.igst, CPI.cgst, CPI.sgst, CPI.max_discount_percentage, CPI.vendor_id, D.trade_name, D.hsn_code')->from('clinic_pharmacy_inventory_inward INW')->join('clinic_pharmacy_inventory CPI', 'INW.drug_id = CPI.drug_id', 'inner')->join('drug D', 'INW.drug_id = D.drug_id', 'inner')->where('INW.drug_id =', $drug_id)->where('INW.batch_no =', $batch_no)->where('INW.clinic_id =', $clinic_id)->where('INW.status =', 1)->get()->result_array();

        $data['view'] = 'Pharmacy_orders/edit_order';

        $this->load->view('layout', $data);
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
        $data['vendor_location'] = $this->input->post("location");
        $data['clinic_id'] = $clinic_id;
        $this->Generic_model->insertData('vendor_master', $data);
        redirect("Pharmacy_orders/Pharmacy_vendors?asuccess");
    }

//Edit Vendor
    public function editVendor() {
        $clinic_id = $this->session->userdata("clinic_id");

        $vendor_id = $this->input->post("vendor_id");
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
            $inwardInfo['quantity'] = $_POST['quantity'][$i];
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

        if ($this->session->userdata('role_id') == 4) {
            $data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id, 'doctor_id' => $this->session->userdata('user_id')), $order = '');
        } else {
            $this->db->select('distinct(doctor_id)');
            $this->db->from('clinic_doctor');
            if ($clinic_id != 0)
                $this->db->where("clinic_id = ", $clinic_id);
            $data['doctors_list'] = $this->db->get()->result();
        }

        // Condition showing with respect to the doctor
        if ($d_id == "all") {
            $docCondition = "";
        } else {
            $docCondition = " and PP.doctor_id = " . $d_id;
        }

        if ($start == $end) {
            $patientPrescriptions = $this->db->query("Select PP.patient_id, PP.patient_prescription_id, PP.doctor_id, PP.clinic_id, PP.appointment_id from patient_prescription as PP where PP.clinic_id ='" . $clinic_id . "'" . $docCondition . " and PP.created_date_time LIKE '" . $start . "%'")->result();
        } else {
            $patientPrescriptions = $this->db->query("Select PP.patient_id, PP.patient_prescription_id, PP.doctor_id, PP.clinic_id, PP.appointment_id from patient_prescription as PP where PP.clinic_id ='" . $clinic_id . "'" . $docCondition . " and PP.created_date_time BETWEEN '" . $start . "%' and '" . $end . "%'")->result();
        }

        $expectedRevenue = 0;
        $convertedRevenue = 0;
        $outPeopleRevenue = 0;
        $billingCount = 0;


        if (count($patientPrescriptions) > 0) {

            // Get the drugs prescribed in each prescription
            foreach ($patientPrescriptions as $prescription) {

                $drugsPrescribed = $this->db->query('select PPD.drug_id, PPD.quantity, INW.mrp/INW.pack_size as amount_per_unit, PPD.quantity * INW.mrp/INW.pack_size as amount from patient_prescription_drug PPD INNER JOIN clinic_pharmacy_inventory_inward INW ON PPD.drug_id = INW.drug_id WHERE clinic_id = ' . $clinic_id . ' AND PPD.patient_prescription_id = ' . $prescription->patient_prescription_id . ' AND INW.status = 1 AND INW.archieve = 0 GROUP BY INW.drug_id order by INW.clinic_pharmacy_inventory_inward_id ASC')->result();

                if (count($drugsPrescribed) > 0) {
                    foreach ($drugsPrescribed as $drugAmount) {
                        $expectedRevenue = (float) $expectedRevenue + (float) $drugAmount->amount;
                    }
                }

                // Check whether this prescription converted as a bill or no
                // If converted get the amount of the billing
                $convertedPrecription = $this->db->select('billing_id')->from('billing')->where('patient_prescription_id =', $prescription->patient_prescription_id)->get()->result_array();

                if (count($convertedPrecription) > 0) {

                    $billingCount = $billingCount++;

                    foreach ($convertedPrecription as $billing) {

                        // Get the line items of the billing and sum of the amounts
                        $billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =', $billing['billing_id'])->get()->row();
                        $convertedRevenue = (float) $convertedRevenue + (float) $billingInfo->amount;
                    }
                }
            }
        }

        $data['prescriptions_count'] = count($patientPrescriptions);
        $data['billing_count'] = $billingCount;
        $data['expected_revenue'] = number_format(round($expectedRevenue), 2);
        $data['converted_revenue'] = number_format(round($convertedRevenue), 2);
        $lost_revenue = number_format(round($expectedRevenue) - round($convertedRevenue), 2);
        if($lost_revenue>0)
            $data['lost_revenue'] = $lost_revenue;
        else
            $data['lost_revenue'] = 0;

        // Get the list of customers purchased drugs from the pharmacy who are outsiders w.r.to the date
        if ($start == $end) {
            $outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_prescription_id = 0 and clinic_id = '" . $clinic_id . "' and billing_type = 'Pharmacy' and billing_date_time like '" . $start . "%'")->result();
        } else {
            $outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_prescription_id = 0 and clinic_id = '" . $clinic_id . "' and billing_type = 'Pharmacy' and billing_date_time between '" . $start . "%' and '" . $end . "%'")->result();
        }

        if (count($outPeople) > 0) {

            $data['out_people_count'] = count($outPeople);

            foreach ($outPeople as $person) {

                // Get the billing line items info with amount summation
                $personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =' . $person->billing_id)->get()->row();
                $outPeopleRevenue = (float) $outPeopleRevenue + (float) $personBillingInfo->amount;
            }
        } else {
            $data['out_people_count'] = 0;
        }

        $data['out_people_revenue'] = number_format(round($outPeopleRevenue), 2);

        echo $data['expected_revenue'] . "*" . $data['converted_revenue'] . "*" . $data['out_people_revenue'] . "*" . $data['lost_revenue'] . "*" . $data['prescriptions_count'] . "*" . $data['billing_count'] . "*" . $data['out_people_count'];
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
                $this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_inward", $cpiInwardInfo);

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

            $this->clinic_inventory_json();
        }

        $data['drug_master_json_file'] = $this->Generic_model->getFieldValue('master_version', 'json_file_name', array('master_name' => 'drug'));

        $data['view'] = 'Pharmacy_orders/new_orders';
        $this->load->view('layout', $data);
    }

//	Creating inventory json w.r.t to clinic
    public function clinic_inventory_json() {
        $clinic_id = $this->session->userdata('clinic_id');
        $master_version = $this->db->query("select * from master_version where clinic_id='" . $clinic_id . "' and master_name='clinic_inventory'")->row();
        if (sizeof($master_version) == 0) {
            $json_file_name = $clinic_id . '_clinic_inventory_v1.json';
            $data['clinic_id'] = $clinic_id;
            $data['master_name'] = 'clinic_inventory';
            $data['version_code'] = '1';
            $data['json_file_name'] = $json_file_name;
            $this->Generic_model->insertData('master_version', $data);
        } else {
            unlink($master_version->json_file_name);
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
            if (count($vendorInfo) > 0) {
                $drugInfo->vendor_list = $vendorInfo;
            }
            else
            {
                $drugInfo->vendor_list = '0';
            }

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

    function raise_shortage_indent() {
        $clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');
        $ind_cnt = $this->db->query("select * from pharmacy_indent where indent_no like '%IND-" . $clinic_id . "-%'")->row();
        $icnt = (count($ind_cnt) + 1);
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
        for ($i = 0; $i < $drug_ids; $i++) {
            if ($_POST['rqty'][$i] == "")
                continue;
            $lineinfo['pharmacy_indent_id'] = $last_inserted_id;
            $lineinfo['drug_id'] = $_POST['drgid'][$i];
            $lineinfo['quantity'] = $_POST['rqty'][$i];
            $lineinfo['status'] = 1;
            $lineinfo['created_by'] = $user_id;
            $lineinfo['modified_by'] = $user_id;
            $lineinfo['created_date_time'] = date("Y-m-d H:i:s");
            $lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertDataReturnId("pharmacy_indent_line_items", $lineinfo);
            $this->db->query("update clinic_pharmacy_inventory set status=1 where drug_id=" . $_POST['drgid'][$i] . " and clinic_id=" . $clinic_id);
        }
        redirect('Pharmacy_orders');
    }

    public function drug_add() {

        $clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');

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

            $this->drug_json();
            redirect('Pharmacy_orders');
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

}
