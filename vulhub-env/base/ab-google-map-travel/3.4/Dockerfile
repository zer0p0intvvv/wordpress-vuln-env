FROM wordpress:php7.4-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends default-mysql-client curl unzip; \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp && \
    wp --info --allow-root

COPY plugins/ab-google-map-travel /var/www/html/wp-content/plugins/ab-google-map-travel
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/ab-google-map-travel

# MU-plugin 强制激活（Windows CRLF 兼容）
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate.php /var/www/html/wp-content/mu-plugins/
RUN apt-get update && apt-get install -y dos2unix && \
    dos2unix /var/www/html/wp-content/mu-plugins/force-activate.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate.php && \
    apt-get remove -y dos2unix && apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh 2>/dev/null || true

CMD ["apache2-foreground"]
