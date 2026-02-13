<?php

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$res = "";

$phone = $_REQUEST['phone'];
$customer_id = $_REQUEST['customer_id'];

if ($_POST) {
  
  $message = $_POST['message'];
  if ($message=="") {
    die("ERROR");
  }
  
  $message = urlencode($message);
  
  $url = "http://api.bulker.gr/http/sms.php/sms.php";
  $url .= "?auth_key=dMvsEY66HjKTziHDl9Bdq4v1FAYbKSnC";
  $milliseconds = round(microtime(true) * 1000);
  $url .= "&id=" . $milliseconds;
  $url .= "&from=Panelinios";
  $url .= "&to=" . str_replace(" ", "", $phone);
  $url .= "&text=$message";
  $url .= "&coding=1";
  
  $res = file_get_contents($url) . "<br/><br/>";
  
}

?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <style>
    
    * {
      box-sizing: border-box;
      font-family:sans-serif;
    }
    
    .form-control, #c_sms_template {
      width:100%;
      padding:10px;
      margin-bottom: 20px;
    }
    
  </style>
  
</head>
<body>
  
  <h1>ΑΠΟΣΤΟΛΗ SMS</h1>
  
  <div>
    <?php echo $res; ?>
  </div>
  
  <form method="post" action="sendsms.php">
    
    <input class="form-control" name="phone" type="text" value="<?php echo $phone ?>" />
    
    <?php
    $c_sms_template = new comboBox("c_sms_template", $db1, "SELECT * FROM SMS_TEMPLATES", "id", "description", 0, "TEMPLATE");
    $c_sms_template->get_comboBox();
    ?>
    
    <textarea class="form-control" name="message" id="message" style="height: 100px;">Ο τραπεζικός μας λογαριασμός στην Alphabank είναι: GR1001401440144002002017704. Ποσό κατάθεσης ****€. Αιτιολογία: ***.Panelinios.gr
</textarea>
    
    <input class="form-control" type="submit" value="SEND" />
    
  </form>
  
  <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
  <script>
    
    $(function() {
      
      $('#c_sms_template').change(function() {
          var templateid = $(this).val();
          $.post("_get-sms-template.php",  
            {
              templateid: templateid,
              customer_id: <?php echo $customer_id ?>
              
            },  function(data){ 
              $("#message").html(data);
            });
          
      });
      
      
    });
    
  </script>
  
</body>
</html>
