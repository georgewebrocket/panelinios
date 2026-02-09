<?php

$id = $rs[$i]['id'];
echo "<div style=\"border-bottom:1px solid #ccc; padding:10px; position:relative\">";

//echo $uid . "<br/>";
echo "FROM: " . $rs[$i]['from_address'] . "<br/>";

echo "TO: " . $rs[$i]['to_address'] . "<br/>";

echo "DATE: " . func::str14toDateTime($rs[$i]['email_date']) . "<br/>";
echo "SUBJECT: " . $rs[$i]['subject'] . "<br/>";
echo "<a style=\"cursor:pointer; padding:3px 10px; line-height:30px; background:#cdf\" class=\"open-mail\" data-href=\"message.php?email=$id&account=$myaccount\">Open Message</a><br/>";

echo "<div style=\"position:absolute; bottom:0px; right:0px; font-size:12px; padding:5px; background-color:#ccc\">";
echo func::vlookupRS("description", $rsAccounts, $rs[$i]['email_account']);
echo "</div>";

echo "</div>";