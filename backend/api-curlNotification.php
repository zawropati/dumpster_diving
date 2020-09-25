<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"to\": \"dt2RIRIXmf3R8Ltk2SqO5g:APA91bFpGT2iryd0JZZf0Ery_WSvIQugrpLU2H0g4TrFXh1uGupFwmTGeePqIvST-JAe1tgcrxaOl_z_DqhAHUtkJLdTFy47AIPWFpYdfatDg0gKMuAN2pGNPHC8SGeix7_eExszNzT0\",\n      \"notification\": {\n        \"title\": \"FCM Message\",\n        \"body\": \"This is an FCM Message\",\n        \"icon\": \"./img/icons/android-chrome-192x192.png\"\n      }\n    }");

$headers = array();
$headers[] = 'Authorization: key=AAAAkD7-CnY:APA91bHA__t8qfG8SDy10OgAK8oDjmGoTXf6KyTUc9YUFmFDsecQWHtTlyC7goBTUP33OntBgtp2CL507q8p7vNbuTdDKWAyXgZGlUxXLurFEo0MqwEkUvvHP98LDWEcf-LcF9zzxMJ5';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);