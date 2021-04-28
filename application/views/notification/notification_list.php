   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">NOTIFICATION LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Clinic/clinic_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
        </div>
    </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="notification_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>NOTIFICATION TYPE</th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>CLINIC</th>
                                        <th>PATIENT</th>
                                        <th>UMR NUMBER</th>
                                        <th>APPOINTMENT TIME</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($notification_list as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->notiffication_type;?></td>
                                        <td><?php echo $value->first_name;?></td>
                                        <td><?php echo $value->clinic_name; ?></td>
                                        <td><?php echo $value->last_name; ?></td>
                                        <td><?php echo $value->umr_no; ?></td>
                                        <td><?php echo $value->appointment_time_slot;?></td>
                                        <td><a href=""><i class="fa fa-eye"></i></a>
                                            <a href=""><i class="fa fa-edit"></i></a>
                                            <a href="" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                  <?php } ?>                        
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

    </section>

 <script>
  $(document).ready(function () {
      $('#notification_list').dataTable();
  });
  </script>
  <!-- <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script> -->



