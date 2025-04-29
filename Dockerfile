FROM php:8.2-apache

COPY . /var/www/html/

RUN ls -l /var/www/html/  # 👈 هذا السطر الجديد للطباعة داخل اللوج

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
