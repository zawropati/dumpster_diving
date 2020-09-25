<?php

require_once __DIR__.'/connect.php';
    
//VALIDATE FIRST NAME
$firstName = $_POST['firstName'] ?? '';
if(empty($firstName)){
  sendResponse(0, __LINE__, 'Name filed is empty');
}

if( strlen($firstName) < 2){
  sendResponse(0, __LINE__, 'Name has to be min 2 characters');
}

if( strlen($firstName) > 20){
  sendResponse(0, __LINE__, 'Name has to be max 20 characters');
}

//VALIDATE LAST NAME
$lastName = $_POST['lastName'] ?? '';
if(empty($lastName)){
  sendResponse(0, __LINE__, 'Last name filed is empty');
}

if( strlen($lastName) < 2){
  sendResponse(0, __LINE__, 'Last name has to be min 2 characters');
}

if( strlen($lastName) > 50){
  sendResponse(0, __LINE__, 'Last name has to be max 20 characters');
}

$email = $_POST['email'] ?? '';
if(empty($email)){
  sendResponse(0, __LINE__, 'Email field is empty');
}

if( !filter_var($email, FILTER_VALIDATE_EMAIL)){
  sendResponse(0, __LINE__, 'Email address is not a valid address');
}

//VALIDATE PASSWORD
$userPassword = $_POST['userPassword'] ?? '';
if(empty($userPassword)){
  sendResponse(0, __LINE__, 'Password field is empty');
}

if( strlen($userPassword) < 4){
  sendResponse(0, __LINE__, 'Password has to be min 4 characters');
}

if( strlen($userPassword) > 50){
  sendResponse(0, __LINE__, 'Password has to be max 50 characters');
}

$hashedPassword = password_hash($userPassword, 1);

// TRY-CATCH
try{
  $stmt = $db->prepare('INSERT INTO users VALUES(null, :firstName, :lastName, :email, :userPassword)');
  $stmt->bindValue(':firstName', $firstName);
  $stmt->bindValue(':lastName', $lastName);
  $stmt->bindValue(':email', $email);
  $stmt->bindValue(':userPassword', $hashedPassword);

  $stmt->execute();

  sendResponse(1, __LINE__, 'Successfully signed up');

} catch(PDOException $ex) {
  echo $ex;
}

//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
