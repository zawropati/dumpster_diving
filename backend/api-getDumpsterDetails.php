<?php
require_once __DIR__.'/connect.php';

$dumpsterId = $_GET['dumpsterId'];
if(empty($dumpsterId)){
    sendResponse(0, __LINE__, 'Dumpster id is missing');
}

$db->beginTransaction();

$stmt = $db->prepare('SELECT * FROM reviews WHERE reviews.dumpster_fk = :dumpster_id');  

$stmt->bindValue(':dumpster_id', $dumpsterId);
if(  !$stmt->execute() ){
  sendResponse(0, __LINE__, 'Couldnt get reviews');
  $db->rollBack();
  exit;
}
$reviews = new stdClass();
$reviews->reviews = $stmt->fetchAll();

$stmt = $db->prepare('SELECT food_categories.category, COUNT(*) AS frequency FROM food_in_dumpsters 
JOIN food_categories ON food_in_dumpsters.food_catgories_fk = food_categories.id
WHERE food_in_dumpsters.dumpster_fk = :dumpster_id
GROUP BY food_categories.category 
');

$stmt->bindValue(':dumpster_id', $dumpsterId);

if(  !$stmt->execute() ){
    sendResponse(0, __LINE__, 'Couldnt get food');
    $db->rollBack();
    exit;
}

$food = new stdClass();
$food->food = $stmt->fetchAll();

$obj_merged = (object) array_merge((array) $reviews, (array) $food);

$db->commit();

sendResponse(1, __LINE__, 'Dumpsters data:',json_encode($obj_merged, JSON_NUMERIC_CHECK));
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
