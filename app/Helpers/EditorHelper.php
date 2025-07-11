<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Modules\Shop\Models\ShopProduct;
use Illuminate\Database\Eloquent\Builder;

class EditorHelper
{
    public function jsonToHtml(?string $json, string $title = ''): string
    {
        if (empty($json)) {
            return '';
        }

        $data = json_decode($json, true);

        return $this->arrayToHtml($data, $title);


    }

    public function arrayToHtml(array $data, string $title = ''): string
    {
        $html = '';

        $productsIds = [];
        $productsIdAe = [];

        foreach ($data as $d) {
            if ($d['type'] == 'product') {
                if (!empty($d['data']['id'])) {
                    $productsIds[] = intval($d['data']['id']);
                } elseif (!empty($d['data']['id_ae'])) {
                    $productsIdAe[] = $d['data']['id_ae'];
                }
            }
        }

        $productsByIds = [];
        $productsByIdAes = [];

        if ($productsIds || $productsIdAe)
        {
            $products = ShopProduct::query()
                ->where(function (Builder $query) use ($productsIds, $productsIdAe) {
                    $query->whereIn('id', $productsIds)
                        ->orWhereIn('id_ae', $productsIdAe);
                })
                ->get();

            foreach ($products as $product) {
                if (in_array($product->id, $productsIds)) {
                    $productsByIds[$product->id] = $product;
                } elseif (in_array($product->id_ae, $productsIdAe)) {
                    $productsByIdAes[$product->id_ae] = $product;
                }
            }
        }

        $imgCounter = 0;

        foreach ($data as $d) {
            if ($d['type'] == 'visual' || $d['type'] == 'visualSource') {
                $html .= $d['data']['text'] . PHP_EOL;
            } elseif ($d['type'] == 'image') {
                $imgCounter++;
                //var_dump($d['data']);die;
                $html .= '<p><img src="' . $d['data']['src'] . '" alt="' . htmlspecialchars($title) . ' №'. $imgCounter . '" /></p>' . PHP_EOL;
            } elseif ($d['type'] == 'product') {
                $product = null;
                if (isset($d['data']['id']) && isset($productsByIds[$d['data']['id']])) {
                    $product = $productsByIds[$d['data']['id']];
                } elseif (isset($d['data']['id_ae']) && isset($productsByIdAes[$d['data']['id_ae']])) {
                    $product = $productsByIdAes[$d['data']['id_ae']];
                }

                $html .= view('Shop::shop.editor-product', [
                    'product' => $product,
                    'title' => $d['data']['title'] ?? '',
                    'description' => $d['data']['description'] ?? '',
                    'props' =>  array_filter(array_map('trim', explode("\n", $d['data']['props'] ?? ''))),
                    'cons' => array_filter(array_map('trim', explode("\n", $d['data']['cons'] ?? ''))),
                    'idAe' => $d['data']['id_ae'] ?? ''
                ])->render();
            } elseif ($d['type'] == 'code') {
                //$html .= '<div style="position: relative"><textarea class="_hl2" mode="' . $d['data']['language'] . '">'.$d['data']['text'] . '</textarea></div>' . PHP_EOL;
                $html .= '<div style="position: relative"><textarea class="_hl" mode="' . $d['data']['language'] . '">'.$d['data']['text'] . '</textarea></div>' . PHP_EOL;
            }
        }

        return $html;
    }
}
