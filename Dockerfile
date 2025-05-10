# Usa la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instala extensiones necesarias para MySQL y PDO
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia todo el código de la aplicación al directorio de Apache
COPY . /var/www/html/

# Ajusta permisos de la carpeta de uploads
RUN chown -R www-data:www-data /var/www/html/uploads

# Expón el puerto 80
EXPOSE 80

# Comando por defecto para arrancar Apache en primer plano
CMD ["apache2-foreground"]