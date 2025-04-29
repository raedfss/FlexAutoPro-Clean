FROM php:8.2-apache

# نسخ ملفات المشروع إلى داخل السيرفر
COPY . /var/www/html/

# تفعيل mod_rewrite الخاص بأباتشي
RUN a2enmod rewrite

EXPOSE 80

# تشغيل Apache كخدمة رئيسية
CMD ["apache2-foreground"]
