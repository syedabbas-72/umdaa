<!-- <div class="row page-header no-background no-shadow margin-b-0">
    <div class="col-lg-4 align-self-center ">   
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">DOCTORS</a></li>
            <li class="breadcrumb-item active">ADD NEW</li>
        </ol>
    </div>

    <div class="center_type">
        <label for="clinic-name" class="col-sm-2"><b>TYPE:</b></label> &nbsp; &nbsp;&nbsp;
            <input type="radio" id="active" name="gender" value="female" class="form-check-input">CLINIC &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" id="active" name="gender" value="female" class="form-check-input">INHOUSE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                    
            <input type="radio" id="inactive" name="gender" value="male" class="form-check-input">CONSULTANT
    </div>

    <div class="back_button">
        <a href="#"><b> Back </b></a>
    </div>

</div> -->
<!-- content start -->
        <section class="main-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <!-- <div class="card-header card-default">Inline form</div> -->
                        <div class="card-body">
                            
                                <form method="POST" action="<?php echo base_url('Umdaa_controller/');?>" class="form-inline" >
                                    
                                    <div class="form-group col-md-12">
                                        <label for="demographic" id="demo"> <u class="demo_under_line">DEMOGRAPHIC DETAILS </u></label>
                                    </div>
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="first_name" class="col-form-label">FIRST NAME</label>
                                  <div class="form-group">
                                            <input id="first_name" name="first_name" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="middle_name" class="col-form-label">MIDDLE NAME</label>
                                  <div class="form-group">
                                     <input id="middle_name" name="middle_name" type="text" placeholder="" class="form-control">
                                           
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <label for="last_name" class="col-form-label">LAST NAME</label>
                                  <div class="form-group">
                                            <input id="last_name" name="last_name" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                               </div> 

                                 <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="reg_code" class="col-form-label">REGISTRATION CODE</label>
                                  <div class="form-group">
                                            <input id="reg_code" name="reg_code" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="form-group col-md-3">
                                     <label for="sex" class="col-form-label">SEX</label> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="active" name="sex" value="female" class="form-check-input">Female  &nbsp;&nbsp;&nbsp; 
                                        <input type="radio" id="inactive" name="sex" value="male" class="form-check-input">MALE
                                </div>
                               </div> 

                               <div class="form-group col-md-12">
                                        <label for="qualification-details" id="demo"> <u class="demo_under_line">QUALIFICATION DETAILS </u></label>
                                    </div>
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="qualification" class="col-form-label">QUALIFICATION</label>
                                  <div class="form-group">
                                            <input id="qualification" name="qualification" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="department" class="col-form-label">DEPARTMENT</label>
                                  <div class="form-group">
                                            <select name="department" id="department" class="form-control">
                                                <option>--select department--</option>
                                                <option> </option>
                                            </select>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <label for="university" class="col-form-label">UNIVERSITY</label>
                                    <div class="form-group">
                                            <input id="university" name="university" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                               </div> 
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="location" class="col-form-label">LOCATION</label>
                                  <div class="form-group">
                                            <input id="location" name="location" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="state" class="col-form-label">STATE</label>
                                  <div class="form-group">
                                            <select name="state" id="state" class="form-control">
                                                <option>--select state--</option>
                                                <option> </option>
                                            </select>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <label for="year_pass" class="col-form-label">YEAR OF PASSING</label>
                                  <div class="form-group">
                                        <input id="year_pass" name="year_pass" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                               </div> 
                               <div class="form-group col-md-12">
                                        <label for="contact_details" id="demo"> <u class="demo_under_line">CONTACT DETAILS </u></label>
                                    </div>
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="address" class="col-form-label">ADDRESS</label>
                                  <div class="form-group">
                                            <input id="address" name="address" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="state" class="col-form-label">STATE</label>
                                  <div class="form-group">
                                            <select name="state" id="state" class="form-control">
                                                <option>--select state--</option>
                                                <option> </option>
                                            </select>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <label for="district" class="col-form-label">DISTRICT</label>
                                  <div class="form-group">
                                        
                                             <select name="district" id="district" class="form-control">
                                                <option>--select district--</option>
                                                <option> </option>
                                            </select>
                                    </div>
                                </div>
                               </div> 
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="mobile" class="col-form-label">MOBILE</label>
                                  <div class="form-group">
                                            <input id="mobile" name="mobile" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="phone" class="col-form-label">PHONE</label>
                                  <div class="form-group">
                                      <input id="phone" name="phone" type="text" placeholder="" class="form-control">
                                            
                                    </div>
                                </div>
                                 
                               </div> 
                               <div class="form-group col-md-12">
                                        <label for="bank_details" id="demo"> <u class="demo_under_line">BANK ACCOUNT DETAILS </u></label>
                                    </div>
                                <div class="row col-md-12">
                                <div class="col-md-3">
                                    <label for="acc_no" class="col-form-label">ACCOUNT NO</label>
                                  <div class="form-group">
                                            <input id="acc_no" name="acc_no" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                     <label for="bank_name" class="col-form-label">BANK NAME</label>
                                  <div class="form-group">
                                    <input id="bank_name" name="bank_name" type="text" placeholder="" class="form-control">
                                            
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <label for="isfc_code" class="col-form-label">IFSC CODE</label>
                                  <div class="form-group">
                                        
                                            <input id="isfc_code" name="isfc_code" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                               </div>


                                    <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" class="btn btn-red btn-rounded btn-border btn-sm"> 
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="pull-left">
                                         <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                            <i class=""></i> Cancel
                                        </button>
                                    </div>
                                  
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- <footer class="footer">
                <span>Copyright &copy; 2018 FixedPlus</span>
        </footer> -->