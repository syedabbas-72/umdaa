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

<div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#">HOME</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li> 
          <li><a class="parent-item" href="#">PHARMACY</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">ADD</li>
      </ol>
  </div>
</div>


            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						<div class="row col-md-12" id="adddrug" style="display:none">
						<span style="color: red;font-weight: bold">Drug Is not Available Please add to master<span> <a href="<?= base_url('Pharmacy_orders/drug_add'); ?>" class="btn btn-primary btn-rounded btn-xs box-shadow"> ADD NEW DRUG</a>
						</div>
                         <div class="row">
                         	<div class="col-md-11">
						<!-- <input type="text" class="form-control" name="search_pharmacy" placeholder="search by trade name" id="search_pharmacy" onclick="tnamesearch_inventory();" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span> -->
						<input type="text" class="form-control" name="search_pharmacy" placeholder="search by trade name" id="search_pharmacy" onkeyup="searchDrug(this.value);" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
					</div>
					<!-- <div class="col-md-1 buttonAction">
				    	<button type="button" class="btn btn-success"  onclick="add_inventory_row('orderlist');"><i class="fas fa-plus"></i></button>
				    </div> -->
						</div>
                          <form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_add');?>" role="form">
                          	<div class="table-responsive">
                           <table id="orderlist" class="table table-bordered dt-responsive nowrap" >
							<thead>
								<tr>								
									<th style="width: 250px">Drug Name</th>
									<th style="width: 150px">Batch No</th>
									<th>QTY</th>
									<th style="width: 100px">MRP</th>
									<th style="width: 80px">R-ord<br> Lvl</th>
									<th>IGST</th>
									<th>CGST</th>
									<th>SGST</th>
									<th>Disc</th>
									<th>Pack Size</th>
									<th style="width: 150px">Expiry Date</th>															
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
       