#!/bin/bash
echo "Starting Laravel Task Scheduler..."
echo "Press Ctrl+C to stop"
echo

while true; do
    php artisan schedule:run
    sleep 30
done 