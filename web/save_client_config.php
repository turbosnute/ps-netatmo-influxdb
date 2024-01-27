<?php
    $client_id = $_POST['client_id'];
    $client_secret = $_POST['client_secret'];

    $json = "{\"client_id\":\"$client_id\", \"client_secret\":\"$client_secret\"}";

    $client_config_path = "/config/client.json";
    $file = fopen($client_config_path, "w") or die("Unable to open or create config file! ($client_config_path)");
    fwrite($file, $json);
    fclose($file);

    header('Location: index.php');
?>