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
         <li><a class="parent-item" href="<?php
            echo base_url("department");
            ?>">Departments</a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li class="active">Department Add</li>
      </ol>
   </div>
 </div>


            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('department/department_add');?>" role="form" enctype="multipart/form-data">
                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="department_name" class="col-form-label">DEPARTMENT</label>
                                    <input id="department_name" name="department_name" type="text" placeholder="" class="form-control">
                                </div></div>
                                <div class="col-md-6"><div class="form-group">
                                        <label for="department_icon" >DEPARTMENT ICON</label>
                                        <input id="department_icon" name="department_icon" type="file" class="form-control">
                                    </div>
                                </div>
                             <!--    <div class="col-md-6">
                                    <div class="form-group">
                                     <label for="status" class="col-form-label">STATUS</label>
                                    
                                    <div class="row">
                                       
                                        <div class="radio radio-success">                                                        
                                          <input type="radio" name="status" id="radio12" value="1" checked>
                                          <label for="radio12"> Active </label>
                                        </div>

                                        <div class="radio radio-danger">
                                          <input type="radio" name="status" id="radio13" value="0" >
                                          <label for="radio13"> InActive </label>
                                        </div>
                        
                                     </div>

                                </div>
                                </div> -->
                                
                            </div> 
             
                            

                            
                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>

                                    <!-- <div class="col-sm-6">
                                        <div class="pull-left">
                                            <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                                <i class=""></i> Cancel
                                            </button>
                                        </div>
                                    </div> -->
                                  
                                </form>
                        </div>
                    </div>
                </div>
            </div>
    


