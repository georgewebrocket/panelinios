<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);


require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/utils.php');
require_once('inc.php');

class connSite
{    
    //site
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
    
}
$dbSite = new DB(connSite::$connstr,connSite::$username,connSite::$password); 

$userid = $_SESSION['user_id'];

date_default_timezone_set('Europe/Athens');

$id = $_GET['id'];
$company = new COMPANIES($db1, $id);
$company_name = $company->get_companyname();
$profession = func::vlookup("description", "PROFESSIONS", "id=".$company->get_profession(), $db1);
$crm_text = $company->get_FullDescription();
$area = func::vlookup("description", "AREAS", "id=".$company->get_area(), $db1);


$online_id = $company->get_catalogueid();
$company_site = new companies($dbSite, $online_id);

$categories = "";
$sql = "SELECT categories.description_gr FROM categories INNER JOIN `company_categories` ON categories.id=company_categories.category_id INNER JOIN companies ON companies.id = company_categories.company_id WHERE companies.id=$online_id";
$rsCategories = $dbSite->getRS($sql);
if ($rsCategories) {
    $ar_cat = [];
    foreach ($rsCategories as $category) {
        $ar_cat[] = $category['description_gr'];
    }
    $categories = implode(", ",$ar_cat);
}
$cat_prompt = $categories!=""? "- Extra categories: $categories": "";


$tags = "";
$sql = "SELECT tags.description FROM tags INNER JOIN `company_tags` ON tags.id=company_tags.tagid INNER JOIN companies ON companies.id = company_tags.companyid WHERE companies.id=$online_id";
$rsTags = $dbSite->getRS($sql);
if ($rsTags) {
    $ar_tag = [];
    foreach ($rsTags as $tag) {
        $ar_tag[] = $tag['description'];
    }
    $tags = implode(", ",$ar_tag);
}
$tag_prompt = $tags!=""? "- Keywords: $tags": "";

$categories_prompt = $categories!=""? "- Categories: $categories": "";


$ai_text = "";
$ai_msg = "";

if (isset($_REQUEST['ai'])) {

    $crm_text = $_REQUEST['crm-text'];
    $crm_text_clean = strip_tags($crm_text);

    $instructions = "You are an expert SEO article writer";

    $prompt = <<<PROMPT

    Write an SEO-friendly article in Greek language of at least 500 words (not less), showcasing the company in a yellow-page directory. Be verbose, descriptive, and include rich content.

    
    You are given
    - Company name: "$company_name".
    - Profession: "$profession"
    $categories_prompt
    - CRM Description: "$crm_text_clean".
    $tag_prompt

    Ensure the content includes:
    - General company overview and location ({$area}).
    - Full description of services and categories.
    - Benefits of choosing this company over others in {$area}.
    - What makes the company stand out.
    - A paragraph with a strong call to action.
    - Naturally include the provided keywords for SEO purposes.
    
    Ensure the final output has more than 500 words. If needed, elaborate further to meet this requirement.
    If the generated content is less than 500 words, elaborate more on the services and the local presence in {$area}.
    Aim for a word count of 550–600 to ensure full detail.

    Guidelines:
    • Do not return Markdown; only use valid HTML. 
    • Do not start with ```html 
    • Use `<p>` for paragraphs and `<ul>/<li>` for short bullet points.  
    • Don’t create an `<h1>` tag or `<h2>` tags — structure with `<h3>` and `<h4>` only (and lists where appropriate).  
    • Don’t start the document with an `<h2>` or `<h3>`—begin with a paragraph.  
    • Don't use external links.

    Return the full HTML output as your completion.
    
PROMPT;

    // Prepare request
    $data = [
        "model" => "gpt-4o",
        "messages" => [
            ["role" => "system", "content" => $instructions ],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7,
        "max_tokens"        => 4000,
        "top_p"             => 1,
        "frequency_penalty" => 0,
        "presence_penalty"  => 0,
    ];

    // Initialize cURL
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute request
    $response = curl_exec($ch);


    // 1. Transport / cURL error?
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        echo json_encode(['error' => 'cURL error: ' . $err]);
        $item->status(1); //pending
        $item->Savedata();
        die('error');
    }

    // 2. HTTP status code check
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        // Try to decode any error message from the body
        $body = json_decode($response, true);
        $message = $body['error']['message'] 
                ?? 'Unexpected HTTP status: ' . $httpCode;
        echo json_encode([
            'error' => 'API request failed',
            'status' => $httpCode,
            'message' => $message
        ]);
        $item->status(1); //pending
        $item->Savedata();
        die('error');
    }

    // 3. Decode full response
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'error' => 'Invalid JSON in response: ' . json_last_error_msg()
        ]);
        $item->status(1); //pending
        $item->Savedata();
        die('error');
    }

    // 4. API-level error payload?
    if (isset($result['error'])) {
        $err = $result['error'];
        echo json_encode([
            'error'   => 'OpenAI API error',
            'type'    => $err['type']    ?? 'unknown',
            'message' => $err['message'] ?? 'No message provided',
            'code'    => $err['code']    ?? null
        ]);
        $item->status(1); //pending
        $item->Savedata();
        die('error');
    }

    // 5. Extract content safely
    $content = $result['choices'][0]['message']['content'] ?? '';
    if ($content === '') {
        echo json_encode(['error' => 'Empty content in API response']);
        $item->status(1); //pending
        $item->Savedata();
        die('error');
    }


    //curl_close($ch);

    // Decode the response
    $result = json_decode($response, true);
    $content = $result['choices'][0]['message']['content'] ?? '';

    // Parse the JSON from the AI output
    // Remove ```json and ``` if present
    $cleaned = trim($content);
    $cleaned = preg_replace('/^```json\s*/', '', $cleaned); // Remove starting ```json
    $cleaned = preg_replace('/\s*```$/', '', $cleaned);     // Remove ending ```

    $ai_text = $cleaned;

    
    $promptTokens = $result['usage']['prompt_tokens'] ?? 0;
    $completionTokens = $result['usage']['completion_tokens'] ?? 0;
    // Υπολογισμός κόστους (gpt-4o)
    $promptCost = ($promptTokens / 1000) * 0.005;
    $completionCost = ($completionTokens / 1000) * 0.015;
    $totalCost = $promptCost + $completionCost;
    $ai_msg = "<p>Εκτιμώμενο Κόστος GPT-4o: $totalCost $</p>";


}

