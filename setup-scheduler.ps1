# Laravel SiManKos - Windows Task Scheduler Setup
# Jalankan script ini sebagai Administrator

$phpPath    = "C:\xampp\php\php.exe"
$artisanPath = "C:\xampp\htdocs\Simankos\simankos\artisan"
$workingDir  = "C:\xampp\htdocs\Simankos\simankos"
$taskName    = "Laravel SiManKos Scheduler"

Write-Host "Mendaftarkan Laravel Scheduler ke Windows Task Scheduler..." -ForegroundColor Cyan

$action   = New-ScheduledTaskAction -Execute $phpPath -Argument "$artisanPath schedule:run" -WorkingDirectory $workingDir
$trigger  = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) -Once -At (Get-Date)
$settings = New-ScheduledTaskSettingsSet -MultipleInstances IgnoreNew -ExecutionTimeLimit (New-TimeSpan -Minutes 1)

Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -RunLevel Highest -Force

if ($?) {
    Write-Host "BERHASIL: Task '$taskName' sudah terdaftar." -ForegroundColor Green
    Write-Host "Scheduler akan menjalankan 'php artisan schedule:run' setiap 1 menit." -ForegroundColor Green
} else {
    Write-Host "GAGAL: Coba jalankan script ini sebagai Administrator." -ForegroundColor Red
}
