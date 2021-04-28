   <?php $entity_id = $this->uri->segment(2); 
$user_id=$this->session->userdata('user_id');
?>

<div class="page-bar">
   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">          
          <ol class="breadcrumb page-breadcrumb pull-left">
            <li ><a href="#" class="text-uppercase"><i class="fas fa-home"></i> <?=$this->session->userdata('clinic_name')?> <i class="fa fa-angle-right"></i></a></li>
            <li >EMPLOYEE LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Employee/employee_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>Employee Add</a>
        </div>
    </div>
    </div>


<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="employee_list" class="table table-bordered customTable">
                                <thead>
                                    <tr>
                                        <th>S.No:</th>
										<th>Username</th>

                                        <th>NAME & DOJ</th>
										<!--<th>DOB</th>
										<th>DOJ</th>
                                        <th>QUALIFICATION</th>-->
                                        <th>Contact Info</th>
                                        <th>Assigned Roles</th>
										<th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($employee_list as $value) { 
								   $sta = $value->status;
								   if($sta==1)
								   {
									   $status = "Active";
								   }else{
									   $status = "In-Active";
								   }
                                   if($value->assigned_roles != ""){
                                        $assRoles = $this->db->query("select group_concat(clinic_role_name) as clinic_roles from clinic_roles where clinic_role_id IN (".$value->assigned_roles.")")->row();
                                   }
                                   else{
                                       $assRoles = "";
                                   }
								   ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><span class="font-weight-bold"><?php echo $value->username;?></span></td>
                                        <td><span class="trade_name"><?php echo $value->title." ". $value->first_name." ".$value->last_name; ?></span> <p class="p-0"><span class="formulation m-0"><?=date('d-m-Y',strtotime($value->date_of_joining))?></span></p></td>
										<!--<td><?php echo date('d-m-Y',strtotime($value->date_of_birth)); ?></td>
										<td><?php echo date('d-m-Y H:i:s',strtotime($value->date_of_joining)); ?></td>
										<td><?php echo $value->qualification; ?></td>-->
										<td>
                                            <p class="p-0"><i class="fas fa-phone"></i> <?php echo $value->mobile; ?></p>
                                            <p class="p-0"><i class="fas fa-at"></i> <?php echo $value->email_id; ?></p>
                                        </td>
										<td><?php echo $assRoles->clinic_roles; ?></td>
										<td><?php echo $status; ?></td>
                                        <td>

                                          <a href="<?php echo base_url('Employee/employee_update/'.$value->employee_id);?>"><i class="fa fa-edit"></i></a>
                                          <?php
                                          if($adminInfo->user_id != $value->employee_id && $value->employee_id != $this->session->userdata('user_id')){
                                              ?>
                                                <a href="<?php echo base_url('Employee/employee_delete/'.$value->employee_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
                                              <?php
                                          }
                                          ?>
                                          
                                        </td>
                                    </tr>
                                  <?php } ?>
                               
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

    </section>
 <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle" style="color:white">Upload File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<div class="modal-body">
     <?php
$output = ''; 
$output .= form_open(base_url('employee/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
$output .= '<div class="row">';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
$output .= form_label('Choose file', 'file');
$data = array(
    'name' => 'userfile',
    'id' => 'userfile',
    'class' => 'form-control filestyle',
    'value' => '',
    'data-icon' => 'false'
);
$output .= form_upload($data);
$output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
$data = array(
    'name' => 'importfile',
    'id' => 'importfile-id',
    'class' => 'btn btn-primary',
    'value' => 'Import',
);
$output .= form_submit($data, 'Import Data');
$output .= '</div>
                        </div></div>';
$output .= form_close();
echo $output;
?>
</div>
     </div>
   </div>
 </div>
 <script>
  $(document).ready(function () {
      $('#employee_list').dataTable();
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