$save_msg = "";
if (isset($_REQUEST['save'])) {
    $ai_text = $_POST['ai-text'];
    
    // $sql = "UPDATE COMPANIES SET FullDescription=? WHERE id=?";
    // $retCRM = $db1->execSQL($sql, [$ai_text, $id]);
    // $save_msg .= $retCRM==1? "CRM SAVED. ": "";

    // echo $company->get_FullDescription();
    // echo "<br/>---<br/><br/>";
    // echo $ai_text;

    $companyChange = new company_change($id, $userid, $db1);
    $companyChange->addChange("FullDescription", $company->get_FullDescription(),  $ai_text);
    $companyChange->commitChanges();

    $company->set_FullDescription($ai_text);
    $company->set_fulldescr_dm(date("YmdHis"));
    $company->Savedata();
    $save_msg .= "CRM SAVED. ";   


    $sql = "UPDATE companies SET full_description_gr=?, fulldescr_dm=? WHERE id=?";
    $retSITE = $dbSite->execSQL($sql, [$ai_text, date("YmdHis"), $online_id]);
    $save_msg .= $retSITE==1? "SITE SAVED. ": "";

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Epagelmatias CRM</title>

    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />

    <style type="text/css">

        textarea {
            height:300px;
        }

    </style>

    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

    <script></script>

    <script src="js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector:'#crm-text, #ai-text',
            menubar : false,
            relative_urls : false,
            remove_script_host : false,
            entities: 'raw',
            entity_encoding: 'raw',
            convert_urls : true,
            plugins: 'code, link, lists, image',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            image_advtab: true
        
        }); 

        $(function() {
            $(".btn-ai-text").click(function() {
                $(".please-wait").css("display", "flex");
            });
        });
        

    </script>

</head>
<body style="background-color: #ddd; padding: 30px;">

    <h1><?php echo $company_name . " ($profession / $area)" ?> </h1>
    <p><strong>Categories</strong> <?php echo $categories ?></p>
    <p><strong>Tags</strong> <?php echo $tags ?></p>
    <?php if ($save_msg!="") {
        echo "<p>$save_msg</p>";
    } ?>
        <div style="display:grid; grid-template-columns:50% 1fr;gap:30px;">
            <form action="company-ai-content.php?id=<?php echo $id ?>&ai=1" method="post" style="margin:0px"> 
                <textarea name="crm-text" id="crm-text">
                    <?php 
                    // echo $company->get_FullDescription(); 
                    echo $crm_text;
                    ?>
                </textarea>

                <input type="submit" class="btn-ai-text" value="Δημιουργία κειμένου με AI" style="margin-top:20px" />
                <?php echo $ai_msg ?>
            </form>
            <form action="company-ai-content.php?id=<?php echo $id ?>&save=1" method="post" style="margin:0px"> 
                   
                <textarea name="ai-text" id="ai-text">
                    <?php echo $ai_text ?>
                </textarea>

                <input type="submit" value="ΑΠΟΘΗΚΕΥΣΗ" style="margin-top:20px" />
            
            
            </form>
        </div>

    
        <div style="padding:40px 0px">
            <a class="button mybutton" style="padding:10px;" href="editcompany.php?id=<?php echo $id; ?>">Επιστροφή στην καρτέλα πελάτη</a>
        </div>

        <div style="position:absolute;top:20px;right:20px;font-size:30px">
            <a href="editcompany.php?id=<?php echo $id; ?>"><span class="fa fa-close"></span></a>
        </div>


        <div class="please-wait" style="display:none;position:fixed;top:0px;left:0px;right:0px;bottom:0px;background-color:#00000099;color:#fff;align-items:center;justify-content:center;z-index:99">
            <h2>Παρακαλώ περιμένετε...</h2>
        </div>
    

</body>
</html>