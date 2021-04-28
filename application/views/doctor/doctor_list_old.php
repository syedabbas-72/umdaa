 <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">DOCTOR LIST</li>
                            </ol>
                        </div>
                    </div>


<div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="card card-box">
                                <div class="card-head">
          <table id="doctorlist" class="table table-bordered dt-responsive nowrap">
            <thead>
              <tr>
                <th>S.No:</th>
                <th>Name</th>
                <th>Department</th>
                <th>Email</th>
                <th>Registration code</th>
                
                <!--<th>Status</th>-->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($doctor_list as $value) { ?> 
              <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo "Dr. ".ucwords($value->first_name." ".$value->last_name); ?><br><small><?php echo $value->qualification; ?></small></td>
                 <td><?php echo $value->department_name; ?></td>
                <td><?php echo strtolower($value->email); ?></td>
                <td><?php echo strtoupper($value->registration_code); ?></td>

                <td class="grid-actions">
                  <a class="btn btn-info btn-xs" href="<?php echo base_url('doctor/profile_info/'.$value->doctor_id.'/'.$this->session->userdata('clinic_id'));?>"><i class="fas fa-eye"></i></a>
                  
                </td>
              </tr>
              <?php } ?>                                   
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


<script>
$(document).ready(function () {
    $('#doctorlist').dataTable();
});

function doconfirm(){
  if(confirm("Delete selected messages ?")){
    return true;
  }else{
    return false;  
  } 
}
</script>