<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/config.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');

$rowsPerPage = 50;
$offset = $_REQUEST['offset'];

$account = isset($_REQUEST['account'])? $_REQUEST['account']: 0;
$emailtype = isset($_REQUEST['type'])? $_REQUEST['type']: 0;
$customer = isset($_REQUEST['h_ac_customer'])? $_REQUEST['h_ac_customer']: 0;
$junk = isset($_REQUEST['junk'])? $_REQUEST['junk']: 0;
$spam = isset($_REQUEST['spam'])? $_REQUEST['spam']: 0;

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

$sql .= " ORDER BY email_date DESC LIMIT $offset, $rowsPerPage ";
$rs = $db1->getRS($sql, $params);

$rsAccounts = $db1->getRS("SELECT * FROM EMAIL_ACCOUNTS");
    
for ($i=0;$i<count($rs);$i++) {
  include 'email-listitem.php';
}

if (count($rs)==$rowsPerPage) {
  $newoffset = $offset + $rowsPerPage;
    
echo <<<EOT

<input type="button" class="button see-more-emails" data-url="getEmails.php?account=$account&h_ac_customer=$customer&type=$emailtype&junk=$junk&spam=$spam&offset=$newoffset" value="More" />

<script>
  $(function() {
    $('.see-more-emails').click(function() {
      var url = $(this).data('url');
      var thisEl = $(this);
      $.ajax({url: url})
        .done(function(data) { 
          thisEl.after(data);
          thisEl.hide();
        });
    });
  });
</script>

EOT;

}