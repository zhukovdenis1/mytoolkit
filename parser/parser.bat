@echo off
:loop
php parser.php
timeout /t 10 /nobreak >nul
goto loop
