<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sdate = isset($_REQUEST['mdate'])? $_REQUEST['mdate']:  date("YmdHis");
$semployee = isset($_REQUEST['userid'])? $_REQUEST['userid']: 0;
$hour_in = 0;
$min_in = 0;
$hour_out = 0;
$min_out = 0;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    
    
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>
    
    <script type="text/javascript" src="js/code.js"></script>
    
    <script>
        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        
        });
        
    </script>
    
    <style>
        
        * {
            box-sizing: border-box;
        }
        
        input, select, textArea {
            padding:10px;
            width:100%;
            margin-bottom: 20px;
        }
        
        
        #t_hour_in, #t_hour_out, #t_min_in, #t_min_out {
            width:30%;
            margin-right: 5px;
            font-size: 20px;
        }
        
        #btnSave {
            width: 30%;
        }
        
        
        
    </style>
    
    </head>

    <body class="form">
        <div class="form-container" style="padding:50px; max-width: 1100px; margin:auto">
            
            <h1 style="padding-left: 0px">Ώρες προσωπικού</h1>
            
            <div class="col-4">
                <?php

                $t_date = new textbox("t_date", "ΗΜΕΡ/ΝΙΑ", $sdate);
                $t_date->set_format("DATE");
                $t_date->set_locale($locale);
                $t_date->get_Textbox();
                
                $t_employee = new comboBox("t_employee", $db1, 
                        "SELECT id, fullname FROM USERS WHERE active=1 ORDER BY fullname", 
                        "id", "fullname",
                        $semployee, "ΕΡΓΑΖΟΜΕΝΟΣ");
                $t_employee->set_extraAttr(" size=\"20\"");
                $t_employee->get_comboBox();
                
                /*$btnOK = new button("btnOK", "SEARCH");
                $btnOK->get_button();*/



                ?>
            </div>
            
            <div class="col-1"></div>
            
            <div class="col-7">
                
                <?php
                
                echo '<div class="col-4">ΕΙΣΟΔΟΣ</div><div class="col-8">';
                
                $t_hour_in = new comboBox("t_hour_in", $db1, 
                        "SELECT id, description FROM TIME_HOUR WHERE active=1 ORDER BY id", 
                        "id", "description",
                        $hour_in, "ΩΡΑ");
                $t_hour_in->set_dontSelect("--");
                echo $t_hour_in->comboBox_simple();
                
                echo ": ";
                
                $t_min_in = new comboBox("t_min_in", $db1, 
                        "SELECT id, description FROM TIME_MIN ORDER BY id", 
                        "id", "description",
                        $min_in, "ΛΕΠΤΑ");
                //$t_min_in->set_dontSelect("--");
                $t_min_in->set_enableNoChoice(FALSE);
                echo $t_min_in->comboBox_simple();
                
                echo '</div>';
                
                echo '<div class="clear"></div>';
                
                echo '<div class="col-4">ΕΞΟΔΟΣ</div><div class="col-8">';
                
                $t_hour_out = new comboBox("t_hour_out", $db1, 
                        "SELECT id, description FROM TIME_HOUR WHERE active=1 ORDER BY id", 
                        "id", "description",
                        $hour_out, "ΩΡΑ");
                $t_hour_out->set_dontSelect("--");
                
                echo $t_hour_out->comboBox_simple();
                
                echo ": ";
                
                $t_min_out = new comboBox("t_min_out", $db1, 
                        "SELECT id, description FROM TIME_MIN ORDER BY id", 
                        "id", "description",
                        $min_out, "ΛΕΠΤΑ");
                //$t_min_out->set_dontSelect("--");
                $t_min_out->set_enableNoChoice(FALSE);
                echo $t_min_out->comboBox_simple();
                
                echo '</div>';
                echo '<div class="clear"></div>';
                
                
                
                
                echo <<<EOT
                    <div class="col-4"></div>
                <div class="col-8">
                    <div id="default-hours" class="button" style="width: 30%;">9.30-16.30</div>
                </div>
                <div class="clear" style="height: 20px"></div>
                
