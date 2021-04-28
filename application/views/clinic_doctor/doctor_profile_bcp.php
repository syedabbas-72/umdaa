   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">DOCTOR PROFILE</a></li>
            <!-- <li class="breadcrumb-item active">ADD CLINIC</li> -->
          </ol>
        </div>
       
    </div>

        <section class="main-content">

            <div class="row">
                <div class="col-md-4">
                    <div class="widget padding-0 white-bg">
                        <div class="bg-danger" style="height: 120px;"></div>
                        <div class="thumb-over">
                            <img src="<?php echo base_url(); ?>assets/img/avtar-5.png" alt="" width="100" class="rounded-circle">
                        </div>
                        <div class="padding-20 text-center">                          
                            <p class="lead font-600 margin-b-0">
                              <?php echo "Dr. ".strtoupper($doctor_info->first_name." ".$doctor_info->last_name); ?></p>
                            <p class="text-muted font-500"><?php echo strtoupper($doctor_info->department_name);?></p>                       
                        </div>
                    </div>
                    <div class='widget white-bg friends-group clearfix'>
              <small class="text-muted">Email address </small>
                            <p><?php echo $doctor_info->email;?></p> 
              <small class="text-muted">Phone</small>
                            <p><?php echo $doctor_info->mobile;?></p> 
              <small class="text-muted">Address</small>
                            <p><?php echo $doctor_info->address;?></p>
                                
                    </div>
                </div>
                <div class="col-8">
            
           <div class="card">
                 
   
                               <div class="widget white-bg">
							   
							   <?php if(!empty($clinic_doctor_id)){?>
                                    <div class="row">
                                            <div class="col-md-12 col-xs-6 b-r"> <strong>SCHEDULE</strong>
                                              <a href='<?php echo base_url('clinic_doctor/add_week_day/'.$clinic_doctor_id)?>' class="btn btn-success btn-rounded pull-right">Add Weekday</a>
                                            </div>
                                            
                                    </div>
									
							   <?php }?>
                  <hr>
                 <?php foreach ($weekdays as $key => $value) {
                 	$day_name = date('l', strtotime("Sunday +{$value->weekday} days")); ?>
                 	<div class="card-header card-default">
                           <?php echo strtoupper($day_name); ?>
						  <a href='<?php echo base_url('clinic_doctor/add_sloat/'.$value->weekday.'/'.$value->clinic_doctor_weekday_id);?>' class="btn btn-success btn-rounded  btn-xs">Add Slot</a>
                        </div>
                        <div class="card-body">
                        <?php 
                        $slots = $this->db->query("select * from clinic_doctor_weekday_slots cws inner join clinic_doctor_weekdays cdw on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' and cdw.weekday='".$value->weekday."'")->result();
                        if(count($slots)>0){
                        foreach($slots as $key => $slot) { ?>
                        	
                        		<a  class="btn btn-info btn-rounded btn-border btn-xs"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a>
                        	
                        		<?php
                        }

                        echo "</div>";
                    }else{
                    	echo "No Slots Available On This Day";
                    }

                 }
                 ?>
                </div>
                            

                 
                  
          </div>
         
        
                </div>
            </div>

         </div>

        </section>

 <script>
  $(document).ready(function () {
      $('#clinic_doctor_list').dataTable();
  });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>



