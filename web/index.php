<?php
    /* Check if client config is available (in config file). */
    $client_config_path = "/config/client.json";
    $file = fopen($client_config_path, "w") or die("Unable to open or create config file! ($client_config_path)");
    $filesize = filesize($client_config_path);

    if ($filesize > 0) {
        $client_config_data = fread($file,$filesize);
        $client_config = json_decode($client_config_data);
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
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PS Netatmo InfluxDB</title>
        <link rel="stylesheet" href="simple.min.css">
    </head>

    <body>
        <header>
            <h1>PS Netatmo InfluxDB</h1>
        </header>

        <main>
            <h2>Client Config</h2>
            <form>
                <label for="client_id">Client Id:</label><input type="text" id="client_id" name="client_id" value="<?php echo $client_id; ?>"><br />
                <label for="client_secret">Client Secret:</label><input type="text" id="client_secret" name="client_secret" value="<?php echo $client_secret; ?>"><br />
                <input type="submit" value="Update Config">
            </form>

            <?php
                $state = rand(100000000,900000000);
                if ($client_config != null) {
                    $client_id = $client_config['client_id'];
                    $redirect_uri =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/auth.php";
                    $scope = 'read_station';
                    $url = "https://api.netatmo.com/oauth2/authorize?client_id=".$client_id."&redirect_uri=".$redirect_uri."&scope=".$scope."&state=$state";
                    ?>
                    <a class="button" href="<?php echo $url; ?>">Authenticate</a>
                    <?php
                } else {
                    ?>
                    <p>
                        Missing <mark>Client Id</mark> and <mark>Client Secret</mark>. 
                        <ul>
                            <li>Go to <a href='https://dev.netatmo.com/apps/'>https://dev.netatmo.com/apps/</a> and log in.</li>
                            <li>Click "Create"</li>
                            <li>Fill in the info and create the app</li>
                            <li>Copy the Client Id and Client Secret</li>
                        </ul>
                    </p>
                    <?php
                }

                //echo $url;
            ?>
            
        </main>
    </body>
</html>