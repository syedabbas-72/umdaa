<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    
    <!-- Primary Meta Tags -->
    <title>UMDAA Health Care</title>
    <!-- <meta name="title" content="UMDAA Health Care"> -->
    <!-- <meta name="description" content=""> -->

    <!-- Open Graph / Facebook -->
    <meta property="og:image" content="https://umdaa.co/assets/uploads/abc.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://umdaa.co/clinic">
    <meta property="og:title" content="UMDAA Health Care">
    <meta property="og:description" content="">

    <!-- Twitter -->
    <!-- <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://umdaa.co/blogs/1/when-should-i-see-a-gi-doctor">
    <meta property="twitter:title" content="UMDAA Health Care">
    <meta property="twitter:description" content="">
    <meta property="twitter:image" content="//umdaa.co/assets/uploads/abc.png"> -->
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <!-- <meta name="author" content="umdaa" /> -->
    <!-- <title>UMDAA</title> -->
     <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="<?php echo base_url(); ?>assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/material-design-iconic-font.min.css">
    <!-- bootstrap -->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pages/extra_pages.css">
    <!-- favicon -->
    <link rel="shortcut icon" href="" /> 
    <style type="text/css">
        .login100-form-logo{
            height: 135px;
            width: 135px;
        }
        body
        {
            background: #b3d4ea !important;
        }
    </style>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">

            <div class="row col-md-12 text-center justify-content-center">
                <div class="col-md-4 p-2 rounded">
                <h6 class="text-center text-danger"><i class="fa fa-info-circle"></i>&nbsp;For Best View We Recommend Firefox Browser Only.</h6>
                </div>
            </div>
            <div class="wrap-login100" style = "background: #042942;">

                 <?php if(isset($msg) || validation_errors() !== ''): ?>
                <div class="alert bg-info alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong>Alert !</strong> <?= validation_errors();?>
                    <?= isset($msg)? $msg: ''; ?></div>
                    <?php endif; ?>
             
                    <?php echo form_open(base_url('authentication/login'), 'class="login100-form validate-form" autocomplete="off"'); ?>
                    <span class="login100-form-logo">
                        <img alt="" src="<?php echo base_url(); ?>/assets/img/umdaa_logo.png">
                    </span>
                    <span class="login100-form-title p-b-34 p-t-27">
                        
                    </span>
                    <div class="wrap-input100 validate-input" data-validate = "Enter username">
                        <input class="input100" type="text" name="email" placeholder="Username">
                        <span class="focus-input100" data-placeholder="&#xf207;"></span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Enter password">
                        <input class="input100" type="password" name="password" placeholder="Password">
                        <span class="focus-input100" data-placeholder="&#xf191;"></span>
                    </div>
                    
                    <div class="container-login100-form-btn" style="margin-bottom: 20px;">
                        <input class="login100-form-btn" type="submit" name="submit" id="submit"value="Login">
                    </div>
                    <div class="text-center p-t-30">
                        <!-- <a class="txt1" href="forgot_password.html">
                            Forgot Password?
                        </a> -->
                    </div>
                  <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!-- start js include path -->
     <script src="<?php echo base_ul(); ?>assets/plugins/jquery/jquery.min.js" ></script>
    <!-- bootstrap -->
    <script src="<?php echo base_ul(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="<?php echo base_ul(); ?>assets/js/pages/extra_pages/extra_pages.js"></script>
    <!-- end js include path -->
</body>

</html>