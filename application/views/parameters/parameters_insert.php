<style>
textarea {
  width: 300px;
  height: 150px;
}
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">PARAMETERS LIST</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>
<section class="main-content">
        <div class="row">             
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    	<?php echo form_open('parameters/add');?>
                     <div class="col-md-12">
                      
                        <div class="row col-md-12">
                               <div class="row col-md-12">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="disease_name" class="col-form-label">PARAMETER NAME</label>
                                        <input type="text" name="name" id="disease_name"  class="form-control"  required="required">
                                    </div>
                                </div>
                                  <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="disease_name" class="col-form-label">PARAMETER TYPE</label>
                                        <select name="type" class="form-control" required="">
                                        	<option value="">--Select Type--</option>
                                        	<option value="Clinical">Clinical</option>
                                        	<option value="Lab">Lab</option>	
                                        	</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="form-group">
                                    <label for="disease_name" class="col-form-label">LOW RANGE</label>
                                        <input type="text" name="low_range" id="disease_name"  class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="form-group">
                                    <label for="disease_name" class="col-form-label">HIGH RANGE</label>
                                        <input type="text" name="high_range" id="disease_name"  class="form-control">
                                    </div>
                                </div>
                                
                            </div> 
                	</div>
                
            </div>
             <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;">
              <input type="submit" class="btn btn-success" name="submit" value="Create Parameter">
            </div>
        </form>
        </div>
    </div>
</section>
<!-- <script>
	$(document).ready(function(){
	   $("#vitla_masters_val").click(function(){
		  alert("The paragraph was clicked.");
		});

});
</script> -->
<!-- <script>
	function vital_masters_val_cout(value){
		var myString = $("textarea#Parameter_name").val();
		alert(myString);
		if(myString != ''){
			var values = myString+","+value;
		
		}else{
			var values = myString+value;
		}
		
		if(myString.indexOf(value) != -1){
		    alert('found');
		    myString_1 = myString.replace(values,'');
		    $("textarea#Parameter_name").html(myString_1);
		}else{
			alert('Not found');
			alert(value);
			$("textarea#Parameter_name").html(values);
		}
	
	}
</script> -->
        