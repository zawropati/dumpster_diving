<?php
require_once __DIR__.'/connect.php';


$storeName = $_POST['storeName'];
if(empty($storeName)){
    sendResponse(0, __LINE__, 'Store name is missing');
  }

$address = $_POST['address'];
if(empty($storeName)){
    sendResponse(0, __LINE__, 'Address is missing');
  }


$city = $_POST['city'];
if(empty($city)){
    sendResponse(0, __LINE__, 'City is missing');
  }

$zipcode = $_POST['zipcode'];
if(empty($storeName)){
    sendResponse(0, __LINE__, 'Zipcode is missing');
  }

$lat = $_POST['lat'];
$lng = $_POST['lng'];

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


$categories = json_decode($_POST['categories']);
  if( empty($categories) ){
    sendResponse(0, __LINE__, 'Tell us what could you find in the dumpster');
  }


$db->beginTransaction();

$stmt = $db->prepare('INSERT INTO stores VALUES(null, :name, :lat, :lng, :city, :street, :zipcode, 1, null)');

$stmt->bindValue(':name', $storeName);
$stmt->bindValue(':lat', $lat);
$stmt->bindValue(':lng', $lng);
$stmt->bindValue(':street', $address);
$stmt->bindValue(':city', $city);
$stmt->bindValue(':zipcode', $zipcode);

if(  !$stmt->execute() ){
  sendResponse(0, __LINE__, 'Couldnt add the location');
  $db->rollBack();
  exit;
}

$last_id = $db->lastInsertId();

$stmt = $db->prepare('INSERT INTO reviews(id, review, dumpster_fk, score) VALUES(null, :review, :store_id, :score)');

$stmt->bindValue(':store_id', $last_id);
$stmt->bindValue(':review', $review);
$stmt->bindValue(':score', $score);

if(  !$stmt->execute() ){
  sendResponse(0, __LINE__, 'Couldnt add the review');
  $db->rollBack();
  exit;
}

foreach ($categories as $value) {
  $category_fk = $value;

  $stmt = $db->prepare("INSERT INTO food_in_dumpsters VALUES(null,'".$category_fk."',  '". $last_id."')");
  
  if(  !$stmt->execute() ){
    sendResponse(0, __LINE__, 'Couldnt add food in dumpster');
    $db->rollBack();
    exit;
  }
}

$db->commit();

sendResponse(1, __LINE__, 'Successfully added the dumpster and review');

//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
