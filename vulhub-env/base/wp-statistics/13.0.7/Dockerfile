FROM wordpress:6.4-php8.2-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl less default-mysql-client unzip; \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp && \
    wp --info --allow-root

# Pre-copy plugin files from local source (fast, no network)
COPY plugins/wp-statistics /var/www/html/wp-content/plugins/wp-statistics
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/wp-statistics

# Pre-download plugin zip as fallback (CDN, network required)
RUN curl -fsSL --retry 5 --retry-delay 3 --http1.1 -o /tmp/wp-statistics.13.0.7.zip \
    "https://downloads.wordpress.org/plugin/wp-statistics.13.0.7.zip" || rm -f /tmp/wp-statistics.13.0.7.zip

# Note: No MU-plugin for this CVE.
# wp-statistics uses register_activation_hook to create DB tables.
# A MU-plugin would bypass the activation hook, leaving tables missing.

# Replace the original entrypoint with our custom one
RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh 2>/dev/null || true

CMD ["apache2-foreground"]
