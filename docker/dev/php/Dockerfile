FROM php:8.2-fpm

LABEL maintainer="cativo23"

# Build Args
ARG UID
ARG GID
ARG NODE_VERSION=20

# Environment Variables
ENV UID=${UID}
ENV GID=${GID}
ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

# Set working directory
WORKDIR /var/www/html

# Set timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install general dependencies
RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor libcap2-bin  \
    libpng-dev libzip-dev libicu-dev cron unixodbc-dev \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && apt-get update

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl \
    && docker-php-source delete

# Install PECL extensions
RUN pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear

# Install Composer, Node.js, Yarn, and MySQL client
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y default-mysql-client \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Create user and group
RUN groupadd --force -g ${GID} portfolio
RUN useradd -ms /bin/bash --no-user-group -g ${GID} -u ${UID} portfolio

# Set ownership of working directory
RUN chown -R portfolio:portfolio /var/www/html

# Update PHP-FPM configuration
RUN sed -i "s/user = www-data/user = portfolio/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = portfolio/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

# Set up cron
COPY crontab /etc/cron.d/scheduler
RUN  chmod 0644 /etc/cron.d/scheduler
RUN  crontab /etc/cron.d/scheduler

# Copy scripts and configuration files
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini $PHP_INI_DIR/conf.d/99-portfolio.ini

# Copy start-container script and make it executable
COPY start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# Expose port 9000 and 5173
EXPOSE 9000
EXPOSE 5173

# Set start-container as the entrypoint
ENTRYPOINT ["start-container"]
