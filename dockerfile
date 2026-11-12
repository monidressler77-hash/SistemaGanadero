# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache
# Instalamos las extensiones necesarias para MySQL (mysqli y pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Habilitamos el modulo rewrite de Apache (muy util para frameworks y rutas limpias)
RUN a2enmod rewrite