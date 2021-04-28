<!DOCTYPE html>
<html lang="en">
<head>
  <title>UMDAA ONLINE PAYMENT</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<h1 class="text-center mt-5">Please do not refresh this page..</h1>
<form method="post" action="https://securegw-stage.paytm.in/theia/processTransaction" name="f1">
        <?php
        foreach($list as $name => $value) {
            echo '<input type="hidden" name="' . $name .'" value="' . $value . '"><br>';
            // echo $name."<bR>";
        }
        ?>
    <script type="text/javascript">
        document.f1.submit();
    </script>
</form>
        
</body>
</html>
