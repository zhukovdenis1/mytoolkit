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

    public function getDataByCode(string $code): ?array
    {
        $article = ShopArticle::query()->where('code', $code)->first();
        if (!$article) return null;
        $textData = json_decode($article->text ?? '', true);

        $contentParts = [];
        $content = $this->separateData($textData, $article->separation);
        foreach ($content as &$c) {
            $c = $this->replace($this->editorHelper->arrayToHtml($c)) ?? '';
        }
        /*if ($article->separation) {//туду не только 1
            $sepData = [array_shift($textData)];
            $sep = $this->replace($this->editorHelper->arrayToHtml($sepData)) ?? '';

            $contentParts[] = $sep;
        }

        $content = array_merge([$this->replace($this->editorHelper->arrayToHtml($textData)) ?? ''], $contentParts);*/

        $title = $article->title ?? $article->h1;

        return [
            'h1' => $this->replace($article->h1) ?? '',
            'title' => $this->replace($title) ?? '',
            'keywords' =>  $this->replace($article->keywords) ?? '',
            'description' =>  $this->replace($article->description) ?? '',
            'content' =>  $content,
        ];
    }

    private function separateData(array $data, ?string $separationField): array
    {
        $separation = $this->parseSeparationField($separationField);

        $result = [];
        $usedIndices = []; // Храним реальные индексы PHP (0-based)
        $count = count($data);

        foreach ($separation as $key => $indices) {
            $result[$key] = [];

            foreach ($indices as $index) {
                // Преобразуем индекс в PHP-формат
                if ($index > 0) {
                    $phpIndex = $index - 1; // Человеческий 1-based -> PHP 0-based
                } elseif ($index < 0) {
                    $phpIndex = $count + $index; // -1 становится $count-1
                } else {
                    $phpIndex = 0; // 0 трактуем как первый элемент
                }

                if ($phpIndex >= 0 && $phpIndex < $count) {
                    $result[$key][] = $data[$phpIndex];
                    $usedIndices[$phpIndex] = true;
                }
            }
        }

        // Добавляем rest со всеми неиспользованными элементами
        $result['main'] = [];
        foreach ($data as $phpIndex => $value) {
            if (!isset($usedIndices[$phpIndex])) {
                $result['main'][] = $value;
            }
        }

        return $result;
    }

    private function parseSeparationField(?string $separation): array
    {
        $result = [];

        // Разделяем строку по ';' на отдельные вызовы функций
        $calls = explode(';', $separation ?? '');

        foreach ($calls as $call) {
            if (empty($call)) continue; // Пропускаем пустые строки

            // Находим позицию открывающей скобки '('
            $bracketPos = strpos($call, '(');
            if ($bracketPos === false) continue; // Пропускаем, если нет скобок

            // Имя функции — всё до скобки
            $funcName = substr($call, 0, $bracketPos);

            // Аргументы — содержимое между '(' и ')'
            $argsPart = substr($call, $bracketPos + 1, -1); // -1 чтобы убрать ')'

            // Разделяем аргументы по запятым
            $args = explode(',', $argsPart);

            // Очищаем каждый аргумент от пробелов и приводим к числу, если возможно
            $args = array_map(function($arg) {
                $arg = trim($arg);
                return is_numeric($arg) ? $arg + 0 : $arg;
            }, $args);

            $result[$funcName] = $args;
        }

        return $result;
    }

}
