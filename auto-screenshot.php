<?php

$remotename = file_get_contents('getremotename.txt');
    
$filename = 'remotename.txt';
file_put_contents($filename, 'Remote Name: '. $remotename);

$user = getUser($remotename);

sleep(5);
$im = imagegrabscreen();
imagepng($im, './screenshot/autoss.png');
    
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://ddohq.com/api/Utility/image/ocr/d24935d3-1bfe-4441-83ab-d2e832bfa3b3',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('file'=> new CURLFILE('./screenshot/autoss.png')),
));

$response = curl_exec($curl);
$jsonresp = json_decode($response);

if($jsonresp != NULL){
    foreach($jsonresp as $data){
        foreach($data->lines as $arrdata){
            if(in_array("LOCK3D", (array)$arrdata)){
                echo "locked";
                echo isLock(1, $user[0]->user_id);
            }
        }
    }
} else {
    echo "Response empty: ". $jsonresp;
}

curl_close($curl);
imagedestroy($im);

function getUser($remotename){
    $post_data = [
        "remote_name" => $remotename
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8000/api/users/fetch_userremote.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($post_data),
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer sample123',
          'Content-Type: application/json'
        ),
      ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

function isLock($islock, $userid){
    $post_data = [
        "isLocked" => $islock,
        "userid" => $userid
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8000/api/users/change_userislocked.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($post_data),
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer sample123',
          'Content-Type: application/json'
        ),
      ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>