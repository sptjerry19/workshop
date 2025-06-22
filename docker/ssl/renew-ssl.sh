#!/bin/bash

# Script to renew SSL certificates
echo "Starting SSL certificate renewal..."

# Stop nginx temporarily
docker-compose stop nginx

# Renew certificates
docker-compose run --rm certbot renew

# Start nginx again
docker-compose up -d nginx

echo "SSL certificate renewal completed!"
