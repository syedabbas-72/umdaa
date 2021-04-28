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
            <li class="active">Summary</li>
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
                                Summary
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding-right: 0px">
                            <table class="table table-bordered customTable">
                                <thead>  
                                    <tr>
                                        <th class="text-center" style="width: 5%">#</th>
                                        <th style="width: 85%" class="text-left">Doctor &amp; Date</th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>       
                                </thead>
                                <tbody>  
                                    <?php $i=1; 
                                    if(count($appointments)>0)
                                    {
                                        foreach ($appointments as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center"><?php echo $i++; ?>.</td>
                                                <td class="text-left"><span><?php echo "Dr. ".ucwords($value->first_name." ".$value->last_name);?></span><br><?php echo date("d M. Y",strtotime($value->appointment_date));?></td>
                                                <td class="text-center noBdrRight"><a target="_blank" href="<?php echo base_url("SummaryReports/shortSummary/".$value->appointment_id); ?>"><i class="fa fa-print"></i></a></td>
                                            </tr>
                                        <?php }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr>
                                            <td colspan="3" class="text-center font-weight-bold">No Data Found.</td>
                                        </tr>
                                        <?php   
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function(){
        $('#doctorlist').dataTable();
    });
</script>