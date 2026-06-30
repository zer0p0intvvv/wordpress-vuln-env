FROM wordpress:php7.4-apache

RUN apt-get update && \
    apt-get install -y --allow-unauthenticated default-mysql-client curl && \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

COPY plugins/ab-google-map-travel /var/www/html/wp-content/plugins/ab-google-map-travel
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/ab-google-map-travel

# MU-plugin 强制激活（Windows CRLF 兼容）
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate.php /var/www/html/wp-content/mu-plugins/
RUN chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate.php

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
