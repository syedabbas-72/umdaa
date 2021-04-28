

<script type="text/javascript">

	$(document).ready(function(){
		$(".dependencyDiv").hide();
	});

    function dependencyDiv(id, div_id) {
        if ($("#"+id).prop("checked") == true) {
            $("#"+div_id).show();
        } else {
            $("#"+div_id).hide();
        }
    }

    function showHide(id,div_class){
    	var target = $('#'+id).data('target-id'); 
		$('.'+div_class).hide(); 
		$('.'+div_class+'[data-target="'+target+'"]').show();	
    }

</script>

<style type="text/css">
	.title{
		font-weight: bold;
		text-transform: uppercase;
		padding-bottom: 5px;
		margin: 35px 0px 20px 0px;
		color: #000;
		border-bottom: 1px dotted #ccc;
		font-size: 18px;
	}

	.field_label{
		font-weight: 700;
		margin:0px;
		padding: 0px;
		color: #333;
	}

	.breadcrumb{
		text-transform: uppercase !important;
		font-weight: 600;
		font-size: 14px;
	}
</style>

<?php 

$CI =&get_instance();
if(isset($form_id)) {
	$res = $CI->Generic_model->selectRecord('form_bcp','*',array('form_id'=>$form_id));
	if($res->num_rows()) {
		$rec = $res->row_array();	
		extract($rec);
	}else{
		echo 'No form data, please check the form id and try again';
		exit();
	}
}


?>

<div class="row page-header" style="margin-bottom: 0px;">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('FormBuilder')?>">FORMS</a></li>
          <li class="breadcrumb-item active"><a href="#"><?php echo ucfirst($form_name);?></a></li>          
        </ol>
  </div>
</div>

<?php if($form_name != ''){ ?>
	<h3><?php echo ucfirst($form_name);?></h3>	
<?php } ?>
<section class="main-content" style="margin-top: 0px;">
<div class="row">
 <div class="col-md-6">
<?php 

	$section_res = $CI->Generic_model->selectRecord('section_bcp','*',array('form_id'=>$form_id))->result();
	
	// is section exist
	if(count($section_res)>0){
		
		// Create HTML form
		echo "<form id='dynamicForm' name='dynamicForm' method='post' action='' class='form'>";
		
		foreach($section_res as  $key=>$value){
			
			if($value->format_type == 'normal'){

				// if section title exist
				if($value->title != '')
					echo "<div class='title'>".$value->title."</div>";	

				// if brief exists
				if($value->brief != '')
					echo "<div class='brief'>".$value->brief."</div>";

				// get fields for the section whose parent_field_id is NULL
				$field_res = $CI->Generic_model->selectRecord('field_bcp','*',array('section_id'=>$value->section_id, 'parent_field_id' => 'NULL'))->result();

				// if fields exists
				if(count($field_res) >0){

					foreach($field_res as $key2=>$value2){
						echo $CI->getField($value2);
					}
				}

			}elseif($value->format_type == 'tabular') { // close normal

				// draw table structure with columns and rows information
				// create table
				echo "<table cellspacing='0' cellpadding='0'>";

				// if section title exist
				if($value->title != '')
					echo "<tr><td colspan='2'><h1>".$value->title."</h1></td></tr>";	

				// if brief exists
				if($value->brief != '')
					echo "<tr><td colspan='2'><h3>".$value->brief."</h1></td></tr>";	

				// get fields for the section
				$field_res = $obj->selectRecord('field_bcp','*',array('section_id'=>$value->section_id));

				// if fields exists
				if($field_res->num_rows){

					while($field_rec = $field_res->fetch_assoc()){

						// extracting array to variables
						echo '<pre>';
						print_r($field_rec);
						echo '</pre>';

					}
				}
			}

			// end HTML form
			echo "</form>";

		}

	}else{
		echo "No sections were created yet. Start creating by clicking on the <b>New Section</b>";
	}
			
?>	
</div>
</div>

 </section>