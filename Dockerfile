FROM php:8.2-apache

# نسخ ملفات المشروع إلى داخل السيرفر
COPY . /var/www/html/

# تفعيل mod_rewrite الخاص بأباتشي (مهم لدعم روابط ديناميكية)
RUN a2enmod rewrite

# فتح بورت 80
EXPOSE 80
