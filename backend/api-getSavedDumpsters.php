<?php
require_once __DIR__.'/connect.php';

$userID = $_GET['userID'];
if(empty($userID)){
    sendResponse(0, __LINE__, 'User id is missing');
}

$stmt = $db->prepare('SELECT city, lat, lng, location_fk, name, note, street, zip,  AVG(score) as avgScore
FROM stores 
JOIN saved_dumpsters ON stores.id = saved_dumpsters.location_fk
JOIN reviews ON stores.id = reviews.dumpster_fk
WHERE saved_dumpsters.user_fk = :userId
GROUP BY reviews.dumpster_fk
');

$stmt->bindValue(':userId', $userID);

$stmt->execute();

$dumpsters = $stmt->fetchAll();

sendResponse(1, __LINE__, 'Dumpsters data:',json_encode($dumpsters,JSON_NUMERIC_CHECK));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
