<?php
    $uri = 'https://api.netatmo.com/oauth2/token';

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

    $redirect_uri = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/auth.php";

    echo "<p>";
    echo "<strong>uri: </strong>".$uri."<br />";
    echo "<strong>code: </strong>".$code."<br />";
    echo "<strong>client_id: </strong>".$client_id."<br />";
    echo "<strong>client_secret: </strong>".$client_secret."<br />";
    echo "<strong>state: </strong>".$state."<br />";
    echo "</p>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'scope' => 'read_station'
    ));
        
    $data = curl_exec($ch);
    $file = fopen("/config/conf.json", "w") or die("Unable to open or create config file! (/config/conf.json)");
    fwrite($file, $data);
    fclose($file);
?>