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
docker run -d -p 8800:80 ps-netatmo-influxdb
```

## Setup
...