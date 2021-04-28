<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/investigations');
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab Investigations&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Setup</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Clinic Lab Investigations Setup</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->				        

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">							
							<div class="row">
								<div class="col-md-12">
									<div class="setup">
										<h2>Setup using Departments</h2>
										<p>All lab departments will be listed. For setting up the list of investigations your clinic may perform, check the departments and then select the respectoive investigation list and do setup</p>
										<a href="<?= base_url('Lab/departments'); ?>" class="customBtn">Use Departments</a>
									</div>
								</div>
								<?php /*
								<div class="col-md-12">
									<div class="setup">
										<h2>Setup using search box</h2>
										<p>This page will be provided with a search box, where you can simply type in the investigation with its name and select the appropriate investigation and then add it to your clinic lab.</p>
										<a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="customBtn">Use Search box</a>
									</div>
								</div>
								*/ ?>
							</div>							
						</div>                        
					</div>
				</div>
			</div>
		</div>
	</div>
</section>