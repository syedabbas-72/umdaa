<style type="text/css">
  .radio label::after{
    top:10px !important;
  }
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Consent Forms</li>
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
                <div class="row col-md-12 "> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12 ">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="view_caseresults" class="view_caseresults">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Consent Forms
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding-right: 0px">
                            <table id="doctorlist" class="table dt-responsive nowrap customTable">
                                <thead>  
                                    <tr>
                                        <th style="width: 5%" class="text-center">S#</th>
                                        <th style="width: 15%;" class="text-center">Date</th>
                                        <th style="width: 70%" class="text-left">Consent Form &amp; Doctor</th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>       
                                </thead>
                                <tbody>  
                                    <?php
                                    if(count($patient_consent_form) > 0){ 
                                        $i=1; 
                                        foreach ($patient_consent_form as $pcf) { 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++; ?>.</td>
                                            <td class="text-center"><?php echo date("d M. Y",strtotime($pcf->created_date_time));?></td>
                                            <td class="text-left"><?php echo "<span class='bolo'>".ucwords($pcf->consent_form_title)."</span><br>Dr. ".ucwords($pcf->first_name." ".$pcf->last_name);?></td>
                                            <td class="text-center noBdrRight"><a target="_blank" href="<?php echo base_url() . 'uploads/consentforms/' . $pcf->patient_consent_form;  ?>"><i class="fa fa-print"></i></a></td>
                                        </tr>
                                        <?php 
                                        } 
                                    }else{
                                    ?>
                                    <tr>
                                        <td colspan="4" class="noData">No Consent Forms Found!</td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>