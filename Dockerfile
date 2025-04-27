FROM php:8.3.20-apache

# Update & install dependencies
RUN apt-get update && \
    apt-get install -y \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libpcre3-dev \
        git \
        unzip \
        zip \
        libicu-dev \
        libonig-dev \
        libmagickwand-dev \
    && pecl install apcu imagick \
    && docker-php-ext-enable apcu imagick \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd intl mbstring curl xml mysqli pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install SQL Server driver (msodbcsql17)
RUN apt-get update && \
    apt-get install -y gnupg2 curl apt-transport-https unixodbc-dev && \
    curl -sSL -O https://packages.microsoft.com/config/debian/$(grep VERSION_ID /etc/os-release | cut -d '"' -f 2 | cut -d '.' -f 1)/packages-microsoft-prod.deb && \
    dpkg -i packages-microsoft-prod.deb
    # && apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql17

# Install PHP extensions
RUN apt-get update
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql18

RUN pecl install sqlsrv pdo_sqlsrv

RUN echo "; priority=20\nextension=sqlsrv.so\n" > /usr/local/etc/php/conf.d/sqlsrv.ini
RUN echo "; priority=30\nextension=pdo_sqlsrv.so\n" > /usr/local/etc/php/conf.d/pdo_sqlsrv.ini
# Nonaktifkan short_open_tag dengan mengubah php.ini
RUN echo "short_open_tag = Off" > /usr/local/etc/php/conf.d/00-custom.ini

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Copy source code
COPY . /var/www/

# Copy custom Apache config
COPY def.conf /etc/apache2/sites-available/000-default.conf

# Ganti DocumentRoot directory permissions dan konfigurasi
RUN sed -i 's|<Directory /var/www/html/>|<Directory /var/www/public/>|g' /etc/apache2/apache2.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory (optional tapi bagus)
WORKDIR /var/www/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers


# Start Apache
CMD ["apache2-foreground"]