<style>
.bg-card
{
  background: rgb(2,0,36);
  background: linear-gradient(43deg, #efefef 28%, rgba(255,255,255,1) 28%);
}
.border-dark {
    border-color: #ffffff !important;
}
.badge{
	background: none !important;
}
.bg-light, .bg-light a {
    color: #1F2D3D !important;
}
</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
     
      <ol class="breadcrumb page-breadcrumb">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">Appointments List</a></li>        
      </ol>
  </div>
</div>

        <section class="main-content">
          
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                    <table class="customTable dataTable">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Doctor</th>
                            <th>Appointment Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        <?php 
                        $i=1;
                        foreach($appointments as $value)
                        {
                            if($value->status=='booked'){
                                $status='Booked';
                                $btn="primary";
                            }else if($value->status=='checked_in'){
                                $status="Checked In";
                                $btn="success";
                            }else if($value->status=='in_consultation'){
                                $status="In Consultation";
                                $btn="warning";
                            }else if($value->status=='reschedule'){
                                $status="Reschedule";
                                $btn="warning";
                            }else if($value->status=='vital_signs'){
                                $status="Vital Sign";
                                $btn="success";
                            }else if($value->status=='closed'){
                                $status="Closed";
                                $btn="danger";
                            }else if($value->status=='drop'){
                                $status="Canceled";
                                $btn="danger";
                            }
                            else if($value->status=='waiting'){
                                $status="Waiting";
                                $btn="info";
                            }
                            $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$value->doctor_id."'")->row();
                            $clinic = $this->db->query("select * from clinics where clinic_id='".$value->clinic_id."'")->row();
                            $title = $value->title;
                            if($title == "")
                            {
                              $fullname = $value->first_name." ".$value->last_name;
                            }
                            else
                            {
                              $fullname = $title.". ".$value->first_name." ".$value->last_name;
                            }
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$fullname?><br><span class="sample m-0"><?=$value->umr?></span></td>
                                <td><span class="trade_name">Dr. <?=$docInfo->first_name." ".$docInfo->last_name?></span><br><span class="formulation m-0"><?=$docInfo->qualification." - ".$docInfo->department_name?></span></td>
                                <td><?=date('M d Y', strtotime($value->appointment_date))?> <?=date('h:i A', strtotime($value->appointment_time_slot))?> <p class="m-0 p-0"><span>@ <?=$clinic->clinic_name?></span></p></td>
                                <td><label class="bg-light small border status badge border-<?=$btn?> badge-pill"><?=$status?></label></td>
                                <td>
                                    <a href="<?=base_url('Profile/index/'.$value->patient_id.'/'.$value->appointment_id)?>"><i class="fa fa-eye"></i></a>
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
            </div>
          </div>                   


        </section>  

    <script type="text/javascript">

      $(document).ready(function(){
        $('.dataTable').dataTable();
      });
    </script>
