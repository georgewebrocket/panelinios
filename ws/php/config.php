<?php

class conn1
{
    
    //epagelma_panelinios_site
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';   
    
}

class conn2
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panel_crm;charset=utf8';
    static $username = 'epagelma_panel_user';
    static $password = 'SxTe@V3d_Eb@';
        

}

class app
{
    static $tprefix = "gpdm_";
    static $host = "https://www.panelinios.gr";
}

define("DELIM", "[/$$/]");
define("HOST", "https://www.panelinios.gr");