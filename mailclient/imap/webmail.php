<?php
class Webmail{

function getEmailBody($uid, $imap)
{
    $body = $this->get_part($imap, $uid, "TEXT/HTML");
    
    /*if ($body!="") {
        preg_match_all('/src="cid:(.*)"/Uims', $body, $matches);
        if(count($matches)) {
            $search = array();
            $replace = array();
            
            foreach($matches[1] as $match) {
                //echo "CID=" . $match . "<br/>";
                //$unique_filename = time().".".strtolower($this->attachments[$match]['subtype']);
                //file_put_contents("./uploads/$unique_filename", $this->attachments[$match]['data']);
                //$search[] = "src=\"cid:$match\"";
                //$replace[] = "src='".base_url()."/uploads/$unique_filename'";
                
            }
            
            $body = str_replace($search, $replace, $body);
            
        }
    }*/
    
    // if HTML body is empty, try getting text body
    if ($body == "") {
        $body = $this->get_part($imap, $uid, "TEXT/PLAIN");
    }
    
    return $body;
}





function get_mail_body($body_type = 'html')
{
    $mail_body = '';
    if($body_type == 'html'){
        $this->fetch();
        preg_match_all('/src="cid:(.*)"/Uims', $this->bodyHTML, $matches);
        if(count($matches)) {
            $search = array();
            $replace = array();
            foreach($matches[1] as $match) {
                $unique_filename = time().".".strtolower($this->attachments[$match]['subtype']);
                file_put_contents("./uploads/$unique_filename", $this->attachments[$match]['data']);
                $search[] = "src=\"cid:$match\"";
                $replace[] = "src='".base_url()."/uploads/$unique_filename'";
            }
            $this->bodyHTML = str_replace($search, $replace, $this->bodyHTML);
            $mail_body = $this->bodyHTML;
        }
    }else{
            $mail_body = $this->bodyPlain;
    }
    return $mail_body;
}







function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false)
{
    //echo $uid . "<br/>";
    if (!$structure) {
        $structure = imap_fetchstructure($imap, $uid, FT_UID); //FT_UID
    }
    if ($structure) {
        if ($mimetype == $this->get_mime_type($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }
            $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID); //FT_UID
            switch ($structure->encoding) {
                case 3:
                    return imap_base64($text);
                case 4:
                    return imap_qprint($text);
                default:
                    return $text;
            }
        }

        // multipart
        if ($structure->type == 1) {
            foreach ($structure->parts as $index => $subStruct) {
                $prefix = "";
                if ($partNumber) {
                    $prefix = $partNumber . ".";
                }
                $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                if ($data) {
                    return $data;
                }
            }
        }
    }
    return false;
}


function get_mime_type($structure)
{
    $primaryMimetype = ["TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];

    if ($structure->subtype) {
        return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
    }
    return "TEXT/PLAIN";
}


function extract_attachments($connection, $message_number, $options = 0) {

    $attachments = array();
    $structure = imap_fetchstructure($connection, $message_number, $options);
    
    /*echo "<pre>";
    var_dump($structure->parts);
    echo "</pre>";*/

    if(isset($structure->parts) && count($structure->parts)) {

        for($i = 0; $i < count($structure->parts); $i++) {
            
            //echo $structure->parts[$i]->type . "<br/>";

            $attachments[$i] = array(
                'is_attachment' => false,
                'filename' => '',
                'name' => '',
                'attachment' => ''
            );

            if($structure->parts[$i]->ifdparameters) {
                foreach($structure->parts[$i]->dparameters as $object) {
                    if(strtolower($object->attribute) == 'filename') {
                        $attachments[$i]['is_attachment'] = true;
                        $attachments[$i]['filename'] = $object->value;
                    }
                }
            }

            if($structure->parts[$i]->ifparameters) {
                foreach($structure->parts[$i]->parameters as $object) {
                    if(strtolower($object->attribute) == 'name') {
                        $attachments[$i]['is_attachment'] = true;
                        $attachments[$i]['name'] = $object->value;
                    }
                }
            }
            
            //inline
            /*if(isset($structure->parts[$i]->parts)) {
                //echo "part-part<br/>";
                foreach($structure->parts[$i]->parts as $part) {
                    if ($part->ifdparameters) {
                        foreach($part->dparameters as $object) {
                            if(strtolower($object->attribute) == 'filename') {
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['filename'] = $object->value;
                            }
                        }
                    }
                }
            }*/
            
            

            if($attachments[$i]['is_attachment'] || TRUE) {
                $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i+1, FT_UID );
                if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                }
                elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                }
            }

        }

    }

    return $attachments;

  }

  
  
}






/*
$email_message = new Email_message($imapstream, $uid);
$mailbody = $email_message->get_mail_body();*/



class Email_message {

