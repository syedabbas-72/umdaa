
<style type="text/css">
 
  .padded_table td, .padded_table th {
    padding: 6px;
}
.toppadding_10 {
    padding-top: 10px;
}
.allcaps {
    text-transform: uppercase;
}
.boldtext {
    font-weight: bold;

}
.allpadding_5 {
    padding: 5px;
}
input[type="radio"], input[type="checkbox"] {
    line-height: normal;
    margin: 4px;
}
.settingsInfoText {
    padding-left: 25px;
}

</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">COMMUNICATIONS</li>
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
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="practice_details">
<div class="pr_form pr_tabbar_content">
   
       <form method="POST" action="<?php echo base_url('settings/sms_settings_insert');?>" enctype="multipart/form-data" role="form">
     <div class="card-header card-default">
                <h3 class="text-capitalize" style="margin: 0">SMS SETTINGS</h3>
                <hr class="clearfix" style="margin: 0;border: none;">
                </div>
    
    <div data-smstype="confirmation" class="toppadding_10 sms_field_container" ng-show="smsConfig.appointment_confirmation.display">
      <?php if(count($sms_settings)>0){ 
         
       
        ?>
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="confirmationSMS" id="confirmationSMS" value="0">

            <input type="checkbox" name="confirmationSMS" id="confirmationSMS" value="1" <?php if($sms_settings->appointment_sms == 1){ echo "checked"; } ?>> Appointment Confirmation SMS
        </p>
        <p class="settingsInfoText ng-binding">
            SMS is sent to the Patient on successfully adding an appointment
        </p>
    
        <div class="clearboth"></div>
    </div>



    <div data-smstype="reminder" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="reminderSMS" id="reminderSMS" value="0">
            <input type="checkbox" name="reminderSMS" id="reminderSMS" value="1" <?php if($sms_settings->reminder_sms == 1){ echo "checked"; } ?>> Appointment Reminder SMS
        </p>
        <p class="settingsInfoText">
            This SMS is automatically sent to the Patient at selected time &amp; date before the appointment.
        </p>
      
        <div class="clearboth"></div>
    </div>
    <div data-smstype="followup" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="followupSMS" id="followupSMS" value="0">
            <input type="checkbox" name="followupSMS" id="followupSMS" value="1" <?php if($sms_settings->followup_sms == 1){ echo "checked"; } ?>> Follow-up Reminder SMS
        </p>
        <p class="settingsInfoText ng-binding">
            This SMS is sent to the Patient on the morning of the followup sms.
        </p>
       
        <div class="clearboth"></div>
    </div>
    
  <div class="pr_form pr_tabbar_content">
   
       
     <div class="card-header card-default">
                <h3 class="text-capitalize" style="margin: 0">EMAIL SETTINGS</h3>
                <hr class="clearfix" style="margin: 0;border: none;">
                </div>
    
    <div data-smstype="confirmation" class="toppadding_10 sms_field_container" ng-show="smsConfig.appointment_confirmation.display">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="confirmationEmail" id="confirmationEmail" value="0">
            <input type="checkbox" name="confirmationEmail" id="confirmationEmail" value="1" <?php if($sms_settings->appointment_email == 1){ echo "checked"; } ?>> Appointment Confirmation Email
        </p>
        <p class="settingsInfoText ng-binding">
            Email is sent to the Patient on successfully adding an appointment
        </p>
    
        <div class="clearboth"></div>
    </div>



    <div data-smstype="reminder" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="reminderEmail" id="reminderEmail" value="0">
            <input type="checkbox" name="reminderEmail" id="reminderEmail" value="1" <?php if($sms_settings->reminder_email == 1){ echo "checked"; } ?>> Appointment Reminder Email
        </p>
        <p class="settingsInfoText">
            Email is sent to the Patient on the morning of the appointment date
        </p>
      
        <div class="clearboth"></div>
    </div>
    <div data-smstype="followup" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="followupEmail" id="followupEmail" value="0">
            <input type="checkbox" name="followupEmail" id="followupEmail" value="1" <?php if($sms_settings->followup_email == 1){ echo "checked"; } ?>> Follow-up Reminder Email
        </p>
        <p class="settingsInfoText ng-binding">
           Email is sent to the Patient on the morning of their planned follow-up date
        </p>
       
        <div class="clearboth"></div>
    </div>
   
</div>
<?php } else { ?>



    <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="confirmationSMS" id="confirmationSMS" value="0">
            <input type="checkbox" name="confirmationSMS" id="confirmationSMS" value="1"> Appointment Confirmation SMS
        </p>
        <p class="settingsInfoText ng-binding">
            SMS is sent to the Patient on successfully adding an appointment
        </p>
    
        <div class="clearboth"></div>
    </div>



    <div data-smstype="reminder" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="reminderSMS" id="reminderSMS" value="0">
            <input type="checkbox" name="reminderSMS" id="reminderSMS" value="1"> Appointment Reminder SMS
        </p>
        <p class="settingsInfoText">
            This SMS is automatically sent to the Patient at selected time &amp; date before the appointment.
        </p>
      
        <div class="clearboth"></div>
    </div>
    <div data-smstype="followup" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="followupSMS" id="followupSMS" value="0">
            <input type="checkbox" name="followupSMS" id="followupSMS" value="1"> Follow-up Reminder SMS
        </p>
        <p class="settingsInfoText ng-binding">
            This SMS is sent to the Patient on the morning of the followup sms.
        </p>
       
        <div class="clearboth"></div>
    </div>
    
  <div class="pr_form pr_tabbar_content">
   
       
     <div class="card-header card-default">
                <h3 class="text-capitalize">EMAIL SETTINGS</h3>
                <hr class="clearfix">
                </div>
    
    <div data-smstype="confirmation" class="toppadding_10 sms_field_container" ng-show="smsConfig.appointment_confirmation.display">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="confirmationEmail" id="confirmationEmail" value="0">
            <input type="checkbox" name="confirmationEmail" id="confirmationEmail" value="0"> Appointment Confirmation Email
        </p>
        <p class="settingsInfoText ng-binding">
            Email is sent to the Patient on successfully adding an appointment
        </p>
    
        <div class="clearboth"></div>
    </div>



    <div data-smstype="reminder" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="reminderEmail" id="reminderEmail" value="0">
            <input type="checkbox" name="reminderEmail" id="reminderEmail" value="1"> Appointment Reminder Email
        </p>
        <p class="settingsInfoText">
            Email is sent to the Patient on the morning of the appointment date
        </p>
      
        <div class="clearboth"></div>
    </div>
    <div data-smstype="followup" class="clearboth topbottompadding_10 sms_field_container">
        <p class="allpadding_5 allcaps boldtext">
          <input type="hidden" name="followupEmail" id="followupEmail" value="0">
            <input type="checkbox" name="followupEmail" id="followupEmail" value="1"> Follow-up Reminder Email
        </p>
        <p class="settingsInfoText ng-binding">
           Email is sent to the Patient on the morning of their planned follow-up date
        </p>
       
        <div class="clearboth"></div>
    </div>
   
</div>





  <?php } ?>
<div class="col-sm-6">
                                  <div class="pull-right">
                                      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                  </div>
                                </div>

</form>

                                </div>

                                
                                 </div>
                                    

                                </div>
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
                                               

                         
       
<script>
        $('.edit-row').click(function () {
            var id = $(this).data('row-id');
            var url = 'http://appointo.froid.works/account/booking-times/:id/edit';
            url = url.replace(':id', id);

            $('#modelHeading').html('Edit Booking Times');
            $.ajaxModal('#application-modal', url);
        });

   $.ajaxModal = function (selector, url, onLoad) {

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal').load(url);

        // Trigger to do stuff with form loaded in modal
        $(document).trigger("ajaxPageLoad");

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).find('.modal-footer').html('<button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>');
            $(this).data('bs.modal', null);
        });
    };
    </script>