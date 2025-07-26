@echo off
:loop
php parser_reviews.php
timeout /t 10 /nobreak >nul
goto loop
