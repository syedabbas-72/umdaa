<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Profile</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <div class="pull-left page-title">
                    <?php count($appointmentInfo) > 0 ? $this->load->view('profile/appointment_info_header', $patient_dt->patient_id) : ''; ?>
                </div>
                <div class="row col-md-12"> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="" class="">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Profile
                                <div class="pull-right">
                                    <a href="<?php echo base_url("patients/patient_update/".$patient_id."/".$appointment_id); ?>"><i class="fas fa-pencil-alt edit"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-12">
                            <div class="col-md-3 profilePic">
                                <img src="<?php echo ($patient_dt->photo=='') ? base_url('assets/img/profilePic.jpg') :base_url('uploads/patients/'.$patient_dt->photo) ?>" style="width: 140px; height: 140px; position: relative; overflow: hidden; border-radius: 70px;">
                                <br>
                                <img class="qrcode" src="<?php echo base_url('uploads/qrcodes/patients/'.$patient_dt->qrcode); ?>">
                            </div>
                            <div class="col-md-9 profileMaxInfo">
                                <?php
                                echo $patient_dt->title != '' ? ucwords($patient_dt->title).". " : '';
                                echo ucwords($patient_dt->first_name.' '.$patient_dt->last_name);

                                ?>
                                <br>
                                <span class="pid">
                                    <?php
                                    $dob = $patient_dt->date_of_birth;
                                    $today = date("Y-m-d");
                                    $diff = date_diff(date_create($dob), date_create($today));
                                    $age = $diff->format('%y Years %m Months %d Days');
                                    ?>
                                    <span>Pid: </span><?=ucwords($patient_dt->umr_no)?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span>Mob: </span><?=DataCrypt($patient_dt->mobile, 'decrypt')?><?=($patient_dt->alternate_mobile!='')?' ,'.DataCrypt($patient_dt->alternate_mobile,'decrypt').' (A)':''?>
                                    <br>
                                    <span>Sex: </span><?=$patient_dt->gender != '' ? $patient_dt->gender : ''; ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <!-- <?php if($patient_dt->date_of_birth != '' || $patient_dt->date_of_birth != NULL) { echo date("d M. Y",strtotime($patient_dt->date_of_birth)); } ?>  -->
                                    <span>Age: </span><?php echo $patient_dt->age." ".$patient_dt->age_unit; ?>
                                    <br>
                                    <span>Address: </span>
                                    <?php
                                    if($patient_dt->address_line == '') {
                                        echo '--- NA ---';
                                    }else{
                                        echo ucwords($patient_dt->address_line);
                                    }

                                    //Location
                                    if($patient_dt->location){
                                        echo '<br>';
                                        echo '<span>Location: </span>'.ucwords($patient_dt->location);
                                    }
                                    
                                    // District Name
                                    if($patient_dt->district_name != ''){ 
                                        echo '<br>';
                                        echo '<span>District: </span>'.ucwords($patient_dt->district_name);
                                    }
                                    
                                    // State Name
                                    if($patient_dt->state_name != ''){ 
                                        echo '<br>';
                                        echo '<span>State: </span>'.ucwords($patient_dt->state_name);
                                    }
                                    ?>
                                </span>
                            </div>
                            <!-- <div class="col-md-12"> 
                                <table id="doctorlist" class="table table-striped dt-responsive nowrap">
                                    <thead>  
                                        <tr>
                                            <th style="font-size: 16px">Patient Profile</th>
                                            <th style="text-align:right;"></th>
                                        </tr>       
                                    </thead>
                                    <tbody>  
                                        <tr><td>Name</td><td><?php echo ucwords($patient_dt->first_name.' '.$patient_dt->last_name);?> </td></tr>
                                        <tr><td>DOB : </td><td><?php if($patient_dt->date_of_birth!=''|| $patient_dt->date_of_birth!=NULL) {  echo date("d/m/Y",strtotime($patient_dt->date_of_birth)); } ?></td></tr>
                                        <tr><td>AGE : </td><td><?php echo $patient_dt->age; ?></td></tr>
                                        <tr><td>Gender : </td><td><?php echo $patient_dt->gender; ?></td></tr>
                                        <tr><td>Mobile : </td><td><?php echo $patient_dt->mobile; ?></td></tr>
                                        <tr><td>UMR NO : </td><td><?php echo $patient_dt->umr_no; ?></td></tr>
                                        <tr><td>Address : </td><td><?php echo ucfirst($patient_dt->address_line); ?></td></tr>
                                        <tr><td>District : </td><td><?php echo $patient_dt->district_name; ?></td></tr>
                                        <tr><td>State : </td><td><?php echo $patient_dt->state_name; ?></td></tr>
                                        <tr><td>Pincode : </td><td><?php echo $patient_dt->pincode; ?></td></tr>
                                    </tbody>
                                </table>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>