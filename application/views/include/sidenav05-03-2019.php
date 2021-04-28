<div class="main-sidebar-nav dark-navigation">
    <div class="nano">
        <div class="nano-content sidebar-nav">
			<ul class="metisMenu nav flex-column" id="menu">
				<li class="nav-heading"><span>MAIN</span></li>
                <li class="nav-item active"><a class="nav-link" href="<?php echo base_url('Dashboard'); ?>"> <i class="fas fa-chart-pie"></i><span class="toggle-none">DASHBOARD</span></a></li>
				
				<?php if(accessprofile(CLINICS,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Clinic'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">CLINICS</span></a>
                </li>
                <?php } ?> 
				
				<?php if(accessprofile(Doctors,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Doctor'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">DOCTORS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Patients,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Patients'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">PATIENTS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Appointments,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Appointment'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">APPOINTMENTS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Pharmacy,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Pharmacy_orders'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">PHARMACY </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Prescriptions,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Prescription'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">PRESCRIPTIONS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Orders,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Orders'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">ORDERS </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Inventory,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Inventory'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">INVENTORY </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Indent,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Indent'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">INDENT </span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Pharmacy_Billing,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Pharmacy_Billing'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">PHARMACY BILLING</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Lab,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Lab'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">LAB</span></a>
                </li>
            <?php } ?>
			
			<?php if(accessprofile(Lab_Investigations,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Lab_Investigations'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">LAB INVESTIGATIONS</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Lab_Billing,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Lab_Billing'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">LAB BILLING</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Billing,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Billing'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">BILLING</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Queries,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Queries'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">QUERIES</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Registrations,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Patients'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">REGISTRATIONS</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Nurses,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Nurses'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">NURSES</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Calendar,P_READ)) { ?>
                 <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Calendar_blocking'); ?>" aria-expanded="false"><i class="fas fa-file-medical-alt"></i><span class="toggle-none">CALENDAR</span></a>
                </li>
            <?php } ?>
			<?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-heading"><span><u class="under_line">MASTERS</u></span></li>	
			<?php } ?> 
			<?php if(accessprofile(Medical_Procedures,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('MedicalProcedures'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">MEDICAL PROCEDURES</span></a>
                </li>
                <?php } ?> 
			<?php if(accessprofile(Investigations,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Investigation'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">INVESTIGATIONS</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Drugs,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Drug'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">DRUGS</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Salts,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Salt'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">SALTS</span></a>
                </li>
                <?php } ?>
			
			<?php if(accessprofile(Salt_Contraindications,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Salt'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">SALT CONTRAINDICATIONS</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Departments,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Department'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">DEPARTMENTS</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Employee,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Employee'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">EMPLOYEE</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-heading"><span><u class="under_line">ADMINISTRATION</u></span></li>
              <?php } ?> 
			<?php if(accessprofile(Users,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Users'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">USERS</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Roles,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Admin/roles'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">ROLES</span></a>
                </li>
                <?php } ?>
			<?php if(accessprofile(Profiles,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Admin/profiles'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">PROFILES</span></a>
                </li>
			<?php } ?>
			
			 <?php if(accessprofile(FormBuilder,P_READ)){ ?> 
				<li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('FormBuilder'); ?>" aria-expanded="false"><i class="fas fa-layer-group"></i><span class="toggle-none">FORM BUILDER</span></a>

                </li>
            <?php } ?>
			
			<?php if(accessprofile(User_Access_Control,P_READ)){ ?> 
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('Admin/User_Access_Control'); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none">USER ACCESS CONTROL</span></a>
                </li>
                <?php } ?>
			<li class="nav-item">
                    <a class="nav-link"  href="<?php echo base_url('Authentication/logout'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Profiles.png'); ?>"> &nbsp; <span class="toggle-none">LOGOUT</span></a>
                </li>
			</ul>
		</div>
	</div>
</div>