# Legacy runtime for early-2000s PHP sites.
# NOTE: PHP 5.6 is EOL; this is appropriate for a low-risk portfolio site,
# ideally with Cloudflare in front and no writable endpoints.
FROM php:5.6-apache

# Common Apache modules used by old PHP sites (.htaccess rewrites, etc.)
RUN a2enmod rewrite headers

# Optional: set a sane Apache ServerName to avoid startup warnings
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

# If your site expects certain PHP settings (common in older apps), enable them here.
# Keep this minimal. You can add/remove as needed.
RUN { \
  echo "display_errors=Off"; \
  echo "log_errors=On"; \
  echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT"; \
  echo "expose_php=Off"; \
} > /usr/local/etc/php/conf.d/legacy-ks.ini

# Copy the site into Apache docroot.
# If you have a /public or /www folder, see the alternate version below.
COPY . /var/www/html/

# Ensure Apache can read the files
RUN chown -R www-data:www-data /var/www/html

# Fly will send traffic to port 8080 by default in many examples,
# but Apache in this image listens on 80. Fly can map external->internal,
# so we keep Apache on 80.
EXPOSE 80