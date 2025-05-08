<?php

declare(strict_types=1);

namespace App\Helpers;
class StringHelper
{
    public function uid(int $length = 8): string
    {
        // Генерируем уникальную строку на основе текущего времени и случайных данных
        $uniquePart = uniqid('', true); // "префикс" + микросекунды

        // Преобразуем в шестнадцатеричный формат
        $hex = bin2hex($uniquePart);

        // Обрезаем до нужной длины
        return substr($hex, 0, $length);
    }

    public function normalizeFileName(string $string): string
    {
        $string = self::transliterate($string);

        $string = substr($string, 0, 32);

        return $string;
    }

    public function transliterate(string $string): string
    {
        // Транслитерация русских букв на английские
        $transliteration = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
            'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
            'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo',
            'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
            'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        // Применяем транслитерацию
        $string = strtr($string, $transliteration);

        // Приводим строку к нижнему регистру
        $string = mb_strtolower($string, 'UTF-8');

        // Заменяем все символы, кроме английских букв и цифр и -, на _
        $string = preg_replace('/[^a-z0-9\-]/', '_', $string);

        // Заменяем все подряд идущие _ на один символ _
        $string = preg_replace('/_+/', '_', $string);

        // Убираем _ в начале и конце строки, если они есть
        $string = trim($string, '_');

        return $string;
    }

    public function buildUri(string $string): string
    {
        $string = self::transliterate($string);

        $string = str_replace('_', '-', $string);
        // Заменяем все подряд идущие - на один символ -
        $string = preg_replace('/-+/', '-', $string);

        return $string;
    }
}
