<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">IMMUNIZATION</a></li>
          <li class="breadcrumb-item active"><a href="#">NEW VACCINE</a></li>
        </ol>
  </div>
</div>

    
            <div class="card">
                <div class="card-body">  
                <div class="row">             
        <div class="col-md-12">                       
                    <form method="POST" action="<?php echo base_url('Immunization/Immunization_insert');?>" role="form">
                    <div class="row col-md-12">
                    	<div class="col-md-4">
                        <div class="form-group">
                            <label for="vaccine" class="col-form-label">VACCINE&nbsp;&nbsp;&nbsp;&nbsp;</label>    
                            <input id="vaccine" name="vaccine" type="text" placeholder="" class="form-control" required="">
                        </div>
                      </div>
                        <div class="col-md-4"><div class="form-group">
                            <label for="relates_with" class="col-form-label">RELATES WITH </label>
                             <select id="relates_with" name="relates_with" type="text" placeholder="" class="form-control" >
                                <option value="">--select--</option>
                                <?php foreach ($im_info as $value) {?>
                                <option value="<?php echo $value->vaccine_id;?>">
                                  <?php echo $value->vaccine;?>
                                 </option>
                              <?php } ?>
                            </select>
                        </div></div>
                        
                        
                    </div>
                    <div class="row col-md-12">
                    	<div class="col-md-3"><div class="form-group">
                            <label for="from_age" class="col-form-label">FROM AGE&nbsp;&nbsp;</label>    
                            <select id="from_age" name="from_age" type="text" placeholder="" class="form-control" >
                                <option value="">--select--</option>
                                <?php 
                                	for($i=0;$i<=18;$i++){
                                		?>
                                	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php	}

                                ?>
                            </select>
                        </div>
                      </div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="to_age" class="col-form-label">TO AGE</label>
                             <select id="to_age" name="to_age" type="text" placeholder="" class="form-control" >
                                <option value="">--select--</option>
                               <?php 
                                	for($i=0;$i<=18;$i++){
                                		?>
                                	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php	}

                                ?>
                            </select>
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="unit_of_age" class="col-form-label">UNIT OF AGE</label>
                             <select id="unit_of_age" name="unit_of_age" type="text" placeholder="" class="form-control" >
                                <option value="">--select--</option>
                               <option value="DAYS">DAYS</option>
                               <option value="WEEKS">WEEKS</option>
                               <option value="MONTHS">MONTHS</option>
                               <option value="YEARS">YEARS</option>
                            </select>
                        </div></div>               
                        
                    </div>
                    <div class="col-sm-6">
                        <div class="pull-right">
                            <input type="submit" value="Submit" name="submit" class="btn btn-success btn-rounded box-shadow">
                        </div>
                    </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>
