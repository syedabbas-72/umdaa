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
	                            	<input type="text" class="form-control" style="text-transform: capitalize;" name="search_pharmacy" placeholder="Loading Drugs... Please wait!" disabled="disabled" id="search_pharmacy" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
	                            </div>
	                        </div>
	                    </div>
	                    <div id="testDiv"></div>
						<?php 
						$clinic_id = $this->session->userdata("clinic_id");
						$vendor_list = $this->db->query("select * from vendor_master where clinic_id='".$clinic_id."'")->result();
						 ?>
						<form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_add');?>" role="form" class="form customForm">
							<div class="row col-md-12 " style="margin: 0px !important;padding: 0px !important">
								<div class="form-group">
									<table id="orderlist" class="table table-bordered dt-responsive nowrap" >
									<tbody>
										
									</tbody>
								</table>
								</div>
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
