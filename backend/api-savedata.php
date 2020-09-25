<?php
require_once __DIR__.'/connect.php';

$jsondata = file_get_contents('php://input');
$data = json_decode($jsondata, true);
$data = $data['stores'];

try{
    foreach ($data as $u => $z){
        $name = $z['name'];
        $lat = $z['lat'];
        $lng = $z['lng'];
        $city = $z['city'];
        $street = $z['street'];
        $zip = $z['zip'];

        $stmt = $db->prepare("INSERT INTO locations(id, name, lat, lng, city, street, zip) 
        VALUES (NULL, '".$name."', '".$lat."', '".$lng."', '".$city."', '".$street."', '".$zip."')");
        $stmt->execute();
    }
    sendResponse(1, __LINE__, 'Successfully insterted data ');
} catch(PDOException $ex) {
    echo $ex;
}

//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}

