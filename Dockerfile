FROM php:8.2-apache
ARG DEBIAN_FRONTEND=noninteractive
RUN docker-php-ext-install mysqli
# Include alternative DB driver
# RUN docker-php-ext-install pdo
# RUN docker-php-ext-install pdo_mysql
RUN apt-get update \
    && apt-get install -y libpng-dev \
    && apt-get install -y libzip-dev \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y libonig-dev \
    && apt-get install -y certbot \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2ensite default-ssl.conf
RUN a2enmod headers

COPY ./run.sh /bin/run.sh
CMD ["/bin/run.sh"]

COPY ./apache-conf/apache2.conf /etc/apache2/apache2.conf
COPY ./apache-conf/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

RUN service apache2 restart