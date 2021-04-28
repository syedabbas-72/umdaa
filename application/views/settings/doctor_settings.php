

<div class="page-bar">
    <div class="page-title-breadcrumb">
<!-- <div class=" pull-left">
<div class="page-title">Form Layouts</div>
</div> -->
<ol class="breadcrumb page-breadcrumb pull-left">
    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name?></a>&nbsp;<i class="fa fa-angle-right"></i>
    </li>         
    <li><a href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i></li>
    <li class="active"><a href="#">STAFF</a></li> 
</ol>
</div>
</div>


<div class="row">
    <div class="col-2 list-group ">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <?php $this->view("settings/settings_left_nav"); ?>                         
        </div>
    </div>
    <div class="col-10">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content">  
                            <div class="tab-pane active" id="staff">

                                <div><h4 style="font-weight: bold">DOCTORS</h4></div>
                                <table  class = "col-md-12 table datatable customTable">
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>DEPARTMENT</th>
                                            <th>REGISTRATION CODE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for($i=0;$i<count($clinic_doctor);$i++) { ?>
                                            <tr>
                                                <td><span class="trade_name">DR.<?php echo strtoupper($clinic_doctor[$i]->first_name).' '.strtoupper($clinic_doctor[$i]->last_name);?></span><br><p><?php echo $clinic_doctor[$i]->qualification; ?></p></td>
                                                <td><?php echo $clinic_doctor[$i]->department_name;?></td>
                                                <td><?php echo strtoupper($clinic_doctor[$i]->registration_code);?></td>

                                                <td>
                                                    <a href = "<?php echo base_url('settings/doctor_info/'.$clinic_doctor[$i]->doctor_id)?>"><i class="fa fa-pencil-alt"></i></a>
                                                    
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>

                                </table>

                                <!-- Employees -->
                                <div class = "row col-md-12" style="margin-top: 50px">

                                    <div class="col-md-6 col-xs-6 cols-sm-6"><h4 style="font-weight: bold">EMPLOYEES</h4></div>
                                    <div class="col-md-6 col-xs-6 cols-sm-6 text-right">
                                        <a href = "<?php echo base_url('employee/employee_add')?>" class = "btn btn-primary pull-right" style="margin-top: 10px;">Add</a>
                                    </div>
                                </div>
                                <table class = "table datatable customTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%" class="text-right">S.No.</th>
                                            <th style="width: 25%">Full Name / Emp. code</th>                                
                                            <th style="width: 25%">Email</th>
                                            <th style="width: 20%">Role</th>
                                            <th style="width: 15%">Profile</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for($i=0;$i<count($staff);$i++) { 
                                            ?>
                                            <tr>
                                                <td class="text-right"><?=$i+1?>.</td>
                                                <td data-toggle="tooltip" data-placement="top" title="front office, pharmacy"><span class="trade_name"><?php echo strtoupper($staff[$i]['first_name']).' '.strtoupper($staff[$i]['last_name']);?></span><br><small><?php echo $staff[$i]['employee_code'];?></small></td>
                                                <td><?php echo $staff[$i]['email_id'];?></td>
                                                <td><?php echo $staff[$i]['role_name'];?></td>
                                                <td><?php echo $staff[$i]['profile_name'];?></td>                                      
                                                <td>
                                                <a href = "<?php echo base_url('Employee/employee_update/'.$staff[$i]['employee_id'])?>"><i class="fa fa-pencil-alt"></i></a>
                                                <a href = "<?php echo base_url('Employee/delEmployee/'.$staff[$i]['employee_id'])?>" onclick="return confirm('Are you sure to delete?')"><i class="fa fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>                                  
                                    </tbody>                               
                                </table>                             
                                <!-- End Employees -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div><!-- /.card-body -->
    </div>
    <!-- /.nav-tabs-custom -->
</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.datatable').dataTable();
    });
</script>                    


