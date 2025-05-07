<?php

namespace App\Services;


class EditorService
{
    public function jsonToHtml(?string $json): string
    {
        if (empty($json)) {
            return '';
        }

        $data = json_decode($json, true);

        $html = '';

        foreach ($data as $d) {
            if ($d['type'] == 'visual') {
                $html .= $d['data']['text'] . PHP_EOL;
            } elseif ($d['type'] == 'image') {
                //var_dump($d['data']);die;
                $html .= '<p><img src="' . $d['data']['src'] . '" alt="' . $d['data']['text']. '" /></p>' . PHP_EOL;
            }
        }

        return $html;
    }
}
