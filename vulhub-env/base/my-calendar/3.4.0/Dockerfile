FROM wordpress:php7.4-apache

RUN apt-get update && \
    apt-get install -y --allow-unauthenticated default-mysql-client curl dos2unix && \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

# 把插件和 mu-plugin 放到 /usr/src/wordpress/，WordPress entrypoint 首次启动时
# 会把这个目录 tar 拷贝到 /var/www/html/，确保文件在任何平台都不会丢失
COPY plugins/my-calendar /usr/src/wordpress/wp-content/plugins/my-calendar
RUN find /usr/src/wordpress/wp-content/plugins/my-calendar -name "*.php" -exec dos2unix {} \;

RUN mkdir -p /usr/src/wordpress/wp-content/mu-plugins
COPY mu-plugins/force-activate-my-calendar.php /usr/src/wordpress/wp-content/mu-plugins/
RUN dos2unix /usr/src/wordpress/wp-content/mu-plugins/force-activate-my-calendar.php

RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["apache2-foreground"]
