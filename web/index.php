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
            <?php
                $state = rand(100000000,900000000);
                $client_id = $_ENV['client_id'];
                $redirect_uri = $redirect_uri =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/auth.php";
                $scope = 'read_station';
                $url = "https://api.netatmo.com/oauth2/authorize?client_id=".$client_id."&redirect_uri=".$redirect_uri."&scope=".$scope."&state=$state";
                //echo $url;
            ?>
            <a class="button" href="<?php echo $url; ?>">Authenticate</a>
        </main>
    </body>
</html>