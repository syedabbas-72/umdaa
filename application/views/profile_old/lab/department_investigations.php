<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/departments');
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab Investigations&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Departments</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Setup Using Lab Departments</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->				        

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">							
							<div class="row col-md-12">
								<div class="col-md-12">
									<h2>Department wise Investigations</h2>
									<p>Select investigations and navigate to next step.</p>
									<form method="POST" action="<?php echo base_url('Lab/add_investigations');?>" role="form" class="form">
										<?php 
											$clinic_id = $this->session->userdata('clinic_id');
											$depCount = count($department_investigations);
											$col = 0;
											$html = '';
											$html .= '<div class="row">';
											for($i=0; $i<$depCount; $i++){
												if($col == 3){
													$html .= '</div><div class="row">';
													$col = 0;
												}
												$html .= '<div class="col-md-4" style="border:1px solid #cccccc">';
												$html .= '<h3 class="mb-2">'.$department_investigations[$i]["department_name"].'</h3>';
												$html .= '<div class="row col-md-12"><div class="col-md-12 pl-0"><input type="checkbox" onClick="checkAll(this, \'inv_'.$i.'\')" id="select'.$i.'" />&nbsp;<label for="select'.$i.'" style="margin-top:-4px">Select All</label></div></div>';
												$html .= '<ul class="checkList p-0">';
												foreach($department_investigations[$i]['investigations'] as $investigation){
													$clinicInvInfo = $this->db->query("select * from clinic_investigations where clinic_id='".$clinic_id."' and investigation_id='".$investigation['investigation_id']."'")->row();
													// echo $this->db->last_query();
													if(count($clinicInvInfo)>0)
														continue;
													$html .= '<li class="d-flex mt-2"><input id="'.$investigation["investigation_id"].'_cb" type="checkbox" name="investigation['.$investigation["investigation_id"].']" class="inv_'.$i.'" value="'.$investigation["investigation"].'">&nbsp;<label for="'.$investigation["investigation_id"].'_cb" style="margin-top:-4px !important">'.$investigation["investigation"].'</label></li>';
												}
												$html .= "</ul>";
												$html .= '</div>';
												$col++;												
											}

											echo $html;
											// echo '<pre>';
											// print_r($department_investigations);
										?>
										<div class="row col-md-12 mt-3">
											<input type="submit" name="submit" value="Submit" class="btn btn-app">
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