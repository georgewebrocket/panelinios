<?php




function myData_PostDataWithCurl($url, $data, $headers, $invoiceId) {
    
  $ch = curl_init( $url );
  
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1); //header in response
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $response = curl_exec($ch);
  
  curl_close ($ch);
  
  $filename = "php/mydata/inv-$invoiceId.xml";
  file_put_contents($filename, $data);
  
  return $response;
  
    
}


//px $ids = "1,2,3";
function myData_SendInvoices($ids, $dbo, $publisher) {
  
  //georgeapollo
  //b22bcff06f394905bca31e2f7a4ec472
  //051317980
  //$url = "https://mydata-dev.azure-api.net/SendInvoices";
  
  
  

  if ($publisher==1) { //panelinios k.zigogiannis atomiki
    $AADE_USER_ID = "kzigogiann"; //xxxx
    $OCP_APIM_SUBSCRIPTION_KEY = "f66db967f96e42978a73f0fe2aee34e4"; //xxxx
    $MY_VAT_NUMBER = "105917916"; //xxxx
  }
  else if ($publisher==2) { //kzigogiannis ee
    $AADE_USER_ID = "xartisapouni"; //xxxx
    $OCP_APIM_SUBSCRIPTION_KEY = "3e3e6f03534d4510bfee56ddb4925390"; //xxxx 
    $MY_VAT_NUMBER = "801427289"; //xxxx
  }

  $url = "https://mydatapi.aade.gr/myDATA/SendInvoices";
  
  
  $headers = array("aade-user-id:$AADE_USER_ID",
    "Ocp-Apim-Subscription-Key: $OCP_APIM_SUBSCRIPTION_KEY",
    "Content-Type: text/plain");
    
  $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<InvoicesDoc xmlns="http://www.aade.gr/myDATA/invoice/v1.0"	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
			xsi:schemaLocation="http://www.aade.gr/myDATA/invoice/v1.0/InvoicesDoc-v0.6.xsd"	
			xmlns:icls="https://www.aade.gr/myDATA/incomeClassificaton/v1.0" 
			xmlns:ecls="https://www.aade.gr/myDATA/expensesClassificaton/v1.0">
			
EOT;
  
  $sql = "SELECT * FROM INVOICEHEADERS WHERE id IN ($ids) AND myDataTransfered=0";
  $rsInv = $dbo->getRS($sql);
  
  $sql = "SELECT * FROM INVOICESERIES";
  $rsInvSer = $dbo->getRS($sql);
  //var_dump($rsInvSer);
  //echo $rsInv[$i]['series'];
  
  $sql = "SELECT * FROM INV_PAYMETHODS";
  $rsPayMethods = $dbo->getRS($sql);
  
  for ($i=0;$i<count($rsInv);$i++) {
    $invoiceId = $rsInv[$i]['id'];
    $vatNumber = trim($rsInv[$i]['afm']);
    $zipcode = $rsInv[$i]['zipcode'];
    $city = $rsInv[$i]['city'];
    $invSeries = func::vlookupRS("code", $rsInvSer, $rsInv[$i]['series']);
    $invNr = $rsInv[$i]['icode'];
    $invDate = func::str14toDate($rsInv[$i]['idate'], "-", "EN");
    $payMethodType = in_array($rsInv[$i]['paymethod'], array(1,2))? 3: 1; //......
    $selfPricing = "false"; //xxx
    $payMethodDescr = func::vlookupRS("description", $rsPayMethods, $rsInv[$i]['paymethod']);
    $totalAmount = round($rsInv[$i]['amount'] + $rsInv[$i]['vat'], 2);
    
    
    $sql = "SELECT * FROM COMPANIES WHERE id=" . $rsInv[$i]['company'];
    $rsCustomer = $dbo->getRS($sql);
    
    switch($rsCustomer[0]['mydata_companytype']) {
      case 1: //esoterikou
        $invoiceType = "2.1";
        $incomeCode = "E3_561_001";
        break;
      case 2: //endokoinotikos
        $invoiceType = "2.2";
        $incomeCode = "E3_561_005";
        break;
      case 3: //tritis xoras
        $invoiceType = "2.3";
        $incomeCode = "E3_561_006";
        break;
      default:
        $invoiceType = "2.1";
        $incomeCode = "E3_561_001";
        break;
    }
    $incomeCategory = "category1_3";

    $country_code = substr($vatNumber, 0, 2);
    if (is_numeric($country_code) || $country_code=="EL") {
      $country_code = "GR";
      $counterpart_namestr = "";
    }
    else {
      $counterpart_name = $rsCustomer[0]['eponimia'];
		  $counterpart_namestr = "<name>$counterpart_name</name>";
    }
    
    
    //$invoiceType = "2.1";
    
    //invoice header
    $xml .= <<<EOT
    
<invoice>
		<issuer>
			<vatNumber>$MY_VAT_NUMBER</vatNumber>
			<country>GR</country>
			<branch>0</branch>		          
		</issuer>
		<counterpart>
			<vatNumber>$vatNumber</vatNumber>
			<country>$country_code</country>
			<branch>0</branch>
      $counterpart_namestr            
			<address>                
				<postalCode>$zipcode</postalCode>
				<city>$city</city>
			</address>
		</counterpart>
		<invoiceHeader>
			<series>$invSeries</series>
			<aa>$invNr</aa>
			<issueDate>$invDate</issueDate>
			<invoiceType>$invoiceType</invoiceType>
			<currency>EUR</currency>
		</invoiceHeader>
		<paymentMethods>
			<paymentMethodDetails>
				<type>$payMethodType</type>
				<amount>$totalAmount</amount>
				<paymentMethodInfo>$payMethodDescr</paymentMethodInfo>
			</paymentMethodDetails>
		</paymentMethods>    
    
EOT;

  //invoice lines
  $sql = "SELECT * FROM INVOICES WHERE headerid=$invoiceId";
  //echo $sql;
  $rsInvLines = $dbo->getRS($sql);
  for ($k=0;$k<count($rsInvLines);$k++) {
    
    $lineNr = $k + 1;
    $netValue = round($rsInvLines[$k]['amount'],2);
    $vatAmount = round($rsInvLines[$k]['vat'],2);
    
    $vatCategory = 1; //xxx
    //$incomeCode = "E3_561_001"; //xxx
    //$incomeCategory = "category1_3"; //xxx
    $discountOption = "true"; //xxx
    
    $xml .= <<<EOT
    
    <invoiceDetails>
			<lineNumber>$lineNr</lineNumber>
			<netValue>$netValue</netValue>
			<vatCategory>$vatCategory</vatCategory>
			<vatAmount>$vatAmount</vatAmount>
			<discountOption>$discountOption</discountOption>
			<incomeClassification>
				<icls:classificationType>$incomeCode</icls:classificationType>
				<icls:classificationCategory>$incomeCategory</icls:classificationCategory>
				<icls:amount>$netValue</icls:amount>
			</incomeClassification>
		</invoiceDetails>
    
EOT;
  }
  
  
  $totalNetValue = round($rsInv[$i]['amount'],2);
  $totalVatAmount = round($rsInv[$i]['vat'],2);
  
  //$invExpenseCode = "E3_561_001"; //xxx
  $invExpenseCode = $incomeCode;
  $invExpenseCategory = "category1_3"; //xxx
  
  
  //invoice footer
  $xml .= <<<EOT
  
  <invoiceSummary>
			<totalNetValue>$totalNetValue</totalNetValue>
			<totalVatAmount>$totalVatAmount</totalVatAmount>
			<totalWithheldAmount>0.00</totalWithheldAmount>
			<totalFeesAmount>0.00</totalFeesAmount>
			<totalStampDutyAmount>0.00</totalStampDutyAmount>
			<totalOtherTaxesAmount>0.00</totalOtherTaxesAmount>
			<totalDeductionsAmount>0.00</totalDeductionsAmount>
			<totalGrossValue>$totalAmount</totalGrossValue>
			<incomeClassification>
				<icls:classificationType>$invExpenseCode</icls:classificationType>
				<icls:classificationCategory>$invExpenseCategory</icls:classificationCategory>
				<icls:amount>$totalNetValue</icls:amount>				
			</incomeClassification>
		</invoiceSummary>
    </invoice>
  
  
EOT;


    
  }
  
  $xml .= "</InvoicesDoc>";
  //var_dump($xml);
  
  $res = myData_PostDataWithCurl($url, $xml, $headers, $invoiceId);
  //echo "<pre>";
  var_dump($res);
  //echo "</pre>";
  
  try {
    $myArray = new SimpleXMLElement($res);
  }
  catch(Exception $ex) {
    echo "ERROR XML - INVOICE ID= $invoiceId";
  }
  //$myArray = new SimpleXMLElement($res);
  //var_dump($myArray);
  
  $invoiceIds = explode(",", $ids);
  
  if (count($myArray->response)==1) {
    $index = $myArray->response->index;
    $invoiceUid = $myArray->response->invoiceUid;
    $invoiceMark = $myArray->response->invoiceMark;
    $qrUrl = $myArray->response->qrUrl;
    $statusCode = $myArray->response->statusCode;
    if ($statusCode=="Success") {
      $sql = "UPDATE INVOICEHEADERS SET myDataTransfered=1, myDataMark = ?, myDataQrCode = ? WHERE id = ? ";
      $res = $dbo->execSQL($sql, array($invoiceMark, $qrUrl, $invoiceIds[0]));
      echo "Το παραστατικό με ID " . $invoiceIds[0] . " μεταφέρθηκε στο MyData / MARK= $invoiceMark";
    }
    
  }
  else {
    for ($i=0;$i<count($myArray->response);$i++) {
      $index = $myArray->response[$i]->index;
      $invoiceUid = $myArray->response[$i]->invoiceUid;
      $invoiceMark = $myArray->response[$i]->invoiceMark;
      $statusCode = $myArray->response[$i]->statusCode;
      if ($statusCode=="Success") {
        $sql = "UPDATE INVOICEHEADERS SET myDataTransfered=1, myDataMark=$invoiceMark WHERE id=" . $invoiceIds[$i];
        $res = $dbo->execSQL($sql);
        echo "Το παραστατικό με ID " . $invoiceIds[$i] . " μεταφέρθηκε στο MyData\n";
      }
    }
  }
  
  
  
  
}