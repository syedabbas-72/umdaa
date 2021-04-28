<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
        
   }
public function index(){
    $clinic_id = $this->session->userdata('clinic_id');

    $this->db->select('a.*,c.clinic_name,p.title,p.first_name as pfname,p.middle_name as pmname,p.last_name as plname,p.gender,p.age,p.age_unit,p.mobile,p.umr_no,d.salutation, d.first_name as dfname,d.last_name as dlname,d.color_code,d.department_id,de.department_id,de.department_name,p.photo,a.status as ap_status');
    $this->db->from('appointments a');
    $this->db->join('clinics c', 'a.clinic_id = c.clinic_id','left');
    $this->db->join('patients p', 'p.patient_id = a.patient_id','left');
    $this->db->join('doctors d', 'd.doctor_id = a.doctor_id','left');
    $this->db->join('department de', 'd.department_id=de.department_id','left');
    $this->db->where("a.clinic_id=".$clinic_id." and a.appointment_date = '".date("Y-m-d")."' and a.status!='drop' and a.status!='reschedule'");
    $data['appointment'] = $this->db->get()->result();
        
    $data['view'] = 'appointments/appointments';
    $this->load->view('layout', $data);
}

public function getAppointments(){
    $clinic_id = $this->session->userdata('clinic_id');
    $start = $_POST['startDate'];
    $end = $_POST['endDate'];

    // $this->db->select('a.check_in_time');
    $this->db->select('a.*,c.clinic_name,p.title,p.first_name as pfname,p.middle_name as pmname,p.last_name as plname,p.gender,p.age,p.age_unit,p.mobile,p.umr_no,d.salutation, d.first_name as dfname,d.last_name as dlname,d.color_code,d.department_id,de.department_id,de.department_name,p.photo,a.status as ap_status');
    $this->db->from('appointments a');
    $this->db->join('clinics c', 'a.clinic_id = c.clinic_id','left');
    $this->db->join('patients p', 'p.patient_id = a.patient_id','left');
    $this->db->join('doctors d', 'd.doctor_id = a.doctor_id','left');
    $this->db->join('department de', 'd.department_id=de.department_id','left');
    if($start==$end)
    {
        $this->db->where("a.clinic_id=".$clinic_id." and a.appointment_date = '".$start."' and a.status!='drop' and a.status!='reschedule'");
    }
    else
    {
        $this->db->where("a.clinic_id=".$clinic_id." and a.appointment_date between '".$start."' and '".$end."' and a.status!='drop' and a.status!='reschedule'");
    }
    $appointment = $this->db->get()->result();
    // echo $this->db->last_query();
    // echo "<pre>";print_r($appointment);echo "</pre>";
    // exit;
    $i = 1;
      foreach ($appointment as $value) {
        $a_id=$value->appointment_id;
        if($value->ap_status=='booked'){
            $status='Booked';
            $btn="primary";
        }else if($value->ap_status=='checked_in'){
            $status="Checked In";
            $btn="success";
        }else if($value->ap_status=='in_consultation'){
            $status="In Consultation";
            $btn="warning";
        }else if($value->ap_status=='reschedule'){
            $status="Reschedule";
            $btn="warning";
        }else if($value->ap_status=='vital_signs'){
            $status="Vital Sign";
            $btn="success";
        }else if($value->ap_status=='closed'){
            $status="Closed";
            $btn="danger";
        }else if($value->ap_status=='drop'){
            $status="Canceled";
            $btn="danger";
        }
        else if($value->ap_status=='waiting'){
            $status="Waiting";
            $btn="info";
        }
        

       
        
        ?>
        <tr>
          <td><?=$i?></td>
          <td>
            <?php
             if($status !='Booked'){
                //  echo "hi";
                if($value->check_in_time!=NULL){
                    $day = date('M d, Y H:i:s',strtotime($value->check_in_time));
            ?>
            
            <script type="text/javascript">
                
            var countDownDate<?php echo $a_id; ?> = new Date("<?php echo $day;?>").getTime();
            var x<?php echo $a_id; ?> = setInterval(function() {
            var now<?php echo $a_id; ?> = new Date().getTime();

            var distance =  now<?php echo $a_id; ?> - countDownDate<?php echo $a_id; ?>;

            var days<?php echo $a_id; ?> = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours<?php echo $a_id;?> = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("app<?php echo $a_id; ?>").innerHTML = '<label  style="color: #F84982;">'+hours<?php echo $a_id; ?> + '</label><label class="small font-weight-bold">H</label><label  style="color: #F84982;"> '+ minutes<?php echo $a_id; ?> + '</label><label class="small font-weight-bold">M</label> <label  style="color: #F84982;">' + seconds<?php echo $a_id; ?> + '</label><label class="small font-weight-bold">S</label>';
                if (distance < 0) {
                    clearInterval(x<?php echo $a_id; ?>);
                    document.getElementById("app<?php echo $a_id; ?>").innerHTML = "Waiting";
                }
            }, 1000);
            </script>
                <?php 
            }
        }
            ?>
            <div class="ticking font-weight-bold" style="font-size: 12px">
                <?php
                if($status != 'Booked' && $status!="Closed"){
                    if($value->check_in_time!=""){
                        ?>
                       <p style="font-size: 14px" id="app<?php echo $a_id; ?>" class="font-weight-bold p-0"></p>
                       <span><?=($value->check_in_time == "")?'':date("h:i A",strtotime($value->check_in_time))?></span>
                        <?php
                    }
                }else{
                    ?>
                        <!-- <span  style="color: #F84982;">00</span><span class="small font-weight-bold">H</span>
                        <span  style="color: #F84982;">00</span><span class="small font-weight-bold">M</span> 
                        <span  style="color: #F84982;">00</span><span class="small font-weight-bold">S</span><br> -->
                        <span><?=($value->check_in_time == "")?'':date("h:i A",strtotime($value->check_in_time))?></span>
                    <?php
                } 
                ?>
            </div>
            
         </td>
          <td><?=ucwords(strtolower($value->pfname))." ".ucwords(strtolower($value->plname))?> <br><span class="sample m-0"><?=$value->umr_no?></span></td>
          <td>Dr. <?=ucwords(strtolower($value->dfname))." ".ucwords(strtolower($value->dlname))?></td>
          <td><?=DataCrypt($value->mobile,'decrypt')?></td>
          <td>
            <label class="badge bg-light small border status border-<?php echo $btn; ?> badge-pill"><?php echo str_replace("_"," ",$value->ap_status); ?></label></td>
          <td>
            <a href="<?=base_url('profile/index/'.$value->patient_id.'/'.$value->appointment_id)?>" title="View"><i class="fa fa-eye"></i></a>

          </td>
        </tr>
        <?php
        $i++;
      }    
}

