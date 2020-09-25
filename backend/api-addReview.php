<?php
require_once __DIR__.'/connect.php';

$score = $_POST['score'] ?? '';
if($score == "NaN" || empty($score)){
  sendResponse(0, __LINE__, 'Rating is missing');
}

$review = $_POST['review'] ?? '';
if(empty($review)){
  sendResponse(0, __LINE__, 'Review is missing');
}

if( strlen($review) > 1000 ) {
  sendResponse(0, __LINE__, 'Review is too long, please limit it to 1000 characters');
}

$storeId = $_POST['storeId'] ?? '';
if(empty($storeId)){
  sendResponse(0, __LINE__, 'Location is not specified');
}

$categories = json_decode($_POST['categories']);
  if( empty($categories) ){
    sendResponse(0, __LINE__, 'Tell us what could you find in the dumpster');
  }


$db->beginTransaction();

$stmt = $db->prepare('INSERT INTO reviews(id, review, dumpster_fk, score, date) VALUES(null, :review, :store_id, :score, null)');

$stmt->bindValue(':store_id', $storeId);
$stmt->bindValue(':review', $review);
$stmt->bindValue(':score', $score);

if(  !$stmt->execute() ){
  sendResponse(0, __LINE__, 'Couldnt add the review to the db');
  $db->rollBack();
  exit;
}

foreach ($categories as $value) {
  $category_fk = $value;

  $stmt = $db->prepare("INSERT INTO food_in_dumpsters VALUES(null,'".$category_fk."',  '".$storeId."')");
  
  if(  !$stmt->execute() ){
    sendResponse(0, __LINE__, 'Couldnt add food in dumpster');
    $db->rollBack();
    exit;
  }
}

$db->commit();

sendResponse(1, __LINE__, 'Yay! Thank you for adding the review');
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}