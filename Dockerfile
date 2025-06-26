FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpng-dev && docker-php-ext-install gd

# Instala extensões básicas (você pode remover o que não quiser)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita o módulo de reescrita (importante para frameworks)
RUN a2enmod rewrite

# Copia os arquivos do projeto para o diretório público do Apache
COPY . /var/www/html/

# Define permissões (opcional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
