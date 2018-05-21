# Kraken Bot

Kraken bot written in PHP using Laravel.

This bot watches imap mails and waits for emails with keywords. When email contains keywords like `pair (USDBTC|...) (sell|buy) 10% of account` or `pair (USDBTC|...) (sell|buy) 50 leverage=3`, it creates orders by using Kraken API.

## Requirements
Mysql, PHP 7.1, `php-imap-ext`, `curl`, `php-curl`, `supervisor`, Kraken API

## Installation

Clone this project with git `git clone https://github.com/butschster/KrakenBot.git` on your server or just download zip archive https://github.com/butschster/KrakenBot/archive/master.zip.

After clone you need to intall composer packages `composer install`.

### Configuration

Rename `.env.example` to `.env` and run artisan command `php artisan key:generate`

```
APP_NAME="Kraken Bot"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_DRIVER=sync

# Redis server for queues
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail server with alert emails
IMAP_USERNAME=
IMAP_PASSWORD=
IMAP_HOST=imap.gmail.com

# Kraken API
KRAKEN_KEY=
KRAKEN_SECRET=
KRAKEN_OTP=
```

Run artisan command `php artisan migrate --seed`

Add new job to supervisor with artisan command `php artisan imap:listen`.
You can use this instruction https://laravel.com/docs/5.6/queues#supervisor-configuration

```
[program:imap-listener]
process_name=imap_listener
command=php /var/www/artisan imap:listen
autostart=true
autorestart=true
user=www-data
numprocs=`
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/worker.log
```

