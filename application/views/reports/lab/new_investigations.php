<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/departments');
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Add Investigations</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">New Investigations list</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->				        

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">							
							<div class="row">
								<div class="col-md-12">
									<h2>New Investigations</h2>
									<p>Please specify the pricing for the investigations added to the clinic. Also can edit method, result ranges, other information as per your clinic's lab.</p>
									<form method="POST" action="<?php echo base_url('Lab/add_investigations');?>" role="form" class="form">
										<pre>
										<?php print_r($clinic_investigations); ?>
										</pre>
										<div class="row col-md-12">
											<input type="submit" name="submit" value="Submit">
										</div>
									</form>
								</div>
							</div>							
						</div>                        
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript">
	// Select all / Unselect checkboxes code
	function checkAll(source, cls) {
		checkboxes = document.getElementsByClassName(cls);
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}
</script>