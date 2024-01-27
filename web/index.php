<?php
    /* Check if client config is available (in config file). */
    $client_config_path = "/config/client.json";
    
    if (!file_exists($client_config_path)) {
        file_put_contents($client_config_path, '');
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
            <form method="POST" action="save_client_config.php">
                <label for="client_id">Client Id:</label><input type="text" id="client_id" name="client_id" value="<?php echo $client_id; ?>"><br />
                <label for="client_secret">Client Secret:</label><input type="text" id="client_secret" name="client_secret" value="<?php echo $client_secret; ?>"><br />
                <input type="submit" value="Update Config" <?php // if (($client_id != "") && ($client_secret != "")) { echo "disabled"; } ?>>
            </form>

            <?php
                $state = rand(100000000,900000000);
                if (($client_config != null) && (!file_exists('/config/conf.json'))) {
                    $client_id = $client_config['client_id'];
                    $redirect_uri =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/auth.php";
                    $scope = 'read_station';
                    #$url = "https://api.netatmo.com/oauth2/authorize?client_id=".$client_id."&redirect_uri=".$redirect_uri."&scope=".$scope."&state=$state";
                    ?>
                    <form action="https://api.netatmo.com/oauth2/authorize" method="get">
                    <!-- <a class="button" id="authbutton" href="<?php echo $url; ?>" onClick="this.disabled=true; this.value='Redirecting...';">Authenticate</a>-->
                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $client_id; ?>" />
                        <input type="hidden" id="redirect_uri" name="redirect_uri" value="<?php echo $redirect_uri; ?>" />
                        <input type="hidden" id="scope" name="scope" value="<?php echo $scope; ?>" />
                        <input type="hidden" id="state" name="state" value="<?php echo $state; ?>" />
                        <input type="button" id="authbutton" onClick="this.disabled=true; this.value='Redirecting (please wait)...'; this.form.submit()" value="Authenticate Netatmo" />
                    </form>
                    <?php
                } elseif (file_exists('/config/conf.json')) {
                    ?>
                    <p>
                        <b>Netatmo: <span style="color:#6dc421;">Authenticated</span></b><br />
                        <a class="button" href="deleteauth.php">Clear Authentication</a>
                    </p>
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