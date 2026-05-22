FROM wordpress:6.4-php8.2-apache

LABEL maintainer="phithon <root@leavesongs.com>"

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        curl \
        less \
        default-mysql-client \
        unzip; \
    rm -rf /var/lib/apt/lists/*

# Install WP-CLI
RUN set -eux; \
    curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
    chmod +x /usr/local/bin/wp; \
    wp --info --allow-root

# Download vulnerable plugin version from WP.org archive
# CVE affects <= 3.5.7; 3.5.5 is the latest available version (3.5.7 was never published to WP.org)
RUN set -eux; \
    curl -fsSL -o /tmp/plugin.zip \
        https://downloads.wordpress.org/plugin/expand-maker.3.5.5.zip; \
    unzip /tmp/plugin.zip -d /usr/src/wordpress/wp-content/plugins/; \
    rm /tmp/plugin.zip

ARG INSTALL_XDEBUG=false
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
