<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
        
   }
public function index(){
	$clinic_id = $this->session->userdata('clinic_id');

  $data['doctors'] = $this->db->query("select d.doctor_id,d.first_name,d.last_name from doctors d,clinic_doctor cd where cd.doctor_id=d.doctor_id and cd.clinic_id='".$clinic_id."'")->result();

  $data['view'] = 'clinic_reports/reports';
  $this->load->view('layout', $data);
 
}

public function getData($id){
  $ar['code'] = $id;
  $ar['name'] ="Naveen";
  $ar['result'] = "Success";
  $this->response(json_encode($ar));
}

//Get Followup Appointments Lost Patients Data
public function getLostAppointments()
{
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($startDate==$endDate)
  {
    $dateCond = " and DATE(a.appointment_date) LIKE '".$startDate."%'";
  }
  else
  {
    $dateCond = " and (DATE(a.appointment_date) BETWEEN '".$startDate."%' AND '".$endDate."%')";
  }
  $appInfo = $this->db->query("select * from appointments a, patients p where a.patient_id=p.patient_id and a.appointment_type='Follow-up' and a.status='booked' and a.clinic_id = '".$clinic_id."' and a.doctor_id='".$doctor_id."' ".$dateCond." ")->result();
  $i = 1;
  ?>
<div class="card">
    <div class="card-body">
        <table class="table customTable" id="PatientsTable">
            <thead>
                <tr>
                    <th colspan="6" class="text-center">
                        Patients Lost Followup Appointments
                    </th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Patient Name - UMR NO.</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Doctor - Appointment Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
        foreach($appInfo as $value)
        {
          ?>
                <tr>
                    <td><?=$i?></td>
                    <td><span><?=$value->first_name." ".$value->last_name?></span><br><span class="code m-0 bg-primary"
                            style="color:white !important"><?=$value->umr_no?></span></td>
                    <td><?=DataCrypt($value->email_id,'decrypt')?></td>
                    <td><?=DataCrypt($value->mobile,'decrypt')?></td>
                    <td><span><?=getDoctorName($value->doctor_id)?></span>
                        <p class="pl-0 ml-0"><?=$value->appointment_date?> @
                            <?=date("h:i A", strtotime($value->appointment_time_slot))?></p>
                    </td>
                    <td>
                        <button class="btn btn-xs btn-app sendsms" id="<?=$value->appointment_id?>">Send SMS</button>
                    </td>
                </tr>
                <?php
          $i++;
        }
        ?>
            </tbody>
        </table>
    </div>
</div>
<?php
}