public function appointment_status($status_type='',$appointment_id=''){
    
    echo $status_type.''.$appointment_id;
    
        $condition=array('appointment_id'=>$appointment_id);
        if($status_type=='checked_in')
        {
            
            $appointment['status']='checked_in';
            $appointment['check_in_time']=date('Y-m-d H:i:s');
            $appointment['modified_by']=$user_id;
            $appointment['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments",$appointment,$condition);
            $ap_details = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id),'');
            
            $this->Generic_model->pushNotifications($ap_details->patient_id,$appointment_id,$ap_details->doctor_id,$ap_details->clinic_id,'check_in','patient_21_details_tab');
        }
        else if ($status_type=='drop'){
            $appointment['status']='drop';
            $appointment['modified_by']=$user_id;
            $appointment['modified_date_time']=date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments",$appointment,$condition);
            
        }
        redirect('calendar_view');
    
}

public function reschedule(){


$odate = $this->input->post("date");
//echo $adate."-".date("Y-m-d",strtotime($adate));exit;
$appointment_id = $this->input->post("appointment_id");
$data['status'] = "reschedule";
$this->Generic_model->updateData('appointments',$data,array('appointment_id'=>$appointment_id));

            $appointment['parent_appointment_id']=$this->input->post("appointment_id");;
            $appointment['clinic_id']=$this->session->userdata('clinic_id');
            $appointment['patient_id']=$this->input->post("patient_id");
            $appointment['umr_no']=$this->input->post("umr_no");
            $appointment['doctor_id']=$this->input->post("doctor_id");
            $appointment['appointment_type']="New";
            $appointment['appointment_date']=date("Y-m-d",strtotime($odate));
            $appointment['appointment_time_slot']=$this->input->post("slots");
            $appointment['priority']="none";
            $appointment['status']="booked";
            $appointment['payment_status'] = $this->input->post("payment_status");
            $appointment['created_by']=$user_id;
            $appointment['modified_by']=$user_id;
            $appointment['created_date_time']=date('Y-m-d H:i:s');
            $appointment['modified_date_time']=date('Y-m-d H:i:s');
            $param['appointment']['pdf_file']=NULL;
            $this->Generic_model->insertData("appointments",$appointment);
            //echo $this->db->last_query();
            redirect("calendar_view");

    }

    public function sms_test(){
        sendsms("9849865554","Dear Uday Kanth Rapalli,
            
            Your appointment is fixed with Dr. Abdul Khaliq,  Health Inn Multispeciality Clinic on 15 Mar. '19 at 03:00 PM. Please be present atleast 15 minutes before your scheduled time.

            Have a Good Day");
    }

    

}
