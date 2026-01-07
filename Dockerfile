FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Copiar el código fuente
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html
