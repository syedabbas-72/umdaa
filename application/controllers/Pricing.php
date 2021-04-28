<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Pricing extends CI_Controller {
    public function __construct() {
        parent::__construct();
    $is_logged_in = $this->session->has_userdata('is_logged_in');
        if($is_logged_in == 0){
            redirect('Authentication');
        }
    }

    public function index(){
            
        $data['doctors_list'] = $this->db->query("select *,d.created_date_time as docDate from doctors d,department de where de.department_id=d.department_id order by d.doctor_id DESC")->result();   
        $data['billingTypes'] = $this->db->query("select billing_type from billing group by billing_type")->result();   
        $data['umdaaPricing'] = $this->db->query("select * from umdaa_pricing order by umdaa_pricing_id DESC")->row();   
        $data['view'] = 'Pricing/Pricing';
        $this->load->view('layout', $data);
    }

    // GetClinicsList
    public function getClinicsList(){
      extract($_POST);
      $clinicsList = $this->db->query("select * from clinic_doctor cd,clinics c where cd.clinic_id=c.clinic_id and cd.doctor_id='".$doctor_id."'")->result();
      $docInfo = $this->db->query("select *,d.created_date_time as docDate from doctors d,department de where de.department_id=d.department_id and d.doctor_id='".$doctor_id."' order by d.doctor_id")->row();
      ?>
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-4">
              <label class="col-form-label"><b>Qualification</b> : <?=$docInfo->qualification?></label>
            </div>
            <div class="col-md-4">
              <label class="col-form-label"><b>Department</b> : <?=$docInfo->department_name?></label>
            </div>
            <div class="col-md-4">
              <label class="col-form-label"><b>Gender</b> : <?=ucwords($docInfo->gender)?></label>
            </div>
            <div class="col-md-4">
              <label class="col-form-label"><b>Registration Code</b> : <?=$docInfo->registration_code?></label>
            </div>
            <div class="col-md-4">
              <label class="col-form-label"><b>MOB</b> : <?=$docInfo->mobile?></label>
            </div>
            <div class="col-md-4">
              <label class="col-form-label"><b>Email</b> : <?=$docInfo->email?></label>
            </div>
          </div>
        </div>
      </div>
          <div class="row mt-2">
            <div class="col-md-12">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active nav-heading" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Clinic Details</a>
                </li>
              </ul>
            </div>
      <?php
      foreach ($clinicsList as $clinic_list) {
        $state_list=$this->Generic_model->getSingleRecord('states', array('state_id'=>$clinic_list->state_id), $order='');
        $district=$this->Generic_model->getSingleRecord('districts', array('district_id'=>$clinic_list->district_id), $order='');
        ?>
            <div class="col-md-12">
              <div class="card shadow-none">
                <div class="card-body">
                  <h5 class="appColorHeader p-0" style="margin: 0px !important"><?=$clinic_list->clinic_name?> 
                    <?php if($clinic_list->primary_clinic==1) { ?><span class="formulation">PRIMARY</span><?php } ?>
                  </h5>
                  <div class="row">
                    <div class="col-md-4">
                      <label class="col-form-label"><b>MOB</b> : <?=$clinic_list->clinic_phone?></label>
                    </div>
                    <div class="col-md-6">
                      <label class="col-form-label"><b>Email</b> : <?=$clinic_list->email?></label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label class="col-form-label"><b>Address</b> : <?=$clinic_list->address.", ".$clinic_list->location.", ".$district->district_name.", ".$state_list->state_name." - ".$clinic_list->pincode?></label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <?php
      }
      ?>
          </div>
      <?php
    }

    //Set Pricing For Invoices
    public function setPricing(){
    	extract($_POST);


	   	$data['consultation'] = $consultation;
    	$data['lab'] = $lab;
    	$data['sms'] = $sms;
    	$data['pharmacy'] = $pharmacy;
    	$data['procedure'] = $procedure;
    	$data['registration'] = $registration;
    	$data['created_date_time'] = date("Y-m-d h:i:s");
    	$data['modified_date_time'] = date("Y-m-d h:i:s");
    	$this->Generic_model->insertData('umdaa_pricing',$data);
    	redirect('Pricing');
    }

    //Get Approved Invoices List
    public function getInvoicesCount(){
      extract($_POST);
      if($type=="All")
      {
        $cond = '';
      }
      else
      {
        $cond = "and status ='".$type."'";
      }
      if($type == 1)
        $title = "Approved Invoices Count";
      elseif($type == 2)
        $title = "Dropped invoices Count";
      elseif($type == "All")
        $title = "Total Invoices Count";
      $invoicesInfo = $this->db->query("select count(*) as count,billing_type from billing where doctor_id='".$doctor_id."' ".$cond." group by billing_type order by count DESC")->result();
      ?>
      <table class="table table-bordered customTable">
        <thead>
          <th colspan="2" class="text-center"><?=$title?></th>
        </thead>
        <tbody>
      <?php
      foreach ($invoicesInfo as $value) 
      {
        $total += $value->count;
        ?>
        <tr>
          <td><span class="trade_name"><?=$value->billing_type?></span></td>
          <td><span class="trade_name pull-right"><?=$value->count?></span></td>
        </tr>
        <?php
      }
      ?>
          <tr style="background: #e9ebed">
            <td><span class="trade_name">Total Count</span></td>
            <td><span class="trade_name pull-right"><?=$total?></span></td>
          </tr>
        </tbody>
      </table>
      <?php
    }

    //Get Module Info
    public function getModuleInfo(){
      extract($_POST);
      $i = 1;
      $clinicDoctor = $this->db->query("select cd.clinic_id,c.clinic_name from clinic_doctor cd,clinics c where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doctor_id."'")->result();
      foreach ($clinicDoctor as $value) 
      {
        $clinicModuleInfo = $this->db->query("select * from users where clinic_id='".$value->clinic_id."' and role_id IN ('3','6','7','8')")->result();
        foreach ($clinicModuleInfo as $clinics) 
        {
          $roles = $this->db->query("select * from roles where role_id='".$clinics->role_id."'")->row();
          ?>
          <tr>
            <td><?=$i?></td>
            <td><?=$value->clinic_name?></td>
            <td><?=$roles->role_name?></td>
            <td><?=$clinics->username?></td>
          </tr>
          <?php
          $i++;
        }
      }
    }

    public function generateInvoices(){
      extract($_POST);
      $dateSplit = explode("-", $start);
      $check = $this->db->query("select count(*) as count,created_date_time,invoice_pricing_id from invoice_pricing where doctor_id='".$doctor_id."'")->row();
      $last_synced_time = date_create("2019-11-19 20:00:00");
      $today = date_create(date("Y-m-d H:i:s"));
      $diff = date_diff($last_synced_time,$today);
      $diff_days = $diff->format("%a");
      if($diff_days == "0")
      {
        echo "0";
      }
      else
      {
          $inl = $this->db->query("select * from invoice_line_items where invoice_pricing_id='".$check->invoice_pricing."' and item_information='sms'")->row();
          $start = $check->created_date_time;
          $end = date("Y-m-d H:i:s");
          $created_date_time = date("Y-m-d H:i:s");
          $modified_date_time = date("Y-m-d H:i:s");

          $paidcount = $this->db->query("select count(invoice_pricing_id) as count from invoice_pricing")->row();
          $paidTotalCount = $paidcount->count+1;

          $smsCounter = $this->db->query("select (January + February + March + April + May + June + July + August + September + October + November + December) as smsTotal from sms_counter where doctor_id='".$doctor_id."'")->row();
          $sms = $smsCounter->smsTotal-$inl->quantity;
          $umdaaPricing = $this->db->query("select * from umdaa_pricing order by umdaa_pricing_id DESC LIMIT 1")->result_array();  
          $billing = $this->db->query("select doctor_id,count(*) as count,billing_type,created_date_time from billing where doctor_id='".$doctor_id."' and (created_date_time between '".$start."%' and '".$end."%') group by doctor_id,billing_type")->result();
          foreach ($billing as $value) {
            $split = explode("-",$value->created_date_time);
            $btype = strtolower(explode(" ", $value->billing_type)[0]);
            $count += $value->count;
            $data['doctor_id'] = $value->doctor_id;
            $data['month'] = $split[1];
            $data['year'] = $split[0];
            $data['amount']  += $value->count*$umdaaPricing[0][$btype];
          }
          if($count>100)
            $status = "Charge";
          else
            $status = "NA";
          $invoice_no = $paidTotalCount."-".$doctor_id.date("dmy");
          $data['invoice_no'] = $invoice_no;
          $data['totalInvoices'] = $count;
          $data['created_date_time'] = $created_date_time;
          $data['created_by'] = $this->session->userdata('user_id');
          $data['status'] = $status;
          $invoice_pricing_id = $this->Generic_model->insertDataReturnId("invoice_pricing",$data);
          foreach ($billing as $value) 
          {
            $btype = strtolower(explode(" ", $value->billing_type)[0]);
            $data1['invoice_pricing_id'] = $invoice_pricing_id;
            $data1['item_information'] = $value->billing_type;
            $data1['per_unit_price'] = $umdaaPricing[0][$btype];
            $data1['quantity'] = $value->count;      
            $data1['created_date_time'] = $created_date_time;
            $data1['modified_date_time'] = $modified_date_time;
            $this->Generic_model->insertData("invoice_line_items",$data1);
          }
            $data2['invoice_pricing_id'] = $invoice_pricing_id;
            $data2['item_information'] = "SMS";
            $data2['per_unit_price'] = $umdaaPricing[0]['sms'];
            $data2['quantity'] = $sms;      
            $data2['created_date_time'] = $created_date_time;
            $data2['modified_date_time'] = $modified_date_time;
            $this->Generic_model->insertData("invoice_line_items",$data2);
          echo "1";
      }
      

    }


    //GetPaid Invoices Data to Umdaa
    public function getPaidInvoices()
    {
    	extract($_POST);
    	$this->db->select("*");
    	$this->db->from("invoice_pricing");
    	$this->db->where("doctor_id='".$doctor_id."'");
      $this->db->order_by("invoice_pricing_id DESC");
    	$invoiceInfo = $this->db->get()->result();

    	$i = 1;
    	foreach ($invoiceInfo as $value) 
    	{
        $dateObj   = DateTime::createFromFormat('!m', $value->month);
    		?>
    		<tr>
    			<td><?=$i?></td>
          <td><?=$dateObj->format('F')." - ".$value->year?></td>
          <td><?=$value->totalInvoices?></td>
          
            <?php
            if($value->totalInvoices<=100)
            {
              ?>
              <td>
                <span style="font-size:12px;font-weight: bold;color: green !important">FREE</span>
              </td>
              <?php
             
            }
            else
            {
              $total = $value->amount-100;
              ?>
              <td class="text-right">
                <span style="font-size:12px;font-weight: bold;"><?=number_format($total,2)?></span>
              </td>
              <?php
            }
            ?>
          <td><a href="<?=base_url('Pricing/printInvoice/'.$value->invoice_pricing_id)?>" target="blank"><i class="fa fa-print"></i></a></td>
    		</tr>
    		<?php
    		$i++;
    	}
    }

    //Print Invoice 
    public function printInvoice($id){
      $data['invoice_master'] = $this->db->query("select * from invoice_pricing where invoice_pricing_id='".$id."'")->row();
      $data['doctorInfo'] = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['invoice_master']->doctor_id."'")->row();
      $data['invoice_line_items'] = $this->db->query("select * from invoice_line_items where invoice_pricing_id='".$id."'")->result();
      $data['umdaaPricing'] = $this->db->query("select * from umdaa_pricing order by umdaa_pricing_id DESC LIMIT 1")->result_array();  
      $this->load->library('M_pdf');
      $html = $this->load->view('Pricing/invoicePrint',$data,true);
      $pdfFilePath = $data['invoice_master']->invoice_no.".pdf";
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
      $this->m_pdf->pdf->Output("./uploads/umdaaInvoices/".$pdfFilePath, "F");
      $this->m_pdf->pdf->Output("./uploads/umdaaInvoices/".$pdfFilePath, "F");
      redirect('uploads/umdaaInvoices/'.$pdfFilePath);

    }

}

?>

 


