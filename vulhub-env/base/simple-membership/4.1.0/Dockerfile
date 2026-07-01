FROM wordpress:6.4-php8.2-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl less default-mysql-client unzip; \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

COPY plugins/simple-membership /var/www/html/wp-content/plugins/simple-membership
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/simple-membership

# MU-plugin 强制激活（Windows CRLF 兼容）
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate-simple-membership.php /var/www/html/wp-content/mu-plugins/
RUN apt-get update && apt-get install -y dos2unix && \
    dos2unix /var/www/html/wp-content/mu-plugins/force-activate-simple-membership.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate-simple-membership.php && \
    apt-get remove -y dos2unix && apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
