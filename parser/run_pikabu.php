<?php

$captchaCounter = 0;
$message = '';

while (true) {
    include 'parser_pikabu.php';
    if ($message == 'Captcha') {
        $captchaCounter++;
        sleep(60*$captchaCounter);
    } else {
        $captchaCounter && $captchaCounter--;
        sleep(mt_rand(30,60));
    }
    echo 'captchaCounter=' . $captchaCounter . "\n";
}
