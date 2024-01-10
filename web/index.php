<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ps-netatmo-influxdb</title>
        <link rel="stylesheet" href="simple.min.css">
    </head>

    <body>
        <header>
            <h1>PS Netatmo Influxdb</h1>
        </header>

        <main>
            <p>...</p>
            <?php
                $client_id = '';
                $redirect_uri = 'http://localhost:8088/auth.php';
                $scope = 'read_station';
                $url = "https://api.netatmo.com/oauth2/authorize?client_id=".$client_id."&redirect_uri=".$redirect_uri."&scope=".$scope."&state=jieoadjsoadoijeeer134";
                //echo $url;
            ?>
            <a class="button" href="<?php echo $url; ?>">Authenticate</a>
        </main>
    </body>
</html>