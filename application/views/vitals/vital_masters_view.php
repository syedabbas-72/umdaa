<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
            <li class="breadcrumb-item active">VITAL</li>
            <li class="breadcrumb-item active">VIEW</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('PatientsVital/vital_standard_master_insert/'.$vital_sign_id.'');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD VITAL PROPERTIES</a>
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
                                <th>vital sign</th>
                                <th>Parameter</th>
                                <th>Age From</th>
                                <th>Age To </th>
                                <th>Age Type</th>
                                <th>Value Low</th>
                                <th>Value High</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($vital_standard_master as $value){ 
                                if($value->status == "1"){
                                    $status = "Active";
                                }else{
                                    $status = "Inactive";
                                }

                                ?>
                            <tr>
                                <td><?php echo $i++;?></td>
                                <td><?php echo $value->vital_sign;?></td>
                                <td><?php echo $value->parameter;?></td>
                                <td><?php echo $value->age_from;?></td>
                                <td><?php echo $value->age_to;?></td>
                                <td><?php echo $value->age_type;?></td>
                                <td><?php echo $value->value_low;?></td>
                                <td><?php echo $value->value_high;?></td>
                                <td><?php echo $status;?></td>
                                <td><a href="#"><i class="fa fa-eye"></i></a>
                                  <a href="<?php echo base_url('PatientsVital/vital_standard_master_edit/'.$value->vital_sign_standard_id);?>"><i class="fa fa-edit"></i></a>
                                  <a href="<?php echo base_url('PatientsVital/vital_standard_masters_delete/'.$value->vital_sign_standard_id)."/";?><?php echo $value->vital_sign_id;?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
                            </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
