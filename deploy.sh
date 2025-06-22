#!/bin/bash

# Deployment script for EC2 with SSL
set -e

echo "🚀 Starting deployment..."

# Update system packages
echo "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install Docker and Docker Compose if not installed
if ! command -v docker &> /dev/null; then
    echo "🐳 Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
fi

if ! command -v docker-compose &> /dev/null; then
    echo "🐳 Installing Docker Compose..."
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p docker/ssl docker/letsencrypt

# Set proper permissions
sudo chmod +x docker/ssl/renew-ssl.sh

# Copy environment file if not exists
if [ ! -f .env ]; then
    echo "⚠️  Please create .env file with your configuration"
    exit 1
fi

# Build and start containers
echo "🔨 Building and starting containers..."
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Wait for nginx to be ready
echo "⏳ Waiting for nginx to be ready..."
sleep 10

# Get SSL certificate
echo "🔒 Getting SSL certificate..."
docker-compose run --rm certbot certonly --webroot --webroot-path=/var/www/html --email duylinhvnu@gmail.com --agree-tos --no-eff-email -d aahome.click -d www.aahome.click

# Restart nginx with SSL
echo "🔄 Restarting nginx with SSL..."
docker-compose restart nginx

# Set up automatic renewal
echo "⏰ Setting up automatic SSL renewal..."
(crontab -l 2>/dev/null; echo "0 12 * * * cd $(pwd) && ./docker/ssl/renew-ssl.sh >> /var/log/ssl-renewal.log 2>&1") | crontab -

echo "✅ Deployment completed successfully!"
echo "🌐 Your site should be available at: https://aahome.click"
echo "📧 Don't forget to update the email in docker-compose.yml and run the certbot command again!"
