<?php

require_once __DIR__.'/connect.php';

//VALIDATE EMAIL
$email = $_POST['email'] ?? '';
if(empty($email)){
  sendResponse(0, __LINE__, 'Email field is empty');
}

if( !filter_var($email, FILTER_VALIDATE_EMAIL)){
  sendResponse(0, __LINE__, 'Email address is not a valid address');
}

$stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
$stmt->bindValue(':email', $email);
$stmt->execute();
$user = $stmt->fetchAll();

if(empty($user)){
  sendResponse(0, __LINE__, 'User doesnt exist in the database');
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

if( !password_verify($userPassword, $user[0]['userPassword'] )){
  sendResponse(0, __LINE__, 'Wrong credentials, try signing in again.');
}

$userID = $user[0]['id'];

session_start();
$_SESSION['userId'] = $userID;

sendResponse(1, __LINE__, 'Successfully signed in', $userID);
//**************************************************
function sendResponse($bStatus, $iLineNumber, $message, $data='{}'){
  echo '{"status": '.$bStatus.', "code": '.$iLineNumber.', "message":"'.$message.'", "data":'.$data.'}';
  exit;
}
