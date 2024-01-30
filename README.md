# PS-Netatmo-InfluxDB
Netatmo to InfluxDB logger. Written in Powershell, running in Docker.

This container is made to be able to run on Raspberry Pi OS.

## Build
```
git clone git@github.com:turbosnute/ps-netatmo-influxdb.git
cd ps-netatmo-influxdb
docker build -t "ps-netatmo-influxdb" .
```

## Run
```
docker network create weather-net
docker run -d -p 8800:80 -v psnetatmo:/config --name "ps-netatmo-influxdb" --network "weather-net" ps-netatmo-influxdb 
```

## Setup
### Create Netatmo Client Id and Secret
This script utilizes the Netatmo API to access data. To authenticate with the API, you'll need to obtain a Client ID and Client Secret by following these steps:

1. **Visit Netatmo Developer Website:** Go to the [Netatmo Developer](https://dev.netatmo.com) portal and sign in.

2. **Create an Application:** Once logged in, navigate to the "My Apps" section and create a new application. Fill in the required details about your application.

3. **Retrieve Client ID and Client Secret:** After creating the application, you'll be provided with a Client ID and Client Secret.

### InfluxDB Setup
If you don't have a InfluxDB server already you can run one in docker:
```
docker run -d --name=influxdb -p 8086:8086 --network "weather-net" -v influxdb-vol:/root/.influxdb2 influxdb:2.7.5
```

If you already have a influxdb container running. Make sure it can be contacted from the ps-netatmo-influxdb container. One way to do this is to join it to the same network:
```
docker network connect "weather-net" influxdb
```

...
### PS Netatmo InfluxDB Config
...