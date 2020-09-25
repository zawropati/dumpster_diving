<?php
require_once __DIR__.'/connect.php';

$dumpsterId = $_POST['dumpsterId'] ?? '';
if(empty($dumpsterId)){
  sendResponse(0, __LINE__, 'Dumpster id is missing');
}

$note = $_POST['note'] ?? '';
if(empty($note)){
  sendResponse(0, __LINE__, 'Note is missing');
}

if( strlen($note) > 1000 ) {
  sendResponse(0, __LINE__, 'Note is too long, please limit it to 1000 characters');
}


$userId = $_POST['userId'] ?? '';
if(empty($userId)){
  sendResponse(0, __LINE__, 'User id is missing');
}

$stmt = $db->prepare('INSERT INTO saved_dumpsters VALUES (null, :user_id, :dumpster_id, :note, 1) ');
  
$stmt->bindValue(':dumpster_id', $dumpsterId);
$stmt->bindValue(':user_id', $userId);
$stmt->bindValue(':note', $note);


$stmt->execute();


sendResponse(1, __LINE__, 'Dumpster was saved');
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}


