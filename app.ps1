$client_id = $env:netatmo_client_id
$client_secret = $env:netatmo_client_secret

$scope = "read_station"
$configPath = "/config/conf.json"

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
        [Parameter(Mandatory=$true)][string]$client_id,
        [Parameter(Mandatory=$true)][string]$client_secret,
        [Parameter(Mandatory=$true)][string]$configPath
    )
    
    begin {
    }
    
    process {
        $config = Get-Content -Path $configPath | ConvertFrom-Json

        $refresh_payload = @{
            grant_type = "refresh_token"
            refresh_token = $config.refresh_token
            client_id = $client_id
            client_secret = $client_secret
        }

        $refresh_args = @{
            uri = 'https://api.netatmo.com/oauth2/token'
            method = 'Post'
            body = $refresh_payload
            ContentType = 'application/json'
        }

        Invoke-RestMethod @refresh_args
    }
    
    end {
    }
}

#
# Check if config exists
#

$configExists = Test-Path -Path $configPath

if ($configExists) {
    $config = Get-Content $configPath | ConvertFrom-Json

    #
    # Before the loop(?). Try to refresh token.
    #

} else {
    # No config
}
<#
https://api.netatmo.com/oauth2/authorize?
    client_id=[YOUR_APP_ID]
    &redirect_uri=[YOUR_REDIRECT_URI]
    &scope=[SCOPE_SPACE_SEPARATED]
    &state=[SOME_ARBITRARY_BUT_UNIQUE_STRING]
#>