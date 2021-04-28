<style type="text/css">
.leftNav li a{
    border-radius: 0px !important;
    border-bottom:1px solid #ebebeb;
    color: #25313e;
    font-size: 16px !important;
    font-weight: 400;
    padding: 12px 20px;
}    
</style>

<div class="row page-title">
    <div class="col-md-12">
        Menu
    </div>
</div>
<ul class="nav nav-pills flex-column leftNav" style="padding-left: 0px !important">
    <li>
        <a id="profile" class="nav-link nav-item" href="<?php echo base_url('profile/index/'.$patient_id.'/'.$appointment_id); ?>">Profile</a>
    </li>
    <?php /*
    // Payment menu item disabled permanently for better user experience
    // Author: Uday Kanth Rapalli
    // Dt: 05July2019 -- 0410pm
    <!-- 
        // If any payments pending then only this menu will get showed up
     -->
    <a id="patients" class="nav-link nav-item" href="<?php echo base_url('patients/confirm_payment/'.$patient_id); ?>">Payment</a>
    */?>
    <li>
        <a id="Vitals" class="nav-link nav-item" href="<?php echo base_url('Vitals/index/'.$patient_id.'/'.$appointment_id); ?>">Vitals</a>
    </li>
    <?php /*
    <!-- <a id="procedure" class="nav-link nav-item" href="<?php echo base_url('Billing/procedures/'.$patient_id); ?>">Procedures</a> -->
    <!-- <a id="prescription" class="nav-link nav-item" href="<?php echo base_url('prescription/index/'.$patient_id); ?>">Prescription</a> -->
    */ ?>
    <!-- <li>
        <a id="Billing" class="nav-link nav-item" href="<?php echo base_url('Billing/patient_invoice/'.$patient_id.'/'.$appointment_id); ?>">Invoices</a>
    </li> -->
    <li>
        <a id="consentform" class="nav-link nav-item" href="<?php echo base_url('consentform/reports/'.$patient_id.'/'.$appointment_id); ?>">Consent Form</a>
    </li>
    <?php
    if($appointment_id != '' || $appointment_id != NULL){
        $status = array('checked_in','vital_signs','waiting','in_consultation','closed');    
        ?>
        <li>
            <a id="OtherPayments" class="nav-link nav-item" href="<?php echo base_url('patients/confirm_payment/'.$patient_id.'/'.$appointment_id); ?>">Other Payments</a>
        </li>    
        <?php
    }    
    ?>
    <li>
        <a id="SummaryReports" class="nav-link nav-item" href="<?php echo base_url('SummaryReports/index/'.$patient_id.'/'.$appointment_id); ?>">Summary</a>
    </li>
</ul>

<script type="text/javascript">
    $(function() {
        var method = '<?php echo  $this->uri->segment(2); ?>';
        console.log(method);
        if(method == 'procedures'){
            $("a#procedure").addClass('active');
        }
        else{
            var link = '<?php echo  $this->uri->segment(1)==''?'profile': $this->uri->segment(1); ?>';
            $("a#"+link).addClass('active');
        }
    });
</script>