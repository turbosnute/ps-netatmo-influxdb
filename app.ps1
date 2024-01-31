Write-Host ""
Write-Host "+---------------------+"
Write-Host "| $($PSStyle.Italic)PS Netatmo InfluxDB$($PSStyle.Reset) |"
Write-Host "+---------------------+"
Write-Host ""
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

# First we refresh the token...
Invoke-RefreshToken -configPath $configPath -client_config_path $client_config_path

$i = 0
while ($true) {
    # the loop.

    # check if token is available.
    if (Test-TokenStatus -ConfigPath $configPath -client_config_path $client_config_path -influx_config_path $influx_config_path) {

        # Get weather data from netatmo:
        if ($env:DEBUG -eq $true) {
            Write-Host "Getting Weatherdata..."
        }
        $weather_data = Get-WeatherData -Token $env:netatmo_token

        if ((-not $weather_data.error) -and ($null -ne $weather_data)) {
            # if no error:
            if ($env:DEBUG -eq $true) {
                Write-Host " Got Weatherdata."
            }
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
            # Error returned from netatmo or no data returned.

            if ($weather_data.error.code -eq "3") {
                # Token Expired.
                Write-Host "Token Expired, deleted config. Please reauthenticate with Netatmo. http://servername/8800/"
                Remove-Item -Path $configPath -Force # Removes the config.
            } elseif ($null -eq $weather_data) {
                Write-Host "No data returned from Netatmo API, error in API communication?"
            } else {
                Write-Host "Error $($weather_data.error.Message)"
            }
        }


    } else {
        Write-Host "Configuration is incomplete. Navigate your web browser to http://servername:8800/ and setup the connection to netatmo and influxdb. Waiting 5 minutes until next try."
        Start-Sleep -Seconds (300)
    }

}
