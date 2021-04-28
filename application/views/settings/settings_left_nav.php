<a class="nav-link list-group-item list-group-item-action" id="settings" href="<?php echo base_url('settings'); ?>" >Clinic Profile</a>
                <a class="nav-link list-group-item list-group-item-action" id="staff" href="<?php echo base_url('settings/staff'); ?>" >Staff</a>
                <a class="nav-link list-group-item list-group-item-action" id="communication" href="<?php echo base_url('settings/communication'); ?>" >Communications</a>
                 <a class="nav-link list-group-item list-group-item-action" id="procedures" href="<?php echo base_url('settings/procedures'); ?>" >Procedures</a>
                  <a class="nav-link list-group-item list-group-item-action" id="referral_doctors" href="<?php echo base_url('settings/referral_doctors'); ?>" >Referral Doctors</a>
                  <a class="nav-link list-group-item list-group-item-action" id="gallery" href="<?php echo base_url('settings/gallery'); ?>" >Gallery</a>
                   <a class="nav-link list-group-item list-group-item-action" id="print" href="<?php echo base_url('settings/print'); ?>" >Print Settings</a>
                   

                  <script type="text/javascript">
                  	$(function() {
                  		var link = '<?php echo  $this->uri->segment(2)==''?'settings': $this->uri->segment(2); ?>';
                  	
   $("a#"+link).addClass('active');
});
                  </script>