@echo off
:loop
php parser_pikabu.php
timeout /t 10 /nobreak >nul
goto loop
