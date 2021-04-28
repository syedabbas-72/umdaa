<div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">INDENT</li>
      </ol>
  </div>
</div>


<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						<div class="row">
							<div class="col-md-12">
						<span>Search Drug By Trade Name</span><input type="text" class="form-control" name="search_pharmacy" id="search_pharmacy" onclick="tnamesearch();" />&nbsp;&nbsp;&nbsp;<span class="pluse">  <i class="fa fa-plus-circle" aria-hidden="true" onclick="add_indent_row('orderlist');"></i></span>
						
					</div>
						</div>
						<form method="POST" action="<?php echo base_url('Indent/indent_add');?>" role="form">
						<table id="orderlist" class="table table-bordered dt-responsive nowrap" >
						<thead>
						<tr>
							<th>S.No:</th>
							<th>Drug Name</th>							
							<th>Quantity</th>																					
						</tr>
						</thead>
						<tbody>
						</tbody>
						</table>
						<table id="orderlist1" class="table table-bordered dt-responsive nowrap" >
						<tbody>						
						<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
						</tbody>
						</table>						
						</form>
						</div>
					</div>
				</div>
</div>
