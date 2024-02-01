# PS-Netatmo-InfluxDB
Netatmo to InfluxDB logger. Written in Powershell, running in Docker.

This container is made to be able to run on Raspberry Pi 5 (Raspberry Pi OS) and have also been tested om amd64.
 
## Build
```
git clone git@github.com:turbosnute/ps-netatmo-influxdb.git
cd ps-netatmo-influxdb
docker build -t "ps-netatmo-influxdb" .
```

## Run
```
docker network create weather-net
docker run -d -p 8800:8800 -v psnetatmo:/config --name "ps-netatmo-influxdb" --network "weather-net" ps-netatmo-influxdb 
```

## Setup
### Create Netatmo Client Id and Secret
This script utilizes the Netatmo API to access data. To authenticate with the API, you'll need to obtain a Client ID and Client Secret by following these steps:

1. **Visit Netatmo Developer Website:** Go to the [Netatmo Developer](https://dev.netatmo.com) portal and sign in.

2. **Create an Application:** Once logged in, navigate to the "My Apps" section and create a new application. Fill in the required details about your application.

3. **Retrieve Client ID and Client Secret:** After creating the application, you'll be provided with a Client ID and Client Secret.

### InfluxDB Setup
If you don't already have an InfluxDB server, you can easily run one using Docker:
```
docker run -d --name=influxdb -p 8086:8086 --network "weather-net" -v influxdb-vol:/root/.influxdb2 influxdb:2.7.5
```

If you already have an InfluxDB container running, ensure it's accessible from the PS-Netatmo-InfluxDB container by joining it to the same network:
```
docker network connect "weather-net" influxdb
```
Once your InfluxDB server is up and running, you'll need to set up an organization, a bucket, and an API token with write and read access to the bucket.

The easiest way to do this is through the InfluxDB Web UI. Navigate to http://servername:8086

Copy the super user Token or create a new token with write and read access to the bucket.

### PS Netatmo InfluxDB Config
![WebUi Screenshot](https://raw.githubusercontent.com/turbosnute/ps-netatmo-influxdb/main/doc/webui.png)
1. **Access Configuration Page**:

   Navigate to `http://servername:8800` in your web browser. Replace `servername` with the hostname or IP address of the server where PS-Netatmo-InfluxDB is running.

2. **Enter Netatmo Client ID and Client Secret**:

   Enter your Netatmo client ID and client secret in the respective fields on the configuration page.

3. **Authenticate with Netatmo API**:

   Click on the "Authenticate Netatmo" button and follow the sign-in procedure to authenticate with the Netatmo API.

4. **Enter InfluxDB Settings**:
    Enter your InfluxDB hostname, organization, bucket and token in the respective fields.

   
5. **Test Connection**:

   Click on the "Test Connection" button to verify if the settings are valid. Ensure that the connection to InfluxDB is successful.

6. **Save InfluxDB Config**:

   After successful validation of the settings, click on "Save InfluxDB Config" to save the configuration.

## Start Logging

Once the configuration is complete, PS-Netatmo-InfluxDB will start logging data from your Netatmo weather station to your InfluxDB database. It may take up to 5 minutes for the logging to begin.

# Contributing
Contributions are welcome! Feel free to open issues or pull requests on GitHub to suggest improvements, report bugs, or add new features to PS-Netatmo-InfluxDB. Please keep in mind that I work on this project during my limited free time. Your understanding and patience are greatly appreciated.

# Help Wanted
I need help on the following:
1. **Multi platform build** - I need a easy way to build this project on arm64 and amd64 and publishing it on Docker Hub.
2. **Make the Web UI look better** - Yeah...
3. **Error handling** - I feel like there should be a little more error handling and feedback to the user. 