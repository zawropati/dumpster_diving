<?php
require_once __DIR__.'/connect.php';

$userID = $_GET['userID'];
if(empty($userID)){
    sendResponse(0, __LINE__, 'User id is missing');
}

$stmt = $db->prepare('SELECT * FROM users WHERE users.id = :userID');

$stmt->bindValue(':userID', $userID);
$stmt->execute();
$userData = $stmt->fetchAll();

sendResponse(1, __LINE__,'Usedata', json_encode($userData));

//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
