# 🚀 Deployment Guide - EC2 with SSL

## Prerequisites

1. **EC2 Instance** with Ubuntu 20.04 or later
2. **Domain Name** pointing to your EC2 instance (aahome.click)
3. **Security Groups** configured:
    - Port 80 (HTTP)
    - Port 443 (HTTPS)
    - Port 22 (SSH)
    - Port 3306 (MySQL - optional, for external access)

## Step 1: Connect to EC2 Instance

```bash
ssh -i your-key.pem ubuntu@your-ec2-ip
```

## Step 2: Clone Your Repository

```bash
git clone your-repository-url
cd your-project-directory
```

## Step 3: Configure Environment

```bash
# Copy environment file
cp .env.production.example .env

# Edit environment file
nano .env
```

**Important configurations:**

-   Update `APP_URL=https://aahome.click`
-   Set strong database passwords
-   Configure email settings
-   Set JWT secrets

## Step 4: Update Docker Compose

Edit `docker-compose.yml` and update the email in certbot service:

```yaml
certbot:
    command: certonly --webroot --webroot-path=/var/www/html --email your-email@example.com --agree-tos --no-eff-email -d aahome.click -d www.aahome.click
```

## Step 5: Run Deployment Script

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

## Step 6: Manual SSL Certificate (if needed)

If the automatic certificate generation fails:

```bash
# Stop nginx
docker-compose stop nginx

# Get certificate manually
docker-compose run --rm certbot certonly --webroot --webroot-path=/var/www/html --email your-email@example.com --agree-tos --no-eff-email -d aahome.click -d www.aahome.click

# Start nginx
docker-compose up -d nginx
```

## Step 7: Verify Deployment

1. **Check containers:**

    ```bash
    docker-compose ps
    ```

2. **Check SSL certificate:**

    ```bash
    docker-compose exec nginx nginx -t
    ```

3. **Test website:**
    - Visit `https://aahome.click`
    - Check SSL certificate in browser

## Step 8: Database Setup

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed data (if needed)
docker-compose exec app php artisan db:seed

# Generate application key
docker-compose exec app php artisan key:generate

# Clear and cache config
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Step 9: File Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Monitoring and Maintenance

### Check Logs

```bash
# Application logs
docker-compose logs app

# Nginx logs
docker-compose logs nginx

# SSL renewal logs
tail -f /var/log/ssl-renewal.log
```

### SSL Certificate Renewal

Certificates auto-renew daily at 12:00 PM. Manual renewal:

```bash
./docker/ssl/renew-ssl.sh
```

### Backup Database

```bash
# Create backup
docker-compose exec db mysqldump -u root -p your_database > backup.sql

# Restore backup
docker-compose exec -T db mysql -u root -p your_database < backup.sql
```

## Troubleshooting

### SSL Certificate Issues

1. Check domain DNS settings
2. Verify ports 80 and 443 are open
3. Check certbot logs: `docker-compose logs certbot`

### Nginx Issues

1. Test configuration: `docker-compose exec nginx nginx -t`
2. Check logs: `docker-compose logs nginx`
3. Restart nginx: `docker-compose restart nginx`

### Application Issues

1. Check Laravel logs: `docker-compose exec app tail -f storage/logs/laravel.log`
2. Clear caches: `docker-compose exec app php artisan cache:clear`
3. Check permissions: `ls -la storage bootstrap/cache`

## Security Checklist

-   [ ] SSL certificate installed and working
-   [ ] HTTP to HTTPS redirect working
-   [ ] Security headers configured
-   [ ] Database passwords are strong
-   [ ] JWT secrets are secure
-   [ ] File permissions are correct
-   [ ] Firewall rules configured
-   [ ] Regular backups scheduled
-   [ ] SSL auto-renewal working

## Performance Optimization

1. **Enable OPcache:**

    ```bash
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache
    ```

2. **Configure Redis for sessions and cache**

3. **Enable Nginx gzip compression** (already configured)

4. **Set up monitoring** (optional):
    - New Relic
    - DataDog
    - AWS CloudWatch

## Support

For issues or questions:

1. Check logs first
2. Review this documentation
3. Check Laravel and Docker documentation
4. Contact your development team
