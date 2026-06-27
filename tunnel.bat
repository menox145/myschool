@echo off
cd /d "%~dp0"
php artisan config:clear
start "Laravel 8000" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3
start "CF Tunnel" cmd /k "cloudflared tunnel run aisyah"
pause