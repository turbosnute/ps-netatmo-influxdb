$scope = "read_station"
$configPath = "/config/conf.json"
$client_config_path = "/config/client.json"

#
# Functions
#

<#
.SYNOPSIS
    This function is used to get a new token from Netatmo and update the config with it.
.DESCRIPTION
    This function is used to get a new token from Netatmo and update the config with it. If the refresh token has expired, the config will be cleared.
#>
function Invoke-RefreshToken {
    [CmdletBinding()]
    [OutputType([int])]
    param(
        [Parameter(Mandatory=$true)][string]$configPath,
        [Parameter(Mandatory=$true)][string]$client_config_path
    )
    
    begin {
        $tokens = Get-Content -Path $ConfigPath | ConvertFrom-Json
        $client = Get-Content -Path $client_config_path | ConvertFrom-Json
    }
    
    process {

        $env:netatmo_token = $tokens.token
        $env:netatmo_refresh_token = $tokens.refresh_token

        $refresh_payload = @{
            grant_type = "refresh_token"
            refresh_token = $tokens.refresh_token
            client_id = $client.client_id
            client_secret = $client.client_secret
        }

        $headers = @{
            Authorization = "Bearer $($tokens.access_token)"
        }

        $refresh_args = @{
            Uri = 'https://api.netatmo.com/oauth2/token'
            Method = 'Post'
            Body = $refresh_payload
            Headers = $headers
        }

        $res = Invoke-RestMethod @refresh_args

        $res | ConvertTo-Json | Out-File -encoding utf8 -Path $configPath
        
        #
        # Update or clear (if token is too old) config here.
        #
    }
    
    end {
    }
}

#
# Check if config exists
#
if ((Test-Path -Path $configPath) -and (Test-Path -Path $client_config_path)) {
    $tokens = Get-Content -Path $ConfigPath | ConvertFrom-Json
    $client = Get-Content -Path $client_config_path | ConvertFrom-Json

    #
    # Before the loop(?). Try to refresh token.
    #

} else {
    # No config
    Write-Host "Can't find config. Go to http://server:8088/ and complete setup."
}


<#
.SYNOPSIS
    Gets weather data from the users Netatmo Weather Station.
.DESCRIPTION
    Gets weather data from the users Netatmo Weather Station.
#>
function Get-WeatherData {
    [CmdletBinding()]
    [OutputType([int])]
    param(
        [Parameter(Mandatory=$true)][string]$Token
    )
    
    process {
        $uri = "https://api.netatmo.com/api/getstationsdata?get_favorites=false"

        $headers = @{
            'Authorization' = "Bearer $Token"
        }

        $data = Invoke-RestMethod -Uri $uri -Headers $headers

        $data
    }   
}

<#
.SYNOPSIS
    Checks if an object is numeric.
.DESCRIPTION
    This function determines whether the provided object is numeric. It checks if the object
    is of type Double, Int32, Int64, or if it's a string consisting only of numbers.
.PARAMETER Object
    The object to be checked for numeric type.
.EXAMPLE
    Is-Numeric 42
    # Output: True
.EXAMPLE
    Is-Numeric "123.5"
    # Output: True
.EXAMPLE
    Is-Numeric "abc"
    # Output: False
#>
function Is-Numeric {
    [CmdletBinding()]
    param (
        [Parameter(Mandatory=$true, ValueFromPipeline=$true)]
        [Object]$Object
    )

    $Object -is [System.Double] -or $Object -is [System.Int32] -or $Object -is [System.Int64] -or ($Object -match '^\d+(\.\d+)?$')
}

<#
.SYNOPSIS
    Sends a single line of data to an InfluxDB instance for insertion into a specified bucket.

.DESCRIPTION
    This function sends a single line of data to an InfluxDB instance using the InfluxDB line protocol format.
    It allows for easy insertion of data into a specific bucket within an organization.

.PARAMETER Influx_Host
    The URL of the InfluxDB instance.

.PARAMETER Influx_Org
    The name of the organization within InfluxDB.

.PARAMETER Influx_Bucket
    The name of the bucket within InfluxDB where the data will be inserted.

.PARAMETER Influx_Token
    The authorization token used for authentication with InfluxDB.

.PARAMETER InfluxLine
    A single line of data in the InfluxDB line protocol format to be inserted into InfluxDB.

