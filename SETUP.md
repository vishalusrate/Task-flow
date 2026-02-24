# TaskFlow Pro — Setup Guide

## Step 1 — Folder copy kara
`taskflow` folder XAMPP/WAMP madhe copy kara:
- XAMPP: `C:\xampp\htdocs\taskflow\`
- WAMP:  `C:\wamp64\www\taskflow\`

## Step 2 — Database setup
1. phpMyAdmin open kara: http://localhost/phpmyadmin
2. `database.sql` chi content copy kara
3. SQL tab madhe paste karun Execute kara

## Step 3 — config.php edit kara
`config.php` file open kara ani change kara:
```php
define('DB_USER', 'root');     // apla MySQL username
define('DB_PASS', '');         // apla MySQL password
```
(XAMPP default: user=root, pass=empty)

## Step 4 — Login kara
Browser madhe open kara: http://localhost/taskflow

Demo login:
- Email: admin@taskflow.com
- Password: Admin@123

---

## Email Setup (Optional)
Gmail App Password banava:
1. Google Account → Security → 2-Step Verification ON kara
2. "App Passwords" search kara → Mail sathi create kara
3. `config.php` madhe:
```php
define('MAIL_USERNAME', 'your@gmail.com');
define('MAIL_PASSWORD', '16-char-app-password');
```
4. Composer install: `cd taskflow && composer install`

## WhatsApp Setup (Optional — Twilio)
1. twilio.com var signup kara
2. WhatsApp sandbox join kara
3. `config.php` madhe SID aur Token bhara

## Cron Setup (Auto Reminders)
Server madhe crontab -e madhe add kara:
```
0 * * * * php /var/www/html/taskflow/cron/reminders.php
```
Browser madhe manual test: http://localhost/taskflow/cron/reminders.php

---

## Features
- Login / Register (email verification optional)
- Tasks — Add/Edit/Delete, Priority, Status, CC emails, Recurring (Daily/Weekly/Monthly)
- Expenses — with person notification
- Dues/Udhari — track who owes what, partial payments
- Auto reminders — Email + WhatsApp + Push
- Admin panel — user management
