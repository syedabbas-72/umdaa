<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">DRUG</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('Drug/drug_update/'.$drug->drug_id);?>" role="form">
                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="salt" class="col-form-label">SALT </label>
                                     <select id="salt_id" name="salt_id" type="text" placeholder="" class="form-control" >
                                        <option>--select--</option>
                                        <?php foreach ($salt as $value) {
                                            if($drug->salt_id==$value->salt_id){
                                            ?>
                                        <option value="<?php echo $value->salt_id;?>" selected>
                                          <?php echo $value->salt_name;?>
                                         </option>
                                     <?php }else{ ?>
                                         <option value="<?php echo $value->salt_id;?>" >
                                          <?php echo $value->salt_name;?>
                                         </option>
                                      <?php }} ?>
                                    </select>
                                </div></div>
                               
                                <div class="col-md-3"><div class="form-group">
                                    <label for="trade_name" class="col-form-label">TRADE NAME</label>    
                                    <input id="trade_name" value="<?php echo $drug->trade_name; ?>" name="trade_name" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div>
                            </div> 


                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="formulation" class="col-form-label">FORMULATION</label>    
                                    <input id="formulation" value="<?php echo $drug->formulation; ?>" name="formulation" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="composition" class="col-form-label">COMPOSITION</label>
                                   <input id="composition" value="<?php echo$drug->composition; ?>" name="composition" type="text" placeholder="" class="form-control-demo" required="">
                                  </div>
                                </div>
                                
                            </div> 
                
                                
                                 
                            </div> 
                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  