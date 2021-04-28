<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li><a class="parent-item" href="<?php echo base_url("lab/lab_investigations"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
						Lab &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; New Investigation
					</div>
				</div>

				<div class="card-body">

					<div class="row caution" id="adddrug" style="display: none">
						<div class="col-md-12">
							<p>Investigation you are trying to search is not available. You can add it to the master by clicking on below...</p>
							<a href="<?= base_url('Investigation/investigation_add'); ?>" class="customBtn">ADD NEW INVESTIGATION</a>
						</div>
					</div>

					<div class="row caution" id="alreadyadded" style="display: none">
						<div class="col-md-12 error">
							<i class="fas fa-exclamation-triangle"></i> Investigation already added!
						</div>	
					</div>			

					<div class="row caution" id="exist_div" style="display: none">
						<div class="col-md-12 exist" id="exist_div">
							<i class="fas fa-exclamation-triangle"></i> <span id="exist_span"></span>
						</div>	
					</div>						

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="title" class="col-form-label">Search by Investigation Name</label>
								<input type="text" class="form-control" name="search_investigation" placeholder="Enter your search" id="search_investigation" onclick="investigationMasterSearch();" />
							</div>
						</div>
					</div>

					<form method="POST" action="<?php echo base_url('Lab/add_clinic_investigation');?>" role="form" class="form">
						<div class="row">
							<div class="col-md-12">
								<table cellspacing="0" cellpadding="0" class="customTable">
									<tr>
										<th>
											List of investigations	
										</th>
									</tr>
									<tr id="noData_tr">
										<td class="noData">
											No investigations were added so far...
										</td>
									</tr>
									<tr>
										<td class="data_list" id="investigation_list" style="padding-top: 0px !important;">
											<table cellpadding="0" cellspacing="0" class="table customTable">
												<tr>
													<th>S.No.</th>
													<th>Investigation & Method</th>
													<th>Short Form</th>
													<th>Low</th>
													<th>High</th>
													<th>Units</th>
													<th>Other Information</th>
													<th>Actions</th>
												</tr>
												<tr>
													<td>1.</td>
													<td><span>Complete Blood Picture</span><br><small><i>BioChemist</i></small></td>
													<td>CBP</td>
													<td>32</td>
													<td>56</td>
													<td>mg/dL</td>
													<td><b>Male:</b> 32 - 48<br><b>Female:</b> 38 - 56 mg/dl</td>
													<td></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr id="submit_tr" style="display: none;">
										<td>
											<input type="submit" value="Submit" class="btn btn-success">
										</td>
									</tr>
								</table>	
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>