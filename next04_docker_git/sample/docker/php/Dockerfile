FROM php:8.2-fpm-alpine

# システムパッケージのインストール
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    autoconf \
    g++ \
    make \
    linux-headers

# PHP拡張モジュールのインストール
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        mbstring \
        intl \
        opcache \
        bcmath \
        exif

# Redis拡張のインストール
RUN pecl install redis \
    && docker-php-ext-enable redis

# Xdebug拡張のインストール（開発用）
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリの設定
WORKDIR /var/www/html

# PHPユーザーの作成と権限設定
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# ディレクトリの所有者変更
RUN chown -R www:www /var/www/html

# ユーザー切り替え
USER www

# ポート公開
EXPOSE 9000

# PHP-FPM起動
CMD ["php-fpm"]