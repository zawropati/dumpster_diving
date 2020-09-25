<?php
require_once __DIR__.'/connect.php';

$lastDate = $_GET['lastDate'];

$stmt = $db->prepare('SELECT COUNT(*) as new_dumpsters FROM stores WHERE stores.has_dumpster = 1 AND stores.timestamp > :lastDate');

$stmt->bindValue(':lastDate', $lastDate);

$stmt->execute();

$dumpsters = $stmt->fetchAll();

sendResponse(1, __LINE__, 'Dumpsters data:',json_encode($dumpsters, JSON_NUMERIC_CHECK));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
