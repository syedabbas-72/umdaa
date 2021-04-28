  <div class="page-bar">
   <div class="page-title-breadcrumb">
      <div class=" pull-left">
         <div class="page-title"><?php
            echo $_SESSION['clinic_name'];
            ?></div>
      </div>
      <ol class="breadcrumb page-breadcrumb pull-right">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li class="active">Medical Procedures</li>
      </ol>
   </div>
</div>

<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <table id="department_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Procedure Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach($procedure_list as $value) { 
              
                                    ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->medical_procedure;?></td>
                                        <td><a class="btn btn-info btn-xs" target="_blank" href="<?php echo base_url('uploads/procedures/'.$value->file_name);?>"><i class="fa fa-eye"></i></a>
                                          <a class="btn btn-info btn-xs" href="<?php echo base_url('procedure/update/'.$value->medical_procedure_id);?>"><i class="fa fa-edit"></i></a>
                                         
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
      $('#department_list').dataTable();
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



