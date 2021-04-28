<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Umdaa Login</title>
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/umdaa_logo.png'); ?>">
        <!-- Common Plugins -->
        <link href="<?php echo base_url('assets/lib/bootstrap/css/bootstrap.min.css');?>" rel="stylesheet">

        <!-- Custom Css-->
        <link href="<?php echo base_url('assets/scss/style.css');?>" rel="stylesheet">

        <style type="text/css">
            html,body{
                height: 100%;
            }
            /*body{
                width:100%;
                background: url(../assets/img/login.jpg) 0px 0px / cover no-repeat;
            }*/
        </style>
    </head>
    <body class="bg-light">

        <div class="misc-wrapper">
            <div class="misc-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4">
                              <div class="misc-header text-center">
                                <img alt="" src="<?php echo base_url('assets/img/umdaa_logo.png'); ?>" class="logo-icon margin-r-10">
                               
                            </div>
                <?php if(isset($msg) || validation_errors() !== ''): ?>
                    <div class="alert bg-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                        <?= validation_errors();?>
                        <?= isset($msg)? $msg: ''; ?>
                    </div>
                <?php endif; ?>

                            <div class="misc-box">   
                                <?php echo form_open(base_url('Authentication/login'), 'class="login100-form validate-form" autocomplete="off"'); ?>
                                    <div class="form-group">                                      
                                        <label  for="exampleuser1">Username</label>
                                        <div class="group-icon">
                                        <input id="" type="text" placeholder="Username" class="form-control" name="email">
                                        <span class="icon-user text-muted icon-input"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <div class="group-icon">
                                        <input id="exampleInputPassword1" type="password" placeholder="Password" class="form-control" name="password">
                                        <span class="icon-lock text-muted icon-input"></span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <div class="float-right">
                                            <input type="submit" name="submit" value="Login" class="btn btn-block btn-warning btn-rounded box-shadow">
                                        </div>
                                    </div>
                                    <!--<hr>
                                    <p class="text-center">Need to Signup?</p>
                                    <a href="page-register.html" class="btn btn-block btn-success btn-rounded box-shadow">Register Now</a>-->
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Plugins -->
        <script src="<?php echo base_url('assets/lib/jquery/dist/jquery.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/bootstrap/js/popper.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/lib/bootstrap/js/bootstrap.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/pace/pace.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/jasny-bootstrap/js/jasny-bootstrap.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/slimscroll/jquery.slimscroll.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/nano-scroll/jquery.nanoscroller.min.js');?>"></script>
        <script src="<?php echo base_url('assets/lib/metisMenu/metisMenu.min.js');?>"></script>
        <script src="<?php echo base_url('assets/js/custom.js');?>"></script>


        
    </body>

</html>
