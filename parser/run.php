<?php

$captchaCounter = 0;
$message = '';

while (true) {
    sleep(mt_rand(0,25));
    include 'parser.php';
    if ($message == 'Captcha') {
        $captchaCounter++;
        sleep(60*$captchaCounter);
    } else {
        $captchaCounter--;
    }
    echo 'captchaCounter=' . $captchaCounter . "\n";
}
