 <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">PARAMETERS LIST</a></li>
            <!-- <li class="breadcrumb-item active">ADD CLINIC</li> -->
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('parameters/add/');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Add</a>
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
                                        
                                        <th>PARAMETER NAME</th>
                                        <th>PARAMETER TYPE</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($parameters_list as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->parameter_name; ?></td>
                                        <td><?php echo $value->parameter_type; ?></td>
                                        <td>
                                          <a href="<?php echo base_url('parameters/edit/'.$value->parameter_id);?>"><i class="fa fa-edit"></i></a>
                                          <a href="<?php echo base_url('parameters/delete/'.$value->parameter_id);?>" onclick="return confirm('Are you sure you want to delete?');"><i class="fa fa-trash"></i></a></td>
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