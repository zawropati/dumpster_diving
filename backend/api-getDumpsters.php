<?php
require_once __DIR__.'/connect.php';

$userId = $_GET['userId'];

$stmt = $db->prepare('SELECT city, dumpster_fk, lat, lng, name, AVG(score) as avgScore, street, zip, is_saved
FROM stores 
JOIN reviews ON stores.id = reviews.dumpster_fk
LEFT JOIN saved_dumpsters ON stores.id = saved_dumpsters.location_fk and saved_dumpsters.user_fk = :userId
WHERE stores.has_dumpster = 1
GROUP BY reviews.dumpster_fk
');

$stmt->bindValue(':userId', $userId);

$stmt->execute();

$dumpsters = $stmt->fetchAll();

sendResponse(1, __LINE__, 'Dumpsters data:',json_encode($dumpsters, JSON_NUMERIC_CHECK));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
