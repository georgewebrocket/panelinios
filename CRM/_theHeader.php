<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
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

<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

<link href="css/tableexport.css" rel="stylesheet" type="text/css">
<script src="js/FileSaver.min.js"></script>
<script src="js/Blob.min.js"></script>
<script src="js/xls.core.min.js"></script>
<script src="js/tableexport.js"></script>


<script>
$(document).ready(function() {	
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 800 });
    $("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });

    $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
});
</script>

<style>
    #grid { max-width: 1000px;}
</style>

<?php if (isset($myStyle)) { echo $myStyle; } ?>

</head>
<body>
    
<?php include "blocks/header.php"; ?>
<?php include "blocks/menu.php"; ?>

<!--<div style="clear:both"></div>-->

<div class="main">
        
