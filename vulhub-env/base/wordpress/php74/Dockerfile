FROM wordpress:php7.4-apache

RUN apt-get update --allow-insecure-repositories 2>/dev/null || apt-get update -o Acquire::AllowInsecureRepositories=true || true && \
    apt-get install -y --allow-unauthenticated --no-install-recommends \
    curl less default-mysql-client unzip 2>/dev/null || \
    apt-get install -y --no-install-recommends curl less unzip || true && \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp && wp --info --allow-root

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
