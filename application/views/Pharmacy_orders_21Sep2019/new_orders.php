<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>
			<li>Pharmacy&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Inventory&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Add New Drug</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="row page-title">
					<div class="col-md-12">
						Pharmacy Inventory &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Add New Drug
					</div>
				</div>
				<div class="row mainContent">
					<div class="col-md-12">	
						<div id="adddrug" style="display:none">
							<span style="color: red;font-weight: bold">Drug Is not Available Please add to master</span> <a href="<?= base_url('Pharmacy_orders/drug_add'); ?>" class="btn btn-primary btn-rounded btn-xs box-shadow"> ADD NEW DRUG</a>
						</div>
						<div class="row customForm">
							<div class="col-md-12">
	                            <div class="form-group">
	                            	<label for="search_pharmacy" class="col-form-label">Search drug using Trade Name (Eg. Dolo 650) <span class="imp">*</span></label>
	                            	<input type="text" class="form-control" style="text-transform: capitalize;" name="search_pharmacy" placeholder="Search by Trade Name" id="search_pharmacy" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
	                            </div>
	                        </div>
	                    </div>
	                    <?php /*
						<div class="row">
							<div class="col-md-11">
								<input type="text" class="form-control" name="search_pharmacy" placeholder="Search by Trade Name" id="search_pharmacy" onkeyup="searchDrug(this.value);" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
							</div>
						</div>
						*/ ?>
						<form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_add');?>" role="form" class="form customForm">
							<div class="table-responsive">
								<table id="orderlist" class="table table-bordered dt-responsive nowrap" >
									<thead>
										<tr>								
											<th>Drug Name</th>
											<th>Batch No</th>
											<th>QTY</th>
											<th>MRP</th>
											<th>R-ord<br> Lvl</th>
											<th>IGST</th>
											<th>CGST</th>
											<th>SGST</th>
											<th>Disc</th>
											<th>Pack Size</th>
											<th>Expiry Date</th>	
											<th>&nbsp;</th>														
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<table id="orderlist1" class="table table-striped dt-responsive nowrap" style="display:none">
								<tbody>						
									<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
								</tbody>
							</table>						
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>