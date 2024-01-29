FROM php:8.3-apache-bullseye
RUN apt-get update && \
    apt-get install -y wget && \
    mkdir /powershell && \
    cd /powershell
COPY install-pwsh.sh /powershell/
RUN chmod 700 /powershell/install-pwsh.sh && \
    /bin/sh /powershell/install-pwsh.sh && \
    rm -rf /var/lib/apt/lists/* /usr/share/doc /usr/share/man && \
    apt-get clean && \
    mkdir /app/ && \
    mkdir /config/ && \
    chown www-data /config/ && \
    chmod 700 /config/
COPY ./web /var/www/html
COPY app.ps1 /app/