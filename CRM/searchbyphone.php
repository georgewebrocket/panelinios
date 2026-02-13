<?php


$phone = $_REQUEST['phone'];

if (substr($phone, 0, 3)=='+30') {
    $phone = substr($phone, 3 );
}


$redirect = "https://crm.panelinios.gr/home.php?h-company-name=0&s-phone=$phone&cReference=0&cCategory=0&cArea=0&c_city_id=0&cCompanyType=0&c_hasAfm=0&s-afm=&cStatus=0&c_companystatus=0&c_productcategory=0&t_statusdate1=&t_statusdate2=&cUser=0&cOnlineStatus=-1&c_hasdomain=-1&c_courier=0&c_sort=id&t_recordsperpage=30&BtnSearch=SEARCH";

header("Location:  $redirect");