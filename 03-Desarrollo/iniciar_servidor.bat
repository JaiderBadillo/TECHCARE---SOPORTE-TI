@echo off
title Servidor TechCare Soporte TI
echo ========================================================
echo   Iniciando Servidor Web TechCare Soporte TI (PHP)
echo ========================================================
echo.
cd /d "%~dp0"
echo Abriendo en su navegador: http://localhost:8000
start http://localhost:8000/
echo.
echo Presione Ctrl+C en esta ventana para detener el servidor.
echo.
php -S 127.0.0.1:8000
pause