EOT;
                
                $chk_dayoff = new checkbox("chk_dayoff", "ΑΔΕΙΑ", 0);
                $chk_dayoff->get_Checkbox();
                
                $t_hoursoff = new textbox("t_hoursoff", "ΩΡΕΣ ΑΔΕΙΑΣ<br/>(πχ 2+1/2h = 2.50)", "", "0.00");
                //$t_hoursoff->set_format("CURRENCY");
                $t_hoursoff->get_Textbox();                
                
                $t_comments = new textbox("t_comments", "ΣΧΟΛΙΑ", "");
                $t_comments->set_multiline();
                $t_comments->get_Textbox();
                
                $t_user_role = new comboBox("t_user_role", $db1, 
                        "SELECT * FROM USER_ROLES");
                $t_user_role->set_label("User Role");
                $t_user_role->get_comboBox();
                
                $btnSave = new button("btnSave", "Αποθήκευση");
                $btnSave->get_button();
                
                ?>
                <div class="clear"></div>
                <div class="col-4"></div>
                <div class="col-8">
                    <div id="username">...</div>
                </div>
                
                
                
                
                
                
            </div>
            
            <div class="clear"></div>
            
            
            
        </div>
        
        <script>
            
            $(function() {                
                
                $("#t_employee").change(function() { getUserWorkData(); });
                
                $("#t_date").change(function() { getUserWorkData(); });
                
                $("#btnSave").click(function() { setUserWorkData(); });
                
                $("#default-hours").click(function() { setDefaultHours(); });
                
                <?php if ($semployee>0) { ?>
                getUserWorkData();        
                <?php } ?>
                
                
            });
            
            
            function getUserWorkData() {
                var employeeId = $("#t_employee").val();
                var eDate = $("#t_date").val();
                $.post( "_getUserWorkData.php", 
                    { employeeId: employeeId, eDate: eDate },
                    function(data) {
                        if (data!=="error") {
                            //console.log(data);
                            var obj = JSON.parse(data);
                            $("#t_hour_in").val(obj.hour_in);
                            $("#t_min_in").val(obj.min_in);
                            $("#t_hour_out").val(obj.hour_out);
                            $("#t_min_out").val(obj.min_out);
                            $("#t_comments").val(obj.comments);
                            $("#t_user_role").val(obj.user_role);
                            $("#username").html(obj.username);
                            if (obj.dayoff == 1) { $( "#chk_dayoff").prop('checked', true);}
                            else { $( "#chk_dayoff").prop('checked', false);}
                            var hoursoff = obj.hoursoff==0? '0.00': obj.hoursoff;
                            $("#t_hoursoff").val(hoursoff);
                            
                        }
                        
                    }); 
            }
            
            function setUserWorkData() {
                var employeeId = $("#t_employee").val();
                var eDate = $("#t_date").val();
                var hourIn = $("#t_hour_in").val();
                var minIn = $("#t_min_in").val();
                var hourOut = $("#t_hour_out").val();
                var minOut = $("#t_min_out").val();
                var comments = $("#t_comments").val();
                var dayoff = $('#chk_dayoff').is(":checked")? 1:0;
                var hoursoff = $("#t_hoursoff").val();
                
                var user_role = $("#t_user_role").val();
                //console.log(user_role);
                
                $.post( "_setUserWorkData.php", 
                    { 
                        employeeId: employeeId, 
                        eDate: eDate,
                        hourIn: hourIn,
                        minIn: minIn,
                        hourOut: hourOut,
                        minOut: minOut,
                        comments: comments,
                        dayoff: dayoff,
                        hoursoff: hoursoff,
                        userrole: user_role
                    },
                    function(data) {
                        //console.log(data);
                        $("#username").html(data);
                    }); 
                
            }
            
            function setDefaultHours() {
                $("#t_hour_in").val(10);
                $("#t_min_in").val(7);
                $("#t_hour_out").val(17);
                $("#t_min_out").val(7);
            }
            
            
        </script>
        
    </body>
    
</html>