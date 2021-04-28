<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sensational</title>

        <!-- Common Plugins -->
        <link href="<?= base_url() ?>assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom Css-->
        <link href="<?= base_url() ?>assets/scss/style.css" rel="stylesheet">
    
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            html,body{
                height: 100%;
            }
        </style>
    </head>
    <body class="bg-light">

        <div class="misc-wrapper">
            <div class="misc-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4">
                              <div class="misc-header text-center">
                <img alt="" src="<?= base_url() ?>public/images/logo_round.png" class="logo-icon margin-r-10">
               
                            </div>
                             <?php if(isset($msg) || validation_errors() !== ''): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                    <?= validation_errors();?>
                    <?= isset($msg)? $msg: ''; ?>
                </div>
                <?php endif; ?>
                            <div class="misc-box">   
                                <?php echo form_open(base_url('auth/reset_password'), 'class="login100-form validate-form" autocomplete="off"'); ?>
                                    <div class="form-group">                                      
                                        <label  for="email">Password</label>
                                        <div class="group-icon">
                                        <input id="email" type="text" placeholder="Email" name="password" class="form-control" required="">
                                        <span class="icon-user text-muted icon-input"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Confirm Password</label>
                                        <div class="group-icon">
                                        <input id="password" type="password" name="cpassword" placeholder="Password" class="form-control">
                                        <span class="icon-lock text-muted icon-input"></span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                       
                                            
                                            <input class="btn btn-block btn-primary btn-rounded box-shadow" type="submit" name="submit" id="submit" value="Reset Password">
                                        </div>
                                    </div>


                                    
                                <?php echo form_close(); ?>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Plugins -->
        <script src="<?= base_url() ?>assets/lib/jquery/dist/jquery.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/pace/pace.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/nano-scroll/jquery.nanoscroller.min.js"></script>
        <script src="<?= base_url() ?>assets/lib/metisMenu/metisMenu.min.js"></script>
        <script src="<?= base_url() ?>assets/js/custom.js"></script>
    
    </body>

</html>
