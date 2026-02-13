<?php

// $REGION = '';  // If German region, set this to an empty string: ''
// $BASE_HOSTNAME = 'storage.bunnycdn.com';
// $HOSTNAME = (!empty($REGION)) ? "{$REGION}.{$BASE_HOSTNAME}" : $BASE_HOSTNAME;
// $STORAGE_ZONE_NAME = 'panelinios';
// $FILENAME_TO_UPLOAD = 'test/sms.svg';
// $ACCESS_KEY = '79af6afe-d2d8-49db-926fcbbdf562-a054-419e';
// $FILE_PATH = 'img/sms.svg';  // Full path to your local file

$cdn = new stdClass();
$cdn->REGION = '';
$cdn->BASE_HOSTNAME = 'storage.bunnycdn.com';
$cdn->STORAGE_ZONE_NAME = 'panelinios';
$cdn->ACCESS_KEY = '79af6afe-d2d8-49db-926fcbbdf562-a054-419e';
$cdn->PUBLIC_URL = 'https://cdn.panelinios.gr';



/*$url = "https://{$HOSTNAME}/{$STORAGE_ZONE_NAME}/{$FILENAME_TO_UPLOAD}";

$ch = curl_init();

$options = array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_PUT => true,
  CURLOPT_INFILE => fopen($FILE_PATH, 'r'),
  CURLOPT_INFILESIZE => filesize($FILE_PATH),
  CURLOPT_HTTPHEADER => array(
    "AccessKey: {$ACCESS_KEY}",
    'Content-Type: application/octet-stream'
  )
);

curl_setopt_array($ch, $options);

$response = curl_exec($ch);

if (!$response) {
  die("Error: " . curl_error($ch));
} else {
  print_r($response);
}

curl_close($ch);*/


function cdn_upload($local_file, $remote_path, $cdn) {
  $HOSTNAME = (!empty($cdn->REGION)) ? "{$cdn->REGION}.{$cdn->BASE_HOSTNAME}" : $cdn->BASE_HOSTNAME;
  
  $url = "https://{$HOSTNAME}/{$cdn->STORAGE_ZONE_NAME}/{$remote_path}";

  $ch = curl_init();

  $options = array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_PUT => true,
    CURLOPT_INFILE => fopen($local_file, 'r'),
    CURLOPT_INFILESIZE => filesize($local_file),
    CURLOPT_HTTPHEADER => array(
      "AccessKey: {$cdn->ACCESS_KEY}",
      'Content-Type: application/octet-stream'
    )
  );
  
  curl_setopt_array($ch, $options);
  
  $response = curl_exec($ch);
  
  if (!$response) {
    die("Error: " . curl_error($ch));
  } else {
    print_r($response);
    echo $cdn->PUBLIC_URL . "/" . $remote_path;
  }
  
  curl_close($ch);
  
}


function cdn_delete($remote_path, $cdn) {
  $HOSTNAME = (!empty($cdn->REGION)) ? "{$cdn->REGION}.{$cdn->BASE_HOSTNAME}" : $cdn->BASE_HOSTNAME;
  
  $url = "https://{$HOSTNAME}/{$cdn->STORAGE_ZONE_NAME}/{$remote_path}";
  
  // Initialize cURL session
  $ch = curl_init($url);

  // Set cURL options
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "AccessKey: $cdn->ACCESS_KEY"
  ]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // To get response

  // Execute cURL request
  $response = curl_exec($ch);

  // Check for errors
  if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
  } else {
      echo 'Response: ' . $response;
  }

  // Close cURL session
  curl_close($ch);
}



//cdn_upload("img/sms.svg", "test/sms2.svg", $cdn);

cdn_delete("test/sms4.svg", $cdn);