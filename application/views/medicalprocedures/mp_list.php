   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">PATIENTS</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('MedicalProcedures/mp_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Add</a>
        </div>
    </div>
<section class="main-content">
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table id="userlist" class="table table-striped dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>S.No:</th>
                            <th>CLINIC</th>
                            <th>DEPARTMENT</th>
                            <th>MEDICAL PROCEDURE</th>
                            <th>PROCEDURE DESCRIPTION</th>
                            <th>FILE</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                       <?php $i=1; foreach ($patients as $value) { ?> 
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo $value->clinic_id;?></td>
                            <td><?php echo $value->department_id; ?></td>
                            <td><?php echo $value->medical_procedure; ?></td>
                            <td><?php echo $value->procedure_description; ?></td>
                            <td><?php echo $value->file_name; ?></td>
                           
                            <td><a href=""><i class="fa fa-eye"></i></a>
                              <a href="<?php echo base_url('MedicalProcedures/mp_update/'.$value->medical_procedure_id);?>"><i class="fa fa-edit"></i></a>
                              <a href="<?php echo base_url('MedicalProcedures/mp_delete/'.$value->medical_procedure_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a></td>
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
      $('#userlist').dataTable();
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



 