<?php

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
    
    $uri = 'https://api.netatmo.com/oauth2/token';

    $grant_type = 'authorization_code';

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
    $scope = 'read_station';

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