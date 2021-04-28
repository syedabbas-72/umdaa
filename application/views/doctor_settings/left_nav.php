 <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link" id="doctor_info" href="<?php echo base_url('doctor_settings/doctor_info/'.$this->uri->segment(3).'/'.$this->uri->segment(4)); ?>" >Consultation</a>
                <a class="nav-link" id="doctor_timings" href="<?php echo base_url('doctor_settings/doctor_timings/'.$this->uri->segment(3).'/'.$this->uri->segment(4)); ?>" >Visit Timings</a>
                <a class="nav-link" id="block_dates" href="<?php echo base_url('doctor_settings/block_dates/'.$this->uri->segment(3).'/'.$this->uri->segment(4)); ?>" >Calendar blocking</a>
                <?php if($type==0){ ?>

                 <a class="nav-link" id="user" href="<?php echo base_url('doctor_settings/user/'.$this->uri->segment(3).'/'.$this->uri->segment(4)); ?>" >Front Office</a>
               <?php  } ?>

</div>

                  <script type="text/javascript">
                  	$(function() {
                  		var link = '<?php echo  $this->uri->segment(2)==''?'doctor_settings': $this->uri->segment(2); ?>';
                  	
   $("a#"+link).addClass('active');
});
                  </script>