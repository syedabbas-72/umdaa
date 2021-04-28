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
     
         <li class="active">Departments</li>
      </ol>
   </div>
 </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <a href="<?php echo base_url('Department/department_add');?>" class="pull-right btn btn-app box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
                                </div>
                            </div>
                            <table id="department_list" class="table customTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>ICON</th>
                                        <th>DEPARTMENT</th>
                                    
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($department_list as $value) { 
                                       if($value->department_icon == "")
                                       {
                                            $src = "dummyDEPT.png";
                                       }
                                       else
                                       {
                                            $src = $value->department_icon;
                                       }
                                       ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><a href=""><img src="<?=base_url('uploads/departments/'.$src)?>" style="width:50px"></a></td>
                                        <td><?php echo $value->department_name;?></td>
                                       
                                        <td>
                                          <a href="<?php echo base_url('department/department_update/'.$value->department_id);?>"><i class="fa fa-edit"></i></a>
                                          <a href="<?php echo base_url('department/department_delete/'.$value->department_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
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
        if(confirm("Delete Department ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>



