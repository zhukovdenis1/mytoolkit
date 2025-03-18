<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\FileStore\Http\Requests\User\StoreFileRequest;
use App\Modules\FileStore\Http\Resources\User\FileResource;
use App\Services\TelegramStorage;

class CacheController extends BaseController
{
    public function public(int $id, string $name)
    {
        return $this->get($id, $name, false);
    }

    public function private(int $id, string $name)
    {
        return $this->get($id, $name, true);
    }
    private function get(int $id, string $name, bool $private)
    {
        var_dump($id, $name, $private);die;
        /*$info = pathinfo($fileName);

        $fileId = $info['filename']; // Имя файла без расширения
        $fileExt = $info['extension']; // Расширение файла
        $path = '/uploads/' . ($isMyStorage ? 'tme' : 't') . '/';

        $botToken = '7983496183:AAFVULz9fk7FgiF9t3kkzh1wdZyFov4W15E';
        $chatId = '138664577'; // ID бота

        $storage = new TelegramStorage($botToken, $chatId);
        $content = file_get_contents($storage->getFileUrlByMessageId($messageId, $botToken));

        file_put_contents(
            getcwd() . $path . $fileId . '.' . $fileExt,
            $content
        );

        return $content;*/
    }
}