    public $connection;
    public $messageNumber;    
    public $bodyHTML = '';
    public $bodyPlain = '';
    public $attachments;
    public $getAttachments = true;

    public function __construct($config_data = array()) {

        $this->connection = $config_data['connection'];
        $this->messageNumber = isset($config_data['message_no'])?$config_data['message_no']:1;
    }

    public function fetch() {

        $structure = imap_fetchstructure($this->connection, $this->messageNumber, FT_UID);
        if(!$structure) {
            return false;
        }
        else {
            if(isset($structure->parts)){
                $this->recurse($structure->parts);
            }
            return true;
        }

    }

    public function recurse($messageParts, $prefix = '', $index = 1, $fullPrefix = true) {

        foreach($messageParts as $part) {

            $partNumber = $prefix . $index;

            if($part->type == 0) {
                if($part->subtype == 'PLAIN') {
                    $this->bodyPlain .= $this->getPart($partNumber, $part->encoding);
                }
                else {
                    $this->bodyHTML .= $this->getPart($partNumber, $part->encoding);
                }
            }
            elseif($part->type == 2) {
                $msg = new Email_message(array('connection' =>$this->connection,'message_no'=>$this->messageNumber));
                $msg->getAttachments = $this->getAttachments;
                if(isset($part->parts)){
                    $msg->recurse($part->parts, $partNumber.'.', 0, false);
                }
                $this->attachments[] = array(
                    'type' => $part->type,
                    'subtype' => $part->subtype,
                    'filename' => '',
                    'data' => $msg,
                    'inline' => false,
                );
            }
            elseif(isset($part->parts)) {
                if($fullPrefix) {
                    $this->recurse($part->parts, $prefix.$index.'.');
                } else {
                    $this->recurse($part->parts, $prefix);
                }
            }
            elseif($part->type > 2) {
                if(isset($part->id)) {
                    $id = str_replace(array('<', '>'), '', $part->id);
                    $this->attachments[$id] = array(
                        'type' => $part->type,
                        'subtype' => $part->subtype,
                        'filename' => $this->getFilenameFromPart($part),
                        'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
                        'inline' => true,
                    );
                } else {
                    $this->attachments[] = array(
                        'type' => $part->type,
                        'subtype' => $part->subtype,
                        'filename' => $this->getFilenameFromPart($part),
                        'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
                        'inline' => false,
                    );
                }
            }

            $index++;

        }

    }

    function getPart($partNumber, $encoding) {

        $data = imap_fetchbody($this->connection, $this->messageNumber, $partNumber, FT_UID);
        switch($encoding) {
            case 0: return $data; // 7BIT
            case 1: return $data; // 8BIT
            case 2: return $data; // BINARY
            case 3: return base64_decode($data); // BASE64
            case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
            case 5: return $data; // OTHER
        }


    }

    function getFilenameFromPart($part) {

        $filename = '';

        if($part->ifdparameters) {
            foreach($part->dparameters as $object) {
                if(strtolower($object->attribute) == 'filename') {
                    $filename = $object->value;
                }
            }
        }

        if(!$filename && $part->ifparameters) {
            foreach($part->parameters as $object) {
                if(strtolower($object->attribute) == 'name') {
                    $filename = $object->value;
                }
            }
        }

        return $filename;

    }

    function get_mail_body($emailaccount = "default", $body_type = 'html')
    {
        $mail_body = '';
        $uid = $this->messageNumber;
        if($body_type == 'html'){
            $this->fetch();
            preg_match_all('/src="cid:(.*)"/Uims', $this->bodyHTML, $matches);
            if(count($matches)) {
                $search = array();
                $replace = array();
                $index = 1;
                foreach($matches[1] as $match) {
                    //$unique_filename = time().".".strtolower($this->attachments[$match]['subtype']);
                    $unique_filename = "inlineimage-$index".".".strtolower($this->attachments[$match]['subtype']);
                    
                    $target_dir = "emails/$emailaccount/$uid";
                    if (!is_dir($target_dir)) {
                      mkdir($target_dir);
                    }
                    
                    file_put_contents("emails/$emailaccount/$uid/$unique_filename", $this->attachments[$match]['data']);
                    $search[] = "src=\"cid:$match\"";
                    //$replace[] = "src='" . base_url() . "uploads/$unique_filename'";
                    $replace[] = "src='" . "https://crm.panelinios.gr/mailclient/emails/$emailaccount/$uid/$unique_filename'";
                    $index++;
                }
                $this->bodyHTML = str_replace($search, $replace, $this->bodyHTML);
                $mail_body = $this->bodyHTML;
            }
        }else{
                $mail_body = $this->bodyPlain;
        }
        return $mail_body;
    }
    
    function base_url() {
        return "https://crm.panelinios.gr/mailclient/";
    }

}
