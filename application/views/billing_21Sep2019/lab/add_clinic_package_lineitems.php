<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
			<li><a href="<?= base_url('Lab/lab_packages'); ?>">Packages</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Add Investigation</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">             
		<div class="col-md-12">
			<div class="card">

				<div class="row page-title">
                    <div class="col-md-12">
                        Clinic Lab Package &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Add Investigation
                    </div>
                </div>

				<div class="card-body">
					<div class="row col-md-12" id="adddrug" style="display:none">
						<span style="color: red;font-weight: bold">Investigation Is not Available Please add to Clinic Master</span> <a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="btn btn-primary btn-rounded btn-xs box-shadow"> ADD NEW INVESTIGATION</a>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Search Investigation</label>
								<input type="text" class="form-control" name="search_investigation" placeholder="search by investigation name" id="search_investigation" onclick="clinicInvestigationSearch();" />
								<input type="hidden" id="itemCount_tb" value="">
							</div>
						</div>
					</div>
					<div class="row" id="listdiv">
						<div class="col-md-12">	

			
							<form method="POST" action="<?php echo base_url('Lab/add_clinic_package_lineitems/'.$package_id);?>" role="form" class="form">
								<table id="orderlist" class="table customTable" >
									<thead>
										<tr>						
											<th style="width: 5%" class="text-right">S.No.</th>		
											<th style="width: 35%">Investigation Name</th>
											<th style="width: 15%" class="text-center">Item Code</th>
											<!-- <th style="width: 15%" class="text-center">Short form</th> -->
											<th style="width: 10%" class="text-center">Price</th>
											<th style="width: 10%" class="text-center">Actions</th>															
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
								<div class="col-md-12 text-left"><input type="submit" value="Submit" class="btn btn-success"></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>  
