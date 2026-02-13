<?php

ini_set('display_errors',0); 
// error_reporting(E_ALL);



$afm = isset($_REQUEST['afm'])? $_REQUEST['afm']: "";

function checkVATGR($username,$password,$AFMcalledby="",$AFMcalledfor)
{
    $client = new SoapClient( 
        "https://www1.gsis.gr/wsaade/RgWsPublic2/RgWsPublic2?WSDL",
        array(
            'trace' => true,
            'exceptions' => true,
            'soap_version' => SOAP_1_2, // Ensure the correct SOAP version
            ) 
    );


	$authHeader = new stdClass();
	$authHeader->UsernameToken->Username = "$username";
	$authHeader->UsernameToken->Password = "$password";
	$Headers[] = new SoapHeader(
        'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 
        'Security', 
        $authHeader,
        TRUE
    );
	$client->__setSoapHeaders($Headers);

    $result = $client->rgWsPublic2AfmMethod(
		array(
            'INPUT_REC' => array(
                'afm_called_by'=> "$AFMcalledby",
                'afm_called_for'=> "$AFMcalledfor"
            )
        )
		);

	return $result;
}

if ($afm!="") {
    $data = checkVATGR("GPMAD11374", "Aa43gP6620@", "", $afm);
    //$company_data = $data->result->rg_ws_public2_result_rtType->basic_rec;
    echo json_encode($data);

    //firm_act_tab

}