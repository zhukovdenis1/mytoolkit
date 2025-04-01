<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services\StorageManager;

use App\Modules\FileStorage\Models\File;

use CURLFile;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class TelegramStorage extends BaseStorage implements StorageInterface
{
    private $bot;
    private $token;
    private $chatId;

    public function __construct(string $botToken, string $chatId)
    {
        $this->bot = new BotApi($botToken);
        $this->token = $botToken;
        $this->chatId = $chatId;
        parent::__construct(true);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function saveFile(File $file, string $uploadedFilePath): array
    {
        $curlFile = new CURLFile($uploadedFilePath, null, "{$file->name}.{$file->ext}");
        $response = $this->bot->sendDocument($this->chatId, $curlFile, "{$file->module_name}:{$file->module_id}");
//        $data = [
//            'message_id' => $response->getMessageId(),
//            'chat_id' => $response->getChat()->getId(),
//            'date' => $response->getDate(),
//
//            // Данные документа
//            'document' => [
//                'file_id' => $response->getDocument()->getFileId(),
//                'file_name' => $response->getDocument()->getFileName(),
//                'mime_type' => $response->getDocument()->getMimeType(),
//                'file_size' => $response->getDocument()->getFileSize()
//            ],
//
//            // Опциональные поля
//            'caption' => $response->getCaption() ?? null,
//        ];

        $file = $this->bot->getFile($response->getDocument()->getFileId());

        return [
            'success' => true,
            'data' => [
                'telegram' => [
                    'chat_id' => $response->getChat()->getId(),
                    'message_id' => $response->getMessageId(),
                    'file_id' => $response->getDocument()->getFileId(),
                    'file_path' => $file->getFilePath(),
                ]
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function deleteFile(File $file, bool $backup = false): bool
    {
        $chatId = $backup ? $file->data['backup']['telegram']['chat_id'] : $file->data['telegram']['chat_id'];
        $messageId = $backup ? $file->data['backup']['telegram']['message_id'] : $file->data['telegram']['message_id'];
        return $this->bot->deleteMessage($chatId, $messageId);
    }


    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getUrl(File $file, bool $attachment = false, bool $cache = true): string
    {
        if ($cache) {
            if ($attachment) {
                return '/' . $this->cache->getUri($file) . '?attachment=1';
            } else {
                return '/' . $this->cache->getUri($file);
            }
        } else {
            //$storage = $file->storage()->first();
            return 'https://api.telegram.org/file/bot' . $this->token . '/' . $file->data['telegram']['file_path'];
        }
    }

    public function getDownloadPath(File $file): string
    {
        return 'https://api.telegram.org/file/bot' . $this->token . '/' . $file->data['telegram']['file_path'];
    }



//    /**
//     * Получает веб-ссылку на файл
//     *
//     * @param string $fileId file_id файла в Telegram
//     * @return string|null Ссылка на файл или null, если файл не найден
//     */
//    private function getFileUrl(File $file): ?string
//    {
////        try {
//            $file = $this->bot->getFile($fileId);
//            $filePath = $file->getFilePath();
//            return "https://api.telegram.org/file/bot{$this->token}/$filePath";
////        } catch (Exception $e) {
////            error_log("Ошибка при получении ссылки на файл: " . $e->getMessage());
////            return null;
////        }
//    }


    /**
     * Проверяет, существует ли файл в Telegram
     */
    public function fileExists(File $file): bool
    {
        return false;
        //Файл существует еще какое-то время после удаления
        //$file = $this->bot->getFile($file->data['telegram']['file_id']);
        //return $file !== null;
    }

    public function isDeleted(File $file): bool
    {
        return true;
    }
}
