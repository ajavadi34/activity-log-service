FROM php:5.6-apache

LABEL MAINTAINER="AJ"

ENV PORT=80

COPY . /var/www/html

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli \
    && apache2ctl restart

EXPOSE $PORT

# Commands to mount source code to container for development
# windows: ${pwd} / unix: $(pwd)
#docker run -d -p 8080:80 -v $(pwd):/var/www/html php:5.6-apache
#docker exec {containerId} docker-php-ext-install mysqli
#docker exec {containerId} apache2ctl restart