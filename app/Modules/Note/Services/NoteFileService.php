<?php

declare(strict_types=1);

namespace App\Modules\Note\Services;

use App\Helpers\Helper;
//use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Modules\Note\Validators\NoteFileValidator;

class NoteFileService
{
    //private $dir = '';

    public function saveFile(int $storeId, int $noteId, int $userId, UploadedFile $file): array
    {
        $success = false;
        $errorMessage = '';

        try {
            // Извлекаем расширение
            $fileExt = strtolower($file->getClientOriginalExtension());
            //$fileName = Helper::uid(8);
            //$fileName = strtolower(str_replace('lin', '', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
            $fileName = Helper::normalizeFileName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));


            // Генерируем путь для сохранения файла
            $path = 'uploads/' . $userId . '/notes/' . $noteId . '/' . $fileName . '.' . $fileExt;
            $fullPath = getcwd() . '/' . $path;

            if (file_exists($fullPath)) {
                throw new \Exception('File with name "' . $fileName . '.' . $fileExt . '" already exists.');
            }

            // Создаем директории рекурсивно
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            // Сохраняем файл
            //$success = Storage::put($path, file_get_contents($file->getRealPath()));
            copy($file->getRealPath(), $fullPath);

            $success = file_exists($fullPath);

            if (!$success) {
                throw new \Exception('Failed to save file.');
            }

            // Определяем тип файла
            $type = $this->getFileType($fileExt);

            // Получаем размер файла
            //$size = Storage::size($path);
            $size = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Получаем ширину и высоту (для изображений)
            $width = '';
            $height = '';
            if ($type === 'image') {
                //[$width, $height] = getimagesize(Storage::path($path));
                [$width, $height] = getimagesize($fullPath);
            }

            return [
                'data' => [
                    'path'   => $path,
                    'type'   => $type,
                    'size'   => $size,
                    'width'  => $width,
                    'height' => $height,
                    'store_id' => $storeId,
                ],
                'message' => $errorMessage,
                'success' => $success,
            ];
        } catch (\Exception $e) {
            // Удаляем файл, если он не прошел валидацию
            /*if (isset($path)) {
                Storage::delete($path);
            }*/
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return [
                'data' => [],
                'message' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function getFileType(string $extension): string
    {
        $allowedTypes = NoteFileValidator::getFileTypes();
        foreach ($allowedTypes as $type => $extensions) {
            if (in_array(strtolower($extension), $extensions)) {
                return $type;
            }
        }

        return '';
    }
}
