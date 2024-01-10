<?php
    $uri = 'https://api.netatmo.com/oauth2/token';
    $grant_type = 'authorization_code';
    $scope = 'read_station';

    if (isset($_GET['code'])) {
        $code = $_GET['code'];  
    } else {
        die("Error: No Code Specified");
    }

    if (isset($_GET['state'])) {
        $state = $_GET['state'];
    } else {
        // hmmmm?
    }

    if (isset($_ENV['client_id'])) {
        $client_id = $_ENV['client_id'];
    } else {
        die("Environment variable 'client_id' must be set...");
    }

    if (isset($_ENV['client_secret'])) {
        $client_secret = $_ENV['client_secret'];
    } else {
        die("Environment variable 'client_secret' must be set...");
    }

    $code = $code;
    $redirect_uri = '';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ));
        
    $data = curl_exec($ch);
    
    var_dump($data);
    
    /*
HTTP/1.1 200 OK
    Content-Type: application/json;charset=UTF-8
    Cache-Control: no-store
    Pragma: no-cache

    {
    "access_token":"2YotnFZFEjr1zCsicMWpAA",
    "expires_in":10800,
    "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
    }
    */
?>