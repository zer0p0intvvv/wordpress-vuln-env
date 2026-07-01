FROM wordpress:6.4-php8.2-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl less default-mysql-client unzip dos2unix; \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp && \
    wp --info --allow-root

# 预下载漏洞版本插件 zip（绕过运行时 wp.org 下载延迟）
RUN curl -fsSL --retry 5 --retry-delay 3 --http1.1 -o /tmp/mapsvg.6.2.19.zip \
    "https://downloads.wordpress.org/plugin/mapsvg.6.2.19.zip"

# MU-plugin 强制激活（Windows CRLF 兼容）
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate.php /var/www/html/wp-content/mu-plugins/
RUN dos2unix /var/www/html/wp-content/mu-plugins/force-activate.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate.php

# 替换官方 entrypoint
RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh 2>/dev/null || true

CMD ["apache2-foreground"]
