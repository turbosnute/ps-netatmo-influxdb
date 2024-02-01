FROM php:8.3-apache-bullseye
# Configure Apache to listen on port 8800  
RUN sed -i '/Listen 80/c\Listen 8800' /etc/apache2/ports.conf  
# Inform Docker that the container is listening on port 8800  
EXPOSE 8800
RUN apt-get update && \
    apt-get install -y wget && \
    apt-get install -y supervisor && \
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
COPY ./supervisord.conf /etc/supervisor/conf.d/
COPY ./web /var/www/html
COPY app.ps1 functions.ps1 /app/
CMD ["/usr/bin/supervisord"]