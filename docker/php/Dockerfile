FROM php:7.4-fpm-alpine

ARG BUILD_GID=1000
ARG BUILD_UID=1000

# Install packages
RUN apk --update --no-cache add wget \
    curl \
    git \
    grep \
    build-base \
    libmcrypt-dev \
    libxml2-dev \
    imagemagick-dev \
    pcre-dev \
    libtool \
    make \
    autoconf \
    g++ \
    cyrus-sasl-dev \
    libgsasl-dev \
    supervisor \
    nano \
    libpng-dev \
    libzip-dev \
    openldap-dev \
    shadow \
    patch

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
RUN groupdel dialout
RUN groupmod -g ${BUILD_GID} www-data
RUN usermod -s /bin/sh -g ${BUILD_GID} -u ${BUILD_UID} www-data

# Install php modules
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions xdebug imagick mcrypt redis ldap pdo_mysql opcache gd zip soap

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer global require hirak/prestissimo --no-plugins --no-scripts --ignore-platform-reqs

# Set the locale
ENV MUSL_LOCALE_DEPS cmake make musl-dev gcc gettext-dev libintl
ENV MUSL_LOCPATH /usr/share/i18n/locales/musl
RUN apk add --no-cache \
    $MUSL_LOCALE_DEPS \
    && wget https://gitlab.com/rilian-la-te/musl-locales/-/archive/master/musl-locales-master.zip \
    && unzip musl-locales-master.zip \
      && cd musl-locales-master \
      && cmake -DLOCALE_PROFILE=OFF -D CMAKE_INSTALL_PREFIX:PATH=/usr . && make && make install \
      && cd .. && rm -r musl-locales-master
ENV LANG=nl_NL.UTF-8 \
    LANGUAGE=nl_NL.UTF-8 \
    LC_TIME=nl_NL.UTF-8 \
    LC_ALL=nl_NL.UTF-8

# Cleanup
RUN rm -R /var/cache/apk \
 && apk del --purge autoconf g++ make

# Copy configuration
# COPY ./docker/php/supervisord.conf /etc/supervisord.conf
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Copy files to working directory
RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
