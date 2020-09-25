<?php
require_once __DIR__.'/connect.php';


$stmt = $db->prepare('SELECT * FROM food_categories');

$stmt->execute();
$foodCategories = $stmt->fetchAll();
  
sendResponse(1, __LINE__,'These are the food categories', json_encode($foodCategories));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
