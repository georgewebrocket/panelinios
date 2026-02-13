<?php


class arrayfunctions
{
    
    //Array function
    static function filter_by_value ($array, $index, $value){
        $i=0;
        $newarray = array();
        if(is_array($array) && count($array)>0) 
        {            
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] == $value){
                    $newarray[$i] = $array[$key];
                    $i++;
                }                
            }
            //return $newarray;
        }
        return $newarray;
    } 
    
}

class func
{
    static function str14toDate($str14, $delimiter="-", $locale = "GR") {
        if (strlen($str14)!=14) {
            return $str14;
        } 
        $YYYY = substr($str14, 0, 4);
        $MM = substr($str14, 4, 2);
        $DD = substr($str14, 6, 2);
        switch ($locale) {
            case "GR":
                return $DD.$delimiter.$MM.$delimiter.$YYYY;
                break;
            case "EN":
                return $YYYY.$delimiter.$MM.$delimiter.$DD;
                break;
            default:
        }
        
    }
    
    
    static function dateTo14str($val, $delimiter = array("/","-","."), $locale = "GR") {
        $delem = self::explode_by_array($delimiter, $val);
        switch ($locale) {
            case "GR":
                return $delem[2].$delem[1].$delem[0]."000000";
                break;
            case "EN":
                return $delem[2].$delem[0].$delem[1]."000000";
                break;
            default:
        }
    }
    
    
    static function explode_by_array($delim, $input) {
        $unidelim = $delim[0];
        $step_01 = str_replace($delim, $unidelim, $input); //Extra step to create a uniform value
        return explode($unidelim, $step_01);
    }
    
    
    static function format($val,$type,$locale="GR") {
        switch ($type) {
            case "DATE":
                return self::str14toDate($val, "/", $locale);
                break;
            case "CURRENCY":
                return self::nrToCurrency($val, $locale);
                break;
            case "YESNO":
                return self::yesno($val);
            case "IMAGE":
                return "<img src=\"".$val."\" width=\"100%\" height=\"auto\" />";
            case "IMAGE-POPUP":
                return "<a class=\"fancybox\" href=\"".$val."\"><img src=\"".$val."\" width=\"100%\" height=\"auto\" /></a>";    
            default :
                return $val;
        }
    }
    
    
    static function nrToCurrency($val,$locale="GR") {
        switch ($locale) {
            case "GR":
                //echo "VAL=".number_format($val, 2, ",", ".");
                return number_format($val, 2, ",", ".");
                break;
            case "EN":
                return number_format($val, 2, ".", ",");
                break;
            default :
        }
    }
    
    
    static function CurrencyToNr($val,$locale="GR") {
        $mystr = $val;
        switch ($locale) {
            case "GR":
                $mystr = str_replace(".", "", $val);
                $mystr = str_replace(",", ".", $val);
                return $mystr;
                break;
            case "EN":
                $mystr = str_replace(",", "", $val);
                return $mystr;
                break;
            default :
        }
    }
    
    
    static function vlookup($fieldname, $tablename, $criteria, $conn)
    {
        $ssql = "SELECT " . $fieldname . " FROM " . $tablename . " WHERE " . $criteria;	
        $all_rows = $conn->getRS($ssql);
        $iCount = count($all_rows);
        if ($iCount > 0) {
            return $all_rows[0][$fieldname];
        }
        else {
            return "";	
        }
    }
    
    
    static function yesno($val, $locale="GR")
    {
        switch ($locale) {
            case "GR":
                switch ($val){
                    case 1:
                        return "Ναι";
                        break;
                    case 2:
                        return "Όχι";
                        break;
                    default:
                        return "Όχι";
                        break;
                }
            case "EN":
                switch ($val){
                    case 1:
                        return "Υes";
                        break;
                    case 2:
                        return "Νo";
                        break;
                    default:
                        return "Νo";
                        break;
                }
        }
    }
    
    
    static function validateDate($date, $format = "", $locale="GR")
    {
        if($format == ""){
            switch ($locale) {
                case "GR":
                    $format = "d/m/Y";
                    break;
                case "EN":
                    $format = "Y/m/d";
                    break;

            }            
        }
        $d = DateTime::createFromFormat($format, $date);
        return var_dump($d && $d->format($format) == $date);
    }
    
    
    static function shortDescription($str,$length,$LR = "LEFT")
    {
        $myStr = $str;
        if (strlen($str) > $length){
            switch($LR){
                case "LEFT":
                    $myStr = substr($str,0,$length)." ...";
                    break;
                case "RIGHT":
                    $myStr = substr($str,-1,$length)." ...";
                    break;
            }
        }
        return $myStr;        
    }
    
    
    static function rsSum($rs,$col) {
        $res = 0;
        for ($i=0;$i<count($rs);$i++) {
            $res += $rs[$i][$col];
        }
        return $res;
    }
    
    
    static function countImpressionClick($myDB, $field, $compIds) {
        $arrCompIds = explode(",", $compIds);
        $activityDate = self::dateTo14str(date('d-m-Y'),array("/","-","."));
        foreach ($arrCompIds as $value) {
            $sql = "UPDATE company_activity SET ".$field." = ".$field." + 1 WHERE company = ".$value." AND activity_date = '".$activityDate."'";
            if($myDB->execSQL($sql) === 0){
                $myDB->execSQL("INSERT INTO company_activity (company, activity_date, ".$field.") VALUES (?,?,?)", 
                    array($value, $activityDate, 1));
            }
        }
    }
    
    
    static function mlng($key, $language, $lang="gr") {
        $res = "-";
        if (!$language[$lang][$key]) {
            $res = $key;
        } else {
            $res = $language[$lang][$key];
        }       
        return $res;
    }
    
    
    static function normURL($str) {
        $res = str_replace(" ", "-", $str);
		$res = str_replace("&", "-", $res);
		$res = str_replace("<", "-", $res);
		$res = str_replace(">", "-", $res);
        $res = mb_convert_case($res, MB_CASE_LOWER, "UTF-8"); 
        //$res = iconv('UTF-8', 'ASCII//TRANSLIT', $res);
        
        $normalizeChars = array('ά'=>'α', 'έ'=>'ε', 'ή'=>'η', 'ί'=>'ι', 'ό'=>'ο', 'ώ'=>'ω', 'ύ'=>'υ');
        $res = strtr($res, $normalizeChars);
        
        return $res;
    }
    
    
    static function GetDays($sStartDate, $sEndDate){  
        // Firstly, format the provided dates.  
        // This function works best with YYYY-MM-DD  
        // but other date formats will work thanks  
        // to strtotime().  
        $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));  
        $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));  

        // Start the variable off with the start date  
        $aDays[] = $sStartDate;  

        // Set a 'temp' variable, sCurrentDate, with  
        // the start date - before beginning the loop  
        $sCurrentDate = $sStartDate;  

        // While the current date is less than the end date  
        while($sCurrentDate < $sEndDate){  
          // Add a day to the current date  
          $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  

          // Add this new day to the aDays array  
          $aDays[] = $sCurrentDate;  
        }  

        // Once the loop has finished, return the  
        // array of days.  
        return $aDays;  
      }
     
      
    static function get_category_path($catId, $conn) {
        $sep = '-';
        $ssql = "SELECT T2.id".
        " FROM ( ".
            "SELECT ".
                "@r AS _id, ".
                "(SELECT @r := parent_id FROM categories WHERE id = _id) AS parent_id, ".
                "@l := @l + 1 AS lvl ".
            "FROM ".
                "(SELECT @r := ".$catId." , @l := 0) vars, ".
                "categories h ".
            "WHERE @r <> 0) T1 ".
        "JOIN categories T2 ".
        "ON T1._id = T2.id ".
        "ORDER BY T1.lvl DESC ";
        $result = $conn->getRS($ssql);
        $arrPath = array();
        for($i=0;$i<count($result);$i++){
            array_push($arrPath, $result[$i]['id']); 
        }
        $strPath = $sep.implode($sep, $arrPath).$sep;
        return $strPath;
    }
    
    
    static function clearHtml($str) {
        $str = str_replace("<p>", "", $str);
        $str = str_replace("</p>", "", $str);
        $str = str_replace("<span>", "", $str);
        $str = str_replace("</span>", "", $str);
        $str = str_replace("<br/>", "", $str);
        return $str;
    }
    
    
    static function active($pagecode, $linkcode) {
        if ($pagecode==$linkcode) {
            return "active";
        }
        else {
            return "";
        }
    }
    
    static function mb_str_split($string,$string_length=1,$charset='utf-8') {
        if(mb_strlen($string,$charset)>$string_length || !$string_length) {
        do {
        $c = mb_strlen($string,$charset);
        $parts[] = mb_substr($string,0,$string_length,$charset);
        $string = mb_substr($string,$string_length,$c-$string_length,$charset);
        }while(!empty($string));
        } else {
        $parts = array($string);
        }
        return $parts;
    }
    
    
    static function convertToGreeklish($str) {
        
        $str = str_replace("ευ", "ev", $str);
        $str = str_replace("ου", "ou", $str);
        $str = str_replace("αι", "e", $str);
        $str = str_replace("ει", "i", $str);
        $str = str_replace("οι", "i", $str);
        $str = str_replace("αυ", "af", $str);
        
        
        $ar = self::mb_str_split($str);
        
        for ($i = 0; $i < count($ar); $i++) {
            switch (mb_strtolower($ar[$i])) {
                case "α": $ar[$i] = "a"; break;
                case "ά": $ar[$i] = "a"; break;
                case "β": $ar[$i] = "b"; break;
                case "γ": $ar[$i] = "g"; break;
                case "δ": $ar[$i] = "d"; break;
                case "ε": $ar[$i] = "e"; break;
                case "έ": $ar[$i] = "e"; break;
                case "ζ": $ar[$i] = "z"; break;
                case "η": $ar[$i] = "i"; break;
                case "ή": $ar[$i] = "i"; break;
                case "θ": $ar[$i] = "th"; break;
                case "ι": $ar[$i] = "i"; break;
                case "ί": $ar[$i] = "i"; break;
                case "ϊ": $ar[$i] = "i"; break;
                case "ΐ": $ar[$i] = "i"; break;
                case "κ": $ar[$i] = "k"; break;
                case "λ": $ar[$i] = "l"; break;
                case "μ": $ar[$i] = "m"; break;
                case "ν": $ar[$i] = "n"; break;
                case "ξ": $ar[$i] = "ks"; break;
                case "ο": $ar[$i] = "o"; break;
                case "ό": $ar[$i] = "o"; break;
                case "π": $ar[$i] = "p"; break;
                case "ρ": $ar[$i] = "r"; break;
                case "σ": $ar[$i] = "s"; break;
                case "ς": $ar[$i] = "s"; break;
                case "τ": $ar[$i] = "t"; break;
                case "υ": $ar[$i] = "i"; break;
                case "ύ": $ar[$i] = "i"; break;
                case "ϋ": $ar[$i] = "i"; break;
                case "φ": $ar[$i] = "f"; break;
                case "χ": $ar[$i] = "h"; break;
                case "ψ": $ar[$i] = "ps"; break;
                case "ω": $ar[$i] = "o"; break;
                case "ώ": $ar[$i] = "o"; break;
                case " ": $ar[$i] = "-"; break;           
                    
            }
        }
        return implode("", $ar);
        
    }
     
    
    
    static function getSeoURL($company) {
        
        //$company = new companies($myconn, $_id);
        
        $seodescr = $company->get_url_rewrite_gr();
        if ($seodescr=="") { $seodescr = $company->get_profession_descr() . " " 
                . $company->get_city_descr2(); }
        return self::convertToGreeklish($seodescr);
        
        
    }
    
    
}


