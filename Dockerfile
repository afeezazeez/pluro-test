# Use PHP-FPM image
FROM php:8.3-fpm

# Set up variables
ARG user=yourusername
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Node.js, npm and run the build
RUN npm install -g npm@latest n && \
    n latest && \
    npm install && \
    npm run build

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Configure Nginx
COPY docker/nginx/laravel.conf /etc/nginx/conf.d/default.conf

# Set file permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 80 for Render and local
EXPOSE 80

# Start services
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
