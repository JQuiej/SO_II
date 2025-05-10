FROM php:8.1-apache

# Instala extensiones y herramientas si las necesitas
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia tu código
COPY . /var/www/html

# Crea uploads y ajusta permisos
RUN mkdir -p /var/www/html/uploads \
 && chown -R www-data:www-data /var/www/html/uploads

# Expón el puerto 80
EXPOSE 80

# Arranca Apache en foreground
CMD ["apache2-foreground"]
