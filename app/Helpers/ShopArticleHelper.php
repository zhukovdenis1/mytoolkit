<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Modules\ShopArticle\Models\ShopArticle;

class ShopArticleHelper
{
    public function __construct(
        private readonly DateTimeHelper $dateTimeHelper,
        private readonly EditorHelper $editorHelper
    ) {}
    public function replace(?string $text): ?string
    {
        if (is_null($text)) {
            return null;
        }

        $replacements = [
            'year' => date('Y'),
            'month_text' => $this->dateTimeHelper->getMonthName((int)date('m'), 'nominative')
        ];

        foreach ($replacements as $key => $value) {
            // Ищем все варианты плейсхолдера в тексте
            preg_match_all('/\{\*(' . preg_quote($key, '/') . ')\*\}/i', $text, $matches, PREG_SET_ORDER);

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

    public function getDataByCode(string $code): array
    {
        $article = ShopArticle::query()->where('code', $code)->first();
        $textData = json_decode($article->text ?? '', true);

        $contentParts = [];

        if ($article->separation) {//туду не только 1
            $sepData = [array_shift($textData)];
            $sep = $this->replace($this->editorHelper->arrayToHtml($sepData)) ?? '';

            $contentParts[] = $sep;
        }

        $content = array_merge([$this->replace($this->editorHelper->arrayToHtml($textData)) ?? ''], $contentParts);

        $title = $article->title ?? $article->h1;

        return [
            'h1' => $this->replace($article->h1) ?? '',
            'title' => $this->replace($title) ?? '',
            'keywords' =>  $this->replace($article->keywords) ?? '',
            'description' =>  $this->replace($article->description) ?? '',
            'content' =>  $content,
        ];
    }

}
