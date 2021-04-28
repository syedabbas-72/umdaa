<div class="row page-header no-background no-shadow margin-b-0">
  <div class="col-lg-6 align-self-center">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
      <li class="breadcrumb-item active">CONSENTFROM-DEPARTMENT</li>
    </ol>
  </div>

  <div class="col-lg-6 align-self-center text-right">
    <a href="<?php echo base_url('Consentform_department/consent_department_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
  </div>
</div>

<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <table id="consent_dept" class="table table-striped dt-responsive nowrap">
            <thead>
              <tr>
                <th>S.No:</th>
                <th>Department</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($consent_department as $value) { ?> 
              <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value->department_name; ?></td>
                <td class="grid-actions">
                  <a href="<?php echo base_url('Consentform_department/consent_department_view/'.$value->department_id);?>" title='Clinic doctors' ><i class="fas fa-eye"></i></a>
                 <!--  <a href="<?php echo base_url('Consentform_department/update/'.$value->doctor_id);?>"><i class="fas fa-pen"></i></a> -->
                  <!-- <a href="<?php echo base_url('Consentform_department/consent_dept_delete/'.$value->consent_form_department_id);?>" onClick="return doconfirm();"><i class="fas fa-trash"></i></a>   -->                
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

<script>
$(document).ready(function () {
    $('#consent_dept').dataTable();
});

function doconfirm(){
  if(confirm("Delete selected messages ?")){
    return true;
  }else{
    return false;  
  } 
}
</script>