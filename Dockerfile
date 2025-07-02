# استخدام صورة PHP 8.3 مع Apache
FROM php:8.3-apache

# تعيين متغيرات البيئة
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV LANG=ar_SA.UTF-8
ENV LC_ALL=ar_SA.UTF-8

# تثبيت المتطلبات الأساسية
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    locales \
    && rm -rf /var/lib/apt/lists/*

# إعداد اللغة العربية
RUN echo "ar_SA.UTF-8 UTF-8" > /etc/locale.gen && locale-gen

# تثبيت امتدادات PHP المطلوبة
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite headers

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ ملف composer أولاً للاستفادة من cache
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# نسخ باقي الملفات
COPY . .

# تشغيل composer مرة أخرى لإكمال التثبيت
RUN composer dump-autoload --optimize

# إعداد الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# إنشاء ملف .env من المثال إذا لم يكن موجوداً
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# فتح المنفذ 80
EXPOSE 80

# أمر بدء التشغيل
CMD ["apache2-foreground"] 