#!/bin/bash

# Exit on error
set -e

DOMAIN="app.narasumberhukum.online"
DB_NAME="laravel_app"
DB_USER="laravel_user"
# Generate a random password for the database user
DB_PASS=$(openssl rand -base64 12)

echo "Starting setup for $DOMAIN..."

# 1. Update System
echo "Updating system..."
sudo apt update && sudo apt upgrade -y

# 2. Install Dependencies
echo "Installing Nginx, MySQL, and required packages..."
sudo apt install -y nginx mysql-server zip unzip git curl

# 3. Install PHP 8.2
echo "Installing PHP 8.2..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath

# 4. Install Composer
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 5. Install Node.js & NPM
echo "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 6. Configure MySQL
echo "Configuring MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "Database '$DB_NAME' created."
echo "User '$DB_USER' created with password: $DB_PASS"
echo "PLEASE SAVE THIS DB PASSWORD!"

# 7. Configure Nginx
echo "Configuring Nginx..."
sudo tee /etc/nginx/sites-available/$DOMAIN > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root /var/www/$DOMAIN/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# 8. Set Permissions
echo "Setting up directory..."
sudo mkdir -p /var/www/$DOMAIN
sudo chown -R $USER:$USER /var/www/$DOMAIN

# 9. Install Certbot
echo "Installing Certbot..."
sudo apt install -y certbot python3-certbot-nginx

echo "Setup complete! Now upload your code to /var/www/$DOMAIN"
echo "Don't forget to run: sudo certbot --nginx -d $DOMAIN"
