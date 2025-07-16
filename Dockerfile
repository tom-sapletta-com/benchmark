FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    sysbench \
    python3 \
    python3-pip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install glmark2 (for GPU benchmarking)
RUN apt-get update && apt-get install -y \
    libgl1-mesa-dev \
    libglu1-mesa-dev \
    freeglut3-dev \
    mesa-utils \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get update && apt-get install -y glmark2 || echo "glmark2 not available, will use fallback"

# Install Python dependencies
RUN pip3 install numpy scikit-learn

# Configure Apache
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/
RUN chmod +x /var/www/html/benchmark.sh
RUN chmod +x /var/www/html/publish.sh

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html
ENV PHP_MEMORY_LIMIT 128M
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/memory_limit = .*/memory_limit = ${PHP_MEMORY_LIMIT}/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/post_max_size = .*/post_max_size = ${PHP_POST_MAX_SIZE}/' "$PHP_INI_DIR/php.ini"

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
