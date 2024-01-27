FROM php:8.3-apache-bullseye
RUN apt-get update && \
    apt-get install -y wget && \
    ARCHY=`dpkg --print-architecture` && \
    mkdir /powershell && \
    cd /powershell && \
    wget -O powershell.tar.gz -q https://github.com/PowerShell/PowerShell/releases/download/v7.4.1/powershell-7.4.1-linux-$ARCHY.tar.gz && \
    tar -xvf powershell.tar.gz && \
    rm powershell.tar.gz && \
    ln -s /powershell/pwsh /bin/pwsh && \
    rm -rf /var/lib/apt/lists/* /usr/share/doc /usr/share/man && \
    apt-get clean && \
    mkdir /app/ && \
    mkdir /config/ && \
    chown www-data /config/ && \
    chmod 700 /config/
COPY ./web /var/www/html
COPY app.ps1 /app/