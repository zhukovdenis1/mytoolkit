<?php

namespace App\Helpers;

class ArticleHelper
{
    public function __construct(private readonly DateTimeHelper $dateTimeHelper)
    {

    }
    public function replace(?string $text): ?string
    {
        if (is_null($text)) {
            return null;
        }

        $replacements = [
            'year' => date('Y'),
            'month_text' => $this->dateTimeHelper->getMonthName(date('m'), 'nominative')
        ];

        foreach ($replacements as $key => $value) {
            // Ищем все варианты плейсхолдера в тексте
            preg_match_all('/\{\*\*\*(' . preg_quote($key, '/') . ')\*\*\*\}/i', $text, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $originalPlaceholder = $match[0];
                $originalKey = $match[1];

                // Определяем регистр оригинального ключа
                if (strtoupper($originalKey) === $originalKey) {
                    // ВСЕ ЗАГЛАВНЫЕ
                    $replacement = mb_strtoupper($value);
                } elseif (ucfirst($originalKey) === $originalKey) {
                    // Первая заглавная
                    $replacement = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
                } else {
                    // Оригинальный регистр
                    $replacement = $value;
                }

                $text = str_replace($originalPlaceholder, $replacement, $text);
            }
        }

        return $text;
    }

}
