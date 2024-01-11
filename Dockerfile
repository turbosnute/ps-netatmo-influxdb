FROM php:8.3-apache-bullseye
RUN apt-get update && \
    apt-get install -y wget && \
    . /etc/os-release && \
    wget -q https://packages.microsoft.com/config/debian/$VERSION_ID/packages-microsoft-prod.deb && \
    dpkg -i packages-microsoft-prod.deb && \
    rm packages-microsoft-prod.deb && \
    apt-get update && \
    apt-get install -y powershell && \
    rm -rf /var/lib/apt/lists/* /usr/share/doc /usr/share/man && \
    apt-get clean
COPY ./web /var/www/html
RUN mkdir /app/
COPY app.ps1 /app/
RUN mkdir /config/
RUN chown www-data /config/
RUN chmod 700 /config/