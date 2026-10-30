FROM php:8.3-apache

# Ekstensi PHP dan modul Apache
RUN docker-php-ext-install mysqli pdo pdo_mysql \
 && a2enmod rewrite headers \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Pastikan HANYA satu MPM aktif (prefork, wajib untuk PHP)
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
 && a2enmod mpm_prefork

# Izinkan .htaccess (default image: AllowOverride None)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY . /var/www/html/

# Folder upload (di Railway di-mount sebagai Volume ke /data)
RUN mkdir -p /data/uploads && chown -R www-data:www-data /data /var/www/html

# Saat start: pastikan satu MPM, siapkan folder upload, pakai PORT dari Railway
CMD rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
 && a2enmod mpm_prefork \
 && mkdir -p "${UPLOAD_DIR:-/data/uploads}" \
 && chown -R www-data:www-data "${UPLOAD_DIR:-/data/uploads}" \
 && sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
 && sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf \
 && apache2-foreground
