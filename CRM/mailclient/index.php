<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/config.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');

$account = isset($_REQUEST['account'])? $_REQUEST['account']: 0;
$emailtype = isset($_REQUEST['type'])? $_REQUEST['type']: 0;
$customer = isset($_REQUEST['h_ac_customer'])? $_REQUEST['h_ac_customer']: 0;
$junk = isset($_REQUEST['junk'])? $_REQUEST['junk']: 0;
$spam = isset($_REQUEST['spam'])? $_REQUEST['spam']: 0;

$rowsPerPage = 50;

$sql = "SELECT * FROM EMAILS WHERE id>0 ";
$params = array();
if ($account>0) {
  $sql .= " AND email_account=? ";
  array_push($params, $account);
}
$myaccount = $account>0? $account: 1;

if ($emailtype>0) {
  $sql .= " AND email_type=? ";
  array_push($params, $emailtype);
}
if ($customer>0) {
  $sql .= " AND company=? ";
  array_push($params, $customer);
}
if ($junk>0) {
  $sql .= " AND trash=1 ";
}
else {
  $sql .= " AND trash=0 ";
}
if ($spam>0) {
  $sql .= " AND spam=1 ";
}
else {
  $sql .= " AND spam=0 ";
}

$sql .= " ORDER BY email_date DESC LIMIT $rowsPerPage ";
$rs = $db1->getRS($sql, $params);

$rsAccounts = $db1->getRS("SELECT * FROM EMAIL_ACCOUNTS");


?>
<html>
<head>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <title>Epagelmatias | Email client</title>
  
  <link href="../css/reset.css" rel="stylesheet" type="text/css" />
  <link href="../css/grid.css" rel="stylesheet" type="text/css" />
  <link href="../css/global.css" rel="stylesheet" type="text/css" />
  <link href="../fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
  <style>
    
    * {
      box-sizing: border-box;
    }
    
    .attachment {
      margin-bottom:10px; font-size:18px; padding:10px; background:#ccc; display:inline-block; border-radius:20px; margin-right:20px;
    }
    
    .active {
      border-bottom:3px solid #5f5;
    }
    
    
    .search-form {
      display:inline-block;
      margin:0px; padding:0px;
    }
    
    #ac_customer {
      width:150px;
      margin-bottom: 0px;
    }
    
    #account {
      width:150px;
      margin-bottom: 0px;
    }
    
    
    #btnSearch, #new-msg {
      padding:5px 10px;
      margin-bottom: 0px;
      font-size: 15px;
      font-weight: normal;
    }
    
    #ac_customer2 {
      width:150px;
    }
    
    
    
    #change-customerid {
      cursor:pointer;
    }
    
    #change-customerid:hover {
      background-color: #333;
      color:#fff;
    }
    
    .see-more-emails {
      margin-top:20px;
      margin-left:20px;
    }
    
  </style>
          
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
  <script type="text/javascript" src="../js/jquery.easing.1.3.js"></script>
  <script type="text/javascript" src="../js/jquery.cookie.js"></script>        
  <script type="text/javascript" src="../fancybox/jquery.fancybox.js"></script>
  <script type="text/javascript" src="../js/code.js"></script>
  
  
  
  
</head>
<body>
  
  <div class="col-12" style="height: 6vh; padding:10px; font-weight: bold; font-size:18px; border-bottom: 1px solid #ccc;">
    PANELINIOS.GR / EMAIL CLIENT &nbsp;&nbsp;&nbsp;
    
    
    <form action="index.php?account=<?php echo $account ?>&type<?php echo $emailtype ?>" method="get" class="search-form" style="">
      
      <?php
      
      $c_account = new comboBox("account", $db1, "SELECT * FROM EMAIL_ACCOUNTS", "id", "description", $account);
      $c_account->set_dontSelect('All accounts');
      echo $c_account->comboBox_simple() . "&nbsp;";
    
      $ac_customer = new autocomplete("ac_customer", "COMPANIES", $customer, $db1);
      $ac_customer->getAutocompleteSimple();
      
      echo "&nbsp;";
      
      $btnSeach = new button("btnSearch", "SEARCH");
      echo $btnSeach->get_button_simple();
      
      ?>
      
    </form>
    &nbsp;&nbsp;&nbsp;
    
    <a class="<?php echo $emailtype==1? "active": "" ?>" href="https://crm.panelinios.gr/mailclient/?account=<?php echo $account ?>&type=1&h_ac_customer=<?php echo $customer ?>">INBOX</a> &nbsp;
    <a class="<?php echo $emailtype==2? "active": "" ?>" href="https://crm.panelinios.gr/mailclient/?account=<?php echo $account ?>&type=2&h_ac_customer=<?php echo $customer ?>">SENT</a> &nbsp;
    <a class="<?php echo $junk==1? "active": "" ?>" href="https://crm.panelinios.gr/mailclient/?account=<?php echo $account ?>&h_ac_customer=<?php echo $customer ?>&junk=1">TRASH</a> &nbsp;
    <a class="<?php echo $spam==1? "active": "" ?>" href="https://crm.panelinios.gr/mailclient/?account=<?php echo $account ?>&h_ac_customer=<?php echo $customer ?>&spam=1">SPAM</a> &nbsp;
    
    &nbsp;&nbsp;&nbsp;
    <a id="new-msg" href="sendEmail.php?account=<?php echo $account ?>&customer=<?php echo $customer ?>" class="button fancybox">NEW MESSAGE</a>

    &nbsp;&nbsp;&nbsp;
    <a href="import.php" class="button fancybox">CHECK NEW MESSAGES</a>
    
    
  </div>
  
  <div class="col-4" style="padding:0px; background-color: #efefef; height: 94vh; overflow-y: scroll;;">
    
    <?php
    
    for ($i=0;$i<count($rs);$i++) {
      include 'email-listitem.php';
    }
    
    ?>
    
    <input type="button" class="button see-more-emails" data-url="getEmails.php?account=<?php echo $account ?>&h_ac_customer=<?php echo $customer ?>&type=<?php echo $emailtype ?>&junk=<?php echo $junk ?>&spam=<?php echo $spam ?>&offset=<?php echo $rowsPerPage ?>" value="More" />
    
    <script>
      $(function() {
        $('.see-more-emails').click(function() {
          var url = $(this).data('url');
          var thisEl = $(this);
          $.ajax({url: url})
            .done(function(data) { 
              thisEl.after(data);
              thisEl.hide();

              //bind click event to new elements
              $('.open-mail').click(function() {
                var href = $(this).data("href");
                console.log(href);
                
                $.post(href,  {},  function(data){ 
                  $("#mail-container").html(data);
                });
                
                $('.open-mail').each(function() { $(this).parent().css("background-color", "#efefef") });
                $(this).parent().css("background-color", "#dfd");

              });


            });
        });
      });
    </script>
    
    
    
  </div>
  
  <div class="col-8" style="padding:10px 20px; height: 94vh; overflow-y: scroll;;">
    
    <div id="mail-container"></div>
    
    
    
  </div>
  
  
  <script>
    
    
    $(function() {
      
      $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 450 });
      
      $('#ac_customer').attr("placeholder", "Search Customer");
        
      $('.open-mail').click(function() {
        var href = $(this).data("href");
        console.log(href);
        
        $.post(href,  {},  function(data){ 
          $("#mail-container").html(data);
        });
        
        $('.open-mail').each(function() { $(this).parent().css("background-color", "#efefef") });
        $(this).parent().css("background-color", "#dfd");

      });  
      
      
        
        
    });
    
    
  </script>
    
  
</body>
</html>


