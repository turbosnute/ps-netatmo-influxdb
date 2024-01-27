<?php
    $uri = 'https://api.netatmo.com/oauth2/token';
    $client_config_path = "/config/client.json";

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

    $file = fopen($client_config_path, "r") or die("Unable to open or create config file! ($client_config_path)");
    $filesize = filesize($client_config_path);

    if ($filesize > 0) {
        $client_config_data = fread($file,$filesize);
        $client_config = json_decode($client_config_data, true);
    } else {
        // probably no config
        $client_config = null;
    }

    fclose($file);

    $client_id = "";
    $client_secret = "";

    if (isset($client_config['client_id'])) {
        $client_id = $client_config['client_id'];
    }

    if (isset($client_config['client_secret'])) {
        $client_secret = $client_config['client_secret'];
    }
    
    if ($client_id == "" || $client_secret == "$client_config_path") {
        die("Could not find client_id or client secret in config file ()");
    }

    $redirect_uri = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/auth.php";
    /*
    echo "<p>";
    echo "<strong>uri: </strong>".$uri."<br />";
    echo "<strong>code: </strong>".$code."<br />";
    echo "<strong>client_id: </strong>".$client_id."<br />";
    echo "<strong>client_secret: </strong>".$client_secret."<br />";
    echo "<strong>state: </strong>".$state."<br />";
    echo "</p>";
    */
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

    header('Location: index.php');
?>