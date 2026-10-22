# Patched PHP runtime; update this patch tag when PHP publishes security releases.
FROM php:8.5.12-apache

RUN a2enmod rewrite headers negotiation \
 && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

RUN { \
  echo "auto_prepend_file=/var/www/html/request.php"; \
  echo "display_errors=Off"; \
  echo "log_errors=On"; \
  echo "error_reporting=E_ALL"; \
  echo "expose_php=Off"; \
  echo "date.timezone=Europe/Paris"; \
} > /usr/local/etc/php/conf.d/site.ini

COPY apache-site.conf /etc/apache2/conf-available/archive-site.conf
RUN a2enconf archive-site
COPY . /var/www/html/
EXPOSE 80
