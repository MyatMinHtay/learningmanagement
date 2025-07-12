@echo off
title Laravel Task Scheduler
echo Starting Laravel Task Scheduler...
echo Press Ctrl+C to stop
echo.

:loop
php artisan schedule:run
timeout /t 60 /nobreak > nul
goto loop 