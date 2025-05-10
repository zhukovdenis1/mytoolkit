@echo off
set time=25
:loop

php parser_pikabu.php

ping 127.0.0.1 -n %time% >nul
Goto :loop
