<div class="row page-header">

   <div class="col-lg-6 align-self-center ">       

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="#">Home</a></li>

          <li class="breadcrumb-item"><a href="<?php echo base_url('Investigation');?>">Investigations</a></li>

          <li class="breadcrumb-item active"><a href="#">ADD</a></li>     
               

        </ol>

  </div>

</div>

              <!-- content start -->

<section class="main-content">

<div class="row">

  <div class="col-md-12">

    <!-- card start -->

    <div class="card">

      <!-- <div class="card-header card-default">Inline form</div> -->

       <div class="card-body">

          <div class="tabs">

            

              <div class="tab-content">

                  <div role="tabpanel" >

                  <form method="post" action="<?php echo base_url('Investigation/investigation_add');?>"  autocomplete="off" enctype="multipart/form-data">                                  
                     <div class="row col-md-12">
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="investigation" class="col-form-label">INVESTIGATION<span style="color: red;">*</span></label>
                                 <input type="text" name="investigation" type="text" placeholder="" class="form-control" required="">
                                </div>

                              </div>

                              <div class="col-md-4"><div class="form-group">

                                <label for="investigation_code" class="col-form-label">ITEM CODE<span style="color: red;">*</span></label>

                                    <input id="investigation_code" name="investigation_code" type="text" class="form-control" required="">

                                </div>

                              </div>
                             <div class="col-md-4">

                                <div class="form-group">

                                <label for="category" class="col-form-label">CATEGORY<span style="color: red;">*</span></label>

                                    <input id="category" name="category" type="text" class="form-control" required="">

                                </div>

                              </div>

                              </div>

                          </div>


                           <div class="row col-md-12">

                           <div class="col-md-6">

                              <div class="pull-right">

                                  <input type="submit" value="Save" name="submit" class="btn btn-success"> 

                              </div>

                          </div>

                      </form>

                  </div>

                  

          

              



          </div>   <!--Tab end -->

        </div>

      </div>

    </div>

  </div>

</section>

     