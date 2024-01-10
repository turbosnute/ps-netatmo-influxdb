<?php
    $code = $_GET['code'];
    $state = $_GET['state'];

    $uri = 'https://api.netatmo.com/oauth2/token';

    $grant_type = 'authorization_code';
    $client_id = '';
    $client_secret = '';
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