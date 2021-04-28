<!-- FullCalendar -->
        <link href='<?= base_url() ?>assets/lib/fullcalendar/fullcalendar.css' rel="stylesheet"> 
<section class="main-content">
<div class="row">
       <div class="col-md-12">
       <div class="card">
      
        <div class="card-header">
            Calendar
            </div>
        <div class="card-body">
          <div class="row">
      
                <div class="col-md-12">
            <div id="fc-external"></div>
                </div>
        </div>
        </div>
       </div>
            </div>
      </div>
    </section>
    <!-- Full Calendar -->
        <script src="<?= base_url() ?>assets/lib/jquery-ui/jquery-ui.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/momentJs/moment.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/fullcalendar/fullcalendar.min.js"></script>
        <script src="<?= base_url() ?>assets/js/fullcalendar.js"></script>

    <script type="text/javascript">
      $('#fc-external').fullCalendar({
    events:<?php echo json_encode($json); ?> ,
     eventClick:function(event)
    {
        
     if(confirm("Are you sure you want to remove it?"))
     {
      var id = event.id;
      var base_url = '<?php echo base_url(); ?>';
      $.ajax({
       url:base_url+"clinic_doctor/delete",
       type:"POST",
       data:{id:id},
       success:function()
       {
        $('#fc-external').fullCalendar('rerenderEvents');
        $('#fc-external').fullCalendar('refetchEvents');
        alert("Event Removed");
        window.location.href="<?php echo base_url('clinic_doctor'); ?>";
       }
      })
     }
    },
});
    </script>