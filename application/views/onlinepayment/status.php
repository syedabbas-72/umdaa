<!DOCTYPE html>
<html lang="en">
<head>
  <title>UMDAA ONLINE PAYMENT</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
  p{
      word-break: break-word !important;
  }
  </style>
</head>
<body>

<div class="container mt-5">
    <?php
    if($postdata['STATUS'] == "TXN_SUCCESS"){
        ?>
        <div class="jumbotron text-center">
        <h1>Transaction Success. <i class="fa fa-smile-o"></i></h1>
        <p>Transaction Details !</p> 
        <div class="row ">
            <div class="col-12 text-left">
                <p><b>ORDER ID : </b> <br><?=$postdata['ORDERID']?></p>
                <p><b>Transaction ID : </b> <br><?=$postdata['TXNID']?></p>
                <p><b>Transaction Amount : </b> <br><?=$postdata['TXNAMOUNT']?> <?=$postdata['CURRENCY']?></p>
            </div>
        </div>
        <h6 class="text-center mt-5">Your page will be redirected in 5 seconds.</h6>
        </div>
        <?php
    }
    ?>
    <?php
    if($postdata['STATUS'] == "TXN_FAILURE"){
        ?>
        <div class="jumbotron text-center">
        <h1>Transaction Failure. <i class="fa fa-frown-o"></i></h1>
        <p>Transaction Details !</p> 
        <div class="row ">
            <div class="col-12 text-left">
                <!-- <pre><?=print_r($postdata)?></pre> -->
                <p><b>ORDER ID : </b> <br><?=$postdata['ORDERID']?></p>
                <p><b>Transaction ID : </b> <br><?=$postdata['TXNID']?></p>
                <p><b>Message : </b> <br><?=$postdata['RESPMSG']?></p>
                <!-- <p><b>Transaction Amount : </b> <br><?=$postdata['TXNAMOUNT']?> <?=$postdata['CURRENCY']?></p> -->
            </div>
        </div>
        <!-- <h6 class="text-center mt-5">Your page will be redirected in 5 seconds.</h6> -->
        <!-- <a href="http://localhost:8100/#/expert">Click to redirect to your merchant's page. PayTM Care.</a> -->
        </div>
        <?php
    }
    ?>
</div>

<script>
$(document).ready(function(){
    // setTimeout(function(){ var url = "https://localhost:8100/#/expert"; }, 3000);
})
    
</script>
        
</body>
</html>
