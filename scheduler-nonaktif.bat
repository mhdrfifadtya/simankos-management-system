@echo off
:: Minta hak Administrator otomatis
net session >nul 2>&1
if %errorLevel% neq 0 (
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

schtasks /change /tn "Laravel SiManKos Scheduler" /disable
if %errorLevel% equ 0 (
    echo.
    echo [OK] Laravel Scheduler DINONAKTIFKAN
    echo Cron job tidak akan berjalan sampai diaktifkan kembali.
) else (
    echo [GAGAL] Task tidak ditemukan. Jalankan setup-scheduler.ps1 terlebih dahulu.
)
echo.
pause
