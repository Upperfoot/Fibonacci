FROM nginx:1.15-alpine

# set timezone
RUN rm /etc/localtime &&\
    ln -s /usr/share/zoneinfo/Europe/London /etc/localtime &&\
    "date"

# install packages
RUN apk --no-cache add \
    curl bash openssl

# ensure www-data user exists
RUN set -x \
	&& addgroup -g 1000 -S www-data \
    && adduser -u 1000 -D -S -G www-data www-data

# configure nginx
COPY nginx.conf /etc/nginx/nginx.conf

# setup default vhost
COPY default.conf /etc/nginx/sites-enabled/default.conf

# make storage directory writable by nginx
RUN mkdir -p /var/www/app/storage/logs \
    && chown -R www-data:www-data /var/www/app/storage/ \
    && chmod -R 775 /var/www/app/storage/

WORKDIR /var/www/app
