<?php

declare(strict_types=1);

namespace App\Modules\Util\Services;


class UtilService
{
    public function aliexpress(?string $content): string
    {
        $output = '';

        if ($content) {
            preg_match('/<script id="__STREAM_DATA__" type="application\/json">(.*?)<\/script><script src="https:\/\/st\.aliexpress\.ru\/mixer\/ssr\/1\/aer-assets\/system\.js">/isU', $content, $matches);

            $jsonTxt = $matches[1] ?? '';

            if (empty($jsonTxt)) {
                preg_match('/<script id="__AER_DATA__" type="application\/json">(.*?)<\/script><script src="https:\/\/st\.aliexpress\.ru\/mixer\/ssr\/1\/aer-assets\/system\.js">/isU', $content, $matches);

                $jsonTxt =  $matches[1] ?? '';

                if (empty($jsonTxt)) {
                    preg_match('/<script id="__AER_DATA__" type="application\/json">(.*?)<\/script><script src="https:\/\/st\.aestatic\.net\/mixer\/ssr\/1\/aer-assets\/system\.js">/isU', $content, $matches);

                    $jsonTxt = $matches[1] ?? '';
                }
            }

            $json = json_decode($jsonTxt, true);

            $output = $this->aliTree($json, array());

        }

        return $output;
    }

    private function aliTree($tree, array $parents): string
    {
        $BR = PHP_EOL;//'<br />';
        $SPACE = '';//'&nbsp;';
        $output = '';
        foreach ($tree as $k => $v) {
            for ($i=0;$i<count($parents);$i++) $output .= $SPACE;
            if (is_array($v)) {
                $output .= $k.$BR;
                $output .= $this->aliTree($v, array_merge($parents, array($k)));
            } else {
                foreach ($parents as $p) {$output .= '["'.$p.'"]';}
                $output .= '["'.$k.'"]';
                //echo ' - ' . '<u title="">'.$v.'</u>'.$BR;
                $output .= $v.$BR;
            }
        }
        return $output;
    }

    public function parseJson(?string $content): string
    {
        $output = '';
        if ($content) {
            $jsonTxt = trim($content);
            $json = json_decode($jsonTxt, true);

            $output = $this->jsonTree($json, array());
        }

        return $output;
    }

    private function jsonTree($tree, array $parents): string
    {
        $output = '';
        $BR = PHP_EOL;//'<br />';
        $SPACE = '';//'&nbsp;';
        foreach ($tree as $k => $v) {
            for ($i=0;$i<count($parents);$i++) $output .= $SPACE;
            if (is_array($v)) {
                $output .= $k.$BR;
                $output .= $this->jsonTree($v, array_merge($parents, array($k)));
            } else {
                foreach ($parents as $p) {$output .= '["'.$p.'"]';}
                $output .= '["'.$k.'"]';
                $output .= $v.$BR;
            }

        }

        return $output;
    }

    public function esb(?string $content): array
    {
        $output = [];
        if ($content) {
            $content = trim($content);

            $output['json'] = $this->convertSqlToJsonSchema($content);

            $output['postman'] = $this->convertSqlToExampleValues($content);
        }

        return $output;
    }

    private function convertSqlToJsonSchema(string $sql): string
    {
        $lines = explode("\n", $sql);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Извлекаем имя поля (все что между ` и `)
            preg_match('/`([^`]+)`/', $line, $matches);
            $fieldName = $matches[1] ?? '';
            if (empty($fieldName)) continue;

            // Определяем тип
            $type = 'string';
            if (preg_match('/(int|tinyint)\(/', $line)) {
                $type = 'integer';
            }

            // Формируем структуру для поля
            $result[$fieldName] = [
                'type' => $type,
                'empty' => true,
                'fatal' => false
            ];
        }

        // Форматируем вывод как JSON с отступами
        $output = '';
        foreach ($result as $field => $schema) {
            $output .= sprintf('    "%s": %s,%s',
                $field,
                json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                "\n"
            );
        }

        // Убираем последнюю запятую
        return rtrim($output, ",\n");
    }

    private function convertSqlToExampleValues(string $sql): string
    {
        $lines = explode("\n", $sql);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Извлекаем имя поля
            preg_match('/`([^`]+)`/', $line, $matches);
            $fieldName = $matches[1] ?? '';
            if (empty($fieldName)) continue;

            // Определяем тип и соответствующее значение
            if (preg_match('/(int|tinyint)\(/', $line)) {
                $value = 0;
            } else {
                $value = "string";
            }

            $result[$fieldName] = $value;
        }

        // Форматируем вывод
        $output = '';
        foreach ($result as $field => $value) {
            $output .= sprintf('            "%s": %s,%s',
                $field,
                is_string($value) ? '"' . $value . '"' : $value,
                "\n"
            );
        }

        // Убираем последнюю запятую
        return rtrim($output, ",\n");
    }


}
