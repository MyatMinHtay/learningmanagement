Write-Host "Starting Laravel Task Scheduler..." -ForegroundColor Green
Write-Host "Press Ctrl+C to stop" -ForegroundColor Yellow
Write-Host ""

while ($true) {
    php artisan schedule:run
    Start-Sleep -Seconds 30
} 