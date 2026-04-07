# Use an optimized PHP-Apache base for security and performance
FROM php:8.3-apache

# 1. Install System Dependencies & Docker CLI
# This allows the GUI to execute 'docker' commands against the engine container
RUN apt-get update && apt-get install -y \
    docker.io \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure PHP Extensions
# Essential for generating PDF reports or processing scan images
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql

# 3. Security Hardening: User Permissions
# Adding the web user to the docker group so it can run 'docker exec'
RUN usermod -aG docker www-data

# 4. Apache Configuration
# Enable rewrite for 'Pro' clean URLs (e.g., /scan/1 instead of scan.php?id=1)
RUN a2enmod rewrite ssl

# 5. Application Environment
WORKDIR /var/www/html

# Copy your GUI source code into the container
COPY . /var/www/html

# Set final ownership to the web user
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose standard web ports
EXPOSE 80 443

# Start Apache in the foreground
CMD ["apache2-foreground"]