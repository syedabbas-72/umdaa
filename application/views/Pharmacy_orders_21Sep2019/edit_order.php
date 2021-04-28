<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
          <li class="breadcrumb-item"><a href="#">PHARMACY</a></li>
          <li class="breadcrumb-item active"><a href="#">Edit</a></li>
        </ol>
  </div>
</div>
<section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						
                         
                          <form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_edit');?>" role="form">
                           <table id="orderlist" class="table table-bordered dt-responsive nowrap" >
							<thead>
								<tr>
								
									<th style="width: 250px">Drug Name</th>
									<th style="width: 150px">Batch No</th>
									<th>Quantity</th>
									<th>MRP</th>
									<th>HSN</th>
									<th>Reorder<br> Level</th>
									<th>IGST</th>
									<th>CGST</th>
									<th>SGST</th>
									<th>Discount</th>
									<th>Pack Size</th>
									<th style="width: 150px">Expiry Date</th>															
								</tr>
							</thead>
							<tbody>
							<?php foreach($info as $result){ ?>
							<tr><input class="form-control" type="hidden" name="iid[]" value="<?php echo $result['clinic_pharmacy_inventory_inward_id']; ?>" />
							<input type="hidden" name="drug_id[]" value="<?php echo $result['drug_id']; ?>" />
							<input type="hidden" name="clinic_id[]" value="<?php echo $result['clinic_id']; ?>" />
							<td><input class="form-control" type="text" name="dname[]" value="<?php echo $result['trade_name']; ?>" readonly /></td>
							<td><input class="form-control" type="text" name="batch_no[]" value="<?php echo $result['batch_no']; ?>" style="text-transform:uppercase;" /></td>
							<td><input class="form-control" type="text" name="quantity[]" value="<?php echo $result['quantity']; ?>" /></td>
							<td><input class="form-control" type="text" name="mrp[]" value="<?php echo $result['mrp']; ?>" /></td>
							<td><input class="form-control" type="text" name="hsn[]" value="<?php echo $result['hsn_code']; ?>" /></td>
							<td><input class="form-control" type="text" name="reorder_level[]" value="<?php echo $discount->reorder_level; ?>" /></td>
							<td><input class="form-control" type="text" name="igst[]" value="<?php echo $result['igst']; ?>" /></td>
							<td><input class="form-control" type="text" name="cgst[]" value="<?php echo $result['cgst']; ?>" /></td>
							<td><input class="form-control" type="text" name="sgst[]" value="<?php echo $result['sgst']; ?>" /></td>
							<td><input class="form-control" type="text" name="max_discount_percentage[]" value="<?php echo $discount->max_discount_percentage; ?>" /></td>
							<td><input class="form-control" type="text" name="pack_size[]" value="<?php echo $result['pack_size']; ?>" /></td>
							<td><input class="form-control" type="text" name="expiry_date[]" value="<?php echo date('d/m/Y',strtotime($result['expiry_date'])); ?>" /></td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
						<table id="orderlist1" class="table table-striped dt-responsive nowrap" >
						<tbody>						
						<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
						</tbody>
						</table>						
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>