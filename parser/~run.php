<?php

$captchaCounter = 0;
$message = '';

while (true) {
    include 'parser.php';
    if ($message == 'Captcha') {
        $captchaCounter++;
        sleep(20*($captchaCounter+1));
    } else {
        $captchaCounter && $captchaCounter--;
        sleep(mt_rand(30,60)*($captchaCounter+1));
    }
    echo 'captchaCounter=' . $captchaCounter . "\n";
}
