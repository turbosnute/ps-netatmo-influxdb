<?php
    $db_host = $_POST['db_host'];
    $db_org = $_POST['db_org'];
    $db_bucket = $_POST['db_bucket'];
    $db_token = $_POST['db_token'];
    $db_config_path = "/config/influxdb.json";

    $parsed_url = parse_url($db_host);
    // Check if port is present
    if (!isset($parsed_url['port'])) {
        // If port is not present, add port 8086
        $db_host .= ":8086";
    }

    $json = "{\"db_host\":\"$db_host\", \"db_org\":\"$db_org\", \"db_bucket\":\"$db_bucket\", \"db_token\":\"$db_token\"}";

    $file = fopen($db_config_path, "w") or die("Unable to open or create config file! ($db_config_path)");
    fwrite($file, $json);
    fclose($file);
    //echo "$json";
    header('Location: index.php');
?>