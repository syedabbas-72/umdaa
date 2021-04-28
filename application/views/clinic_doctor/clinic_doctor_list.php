   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">CLINIC-DOCTOR LIST</a></li>
            <!-- <li class="breadcrumb-item active">ADD CLINIC</li> -->
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('clinic_doctor/clinic_doctor_add/');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Add</a>
        </div>
    </div>

        <section class="main-content">
        	
            <div class="row">
            	<?php for($i=0;$i<count($clinic_doctor);$i++) { ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <img alt="profile" class="rounded-circle margin-b-10" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_doctor[$i]->clinic_logo);?>" width=auto height = "60">
                            <p class="lead margin-b-0" style = "font-size:1rem;font-weight: 500;"><?php echo $clinic_doctor[$i]->clinic_name;?></p>
                            
                            <hr>
                                   <div style="position: relative; overflow-y: auto; width: auto; height: 150px;">
                                   	<ul class="list-unstyled sidebar-contact-list">
                                   	<?php  $doctors_clinic_info = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_doctor[$i]->clinic_id.'" group by doctor_id')->result();
                                      
                                    for($j=0;$j<count($doctors_clinic_info);$j++) { 
                                         $doctor_info= $this->db->query('select * from doctors where doctor_id ='.$doctors_clinic_info[$j]->doctor_id)->row();
                                    	?>

                                       
                                    <li class="clearfix">
                                        <a href="<?php echo base_url('clinic_doctor/doctor_profile/'.$doctor_info->doctor_id.'/'.$doctors_clinic_info[$j]->clinic_id);?>" class="media-box">
                                           
                                            <span class="float-left">
                                                <img src="<?php echo base_url('uploads/avtar-2.png');?>" alt="user" class="media-box-object rounded-circle" width="50">
                                            </span>
                                            <span class="media-box-body">
                                                <span class="media-box-heading">
                                                    <strong>Dr.<?php echo strtoupper($doctor_info->first_name.' '.$doctor_info->last_name);?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo $doctor_info->qualification;?></small>
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <?php } ?>  
                                </ul>
                                  
                                
                        </div>
                     <hr>
              
                   <?php  $clinic_info = $this->db->query('select * from clinics where clinic_id ='.$clinic_doctor[$i]->clinic_id)->row();?>
                   <p class="text-sm" data-toggle= "tooltip" data-placement = "top" data-original-title="<?php echo $clinic_info->address;?>" style = "text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><b>Address :</b> <?php echo $clinic_info->address;?></p>
                </div>
              </div>
  
            </div>
              <?php } ?>
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



