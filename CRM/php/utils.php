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
    
    
    //returns one element // array
    static function findInArray($rs, $fields, $vals) {
        
        for ($i = 0; $i < count($rs); $i++) {
            $condition = true;
            for ($k = 0; $k < count($fields); $k++) {
                $myCondition = $rs[$i][$fields[$k]] == $vals[$k];
                $condition = $condition && $myCondition;      
            }
            if ($condition) {
                return $rs[$i];                
            }    
        }
        return false;
    }
    
    //returns one element // array
    static function findInArraySum($rs, $fields, $vals, $fieldSum) {
        $sum = 0;
        for ($i = 0; $i < count($rs); $i++) {
            $condition = true;
            for ($k = 0; $k < count($fields); $k++) {
                $myCondition = $rs[$i][$fields[$k]] == $vals[$k];
                $condition = $condition && $myCondition;      
            }
            if ($condition) {
                $sum += $rs[$i][$fieldSum];
                
            }    
        }
        return $sum;
    }
    
}

class func
{
    
    /**
     * sendIndexingRequest
     *
     * Sends a POST request to the Google Indexing webservice (indexing.php) with the given API key
     * and URL. Returns an associative array with 'httpStatus', 'response', and 'error' keys.
     *
     * @param string $endpoint   Full URL to the indexing.php endpoint (e.g. "https://example.com/indexing.php")
     * @param string $apiKey     API key (must match API_KEY defined in indexing.php)
     * @param string $urlToIndex The URL you want to submit to Google’s Indexing API
     *
     * @return array{
     *   httpStatus: int,
     *   response: array|string,
     *   error: string|null
     * }
     */
    static function sendIndexingRequest($endpoint, $apiKey, $urlToIndex)
    {
        // 1) Build JSON payload
        $payload = json_encode(['url' => $urlToIndex]);
        if ($payload === false) {
            return [
                'httpStatus' => 0,
                'response'   => '',
                'error'      => 'Failed to encode JSON payload: ' . json_last_error_msg(),
            ];
        }

        // 2) Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // 3) Set headers
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // 4) Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 5) (Optional) Skip SSL verification for dev—comment out in production
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 6) Execute and capture response
        $rawResponse = curl_exec($ch);
        $curlErr     = curl_error($ch);
        $httpStatus  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($rawResponse === false) {
            return [
                'httpStatus' => 0,
                'response'   => '',
                'error'      => 'cURL error: ' . $curlErr,
            ];
        }

        // 7) Attempt to decode JSON
        $decoded = json_decode($rawResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If JSON decode fails, return raw string in 'response'
            return [
                'httpStatus' => $httpStatus,
                'response'   => $rawResponse,
                'error'      => 'Failed to decode JSON response: ' . json_last_error_msg(),
            ];
        }