.EXAMPLE
    Register-LinesToInfluxDB -Influx_Host "http://localhost:8086" -Influx_Org "MyOrg" -Influx_Bucket "Weather" -Influx_Token "my-token" -InfluxLine "temperature,location=NewYork value=25.5,humidity=60,pressure=1013.25 1612113600"
#>
function Register-LinesToInfluxDB {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)][string]$Influx_Host,
        [Parameter(Mandatory=$true)][string]$Influx_Org,
        [Parameter(Mandatory=$true)][string]$Influx_Bucket,
        [Parameter(Mandatory=$true)][string]$Influx_Token,
        [Parameter(Mandatory=$true)][string]$InfluxLine
    )
    
    process {
        $uri = "http://$Influx_Host/api/v2/write?org=$Influx_Org&bucket=$Influx_Bucket&precision=s"
        Write-Verbose "URI: $uri"

        # Send POST request to InfluxDB API
        $response = Invoke-RestMethod -Uri $uri -Method Post -Headers @{
            Authorization = "Token $Influx_Token"
            "Content-Type" = "text/plain"
        } -Body ($lines -join "`n") -Verbose

        # Check response status
        if ($response.statusCode -ne 204) {
            Write-Host "Error: Failed to insert data into InfluxDB. Status code: $($response.statusCode). For line '$InfluxLine'"
        }
    }
}

<#
.SYNOPSIS
    Takes in weather data mesurements and writes it to InfluxDB.
.DESCRIPTION
    Takes in weather data mesurements and writes it to InfluxDB.
#>
function Register-WeatherDataMesurement {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)][string]$module_id, 
        [Parameter(Mandatory=$true)][string]$module_name,
        [Parameter(Mandatory=$true)][string]$home_id,
        [Parameter(Mandatory=$true)][string]$home_name,
        [Parameter(Mandatory=$true)]$dashboard_data
    )
    
    begin {
    }
    
    process {
        $timestamp = $dashboard_data.time_utc

        if ($false -eq (is-numeric -object $timestamp)) {
            # if timestamp is invalid, set the current time.
            $timestamp = [int][Math]::Floor([DateTime]::UtcNow.Subtract([DateTime]::Parse("1970-01-01")).TotalSeconds) 
        }

        $measurements = foreach ($property in $dashboard_data.PSObject.Properties) {
            $name = $property.name
            $value = $property.value

            $skip = @(
                'time_utc'
            )

            if ($skip -notcontains $name) {
                if (($name -notmatch "^(?:time|date)_") -and (Is-Numeric -Object $value)) {
                    $value = "{0:N1}" -f $value # one decimal, so that influxDB understands that it should store it as double.
                } elseif ((Is-Numeric -Object $Value) -ne $true) {
                    # not numeric. Quote value so it doesn't get interpeted as other datatype.
                    $value = "`"$value`""
                }

                @{
                    measurement = $name
                    tags = @{
                        'module_id' = $module_id
                        'module_name' = $module_name
                        'home_id' = $home_id
                        'home_name' = $home_name
                    }
                    fields = @{
                        value = $value
                    }
                    timestamp = $timestamp
                }
            }


        }

        # Convert data to InfluxDB Line Format
        $lines = foreach($measurement in $measurements) {
            $tags = foreach ($key in $measurement.tags.keys) {
                "$key=$($measurement.tags.$key -replace ' ','\ ')" #escape spaces with \
            }
            $tags = $tags -join ','

            $fields = foreach ($key in $measurement.fields.keys) {
                "$key=$($measurement.fields.$key -replace ' ','\ ')" #escape spaces with \
            }
            $fields = $fields -join ','

            # line
            "$($measurement.measurement),$tags $fields $timestamp"
        }

        # Actually write the data to influxdb
        foreach ($line in $lines) {
            #Register-LinesToInfluxDB
        }

    }
    
    end {
    }
}

<#
.SYNOPSIS
    Takes in weather data processes it and writes it to InfluxDB.
.DESCRIPTION
    Takes in weather data processes it and writes it to InfluxDB.
#>
function Register-WeatherData {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)][string]$weatherdata
    )
    
    begin {
    }
    
    process {
        foreach ($station in $weatherdata.body.devices) {
            <#
                station_name
                station_id
                home_name
                home_id
                dashboard_data
            #>

            foreach ($module in $station.modules) {
                <#
                    battery_percent
                    battery_vp
                    dashboard_data
                    data_type
                    firmware
                    last_message
                    last_seen
                    last_setup
                    module_name
                    reachable
                    rf_status
                    type
                    _id
                #>
            }

        }
    }
    
    end {
    }
}