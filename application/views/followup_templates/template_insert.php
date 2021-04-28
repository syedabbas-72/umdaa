
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">FOLLOW-UP LIST</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>
<section class="main-content">
    <div class="row">             
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                 <div class="col-md-12">
                  <?php echo form_open("followup_templates/insert");?>
                  <div class="row col-md-12">
                         <div class="row col-md-12">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="FollowUpTitle" class="col-form-label">Follow-up Name</label>
                                  <input type="text" name="name" id="FollowUpTitle"  class="form-control"  required="required">
                              </div>
                          </div>
                           <div class="col-md-6">

                              <input style="margin-top:30px" type="submit" class="btn btn-success" name="submit" value="Create Follow-up">
                          </div>
                         
                      </div> 
              	   </div>
                 
                </form>
                </div>
            </div>
          </div>
      </div>
  </div>
</section>
