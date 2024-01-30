#$scope = "read_station"
$configPath = "/config/conf.json"
$client_config_path = "/config/client.json"
$influx_config_path = "/config/influxdb.json"

#
# Functions
# 
. /app/functions.ps1

#
# Script
#

$i = 0
while ($true) {
    # the loop.

    # check if token is available.
    if (Test-TokenStatus -ConfigPath $configPath -client_config_path $client_config_path -influx_config_path $influx_config_path) {

        # Get weather data from netatmo:
        $weather_data = Get-WeatherData -Token $env:netatmo_token
        Register-WeatherData -weatherdata $weather_data

        # refresh token every 10 loop:
        if ($i -gt 9) {
            Invoke-RefreshToken -configPath $configPath -client_config_path $client_config_path
            $i = 0
        }

        # Wait 5 mins between each loop.
        Start-Sleep -Seconds (5*60)
        $i++


    } else {
        "Configuration. Navigate your web browser to http://servername:8800/ and setup the connection to netatmo and influxdb. Waiting 10 minutes until next try."
        Start-Sleep -Seconds (600)
    }

}