class MyUtils
{	
	static function sanit($unsafe_variable) {
		return mysql_real_escape_string($unsafe_variable);
	}
	
	//pros8etei kritiria se ena sql string
    static function AddCriteria(&$ssql, $criteria)
    {
        if (strpos($ssql, "WHERE")>0)
        {
            $ssql .= " AND (" . $criteria . ")";
        }
        else 
        {
            $ssql .= " WHERE (" . $criteria . ")";
        }
    }
    
    //sygkrinei 2 strings me ids ta opoia xwrizontai me sygkekrimeno tropo px me ,
    //kai an kapoio stoixeio tou prwtou string yparxei sto deftero tote epistrefei true
    //alliws false
    //paradeigma: CompareIds_("1,22","-[33][22][2]",",","[","]") => TRUE
    static function CompareIds_($Ids1, $Ids2, $delimiter1, $char1, $char2)
    {
        $arIds1 = explode($delimiter, $Ids1);
        
        for ($i=0;$i<count($arIds1);$i++)
        {
            if (strpos($Ids2, $char1 . $arIds1[$i] . $char2)>0)
            {
                return TRUE;
            }
        }
        return FALSE;        
    }
    
    static function CompareIds($Ids1, $Ids2)
    {
        return CompareIds_($Ids1, $Ids2, ",", "[", "]");
    }
    
