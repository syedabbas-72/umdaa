<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                 <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("investigation"); ?>">Investigations</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">Edit Investigation</li>
                            </ol>
                        </div>
                    </div>




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



                  <form method="post" action="<?php echo base_url('Investigation/investigation_update/'.$investigations->investigation_id);?>"  autocomplete="off" enctype="multipart/form-data">                                  

                     <div class="row col-md-12">

                        <div class="col-md-4">

                          <div class="form-group">

                              <label for="investigation" class="col-form-label">INVESTIGATION<span style="color: red;">*</span></label>

                                 <input type="text" name="investigation" type="text" placeholder="" class="form-control" required="" value="<?php echo $investigations->investigation; ?>">


                                </div>



                              </div>



                              <div class="col-md-4"><div class="form-group">



                                <label for="first_name" class="col-form-label">SHORT FORM<span style="color: red;">*</span></label>



                                    <input id="investigation_code" name="short_form" type="text" class="form-control" required="" value="<?php echo $investigations->short_form; ?> " required="">



                                </div>



                              </div>

                             <div class="col-md-4">



                                <div class="form-group">



                                <label for="category" class="col-form-label">CATEGORY<span style="color: red;">*</span></label>



                                    <input id="category" name="category" value="<?php echo $investigations->category; ?>" type="text" class="form-control" required="">



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

   