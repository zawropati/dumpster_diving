<?php
require_once __DIR__.'/connect.php';

$stmt = $db->prepare('SELECT * FROM stores WHERE has_dumpster = 0');

$stmt->execute();
$locations = $stmt->fetchAll();
  
sendResponse(1, __LINE__,'These are the stores', json_encode($locations));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
