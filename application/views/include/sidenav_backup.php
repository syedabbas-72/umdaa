
<div class="main-sidebar-nav dark-navigation">
    <div class="nano">
        <div class="nano-content sidebar-nav">

            <ul class="metisMenu nav flex-column" id="menu">
                <li class="nav-heading"><span>MAIN</span></li>
                <li class="nav-item active"><a class="nav-link" href="<?php echo base_url('Dashboard'); ?>"> <i class="fas fa-chart-pie"></i><span class="toggle-none">DASHBOARD</span></a></li>

                <?php if(accessprofile(CASE_SHEET,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('CaseSheet'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">CASE SHEET </span></a>
                </li>
            <?php } ?>
			
            <?php if(accessprofile(DOCTORS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Doctor'); ?>" aria-expanded="false"><i class="fas fa-user-md"></i><span class="toggle-none">DOCTORS </span></a>

                </li>
            <?php } ?>
			<?php if(accessprofile(Clinic_doctor,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Clinic_doctor');?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i><span class="toggle-none">CLINIC-DOCTOR</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Calendar_blocking,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Calendar_blocking');?>" aria-expanded="false"><i class="far fa-calendar-times"></i><span class="toggle-none">CALENDAR-BLOCKING</span></a>
                </li>
            <?php } ?>
            <?php if(accessprofile(APPOINTMENTS,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Appointment');?>" aria-expanded="false"><i class="fas fa-calendar-check"></i><span class="toggle-none">APPOINTMENTS</span></a>
                </li>
            <?php } ?>
             <?php if(accessprofile(INVESTIGATIONS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('prescription');?>" aria-expanded="false"><i class="fas fa-prescription"></i><span class="toggle-none">PRESCRIPTION</span></a>
                </li>
            <?php } ?>
                             <!--<li class="nav-item">
                    <a class="nav-link" href="#" aria-expanded="false"><img src="<?php echo base_url('assets/img/Queres.png'); ?>"> &nbsp; <span class="toggle-none">QUERIES </span></a>

                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Umdaa_controller/registrations'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Registration.png'); ?>"> &nbsp;<span class="toggle-none">REGISTRATIONS</span></a>

                </li>-->
                <?php if(accessprofile(PHARMACY,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Pharmacy_orders'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Pharma.png'); ?>"> &nbsp; <span class="toggle-none">PHARMACY</span></a>

                </li>
            <?php } ?>
               <!--  <li class="nav-item">
                    <a class="nav-link" href="javascript: void(0);" aria-expanded="false"><img src="<?php echo base_url('assets/img/Connect.png'); ?>"> &nbsp; <span class="toggle-none">CONNECT</span></a>

                </li> -->
                <?php /*if(accessprofile(PatientsVital,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('PatientsVital'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Connect.png'); ?>"> &nbsp; <span class="toggle-none">PATIENT VITAL</span></a>

                </li>
            <?php }*/ ?>

               <!--  <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Notification'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Connect.png'); ?>"> &nbsp; <span class="toggle-none">NOTIFICATION</span></a>

                </li> -->
                <!------------------------------- Links for Pharmacy ------------------------------------>
                <?php if(accessprofile(INVENTORY,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('New_order');?>" aria-expanded="false"><i class="fas fa-external-link-alt"></i><span class="toggle-none">NEW ORDER </span></a>
                </li>
            <?php } ?>

                <?php if(accessprofile(INVENTORY,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Inventory'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Medical Procedures.png'); ?>"> &nbsp; <span class="toggle-none">INVENTORY</span></a>
                </li>
                <?php } ?>
                <?php if(accessprofile(Billing,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Billing');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Roles.png'); ?>"> &nbsp;<span class="toggle-none">BILLING</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(INVENTORY,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Indent'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Doctor.png'); ?>"> &nbsp; <span class="toggle-none">INDENT  </span></a>
                </li>
				<?php } ?>
				<?php if(accessprofile(PATIENTS,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Patients'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">PATIENTS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(MEDICAL_HISTORY,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Medical_History'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">MEDICAL HISTORY </span></a>
                </li>
            <?php } ?>			
			<?php if(accessprofile(GPE,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Gpe'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">GPE</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(SYSTOMIC_EXAMINATION,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Se'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">SE </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(OS,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Os'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">OS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(MP,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Mp'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">MP </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(FINAL_IMPRESSION,P_READ)){ ?> 
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Final_Impression'); ?>" aria-expanded="false"><i class="fas fa-user-injured"></i><span class="toggle-none">FINAL IMPRESSION </span></a>
                </li>
            <?php } ?>
			<!-- ------------------------------------------------------------------------------------------------------------ -->











<?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-heading"><span><u class="under_line">MASTERS</u></span></li>	
<?php } ?> 				
                 <?php /*if(accessprofile(CLINICTYPE,P_READ)){ ?> 
				<li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Clinic/clinic_type'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">CLINIC TYPE</span></a>
                </li>
                <?php }*/ ?>
				<?php if(accessprofile(CLINICS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Clinic'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">CLINICS</span></a>
                </li>
                <?php } ?> 
                <?php if(accessprofile(Drug,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Drug'); ?>" aria-expanded="false"><i class="fas fa-pills"></i><span class="toggle-none">DRUG</span></a>
                </li>
            <?php } ?>
           
            
			
			
              
            <?php if(accessprofile(EMPLOYEE,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Employee/employee_list'); ?>" aria-expanded="false"><i class="fas fa-user-tie"></i><span class="toggle-none">EMPLOYEES </span></a>
                </li>
            <?php } ?>
             <?php if(accessprofile(PHARMACIES,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Pharmacy'); ?>" aria-expanded="false"><i class="fas fa-plus-square"></i><span class="toggle-none">PHARMACIES</span></a>

                </li>
            <?php } ?>
            <?php if(accessprofile(INVESTIGATIONS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Investigation'); ?>" aria-expanded="false"><i class="fas fa-vial"></i><span class="toggle-none">INVESTIGATIONS </span></a>
                </li>
            <?php } ?>
             <?php if(accessprofile(Procedure,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Procedure'); ?>" aria-expanded="false"><i class="fas fa-notes-medical"></i><span class="toggle-none">MEDICAL PROCEDURES</span></a>
                </li>
                <?php } ?>
                 
                <?php if(accessprofile(DOCTORS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url("Consentform");?>" aria-expanded="false"><i class="fab fa-wpforms"></i><span class="toggle-none">CONSENT FORMS </span></a>

                </li>
                <?php } ?>
                 <?php if(accessprofile(Department,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>Department" aria-expanded="false"><i class="fas fa-project-diagram"></i><span class="toggle-none">DEPARTMENTS </span></a>

                </li>
            <?php } ?>
			<?php if(accessprofile(NURSE,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>Department" aria-expanded="false"><i class="fas fa-project-diagram"></i><span class="toggle-none">NURSES </span></a>

                </li>
            <?php } ?>
               <!--  <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('PatientsVital/vital_masters');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Vitals.png'); ?>"> &nbsp; <span class="toggle-none">VITALS MASTER</span></a>
                </li>  -->              
                <!-- <!-- <li class="nav-item">
                    <a class="nav-link" href="javascript: void(0);" aria-expanded="false"><img src="<?php echo base_url('assets/img/Diseases.png'); ?>"> &nbsp;<span class="toggle-none">DISEASES </span></a>

                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url("parameters");?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Diseases.png'); ?>"> &nbsp;<span class="toggle-none">PARAMETERS </span></a>

                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url("Followup_templates");?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Diseases.png'); ?>"> &nbsp;<span class="toggle-none">FOLLOW-UP</span></a>

                </li> -->
                 <?php if(accessprofile(FormBuilder,P_READ)){ ?> 
				<li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('FormBuilder'); ?>" aria-expanded="false"><i class="fas fa-layer-group"></i><span class="toggle-none">FORM BUILDER</span></a>

                </li>
            <?php } ?>

            <!----------------------------- Pharmacy Masters ------------------------------->
              <?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('drug');?>" aria-expanded="false"><i class="fas fa-prescription-bottle-alt"></i><span class="toggle-none">DRUG MASTERS</span></a>
                </li>
			  <?php } ?>

             <?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Salt');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/User Controls.png'); ?>"> &nbsp; <span class="toggle-none">SALTS</span></a>
                </li>
			<?php } ?>
             <?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/User Controls.png'); ?>"> &nbsp; <span class="toggle-none">SALTS CONTRAINDICATIONS</span></a>
                </li>
			<?php } ?>
        

<!-- -------------------------------------------------------------------------------------------------------------- -->

<?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-heading"><span><u class="under_line">ADMINISTRATION</u></span></li>
              <?php } ?>  
                 
             <?php if(accessprofile(Users,P_READ)){ ?> 
				<li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Users');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/User Controls.png'); ?>"> &nbsp; <span class="toggle-none">USERS</span></a>
                </li>
            <?php } ?>

             
            <?php if(accessprofile(ROLES,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Admin/roles');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Roles.png'); ?>"> &nbsp;<span class="toggle-none">ROLES </span></a>

                </li>
            <?php } ?>
            <?php if(accessprofile(profiles,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Admin/profiles');?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Profiles.png'); ?>"> &nbsp; <span class="toggle-none">PROFILES</span></a>

                </li>
            <?php } ?>
                <!-- <li class="nav-item">
                    <a class="nav-link"  href="javascript: void(0);" aria-expanded="false"><img src="<?php echo base_url('assets/img/User Controls.png'); ?>"> &nbsp; <span class="toggle-none">USER CONTROLS</span></a>
                </li> -->
                <?php if(accessprofile(SPA,P_READ)){ ?>
				<li class="nav-item">
                    <a class="nav-link"  href="#" aria-expanded="false"><img src="<?php echo base_url('assets/img/Profiles.png'); ?>"> &nbsp; <span class="toggle-none">SPA</span></a>
                </li>
            <?php } ?>
                <li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Authentication/logout'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Profiles.png'); ?>"> &nbsp; <span class="toggle-none">LOGOUT</span></a>
                </li>
                
            </ul>

        </div>
    </div>
</div>

