# Imagen oficial de PHP 8.2 con servidor web Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Instalar dependencias del sistema y extensiones de PHP necesarias (MySQLi, PDO MySQL, cURL, MBString)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql curl mbstring \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache para escuchar dinámicamente en el puerto que Render asigne ($PORT o 80)
RUN sed -i 's/80/${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Copiar el código completo de la aplicación al directorio web de Apache
COPY . /var/www/html/

# Ajustar permisos de archivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer el puerto
EXPOSE 80

CMD ["apache2-foreground"]
