# Hướng dẫn Triển khai Production

## Mục lục
1. [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
2. [Chuẩn bị Server](#chuẩn-bị-server)
3. [Cài đặt ứng dụng](#cài-đặt-ứng-dụng)
4. [Cấu hình Web Server](#cấu-hình-web-server)
5. [Bảo mật](#bảo-mật)
6. [Tối ưu hiệu năng](#tối-ưu-hiệu-năng)
7. [Backup và Recovery](#backup-và-recovery)
8. [Monitoring](#monitoring)

---

## Yêu cầu hệ thống

### Minimum
- CPU: 1 core
- RAM: 1GB
- Storage: 10GB SSD
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+

### Recommended
- CPU: 2+ cores
- RAM: 2GB+
- Storage: 20GB+ SSD
- PHP 8.1+
- MySQL 8.0+ / MariaDB 10.6+

---

## Chuẩn bị Server

### 1. Cài đặt PHP và Extensions

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd
```

**CentOS/RHEL:**
```bash
sudo dnf install php php-fpm php-mysqlnd php-mbstring php-xml php-curl php-zip php-gd
```

### 2. Cài đặt MySQL/MariaDB

```bash
# Ubuntu
sudo apt install mysql-server

# Hoặc MariaDB
sudo apt install mariadb-server

# Secure installation
sudo mysql_secure_installation
```

### 3. Cài đặt Nginx

```bash
sudo apt install nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

---

## Cài đặt ứng dụng

### 1. Clone repository

```bash
cd /var/www
sudo git clone https://github.com/your-repo/taskflow.git
sudo chown -R www-data:www-data taskflow
cd taskflow
```

### 2. Tạo file .env

```bash
cp .env.example .env
nano .env
```

Cấu hình:
```env
APP_ENV=production
APP_DEBUG=false

DB_HOST=localhost
DB_NAME=taskflow2
DB_USER=taskflow_user
DB_PASS=your_secure_password
```

### 3. Tạo Database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE taskflow2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'taskflow_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON taskflow2.* TO 'taskflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Import Schema và Migrations

```bash
mysql -u taskflow_user -p taskflow2 < database/taskflow2.sql
php database/migrate-security-fixes.php
php database/migrate-add-indexes.php
```

### 5. Thiết lập quyền thư mục

```bash
# Quyền cơ bản
sudo chown -R www-data:www-data /var/www/taskflow
sudo find /var/www/taskflow -type f -exec chmod 644 {} \;
sudo find /var/www/taskflow -type d -exec chmod 755 {} \;

# Thư mục cần ghi
sudo chmod -R 775 /var/www/taskflow/uploads
sudo chmod -R 775 /var/www/taskflow/storage
sudo chmod -R 775 /var/www/taskflow/logs
```

---

## Cấu hình Web Server

### Nginx Configuration

Tạo file `/etc/nginx/sites-available/taskflow`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/taskflow;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Timeout for long operations
        fastcgi_read_timeout 300;
    }

    # Block access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    # Block PHP execution in uploads
    location ~* /uploads/.*\.php$ {
        deny all;
    }

    # Cache static files
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Logs
    access_log /var/log/nginx/taskflow_access.log;
    error_log /var/log/nginx/taskflow_error.log;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/taskflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL với Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

## Bảo mật

### 1. PHP Configuration

Edit `/etc/php/8.1/fpm/php.ini`:

```ini
; Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Hide PHP version
expose_php = Off

; Session security
session.cookie_httponly = On
session.cookie_secure = On
session.use_strict_mode = On

; Upload limits
upload_max_filesize = 10M
post_max_size = 12M
max_file_uploads = 10

; Error handling (production)
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

### 2. MySQL Security

```sql
-- Remove anonymous users
DELETE FROM mysql.user WHERE User='';

-- Disable remote root login
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

-- Remove test database
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

FLUSH PRIVILEGES;
```

### 3. Firewall

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

---

## Tối ưu hiệu năng

### 1. PHP OPcache

Edit `/etc/php/8.1/fpm/conf.d/10-opcache.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 2. MySQL Tuning

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# InnoDB settings
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2

# Query cache (MySQL 5.7)
query_cache_type = 1
query_cache_size = 32M

# Connection settings
max_connections = 100
```

### 3. Application Cache

TaskFlow có built-in file cache. Để clear cache:

```bash
rm -rf /var/www/taskflow/storage/cache/*
```

---

## Backup và Recovery

### 1. Database Backup Script

Tạo `/opt/scripts/backup-taskflow.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/taskflow"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="taskflow2"
DB_USER="taskflow_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/taskflow/uploads

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

### 2. Cron Job

```bash
sudo crontab -e
```

Thêm:
```
# Daily backup at 2 AM
0 2 * * * /opt/scripts/backup-taskflow.sh >> /var/log/taskflow-backup.log 2>&1
```

### 3. Recovery

```bash
# Restore database
gunzip < /var/backups/taskflow/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u taskflow_user -p taskflow2

# Restore uploads
tar -xzf /var/backups/taskflow/uploads_YYYYMMDD_HHMMSS.tar.gz -C /
```

---

## Monitoring

### 1. Log Rotation

Tạo `/etc/logrotate.d/taskflow`:

```
/var/www/taskflow/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### 2. Health Check Endpoint

Tạo `/var/www/taskflow/api/health.php`:

```php
<?php
header('Content-Type: application/json');

$checks = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'checks' => []
];

// Database check
try {
    require_once __DIR__ . '/../bootstrap.php';
    $db = \Core\Database::getInstance();
    $db->fetchOne("SELECT 1");
    $checks['checks']['database'] = 'ok';
} catch (Exception $e) {
    $checks['status'] = 'error';
    $checks['checks']['database'] = 'error';
}

// Disk space check
$freeSpace = disk_free_space('/var/www/taskflow');
$checks['checks']['disk_free'] = round($freeSpace / 1024 / 1024 / 1024, 2) . ' GB';

echo json_encode($checks);
```

### 3. Uptime Monitoring

Sử dụng các dịch vụ như:
- UptimeRobot (free)
- Pingdom
- StatusCake

Monitor endpoint: `https://your-domain.com/api/health.php`

---

## Troubleshooting

### Lỗi 500 Internal Server Error
```bash
# Check PHP error log
tail -f /var/log/php/error.log

# Check Nginx error log
tail -f /var/log/nginx/taskflow_error.log
```

### Lỗi Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/taskflow
sudo chmod -R 775 /var/www/taskflow/uploads
```

### Database Connection Error
```bash
# Test connection
mysql -u taskflow_user -p -h localhost taskflow2

# Check MySQL status
sudo systemctl status mysql
```

---

## Checklist Triển khai

- [ ] Server đã cài đặt PHP 8.x, MySQL, Nginx
- [ ] Clone code và cấu hình .env
- [ ] Tạo database và import schema
- [ ] Chạy migrations
- [ ] Cấu hình Nginx với SSL
- [ ] Thiết lập quyền thư mục
- [ ] Cấu hình firewall
- [ ] Thiết lập backup tự động
- [ ] Cấu hình log rotation
- [ ] Test health check endpoint
- [ ] Thiết lập monitoring

---

*Cập nhật: 29/12/2024*
