@echo off
set FECHA=%DATE:~6,4%-%DATE:~3,2%-%DATE:~0,2%_%TIME:~0,2%%TIME:~3,2%
set FECHA=%FECHA: =0%
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe" -u root -proot skynet > "C:\virtualhosts\SkyNET\Backend\Backups\backup_%FECHA%.sql"