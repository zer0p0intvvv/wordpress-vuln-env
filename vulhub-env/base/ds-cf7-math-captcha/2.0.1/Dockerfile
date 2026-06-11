FROM wordpress:php7.4-apache

RUN apt-get update && \
    apt-get install -y --allow-unauthenticated default-mysql-client curl dos2unix && \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

COPY plugins/ds-cf7-math-captcha /var/www/html/wp-content/plugins/ds-cf7-math-captcha
RUN find /var/www/html/wp-content/plugins/ds-cf7-math-captcha -name "*.php" -exec dos2unix {} \; && \
    chown -R www-data:www-data /var/www/html/wp-content/plugins/ds-cf7-math-captcha

# MU-plugin: force-activate the plugin without wp-cli (Windows-safe, no shell dependency)
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate-ds-cf7-math-captcha.php /var/www/html/wp-content/mu-plugins/
RUN dos2unix /var/www/html/wp-content/mu-plugins/force-activate-ds-cf7-math-captcha.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate-ds-cf7-math-captcha.php

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
