<?php
header('Access-Control-Allow-Origin: * ');
header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

try{
  $sUserName = 'b3326e65138e72';
  $sPassword = 'f7db8163';
  $sConnection = "mysql:host=eu-cdbr-west-03.cleardb.net; dbname=heroku_7e179fbb358ce53; charset=utf8mb4";

  $aOptions = array(
    // PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
    //commented out because of transaction
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  );
  $db = new PDO( $sConnection, $sUserName, $sPassword, $aOptions );
  
}catch( PDOException $e){
echo '{"status":0,"message":"cannot connect to database"}';
exit();
}
