 <a class="nav-link list-group-item list-group-item-action" id="doctor_info" href="<?php echo base_url('settings/doctor_info/'.$this->uri->segment(3)); ?>" >Profile Info</a>
            <a class="nav-link list-group-item list-group-item-action" id="doctor_timings" href="<?php echo base_url('settings/doctor_timings/'.$this->uri->segment(3)); ?>" >Visit Timings</a>
            <a class="nav-link list-group-item list-group-item-action" id="block_dates" href="<?php echo base_url('settings/block_dates/'.$this->uri->segment(3)); ?>" >Calendar Blocking</a>
            <a class="nav-link list-group-item list-group-item-action" id="doctor_work" href="<?php echo base_url('settings/doctor_work/'.$this->uri->segment(3)); ?>" >Work Info</a>  
                   

                  <script type="text/javascript">
                  	$(function() {
                  		var link = '<?php echo  $this->uri->segment(2)==''?'settings': $this->uri->segment(2); ?>';
                  	
   $("a#"+link).addClass('active');
});
                  </script>