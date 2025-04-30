@echo off
set time=25
:loop

php parser.php

ping 127.0.0.1 -n %time% >nul
Goto :loop
