<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">FOLLOW-UP LIST</a></li>
            <!-- <li class="breadcrumb-item active">ADD CLINIC</li> -->
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('followup_templates/insert/');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Add</a>
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
                                        <th>NAME</th>
                                        <th>DEPARTMENT</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($templates_list as $value) { 
                                    $dept = $this->db->query("select * from department where department_id='".$value->department_id."'")->row();
                                    ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->followup_name; ?></td>
                                        <td><?php echo $dept->department_name; ?></td>
                                        <td>
                                        
                                          <a href="<?php echo base_url('followup_templates/edit/'.$value->followup_id);?>"><i class="fa fa-eye"></i></a>
                                          <a href="<?php echo base_url('followup_templates/delete/'.$value->followup_id);?>" onclick="return confirm('Are you sure you want to delete?');"><i class="fa fa-trash"></i></a></td>
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