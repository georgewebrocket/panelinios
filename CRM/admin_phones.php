<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/db.php');
require_once('php/utils.php');
require_once('php/start.php');

function normPhone($phone) {
    $phone = str_replace("+30", "", $phone);
    $phone = preg_replace("/[^0-9]/", "", $phone);
    return $phone;

}



$start = !empty($_GET['start']) ? (int)$_GET['start'] : 0;

echo $start . "<br/>";

if ($start==0) {
    $sql = "SELECT id, phone1, phone2, mobilephone FROM COMPANIES ORDER BY id LIMIT 100 ";
}
else {
    $sql = "SELECT id, phone1, phone2, mobilephone FROM COMPANIES ORDER BY id LIMIT 100 OFFSET $start";
}
echo $sql . "<br/>";

$rs = $db1->getRS($sql);

//var_dump($rs);

for ($i=0; $i < count($rs); $i++) { 
    $phones = [];

    $phones1 = explode(",", $rs[$i]['phone1']);
    foreach ($phones1 as $phone1) {
        $phones[] = $phone1;
    }

    $phones2 = explode(",", $rs[$i]['phone2']);
    foreach ($phones2 as $phone2) {
        $phones[] = $phone2;
    }

    $mobilephones = explode(",", $rs[$i]['mobilephone']);
    foreach ($mobilephones as $mobilephone) {
        $phones[] = $mobilephone;
    }

    //var_dump($phones);

    foreach ($phones as $phone) {
        if (trim($phone)!="") {
            $sql = "INSERT INTO PHONES (company_id, phone) VALUES (?, ?)";
            $ret = $db1->execSQL($sql, [$rs[$i]['id'], normPhone($phone)]);
            echo $ret . " - " . $rs[$i]['id'] . " - $phone" . "<br/>";
        }        
    }

}

?>
<html>
    <head></head>
    <body></body>
    <?php if ($rs) { ?>
    <script>
        setTimeout(() => {
            let baseUrl = "https://crm.panelinios.gr/admin_phones.php";
            let params = new URLSearchParams({
                start: "<?php echo $start + 100 ?>"
            });
            window.location.href = `${baseUrl}?${params.toString()}`;
        }, 1000);
    </script>
    <?php } ?>
</html>