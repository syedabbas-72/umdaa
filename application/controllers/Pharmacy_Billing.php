<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_Billing extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}

public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
$cond = '';
if(count($this->input->post())>0)
{
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

	$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,c.clinic_name,sum(bi.total_amount) as bamount FROM `billing` b left join billing_line_items bi on b.billing_id=bi.billing_id left join patients p on p.patient_id=b.patient_id left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0 group by bi.billing_id order by b.billing_id desc")->result();
/*  "SELECT a.*,c.clinic_name FROM `appointments` a 
left join clinics c on a.clinic_id = c.clinic_id 
left join patients p on p.patient_id = a.patient_id
left join doctors d on d.doctor_id = a.doctor_id
order by a.appointment_id desc"*/
	$data['view'] = 'billing/billing';
    $this->load->view('layout', $data);
}

public function generateBillingPdf(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	$data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
	$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
	
	$data['from'] = $_POST['date_from'];
	$data['to'] = $_POST['date_to'];
		
	if($clinic_id!=0)
		$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($data['from'])) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($data['to'])) ."'";
	else
		$cond = "where b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($data['from'])) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($data['to'])) ."'";
		

	$data['billings'] = $this->db->query("SELECT b.*,bi.discount as disc,bi.discount_unit as disc_unit,p.first_name as pname,p.mobile as pmob,p.umr_no,p.title,p.last_name as lname,c.clinic_name,sum(bi.amount) as bamount,sum(bi.total_amount) as tamount
							FROM `billing` b
							left join billing_line_items bi on b.billing_id=bi.billing_id
							left join patients p on p.patient_id=b.patient_id
							left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
							group by bi.billing_id order by b.billing_id desc")->result();
	// echo $this->db->last_query();
	if(count($data['billings'])>0)
  	{
  		$data['fromString'] = date("M d Y",strtotime($data['from']))." TO ".date("M d Y",strtotime($data['to']));
  		foreach ($data['billings'] as $value) 
  		{ 
		  	if($value->status==2)
		  		continue;
	  		$discAmount = $value->tamount-$value->bamount;
	  		$discTotal = $discTotal+$discAmount;
		  	$total = $total + $value->bamount;
	  	}
	  	$data['total'] = $total;
	  	$data['discTotal'] = $discTotal;
		$this->load->library('M_pdf');
		$html = $this->load->view('Pharmacy_orders/billingPrint',$data,true);
		$pdfFilePath = $clinic_id.rand(99,999).date("dmY").".pdf";
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
	    $this->m_pdf->pdf->Output("./uploads/clinicBillings/".$pdfFilePath, "F");
	    $this->m_pdf->pdf->Output("./uploads/clinicBillings/".$pdfFilePath, "F");
	    echo $pdfFilePath;
	}
	else
	{
		echo 0;
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
			  $i=1; 
			  if(count($billings)>0)
			  {

			  foreach ($billings as $value) { 
			  	if($value->status==2)
			  		continue;
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
			  ?> 
			  <tr>
			    <td><?php echo $last;?></td>
			    <td><?php echo date("d-m-Y",strtotime($value->billing_date_time));?></td>
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
			    <td class="text-right"><span><?php echo number_format($value->bamount+$discAmount,2); ?></span></td> 
			    <td class="text-right"><span><?php echo number_format($discAmount,2); ?></span></td> 
			    <!-- <td class="text-right"><span><?php echo number_format($taxValue,2); ?></span></td>  -->
			    <td class="text-right"><span><?php echo number_format($value->bamount,2); ?></span></td> 
			  	<td><a href="<?php echo base_url('new_order/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a></td>
			  </tr>
			<?php }
			?>
			<!-- <tr>
				<td><?=$last+1?></td>
				<td>&nbsp;</td>
				<td><span class="trade_name text-right">Total Amount</span></td>
				<td class="text-right"><span class="trade_name text-right"><?=$total?></span></td>
				<td class="text-right"><span class="trade_name text-right"><?=$discTotal?></span></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr> -->
			<?php
			  }
			  else
			  {
			  	?>
			  	<?php
			  }

}

public function billing_report($from='',$to='')
{
	$clinic_id = $this->session->userdata('clinic_id');
$cond = '';

	$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy' and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($from)) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($to)) ."'";
	$data['report_heading'] = "Report Date From : ".date("d M Y",strtotime($from))." Date Till : ".date("d M Y",strtotime($to));

$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,p.umr_no,c.clinic_name,sum(bi.amount) as bamount
FROM `billing` b
left join billing_line_items bi on b.billing_id=bi.billing_id
left join patients p on p.patient_id=b.patient_id
left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
group by bi.billing_id order by b.billing_id")->result();

	$this->load->library('M_pdf');
    $html = $this->load->view('new_order/billing_report',$data,true);
    $pdfFilePath = "pharmacy".date("MdY").".pdf";
    $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");




	$this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F");
	redirect('uploads/billings/'.$pdfFilePath);
}
}
?>