    //Array function
    static function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return $newarray;
    } 
    
    //Array function
    static function filter_by_value_new_key ($array, $index, $value){
        $i=0;
        if(is_array($array) && count($array)>0) 
        {            
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] == $value){
                    $newarray[$i] = $array[$key];
                    $i++;
                }                
            }
          }
      return $newarray;
    } 
    
    //Array function
    static function filter_by_criteria ($array, $index, $value, $criteria) {
        $i=0;
        $condition = FALSE;
        if(is_array($array) && count($array)>0) 
        {            
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                switch ($criteria) {
                    case '=':
                        $condition = ($temp[$key] == $value);
                        break;
                    case '==':
                        $condition = ($temp[$key] == $value);
                        break;
                    case '>':
                        $condition = ($temp[$key] > $value);
                        break;
                    case '>=':
                        $condition = ($temp[$key] >= $value);
                        break;
                    case '<':
                        $condition = ($temp[$key] < $value);
                        break;
                    case '<=':
                        $condition = ($temp[$key] <= $value);
                        break;
                    case '!=':
                        $condition = ($temp[$key] != $value);
                        break;

                    default:
                        break;
                }
                
                if ($condition){
                    $newarray[$i] = $array[$key];
                    $i++;
                }                
            }
          }
      return $newarray;
    }
        
    
    //Array function
    static function filter_by_ids ($array, $index, $values){
        $i=0;
        if(is_array($array) && count($array)>0) 
        {            
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                //echo strpos($values,"[$temp[$key]]") . ",";
                if (strpos($values,"[$temp[$key]]")>0) {
                    $newarray[$i] = $array[$key];
                    $i++;
                }                
            }
          }
      return $newarray;
    }
    
    static function get_ids($array, $index, $unionstr) {
        $my_ids = "";
		
		/*foreach($array as $item){
			echo "array".$item[$index]."</br>";
			$my_ids = $this->myconcat($my_ids, $item[$index], $unionstr);	
		}*/
		
		for ($i=0;$i<count($array);$i++) {
			$my_ids = $this->myconcat($my_ids, $array[$i][$index], $unionstr);
        }
		return $my_ids;
    }
	
	static function myconcat($firststr, $secondstr, $unionstr)
	{	
		if ($firststr=="") {
			return $secondstr;
		}
		else
		{
			return $firststr . $unionstr . $secondstr;
		}
		
	}
	
	static function myconcat2($firststr, $secondstr, $unionstr)
	{	
		if ($secondstr=="") {
			return $firststr;
		}
		else
		{
			return $firststr . $unionstr . $secondstr;
		}
		
	}

    //convert string to time format
    //px 3,29 => 3:29
    static function convert_totime($mystr) {
        $mystr = str_replace(',', ':', $mystr); 
        $mystr = str_replace('.', ':', $mystr);
        //...
        return $mystr;
    }
    
    static function convert_to_secs($myduration) {
        $myar = explode(":", $myduration);        
        $mymins = (int)$myar[0];
        $mysecs = (int)$myar[1];
        $mytime = $mymins * 60 + $mysecs;
        return $mytime;
    }
    
    static function vpn_path($mypath) {
        $myStr = str_replace(MyConfig::$local_mp3_str, MyConfig::$vpn_mp3_str, $mypath);
        $myStr = str_replace(MyConfig::$local_mp3_str2, MyConfig::$vpn_mp3_str, $myStr);
        $myStr = str_replace("\\", "/", $myStr);
        $myStr = urlencode($myStr);
        return $myStr;
    }
	
	static function curPageURL() {
	 	$pageURL = 'http';
	 	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 	$pageURL .= "://";
	 	if ($_SERVER["SERVER_PORT"] != "80") {
	  		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 	} else {
	  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 	}
	 	return $pageURL;
	}
	
	static function get_img_src($img)
	{
		$pos1 = strpos($img, "src=\"");
		$pos2 = strpos($img, "\"", $pos1+5);
		return substr($img, $pos1+5, $pos2-$pos1-5);
	}	
	
	 static function GetNode($SearchInto, $NodeTag) {
        $ipos1 = strpos($SearchInto, '<' . $NodeTag . '>');
        $ipos2 = strpos($SearchInto, '</' . $NodeTag . '>');
        $istart = $ipos1 + strlen($NodeTag) + 2;
        $ilength = $ipos2 - $istart;
        return substr($SearchInto, $istart, $ilength);
    }
	
    
}


?>