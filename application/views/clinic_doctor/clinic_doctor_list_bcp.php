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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="clinic_doctor_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>CLINIC NAME</th>
                                        <th>DOCTOR NAME</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($clinic_doctor as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->clinic_name; ?></td>
                                        <td><?php echo $value->first_name;?></td>
                                        <td><a href="<?php echo base_url('clinic_doctor/view/'.$value->clinic_doctor_id);?>"><i class="fa fa-eye"></i></a>
                                          <a href="<?php echo base_url('clinic_doctor/clinic_doctor_update/'.$value->clinic_doctor_id);?>"><i class="fa fa-edit"></i></a>
                                          <a href="<?php echo base_url('clinic_doctor/clinic_doctor_delete/'.$value->clinic_doctor_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
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



