<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Note\Http\Requests\User\File\DestroyNoteFileRequest;
use App\Modules\Note\Http\Requests\User\File\StoreNoteFileRequest;
use App\Modules\Note\Http\Resources\User\NoteFileResource;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\NoteFileService;
use Illuminate\Http\Request;
use App\Services\TelegramStorage;

class NoteFileController extends BaseController
{
    protected NoteFileService $noteFileService;

    public function __construct(NoteFileService $noteFileService)
    {
        $this->noteFileService = $noteFileService;
    }

    public function store(StoreNoteFileRequest $request, Note $note ): array
    {

        // Сохраняем файл
        $data = $this->noteFileService->saveFile(
            (int) $request->input('store_id'),
            (int) $note->id,//$request->route('note'),
            $request->user()->id,
            $request->files->get('file')//$request->file('file')-так не работает с файлами через link
        );

        return $data;
    }

    public function destroy(DestroyNoteFileRequest $request, Note $note ): array
    {
        return $this->noteFileService->deleteFile(
            (int) $request->input('store_id'),
            (int) $note->id,
            $request->user()->id,
            $request->input('path')
        );
    }

    public function tg(Request $request): array
    {
        $botToken = '7983496183:AAFVULz9fk7FgiF9t3kkzh1wdZyFov4W15E';
        $chatId = '138664577'; // ID бота
        //$chatId = '-1002527364494';//ID чата MyToolKit


        $storage = new TelegramStorage($botToken, $chatId);
        $file = $request->files->get('file');
        $filePath = $file->getRealPath();
        //var_dump(file_get_contents("https://api.telegram.org/bot$botToken/getUpdates"));die;
        // Сохранение файла
        $result = $storage->saveFile($filePath, 'file.png');
        if ($result) {
            echo "Файл сохранён. File ID: {$result['file_id']}, Message ID: {$result['message_id']}\n";

            // Получение ссылки на файл
            //$fileUrl = $storage->getFileUrl($result['file_id']);
            $fileUrl = "https://t.me/MyToolKit/".$result['message_id'];
            if ($fileUrl) {
                echo "Ссылка на файл: $fileUrl\n";
            } else {
                echo "Ошибка при получении ссылки на файл.\n";
            }
        } else {
            echo "Ошибка при сохранении файла.\n";
        }

        // Проверка существования файла
        if ($storage->fileExists($result['file_id'])) {
            echo "Файл существует.\n";
        } else {
            echo "Файл не существует.\n";
        }

        // Удаление файла
        if ($storage->deleteFile($result['message_id'])) {
            echo "Файл удалён.\n";
        } else {
            echo "Ошибка при удалении файла.\n";
        }
        die;
        return ['asdf'];
    }
}
