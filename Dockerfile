# استخدام الصورة الرسمية لـ PHP 8.2 مع خادم Apache
FROM php:8.2-apache

# تحديث الحزم وتثبيت مكتبات الصور (GD)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# تثبيت دعم قواعد البيانات (MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# نسخ ملفات المشروع إلى مجلد الويب الافتراضي
COPY src/ /var/www/html/

# إنشاء مجلد uploads داخل الحاوية وإعطاء صلاحيات كتابة
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# فتح المنفذ 80 (منفذ Apache الافتراضي)
EXPOSE 80