public function getAppointmentsTrends(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
    if($interval_period != "")
    {
      if($interval_period == "Weekly")
      {
        $int = "Week";
        $period = "1 week";
        $grp = "week";  
      }
      elseif($interval_period == "Monthly")
      {
        $int = "Month";
        $period = "1 month";
        $grp = "month";
      }
      elseif($interval_period == "Quarterly")
      {
        $int = "Quarter";
        $period = "3 month";
        $grp = "quarter";
      }
      elseif($interval_period == "Half-Yearly")
      {
        $int = "Half Year";
        $period = "6 month";
        $grp = "half";
      }
      elseif($interval_period == "Annually")
      {
        $int = "Year";
        $period = "12 month";
        $grp = "year";
      }

      $new = $this->db->query("select MONTH(created_date_time) AS month,WEEK(created_date_time) AS week,CEIL(MONTH(created_date_time)/3) AS `quarter`,CEIL(MONTH(created_date_time)/6) AS `half`,CEIL(MONTH(created_date_time)/12) AS `year`,count(*) as newcount from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and appointment_type='New' and DATE(created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
      $counts[] = count($new);
      $json[0]['type'] = "column";
      $json[0]['name'] = "New Appointments";
      if(count($new)>0)
      {
        foreach($new as $value)
        {  
          $json[0]['data'][] = $value->newcount;
        }
      }
      else
      {
        $json[0]['data'][] = 0;
      }

      $followup = $this->db->query("select MONTH(created_date_time) AS month,WEEK(created_date_time) AS week,CEIL(MONTH(created_date_time)/3) AS `quarter`,CEIL(MONTH(created_date_time)/6) AS `half`,CEIL(MONTH(created_date_time)/12) AS `year`,count(*) as fucount from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and appointment_type='Follow-up' and DATE(created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
      $counts[] = count($followup);
      $json[1]['type'] = "column";
      $json[1]['name'] = "Follow Up Appointments";
      if(count($followup)>0)
      {
        foreach($followup as $value)
        {  
          $json[1]['data'][] = $value->fucount;
        }
      }
      else
      {
        $json[1]['data'][] = 0;
      }

      for($d = 1;$d <= max($counts);$d++)
      {
        $xvalues[] = $int." ".$d; 
      }
        
        echo json_encode($xvalues)."*NV$".json_encode($json,JSON_NUMERIC_CHECK);
      }
}

public function getAppointments(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($startDate==$endDate)
  {
    $dateCond = " and DATE(created_date_time) LIKE '".$startDate."%'";
  }
  else
  {
    $dateCond = " and (DATE(created_date_time) BETWEEN '".$startDate."%' AND '".$endDate."%')";
  }
  $appointments = $this->db->query("select count(*) as appointmentCount,a.doctor_id,d.first_name,d.last_name,d.color_code from appointments a,doctors d where d.doctor_id=a.doctor_id and a.doctor_id='".$doctor_id."' and a.clinic_id='".$clinic_id."' group by a.doctor_id ")->result();
  $i = 0;
  if(count($appointments)>0)
  {
    foreach ($appointments as $value) {
      $doctors = $this->db->query("select * from doctors where doctor_id='".$value->doctor_id."' ")->row();
      $new = $this->db->query("select count(*) as newcount from appointments where clinic_id='".$clinic_id."' and doctor_id='".$value->doctor_id."' and appointment_type='New'  ".$dateCond." group by doctor_id")->row();
      $fu = $this->db->query("select count(*) as fucount from appointments where clinic_id='".$clinic_id."' and doctor_id='".$value->doctor_id."' and appointment_type='Follow-up' ".$dateCond." group by doctor_id")->row();
      $json[0]['name'] = "New Appointments";
      $json[0]['y'] = ($new->newcount=="")?0:(int)$new->newcount;
      $json[0]['sliced'] = false;

      $json[1]['name'] = "Follow Up Appointments";
      $json[1]['y'] = ($fu->fucount=="")?0:(int)$fu->fucount;
      $json[1]['sliced'] = false;      
      ?>
<tr>
    <td><?=$i+1;?></td>
    <td><?="Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name))?></td>
    <td><?=$new->newcount?></td>
    <td><?=$fu->fucount?></td>
    <td><?=$new->newcount+$fu->fucount?></td>
</tr>
<?php
      $i++;
    }
  }
  else
  {
      ?>
<tr>
    <td colspan="5">No Appointments Available</td>
</tr>
<?php
    $json[$i]['name'] = "No Appointments Available";
    $json[$i]['y'] = 0;
    $json[$i]['sliced'] = false;
  }
  echo "*NV$".json_encode($json);

}

//Get PatientsDataTrends By Parameters
public function getPatientsTrends(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($interval_period != "")
  {
    if($interval_period == "Weekly")
    {
      $int = "Week";
      $period = "1 week";
      $grp = "week";  
    }
    elseif($interval_period == "Monthly")
    {
      $int = "Month";
      $period = "1 month";
      $grp = "month";
    }
    elseif($interval_period == "Quarterly")
    {
      $int = "Quarter";
      $period = "3 month";
      $grp = "quarter";
    }
    elseif($interval_period == "Half-Yearly")
    {
      $int = "Half Year";
      $period = "6 month";
      $grp = "half";
    }
    elseif($interval_period == "Annually")
    {
      $int = "Year";
      $period = "12 month";
      $grp = "year";
    }


    $wom = $this->db->query("select sum(count) as sum from (select MONTH(p.created_date_time) AS month,WEEK(p.created_date_time) AS week,CEIL(MONTH(p.created_date_time)/3) AS `quarter`,CEIL(MONTH(p.created_date_time)/6) AS `half`,CEIL(MONTH(p.created_date_time)/12) AS `year`,count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='wom' and cdp.doctor_id='".$doctor_id."'  and DATE(p.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp.",cdp.doctor_id) as WOM")->result();
    // echo $this->db->last_query();
    $counts[] = count($wom);
    $json[0]['type'] = "column";
    $json[0]['name'] = "Word Of Mouth";
    if(count($wom)==0)
    {
        $json[0]['data'][] = 0;
    }
    else
    {
      foreach($wom as $value)
      {
        $json[0]['data'][] = (int)$value->sum;
      }
    }

    $online = $this->db->query("select sum(count) as sum from (select MONTH(p.created_date_time) AS month,WEEK(p.created_date_time) AS week,CEIL(MONTH(p.created_date_time)/3) AS `quarter`,CEIL(MONTH(p.created_date_time)/6) AS `half`,CEIL(MONTH(p.created_date_time)/12) AS `year`,count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='online' and cdp.doctor_id='".$doctor_id."'  and DATE(p.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp.",cdp.doctor_id) as ONLINE")->result();
    $counts[] = count($online);
    $json[1]['type'] = "column";
    $json[1]['name'] = "Online";
    if(count($online)==0)
    {
        $json[1]['data'][] = 0;
    }
    else
    {
      foreach($online as $value)
      {
        $json[1]['data'][] = (int)$value->sum;
      }
    }
    
    $doctors = $this->db->query("select sum(count) as sum from (select MONTH(p.created_date_time) AS month,WEEK(p.created_date_time) AS week,CEIL(MONTH(p.created_date_time)/3) AS `quarter`,CEIL(MONTH(p.created_date_time)/6) AS `half`,CEIL(MONTH(p.created_date_time)/12) AS `year`,count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='doctor' and cdp.doctor_id='".$doctor_id."'  and DATE(p.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp.",cdp.doctor_id) as DOCTOR")->result();
    $counts[] = count($doctors);
    $json[2]['type'] = "column";
    $json[2]['name'] = "Doctors";
    if(count($doctors)==0)
    {
        $json[2]['data'][] = 0;
    }
    else
    {
      foreach($doctors as $value)
      {
        $json[2]['data'][] = (int)$value->sum;
      }
    }
    
    $others = $this->db->query("select sum(count) as sum from (select MONTH(p.created_date_time) AS month,WEEK(p.created_date_time) AS week,CEIL(MONTH(p.created_date_time)/3) AS `quarter`,CEIL(MONTH(p.created_date_time)/6) AS `half`,CEIL(MONTH(p.created_date_time)/12) AS `year`,count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and (p.referred_by_type!='doctor' and p.referred_by_type!='wom' and p.referred_by_type!='online') and cdp.doctor_id='".$doctor_id."'  and DATE(p.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp.",cdp.doctor_id) as OTHERS")->result();
    $counts[] = count($doctors);
    $json[3]['type'] = "column";
    $json[3]['name'] = "Others";
    if(count($doctors)==0)
    {
        $json[3]['data'][] = 0;
    }
    else
    {
      foreach($others as $value)
      {
        $json[3]['data'][] = (int)$value->sum;
      }
    }

    for($d=1;$d<=max($counts);$d++)
    {
      $xvalues[] = $int." ".$d;
    }

    echo json_encode($xvalues)."*NV$".json_encode($json);
  }

}


//Get getFinancesTrends By Parameters
public function getFinancesTrends()
{
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($interval_period != "")
  {
    if($interval_period == "Weekly")
    {
      $int = "Week";
      $period = "1 week";
      $grp = "week";  
    }
    elseif($interval_period == "Monthly")
    {
      $int = "Month";
      $period = "1 month";
      $grp = "month";
    }
    elseif($interval_period == "Quarterly")
    {
      $int = "Quarter";
      $period = "3 month";
      $grp = "quarter";
    }
    elseif($interval_period == "Half-Yearly")
    {
      $int = "Half Year";
      $period = "6 month";
      $grp = "half";
    }
    elseif($interval_period == "Annually")
    {
      $int = "Year";
      $period = "12 month";
      $grp = "year";
    }

    $reg = $this->db->query("select MONTH(b.created_date_time) AS month,WEEK(b.created_date_time) AS week,CEIL(MONTH(b.created_date_time)/3) AS `quarter`,CEIL(MONTH(b.created_date_time)/6) AS `half`,CEIL(MONTH(b.created_date_time)/12) AS `year`,b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Registration & Consultation' and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($reg);
    $json[0]['type'] = "column";
    $json[0]['name'] = "Registration & Consultation";
    if(count($reg)==0)
    {
      $json[0]['data'][] = 0;
    }
    else
    {
      foreach($reg as $value)
      {
        $json[0]['data'][] = (int)$value->sum;
      }
    }

    $con = $this->db->query("select MONTH(b.created_date_time) AS month,WEEK(b.created_date_time) AS week,CEIL(MONTH(b.created_date_time)/3) AS `quarter`,CEIL(MONTH(b.created_date_time)/6) AS `half`,CEIL(MONTH(b.created_date_time)/12) AS `year`,b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Consultation' and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($con);
    $json[1]['type'] = "column";
    $json[1]['name'] = "Consultation";
    if(count($con)==0)
    {
      $json[1]['data'][] = 0;
    }
    else
    {
      foreach($con as $value)
      {
        $json[1]['data'][] = (int)$value->sum;
      }
    }
    
    $pro = $this->db->query("select MONTH(b.created_date_time) AS month,WEEK(b.created_date_time) AS week,CEIL(MONTH(b.created_date_time)/3) AS `quarter`,CEIL(MONTH(b.created_date_time)/6) AS `half`,CEIL(MONTH(b.created_date_time)/12) AS `year`,b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Procedure' and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($pro);
    $json[2]['type'] = "column";
    $json[2]['name'] = "Procedure";
    if(count($pro)==0)
    {
      $json[2]['data'][] = 0;
    }
    else
    {
      foreach($pro as $value)
      {
        $json[2]['data'][] = (int)$value->sum;
      }
    }
    
    $lab = $this->db->query("select MONTH(b.created_date_time) AS month,WEEK(b.created_date_time) AS week,CEIL(MONTH(b.created_date_time)/3) AS `quarter`,CEIL(MONTH(b.created_date_time)/6) AS `half`,CEIL(MONTH(b.created_date_time)/12) AS `year`,b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Lab' and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($lab);
    $json[3]['type'] = "column";
    $json[3]['name'] = "Lab";
    if(count($lab)==0)
    {
      $json[3]['data'][] = 0;
    }
    else
    {
      foreach($lab as $value)
      {
        $json[3]['data'][] = (int)$value->sum;
      }
    }
    
    $pha = $this->db->query("select MONTH(b.created_date_time) AS month,WEEK(b.created_date_time) AS week,CEIL(MONTH(b.created_date_time)/3) AS `quarter`,CEIL(MONTH(b.created_date_time)/6) AS `half`,CEIL(MONTH(b.created_date_time)/12) AS `year`,b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Pharmacy' and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($pha);
    $json[4]['type'] = "column";
    $json[4]['name'] = "Pharmacy";
    if(count($pha)==0)
    {
      $json[4]['data'][] = 0;
    }
    else
    {
      foreach($pha as $value)
      {
        $json[4]['data'][] = (int)$value->sum;
      }
    }        

    $clinicDocInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
    $foLost = $this->db->query("select MONTH(appointment_date) AS month,WEEK(appointment_date) AS week,CEIL(MONTH(appointment_date)/3) AS `quarter`,CEIL(MONTH(appointment_date)/6) AS `half`,CEIL(MONTH(appointment_date)/12) AS `year`,count(appointment_id) as count from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and payment_status='0' and DATE(appointment_date) BETWEEN '".$startDate."' and '$endDate' group by ".$grp." ")->result();
    $counts[] = count($foLost);
    // echo $this->db->last_query();
    $json[5]['type'] = "column";
    $json[5]['name'] = "Lost Revenue";
    if(count($foLost)>0)
    {
      foreach($foLost as $value)
      {
        $json[5]['data'][] = $value->count*$clinicDocInfo->consulting_fee;
      }
    }
    else
    {
      $json[5]['data'][] = 0;
    }

    for($d=1;$d<=max($counts);$d++)
    {
      $xvalues[] = $int." ".$d;
    }

    echo json_encode($xvalues)."*NV$".json_encode($json);
  }

}

//Get Data By Parameters
public function getPatients(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($startDate==$endDate)
  {
    $dateCond = " and DATE(p.created_date_time) LIKE '".$startDate."%'";
  }
  else
  {
    $dateCond = " and (DATE(p.created_date_time) BETWEEN '".$startDate."%' AND '".$endDate."%')";
  }

  $doctors = $this->db->select("first_name,last_name")->from("doctors")->where("doctor_id='".$doctor_id."' ")->get()->row();
  $patients = $this->db->query("select count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and cdp.doctor_id='".$doctor_id."' group by cdp.doctor_id,p.referred_by_type order by p.referred_by_type DESC")->result();
  // echo $this->db->last_query();

  $doc[] = "Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name));
  // $data[$i]['name'] = "Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name));
  $j=0;

  $wom = $this->db->query("select sum(count) as sum from (select count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='wom' and cdp.doctor_id='".$doctor_id."' ".$dateCond." group by cdp.doctor_id) as WOM")->row();
  // echo $this->db->last_query();
  $json[0]['name'] = "Word Of Mouth";
  $json[0]['y'] = (int)$wom->sum;
  $json[0]['sliced'] = false;

  $online = $this->db->query("select sum(count) as sum from (select count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='online' and cdp.doctor_id='".$doctor_id."' ".$dateCond." group by cdp.doctor_id) as ONLINE")->row();
  $json[1]['name'] = "Online";
  $json[1]['y'] = (int)$online->sum;
  $json[1]['sliced'] = false;

  $doctor = $this->db->query("select sum(count) as sum from (select count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and p.referred_by_type='doctor' and cdp.doctor_id='".$doctor_id."' ".$dateCond." group by cdp.doctor_id) as DOCTOR")->row();
  $json[2]['name'] = "Doctor";
  $json[2]['y'] = (int)$doctor->sum;
  $json[2]['sliced'] = false;


  $others = $this->db->query("select sum(count) as sum from (select count(*) as count,cdp.doctor_id,p.referred_by_type from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and (p.referred_by_type!='doctor' and p.referred_by_type!='wom' and p.referred_by_type!='online') and cdp.doctor_id='".$doctor_id."' ".$dateCond." group by cdp.doctor_id) as OTHERS")->row();
  $json[3]['name'] = "Others";
  $json[3]['y'] = (int)$others->sum;
  $json[3]['sliced'] = false;

  if(sizeof($json)>0)
  {
      $k=1;
      foreach($json as $value1)
      {
           ?>
<tr>
    <td><?=$k?></td>
    <td><?=$value1['name']?></td>
    <td><?=$value1['y']?></td>
</tr>
<?php
            $k++;
      }

  }   
  else
  {
    $data[0]->name = "No Patients Available";
    $data[0]->y = 0;           
    $data[0]->sliced = false;     
  }  
  echo "*NV$".json_encode($doc)."*NV$".json_encode($json);

}

//Get Data By Parameters 
public function getLocationPatients(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($startDate==$endDate)
      {
        $dateCond = " and created_date_time LIKE '".$startDate."%'";
      }
      else
      {
        $dateCond = " and (created_date_time BETWEEN '".$startDate."%' AND '".$endDate."%')";
      }

          $doctors = $this->db->select("first_name,last_name")->from("doctors")->where("doctor_id='".$doctor_id."' ")->get()->row();
          $ptCount = $this->db->query("select * from patients")->num_rows();
          $patients = $this->db->query("select count(location) as count,cdp.doctor_id,p.location from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and cdp.doctor_id='".$doctor_id."' and p.location!='' group by cdp.doctor_id,p.location order by count DESC LIMIT 5")->result();
          $others = $this->db->query("select sum(count) as sum from (select count(location) as count,cdp.doctor_id,p.location from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and cdp.doctor_id='".$doctor_id."' and p.location!='' group by cdp.doctor_id,p.location order by count DESC LIMIT 5,".$ptCount.") as sum")->row();
          // echo $this->db->last_query();

          $doc[] = "Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name));
          // $data[$i]['name'] = "Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name));
          $j=0;
          if(count($patients)>0)
          {
            foreach ($patients as $pt) 
            {
                $data[$j]['name'] = $pt->location;
                $data[$j]['y'] = (int)$pt->count;
                $data[$j]['sliced'] = false;
                $j++;
            }  
            if(count($others)>0)
            {
                $data[$j]['name'] = "Others";
                $data[$j]['y'] = (int)$others->sum;
                $data[$j]['sliced'] = false;
            }
          }
          else
          {
            $data[0]->name = "No Patients Available";
            $data[0]->y = 0;           
            $data[0]->sliced = false;     
          }  
          $l=0;$k=1;
          foreach ($data as $value) {
            ?>
<tr>
    <td><?=$k?></td>
    <td><?=$value['name']?></td>
    <td><?=$value['y']?></td>
</tr>
<?php
            $l++;$k++;
          }
          ?>

<?php
      echo "*NV$".json_encode($doc)."*NV$".json_encode($data);

}

public function getFinances(){
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  $doctors = $this->db->select("first_name,last_name")->from("doctors")->where("doctor_id='".$doctor_id."'")->get()->row();
  $xvalues[] = "Dr. ".ucwords(strtolower($doctors->first_name))." ".ucwords(strtolower($doctors->last_name));
    if($startDate==$endDate)
    {
      $dateCond = " and DATE(b.created_date_time) LIKE '".$startDate."%'";
    }
    else
    {
      $dateCond = " and (DATE(b.created_date_time) BETWEEN '".$startDate."%' AND '".$endDate."%')";
    }
    $i = 0;

    $reg = $this->db->query("select b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Registration & Consultation' ".$dateCond." ")->row();
    $query = $this->db->last_query();
    $json[0]['name'] = "Registration & Consultation";
    $json[0]['y'] = (int)$reg->sum;
    $json[0]['sliced'] = false;

    $con = $this->db->query("select b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Consultation' ".$dateCond." ")->row();
    $query1 = $this->db->last_query();
    $json[1]['name'] = "Consultation";
    $json[1]['y'] = (int)$con->sum;
    $json[1]['sliced'] = false;

    $pro = $this->db->query("select b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Procedure' ".$dateCond." ")->row();
    $json[2]['name'] = "Procedure";
    $json[2]['y'] = (int)$pro->sum;
    $json[2]['sliced'] = false;

    $lab = $this->db->query("select b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Lab' ".$dateCond." ")->row();
    $json[3]['name'] = "Lab";
    $json[3]['y'] = (int)$lab->sum;
    $json[3]['sliced'] = false;

    $pha = $this->db->query("select b.billing_id,sum(bl.amount) as sum from  billing b,billing_line_items bl where b.billing_id=bl.billing_id and  b.clinic_id = '".$clinic_id."' and b.doctor_id = '".$doctor_id."' and b.status NOT IN (2,3) and b.billing_type = 'Pharmacy' ".$dateCond." ")->row();
    $json[4]['name'] = "Pharmacy";
    $json[4]['y'] = (int)$pha->sum;
    $json[4]['sliced'] = false;

    $clinicDocInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
    $foLost = $this->db->query("select count(appointment_id) as count from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and payment_status='0' and (appointment_date BETWEEN '".$startDate."%' AND '".date('Y-m-d',strtotime($endDate.'+1 day'))."%')")->row();
    // echo $this->db->last_query();
    $json[5]['name'] = "Lost Revenue";
    $json[5]['y'] = $foLost->count*$clinicDocInfo->consulting_fee;
    $json[5]['sliced'] = false;


    $j=0;

    $i=1;
    if(count($json)>0)
    {
      foreach ($json as $value) 
      {
        ?>
<tr>
    <td><?=$i++?></td>
    <td><?=explode("[",$json[$j]['name'])[0]?></td>
    <td>Rs. <?=$json[$j]['y']?>/-</td>
</tr>
<?php
        $j++;
      }
      ?>

<?php
    }   
    else
    {
      $json[0]->name = "No Finances Available";
      $json[0]->y = 0;            
      $json[0]->sliced = false;          
    } 
    
    echo "*NV$".json_encode($json)."*NV$".$query."*NV$".$query1;
}


//Get Pharmacy Finances

public function getPharmacyFinances()
{
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  $start = $startDate;$end = $endDate;
  if($start == $end)
  {
      $exDateCond = "DATE(pp.created_date_time) LIKE '".$start."%'";
      $billDateCond = "DATE(created_date_time) LIKE '".$start."%'";
  }
  else
  {
      $exDateCond = "DATE(pp.created_date_time) BETWEEN '".$start."%' AND '".$end."%'";
      $billDateCond = "DATE(created_date_time) BETWEEN '".$start."%' AND '".$end."%'";
  }

  $exCon = "and pp.doctor_id = '".$doctor_id."' and ".$exDateCond;
  $billCon = "and doctor_id = '".$doctor_id."' and ".$billDateCond;
  $outBillCon = "and ".$billDateCond;

  // Expected Revenue
  $expectedInfo = $this->db->query("select ppd.drug_id,ppd.quantity from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.clinic_id='".$clinic_id."' ".$exCon)->result();
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
  
  $expected_revenue = floatval(round($expected,2));
  $converted_revenue = floatval(round($converted,2));
  $out_people_revenue = floatval(round($outrevenue,2));
  $lost_revenue = (($expected_revenue-$converted_revenue) <= 0) ? '0.00' : floatval(round($expected_revenue-$converted_revenue,2));

  $json[0]['name'] = "Expected Revenue";
  $json[0]['y'] = $expected_revenue;
  $json[0]['sliced'] = false;
  
  $json[1]['name'] = "Converted Revenue";
  $json[1]['y'] = $converted_revenue;
  $json[1]['sliced'] = false;
  
  $json[2]['name'] = "Lost Revenue";
  $json[2]['y'] = $lost_revenue;
  $json[2]['sliced'] = false;
  
  $json[3]['name'] = "Out People Revenue";
  $json[3]['y'] = $out_people_revenue;
  $json[3]['sliced'] = false;

  echo json_encode($json);
}

// Pharmacy Trends
//Get getPharmacyTrends By Parameters
public function getPharmacyTrends()
{
  $clinic_id = $this->session->userdata('clinic_id');
  extract($_POST);
  if($interval_period != "")
  {
    if($interval_period == "Weekly")
    {
      $int = "Week";
      $period = "1 week";
      $grp = "week";  
    }
    elseif($interval_period == "Monthly")
    {
      $int = "Month";
      $period = "1 month";
      $grp = "month";
    }
    elseif($interval_period == "Quarterly")
    {
      $int = "Quarter";
      $period = "3 month";
      $grp = "quarter";
    }
    elseif($interval_period == "Half-Yearly")
    {
      $int = "Half Year";
      $period = "6 month";
      $grp = "half";
    }
    elseif($interval_period == "Annually")
    {
      $int = "Year";
      $period = "12 month";
      $grp = "year";
    }

    $expectedInfo = $this->db->query("select MONTH(pp.created_date_time) AS month,WEEK(pp.created_date_time) AS week,CEIL(MONTH(pp.created_date_time)/3) AS `quarter`,CEIL(MONTH(pp.created_date_time)/6) AS `half`,CEIL(MONTH(pp.created_date_time)/12) AS `year`,group_concat(ppd.drug_id) as drug_id,group_concat(ppd.quantity) as quantity from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.clinic_id='".$clinic_id."' and pp.doctor_id = '".$doctor_id."'  and DATE(pp.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($expectedInfo);
    $json[0]['type'] = "column";
    $json[0]['name'] = "Expected Revenue";
    if(count($expectedInfo)>0)
    {
      foreach($expectedInfo as $value)
      {  
         $drug_id = explode(",",$value->drug_id);
         $quantity = explode(",",$value->quantity);
         $expected = 0;
         for($n=0;$n<count($drug_id);$n++)
         {
          $expected += getDrugPrice($clinic_id,$drug_id[$n],$quantity[$n]);
         }
         $exp[] = $expected;
         $json[0]['data'][] = floatval(round($expected,2));
      }
      
    }
    else
    {
      $json[0]['data'][] = 0;
    }

    
    // Converted Revenue
    $convertedInfo = $this->db->query("select MONTH(created_date_time) AS month,WEEK(created_date_time) AS week,CEIL(MONTH(created_date_time)/3) AS `quarter`,CEIL(MONTH(created_date_time)/6) AS `half`,CEIL(MONTH(created_date_time)/12) AS `year`,billing_id from billing where clinic_id = '".$clinic_id."' and billing_type='Pharmacy' and (status='0' or status='1') and patient_prescription_id!='0'  and DATE(created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($convertedInfo);
    $json[1]['type'] = "column";
    $json[1]['name'] = "Converted Revenue";
    if(count($convertedInfo) < 0)
    {
      $json[1]['data'][] = 0;
    }
    else
    {
      $converted = 0;$i = 0;
      foreach($convertedInfo as $value)
      {
          $billing_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
          $convert[] = $billing_line_info->amount;
          $converted = $billing_line_info->amount;
          $json[1]['data'][] = floatval(round($converted,2));
          $i++;
      }
    }

    for($x=count($convert);$x<count($exp);$x++)
    {
      $convert[] = 0;
    }

    // Lost Revenue
    $counts[] = count($convert);
    $json[2]['type'] = "column";
    $json[2]['name'] = "Lost Revenue";
    if(count($convert)>0)
    {
      $i = 0;
      foreach($convert as $value)
      {
        if($value<0)
          $json[2]['data'][] = 0;
        else  
          $json[2]['data'][] = round($exp[$i]-$value,2);
        $i++;
      }
    }
    else
    {
      $json[2]['data'][] = 0;
    }

    // Out Patients Revenue
    $outBills = $this->db->query("select MONTH(created_date_time) AS month,WEEK(created_date_time) AS week,CEIL(MONTH(created_date_time)/3) AS `quarter`,CEIL(MONTH(created_date_time)/6) AS `half`,CEIL(MONTH(created_date_time)/12) AS `year`,billing_id from billing where clinic_id = '".$clinic_id."' and billing_type='Pharmacy' and (status='0' or status='1') and patient_prescription_id='0'  and DATE(created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
    $counts[] = count($outBills);
    $json[3]['type'] = "column";
    $json[3]['name'] = "Out People Revenue";
    if(count($outBills) < 0)
    {
      $json[3]['data'][] = 0;
    }
    else
    {
      $outrevenue = 0;
      foreach($outBills as $value)
      {
          $OutBill_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
          $outrevenue += $OutBill_line_info->amount;
          $json[3]['data'][] = floatval(round($outrevenue,2));
      }
    }

    for($d = 1;$d <= max($counts);$d++)
    {
      $xvalues[] = $int." ".$d; 
    }
    
    echo json_encode($xvalues)."*NV$".json_encode($json)."*NV$".json_encode($counts)."*NV$".json_encode($lost);
  }

}

public function getDoctorLostAppointments($docId){
  $clinic_id = $this->session->userdata('clinic_id');
  $clinicDocInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$docId."' and clinic_id='".$clinic_id."'")->row();
  $appInfo = $this->db->query("select count(appointment_id) as count from appointments where clinic_id='".$clinic_id."' and doctor_id='".$docId."' and appointment_date BETWEEN '2020-02-01' AND '2020-02-29'")->row();
  echo $appInfo->count;
}


// Send SMS to Lost Followup Patients
public function sendsms(){
  $today = date("Y-m-d");
  extract($_POST);
  $this->db->select("*");
  $this->db->from("appointments a");
  $this->db->join("patients p","p.patient_id=a.patient_id");
  $this->db->where("a.appointment_id",$id);
  $appInfo = $this->db->get()->row();
  $clinicInfo = $this->db->select("*")->from("clinics")->where("clinic_id",$appInfo->clinic_id)->get()->row();
  if($today < $appInfo->appointment_date)
  {
    $smsContent = "Dear ".$appInfo->first_name." ".$appInfo->last_name.", This is a Reminder from UMDAA. Your Appointment is with ".getDoctorName($appInfo->doctor_id)." On ".$appInfo->appointment_date." ".$appInfo->appointment_time_slot." in ".$clinicInfo->clinic_name.". Thank You.";
  }
  elseif($today > $appInfo->appointment_date)
  {
    $smsContent = "Dear ".$appInfo->first_name." ".$appInfo->last_name.", You Lost Appointment with ".getDoctorName($appInfo->doctor_id)." On ".$appInfo->appointment_date." ".$appInfo->appointment_time_slot." in ".$clinicInfo->clinic_name.". Contact Us to Reschedule. Thank You.";
  }
  if($appInfo->mobile == "")
  {
    $mobile = DataCrypt($appInfo->alternate_mobile, 'decrypt');
  }
  else
  {
    $mobile = DataCrypt($appInfo->mobile, 'decrypt');
  }
  
  $res = sendsms($mobile,$smsContent);
  if($res)
    echo "1";
  else
    echo "0";
}

}
?>
