# ServerCheck

Modern dark-mode uptime monitoring dashboard built with PHP Native, MySQL/MariaDB, and Nginx.

## Features

- User authentication (register/login/logout)
- Realtime dashboard with auto-refresh
- Add/delete monitored websites
- Online/offline status badges
- Response time tracking and IP detection
- Responsive futuristic neon UI
- Cronjob-based checker backend

## Setup

1. Create the database and tables:

   ```sql
   SOURCE migrate.sql;
   ```

2. Configure database access in `inc/config.php`.

3. Deploy with Nginx + PHP-FPM. Example server block:

   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /path/to/directory;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_pass unix:/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       }
   }
   ```

4. Set up a cronjob to run `checker.php` every minute:

   ```bash
   * * * * * php /path/to/ServerCheck/checker.php >> /var/log/servercheck.log 2>&1
   ```

## Usage

- Visit `auth/register.php` to create your first account.
- Login and add websites to begin monitoring.
- The dashboard refreshes every 10 seconds.

## Notes

- The checker uses cURL to determine website availability and fetch response time.
- Duplicate websites for the same user are prevented.
- Statuss are stored in the `websites` table and updated automatically by cron.
