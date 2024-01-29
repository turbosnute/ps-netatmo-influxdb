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
        
    }
    
}