        // 8) All good—return decoded JSON and no error
        return [
            'httpStatus' => $httpStatus,
            'response'   => $decoded,
            'error'      => null,
        ];
    }


    static function greekToGreeklish($text, $word_delimiter="-") {
        // Define an array mapping Greek characters to Greeklish equivalents
        $greeklishMap = [
            'α' => 'a', 'β' => 'v', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z',
            'η' => 'i', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm',
            'ν' => 'n', 'ξ' => 'x', 'ο' => 'o', 'π' => 'p', 'ρ' => 'r', 'σ' => 's',
            'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o',
            'ά' => 'a', 'έ' => 'e', 'ή' => 'i', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y',
            'ώ' => 'o', 'ς' => 's', 'ϊ' => 'i', 'ϋ' => 'y', 'ΐ' => 'i', 'ΰ' => 'y',
            'Α' => 'A', 'Β' => 'V', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z',
            'Η' => 'I', 'Θ' => 'TH', 'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M',
            'Ν' => 'N', 'Ξ' => 'X', 'Ο' => 'O', 'Π' => 'P', 'Ρ' => 'R', 'Σ' => 'S',
            'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'CH', 'Ψ' => 'PS', 'Ω' => 'O',
            'Ά' => 'A', 'Έ' => 'E', 'Ή' => 'I', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y',
            'Ώ' => 'O'
        ];
    
        // Replace Greek characters with their Greeklish equivalents
        $text = strtr($text, $greeklishMap);
    
        // Convert the text to lowercase
        $text = mb_strtolower($text, 'UTF-8');
    
        // Remove any remaining non-alphanumeric characters (excluding dashes)
        //$text = preg_replace('/[^a-z0-9\-]/u', '-', $text);
        $text = preg_replace('/[^a-z0-9\- ]+/iu', '-', $text);
    
        // Remove multiple dashes
        $text = preg_replace('/-+/', '-', $text);
    
        // Trim dashes from the beginning and end of the text
        $text = trim($text, '-');

        $text = str_replace(" ", $word_delimiter, $text);
    
        return $text;
    }
    
    
    static function curl_post($url, $data, $headers= array(), $method="POST") {
    
        $ch = curl_init($url);
        
        if ($method=="POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
        }    
    
    
        $jsonData = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
        $response = curl_exec($ch);
        
        curl_close ($ch);
        
        return $response;    
          
    }
    
    
    static function curl_file_get_contents($url) {
        // Initialize a cURL session
        $ch = curl_init();
    
        // Set the URL
        curl_setopt($ch, CURLOPT_URL, $url);
    
        // Set the option to return the response as a string instead of outputting it directly
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Set additional options (optional)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL certificate verification (only if necessary)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout for the request
    
        // Execute the request
        $data = curl_exec($ch);
    
        // Check for errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            return false;
        }
    
        // Close the cURL session
        curl_close($ch);
    
        // Return the data
        return $data;
    }
    
    
    
    static function getCompanyPId($company) {
        return $company->get_catalogueid() * 2 + 7128;
    }
    
    static function getPId($catalogueid) {
        return $catalogueid * 2 + 7128;
    }
    
    static function getCatalogueidFromPid($pId) {
        return ($pId-7128)/2;
    }
    
    
    static function mb_str_pad(
        $input,
        $pad_length,
        $pad_string=" ",
        $pad_style=STR_PAD_RIGHT,
        $encoding="UTF-8")
      {
          return str_pad(
            $input,
            strlen($input)-mb_strlen($input,$encoding)+$pad_length,
            $pad_string,
            $pad_style);
      }
      
    static function substr_unicode($str, $s, $l = null) {
        return join("", array_slice(
            preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
    }
    
    static function fixedLength($str, $length, $pad = FALSE, $padChar = " ", $padType = STR_PAD_RIGHT) {
        $str = trim($str);
        $str = self::substr_unicode($str, 0, $length);
        if ($pad) {
            return self::mb_str_pad($str, $length, $padChar, $padType);
        }
        else {
            return $str;
        }
        
        
    }
    
    static function days($x) {
        if (get_class($x) != 'DateTime') {
            return false;
        }

        $y = $x->format('Y') - 1;
        $days = $y * 365;
        $z = (int)($y / 4);
        $days += $z;
        $z = (int)($y / 100);
        $days -= $z;
        $z = (int)($y / 400);
        $days += $z;
        $days += $x->format('z');

        return $days;
    }
    
    
    static function nrToHours($nr) {
        return floor($nr) . ":" . 
            str_pad(round(($nr-floor($nr)) * 60,0), 2, "0", STR_PAD_LEFT);
    }
       
    
    
    static function XML2Array(SimpleXMLElement $parent)
    {
            $array = array();

            foreach ($parent as $name => $element) {
                    ($node = & $array[$name])
                    && (1 === count($node) ? $node = array($node) : 1)
                    && $node = & $node[];

                    $node = $element->count() ? XML2Array($element) : trim($element);
            }

            return $array;
    }

    static function DATE_To14str($DATE) {
    return  date("Ymd",$DATE);
}
    
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
    
    static function str14toDateTime($str14, $delimiter="-", $locale = "GR") {
        if (strlen($str14)!=14) {
            return $str14;
        } 
        $YYYY = substr($str14, 0, 4);
        $MM = substr($str14, 4, 2);
        $DD = substr($str14, 6, 2);
        $HH = substr($str14, 8, 2);
        $mm = substr($str14, 10, 2);
        $ss = substr($str14, 12, 2);
        switch ($locale) {
            case "GR":
                return $DD.$delimiter.$MM.$delimiter.$YYYY." ".$HH.":".$mm.":"."".$ss;
                break;
            case "EN":
                return $YYYY.$delimiter.$MM.$delimiter.$DD." ".$HH.":".$mm.":"."".$ss;
                break;
            default:
        }
    }
    
    static function str14toDateDM($str14, $delimiter="/", $locale = "GR") {
        if (strlen($str14)!=14) {
            return $str14;
        } 
        $YYYY = substr($str14, 0, 4);
        $MM = substr($str14, 4, 2);
        $DD = substr($str14, 6, 2);
        switch ($locale) {
            case "GR":
                return $DD.$delimiter.$MM;
                break;
            case "EN":
                return $MM.$delimiter.$DD;
                break;
            default:
        }
        
    }
    
    
    static function str14toDateDMYY($str14, $delimiter="/", $locale = "GR") {
        if (strlen($str14)!=14) {
            return $str14;
        } 
        $YY = substr($str14, 2, 2);
        $MM = substr($str14, 4, 2);
        $DD = substr($str14, 6, 2);
        switch ($locale) {
            case "GR":
                return $DD.$delimiter.$MM.$delimiter.$YY;
                break;
            case "EN":
                return $MM.$delimiter.$DD.$delimiter.$YY;
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
            case "DATE":
                return $delem[0].$delem[1].$delem[2]."000000";
                break;
            default:
        }
    }


    static function grdate_to_date($grdate) {
        $ar = explode("/", $grdate);
        return $ar[2] . "-" . $ar[1] . "-" . $ar[0];
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
            case "YESNOHASCONTENT":
                return self::yesno($val, "CUSTOM", array("......",""));
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
            $ar = explode(" AS ", $fieldname);
            if (count($ar)>1) {
                $fieldname = $ar[1];
            }
            return $all_rows[0][$fieldname];
        }
        else {
            return "";	
        }
    }
    
    static function vlookupRS($fieldname, $rs, $idval) {
        for ($i = 0; $i < count($rs); $i++) {
            if ($rs[$i]['id'] == $idval) {
                return $rs[$i][$fieldname];
            }
        }
    }
    
    static function yesno($val, $locale="GR", $arrayYN = NULL)
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
            case "CUSTOM":
                        switch ($val){
                    case 1:
                        return $arrayYN[0];
                        break;
                    case 2:
                        return $arrayYN[1];
                        break;
                    default:
                        return "";
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
    
    static function get_category_path($catId, $conn) {
        $sep = '-';
        $ssql = "SELECT T2.id".
        " FROM ( ".
            "SELECT ".
                "@r AS _id, ".
                "(SELECT @r := parentid FROM CATEGORIES WHERE id = _id) AS parentid, ".
                "@l := @l + 1 AS lvl ".
            "FROM ".
                "(SELECT @r := ".$catId." , @l := 0) vars, ".
                "CATEGORIES h ".
            "WHERE @r <> 0) T1 ".
        "JOIN CATEGORIES T2 ".
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
    
    static function ConcatSpecial($str1,$str2,$delimiter) {
        if ($str1=="") {
            return $str2;
        }
        else {
            return $str1 . $delimiter . $str2;
        }
    }
    
    //2015-12-31 => 31/12
    static function DateToShortDate($str) {
        $Y = substr($str, 0, 4);
        $M = substr($str, 5, 2);
        $D = substr($str, 8, 2);
        return $D."/".$M;
    }        
    
    static function digits($str) {
        $str = str_replace(" ", "", $str);
        $str = str_replace("-", "", $str);
        $str = str_replace("/", "", $str);
        return $str;
    }
    
    static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    static function postData($url, $data) {
        $data['username'] = conn1::$username;
        $data['password'] = conn1::$password;
                
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        $obj = json_decode($result);
        $rs = (array) $obj[0];
        return $rs;
    }
    
    
    static function postDataWithCurl($url, $data) {
        //$data['username'] = conn1::$username;
        //$data['password'] = conn1::$password;
                
        $myvars = http_build_query($data);
        //echo $myvars;
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        return $response;
    }
    
    static function postDataWithCurlFull($url, $data, $headers) {
        $myvars = http_build_query($data);
        //echo $myvars;
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        
        curl_close ($ch);
        
        return $response;
    }
    
    
    
    static function cUrlGetData($url, $post_fields = null, $headers = null) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($post_fields && !empty($post_fields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        if ($headers && !empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $data;
    }
    
    
    
    
    
    static function curlPostData($url, $data) {
        $myvars = http_build_query($data);
        echo $myvars;
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        return $response;
    }
    
    static function myPostData($url, $data) {
        $data['username'] = conn1::$username;
        $data['password'] = conn1::$password;
        $myvars = http_build_query($data);
        $myUrl = $url . "?" . $myvars;
        $result = file_get_contents($myUrl);
        $obj = json_decode($result);
        $rs = (array) $obj[0];
        return $rs;
    }
    
}


class company_change 
{
    
    protected $userid, $companyid;
    protected $changes = array();
    protected $dbo;

    public function __construct($companyid, $userid, $dbo) {
        $this->userid = $userid;
        $this->companyid = $companyid;
        $this->dbo = $dbo;
        //$this->$changes = array();
    }
    
    public function addChange($fieldName, $val1, $val2) {
        if ($val1!=$val2) {
            array_push($this->changes, 
                array("fieldname" => $fieldName, "val1" => $val1, "val2" => $val2));
        }
    }
    
    public function commitChanges() {
        foreach ($this->changes as $change) {
            $companyChange = new COMPANY_CHANGES($this->dbo, 0);
            $companyChange->set_companyid($this->companyid);
            $companyChange->set_fieldname($change['fieldname']);
            $companyChange->set_val1($change['val1']);
            $companyChange->set_val2($change['val2']);
            $companyChange->set_userid($this->userid);
            $companyChange->set_cdatetime(date("YmdHis"));
            $companyChange->Savedata();            
        }
    }
    
    
    
}

class fn
{
    
    static function noSpaces($str) {
        return str_replace(" ", "", $str);
    }
    
    static function ulog($action, $db1) {
        $userid = $_SESSION['user_id'];
        $username =  $_SESSION['user_fullname'];
        date_default_timezone_set('Europe/Athens');
        
        $userlog = new USERLOG($db1, 0);
        $userlog->set_userid($userid);
        $userlog->set_username($username);
        $userlog->set_action($action);
        $userlog->set_ipaddress($_SERVER['REMOTE_ADDR']);
        $userlog->set_ipaddress2($_SERVER['HTTP_X_FORWARDED_FOR']);
        //$_SERVER['HTTP_X_FORWARDED_FOR']
        $userlog->set_uldatetime(date("YmdHis"));
        $userlog->set_comment(date("d/m/Y H.i.s"));
        $userlog->Savedata();
        
    }
    
    
}














class CompanyUtilities
{
    /**
     * Δημιουργία SEO URL για την εταιρεία
     * 
     * @param array $company Δεδομένα εταιρείας
     * @return string
     */
    public static function getSeoURL($company)
    {
        // Έλεγχος αν υπάρχει ήδη SEO URL
        if (!empty($company['url_rewrite_gr'])) {
            return $company['url_rewrite_gr'];
        }
        
        // Δημιουργία URL από το όνομα, επάγγελμα και πόλη
        $companyName = $company['company_name_gr'] ?? '';
        $profession = $company['profession_description'] ?? '';
        $cityShort = $company['city_short'] ?? '';
        
        // Καθαρισμός των χαρακτήρων ΠΡΙΝ τη δημιουργία του URL
        $companyName = self::cleanString($companyName);
        $profession = self::cleanString($profession);
        $cityShort = self::cleanString($cityShort);
        
        // Δημιουργία του SEO URL από τα δεδομένα
        $seoUrl = $profession . "_" . $cityShort . "_" . trim($companyName);
        
        // Κανονικοποίηση και μετατροπή σε greeklish
        return self::convertToGreeklish(self::normURL($seoUrl));
    }
    
    /**
     * Καθαρισμός προβληματικών χαρακτήρων από string
     * 
     * @param string $text
     * @return string
     */
    private static function cleanString($text)
    {
        // ΠΡΩΤΑ: Decode HTML entities (για &amp; -> &)
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // ΔΕΥΤΕΡΑ: Αντικατάσταση του & με "και" (όλες οι παραλλαγές)
        $text = str_replace([' amp ', 'amp', '&'], ' και ', $text);

        // Καθαρισμός διπλών "και"
        $text = str_replace(' και και ', ' και ', $text);
        
        // ΤΡΙΤΑ: Αφαίρεση των άλλων προβληματικών χαρακτήρων
        $text = str_replace(['-', '/', '\\', '+', '=', '?', '#', '%', '|', '"', "'", '(', ')', '[', ']', '{', '}'], ' ', $text);
        
        // Αφαίρεση πολλαπλών κενών
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Αφαίρεση κενών από αρχή και τέλος
        return trim($text);
    }


   
    /**
     * Μετατροπή κειμένου σε slug (URL-friendly)
     * 
     * @param string $text
     * @return string
     */
    public static function slugify($text)
    {
        // Μετατροπή σε πεζά και αφαίρεση κενών από την αρχή και το τέλος
        $text = mb_strtolower(trim($text));
        
        // Αντικατάσταση τονισμένων χαρακτήρων
        $text = str_replace(
            ['ά', 'έ', 'ή', 'ί', 'ό', 'ύ', 'ώ', 'ϊ', 'ΐ', 'ϋ'],
            ['α', 'ε', 'η', 'ι', 'ο', 'υ', 'ω', 'ι', 'ι', 'υ'],
            $text
        );
        
        // Μετατροπή ελληνικών σε λατινικούς χαρακτήρες
        $greekToLatin = [
            'α' => 'a', 'β' => 'v', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e',
            'ζ' => 'z', 'η' => 'i', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k',
            'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'ks', 'ο' => 'o',
            'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'ς' => 's', 'τ' => 't',
            'υ' => 'y', 'φ' => 'f', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o'
        ];
        $text = str_replace(array_keys($greekToLatin), array_values($greekToLatin), $text);
        
        // Αντικατάσταση ειδικών χαρακτήρων με κενά
        $text = preg_replace('/[^a-z0-9\s-]/', ' ', $text);
        
        // Αντικατάσταση κενών με παύλες
        $text = preg_replace('/[\s-]+/', '_', $text);
        
        // Αφαίρεση παυλών από την αρχή και το τέλος
        $text = trim($text, '-_');
        
        return $text;
    }
    
    /**
     * Κανονικοποίηση URL
     * 
     * @param string $str
     * @return string
     */
    public static function normURL($str)
    {
        // ΠΡΩΤΑ: Decode HTML entities (για &amp; -> &)
        $res = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        
        // ΔΕΥΤΕΡΑ: Αντικατάσταση του & με "και" (όλες οι παραλλαγές)
        $res = str_replace([" amp ", "amp", "&"], " και ", $res);

        // Καθαρισμός διπλών "και"
        $res = str_replace(" και και ", " και ", $res);

        // ΤΡΙΤΑ: Αφαίρεση των άλλων προβληματικών χαρακτήρων
        $res = str_replace(["-", "/", "\\", "+", "=", "?", "#", "%", "|", '"', "'", "(", ")", "[", "]", "{", "}"], "_", $res);
        
        // Αντικατάσταση κενών με underscore
        $res = str_replace(" ", "_", $res);
        
        // Μετατροπή σε πεζά
        $res = mb_convert_case($res, MB_CASE_LOWER, "UTF-8");
        
        // Αντικατάσταση τονισμένων χαρακτήρων
        $normalizeChars = [
            'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 
            'ό' => 'ο', 'ώ' => 'ω', 'ύ' => 'υ'
        ];
        
        $res = strtr($res, $normalizeChars);
        
        // Αφαίρεση διπλών underscore
        $res = preg_replace('/_+/', '_', $res);
        
        // Αφαίρεση underscore από αρχή και τέλος
        $res = trim($res, '_');
        
        return $res;
    }


    /**
     * Μετατροπή ελληνικών σε greeklish
     * 
     * @param string $str
     * @return string
     */
    public static function convertToGreeklish($str)
    {
        // Αντικατάσταση διφθόγγων
        $str = str_replace("ευ", "eu", $str);
        $str = str_replace("ου", "ou", $str);
        $str = str_replace("αυ", "au", $str);
        $str = str_replace("ντ", "nt", $str);
        $str = str_replace("μπ", "mp", $str);
        
        // Μετατροπή χαρακτήρων
        $greekToLatin = [
            'α' => 'a', 'β' => 'v', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e',
            'ζ' => 'z', 'η' => 'i', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k',
            'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'ks', 'ο' => 'o',
            'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'ς' => 's', 'τ' => 't',
            'υ' => 'y', 'φ' => 'f', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o',
            'ϊ' => 'i', 'ΐ' => 'i', 'ϋ' => 'y', ' ' => '_'
        ];
        
        $result = '';
        $length = mb_strlen($str, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_strtolower(mb_substr($str, $i, 1, 'UTF-8'), 'UTF-8');
            $result .= $greekToLatin[$char] ?? $char;
        }
        
        // Καθαρισμός τελικού αποτελέσματος
        $result = preg_replace('/[^a-z0-9_]/', '_', $result);
        $result = preg_replace('/_+/', '_', $result);
        $result = trim($result, '_');
        
        return $result;
    }
}
