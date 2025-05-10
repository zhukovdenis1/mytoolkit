<?php

namespace App\Services;
//
//require __DIR__ . '/vendor/autoload.php';

use CURLFile;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;

class TelegramStorage
{
    private $bot;
    private $chatId;

    /**
     * @param string $botToken Токен вашего Telegram-бота
     * @param string $chatId ID чата или канала, куда будут сохраняться файлы
     */
    public function __construct(string $botToken, string $chatId)
    {
        $this->bot = new BotApi($botToken);
        $this->chatId = $chatId;
    }

    public function getFile($fileId): \TelegramBot\Api\Types\File
    {
        return $this->bot->getFile($fileId);
    }


    /**
     * Сохраняет файл в Telegram
     *
     * @param string $filePath Путь к файлу на сервере
     * @param string $fileName Имя файла в Telegram
     * @return array|null Возвращает массив с file_id и message_id
     */
    public function saveFile(string $filePath, string $fileName): ?array
    {
        try {
            $file = new CURLFile($filePath);
            $response = $this->bot->sendDocument($this->chatId, $file, $fileName);
//var_dump($response);die;
            return [
                'file_id' => $response->getDocument()->getFileId(),
                'message_id' => $response->getMessageId(),
            ];
        } catch (Exception $e) {
            error_log("Ошибка при сохранении файла: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Получает веб-ссылку на файл
     *
     * @param string $fileId file_id файла в Telegram
     * @return string|null Ссылка на файл или null, если файл не найден
     */
    public function getFileUrl(string $fileId): ?string
    {
        try {
            $file = $this->bot->getFile($fileId);
            $filePath = $file->getFilePath();
            $token = $botToken = '7983496183:AAFVULz9fk7FgiF9t3kkzh1wdZyFov4W15E';
            return "https://api.telegram.org/file/bot{$token}/$filePath";
        } catch (Exception $e) {
            error_log("Ошибка при получении ссылки на файл: " . $e->getMessage());
            return null;
        }
    }

    public function getFileUrlByMessageId(int $messageId, string $token): string
    {
        return 'https://api.telegram.org/file/bot' . $token . '/documents/file_' . $messageId . '.tmp';
    }

    /**
     * Удаляет файл из Telegram
     *
     * @param int $messageId ID сообщения, содержащего файл
     * @return bool Успешно ли удаление
     */
    public function deleteFile(int $messageId): bool
    {
        try {
            $this->bot->deleteMessage($this->chatId, $messageId);
            return true;
        } catch (Exception $e) {
            error_log("Ошибка при удалении файла: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Проверяет, существует ли файл в Telegram
     *
     * @param string $fileId file_id файла в Telegram
     * @return bool Существует ли файл
     */
    public function fileExists(string $fileId): bool
    {
        try {
            $file = $this->bot->getFile($fileId);
            return $file !== null;
        } catch (Exception $e) {
            error_log("Ошибка при проверке файла: " . $e->getMessage());
            return false;
        }
    }

}

// Пример использования
//$botToken = '7983496183:AAFVULz9fk7FgiF9t3kkzh1wdZyFov4W15E';
//$chatId = 'ВАШ_CHAT_ID'; // ID чата или канала
//
//$storage = new TelegramStorage($botToken, $chatId);
//
//// Сохранение файла
//$result = $storage->saveFile('/path/to/file.txt', 'file.txt');
//if ($result) {
//    echo "Файл сохранён. File ID: {$result['file_id']}, Message ID: {$result['message_id']}\n";
//} else {
//    echo "Ошибка при сохранении файла.\n";
//}
//
//// Проверка существования файла
//if ($storage->fileExists($result['file_id'])) {
//    echo "Файл существует.\n";
//} else {
//    echo "Файл не существует.\n";
//}
//
//// Удаление файла
//if ($storage->deleteFile($result['message_id'])) {
//    echo "Файл удалён.\n";
//} else {
//    echo "Ошибка при удалении файла.\n";
//}

/**
 * Получение токена бота:
 * Откройте Telegram и найдите бота @BotFather.
 *
 * Начните диалог с @BotFather и используйте команду /newbot.
 *
 * Следуйте инструкциям:
 *
 * Укажите имя бота (например, MyFileStorageBot).
 *
 * Укажите username бота (например, MyFileStorageBot).
 *
 * После создания бота вы получите токен. Он выглядит примерно так:
 *
 * 123456789:ABCdefGhIJKlmNoPQRstuVWXyz
 *
 * Добавьте бота в чат или канал:
 *
 * Добавьте бота в чат или канал, куда будут сохраняться файлы.
 *
 * Убедитесь, что бот имеет права на отправку сообщений.
 *
 * Получите chat_id:
 *
 * Для чата: отправьте любое сообщение в чат, затем используйте метод getUpdates для получения chat_id.
 *
 * Для канала: используйте формат @username (например, @my_channel).
 *
 * Запустите скрипт:
 *
 * Замените ВАШ_ТОКЕН_БОТА и ВАШ_CHAT_ID на реальные значения.
 *
 * Запустите скрипт.
 */
