FROM php:8.2-apache

# تفعيل mod_rewrite
RUN a2enmod rewrite

# تغيير إعدادات Apache ليستمع على المنفذ 8080 بدلاً من 80
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# نسخ ملفات الموقع إلى السيرفر
COPY . /var/www/html/

# تعيين صلاحيات الملفات (احتياطًا)
RUN chown -R www-data:www-data /var/www/html

# تعريف المنفذ 8080 لأن Railway تتطلبه
EXPOSE 8080

# تشغيل Apache في الواجهة الأمامية
CMD ["apache2-foreground"]
