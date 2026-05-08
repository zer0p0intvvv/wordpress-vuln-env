FROM wordpress:php7.4-apache

RUN apt-get update && \
    apt-get install -y --allow-unauthenticated default-mysql-client curl && \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

COPY plugins/paid-memberships-pro /var/www/html/wp-content/plugins/paid-memberships-pro
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/paid-memberships-pro

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
