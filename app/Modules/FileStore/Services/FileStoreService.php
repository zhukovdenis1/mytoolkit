<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Services;

use App\Helpers\Helper;
//use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Modules\Note\Validators\NoteFileValidator;
use App\Exceptions\ErrorException;

class FileStoreService
{
    //private $dir = '';

    public function saveFile(int $storeId, int $noteId, int $userId, UploadedFile $file): array
    {
        // Извлекаем расширение
        $fileExt = strtolower($file->getClientOriginalExtension());
        //$fileName = Helper::uid(8);
        //$fileName = strtolower(str_replace('lin', '', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
        $fileName = Helper::normalizeFileName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        // Генерируем путь для сохранения файла
        $path = $this->getPath($storeId, $noteId, $userId) . $fileName . '.' . $fileExt;
        $fullPath = getcwd() . '/' . $path;

        $warnings = [];

        try {
            if (file_exists($fullPath)) {
                //$warnings[] = 'File with name "' . $fileName . '.' . $fileExt . '" already exists.';
                throw new ErrorException('File with name "' . $fileName . '.' . $fileExt . '" already exists.');
            } else {
                // Создаем директории рекурсивно
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
                }

                // Сохраняем файл
                //$success = Storage::put($path, file_get_contents($file->getRealPath()));
                copy($file->getRealPath(), $fullPath);

                if (!file_exists($fullPath)) {
                    throw new ErrorException('Failed to save file.');
                }
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
                'success' => true,
                'warnings' => $warnings
            ];
        } catch (ErrorException $e) {
            // Удаляем файл, если он не прошел валидацию
            /*if (isset($path)) {
                Storage::delete($path);
            }*/

            return [
                'data' => [],
                'errors' => [$e->getMessage()],
                'success' => false,
            ];
        }
    }

    public function deleteFile(int $storeId, int $noteId, int $userId, string $path): array
    {
        $success = false;
        $errors = false;

        $fileName = basename($path);

        $path = $this->getPath($storeId, $noteId, $userId) . $fileName;
        $fullPath = getcwd() . '/' . $path;

        if (!is_file($fullPath)) {
            $success = true;
            $errors = ['File with name "' . $fileName . '" does not exist.'];
        } else {
            $success = unlink($fullPath);
        }

        return [
            'errors' => $errors,
            'success' => $success,
        ];
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

    private function getPath(int $storeId, int $noteId, int $userId): string
    {
        return 'uploads/' . $userId . '/notes/' . $noteId . '/';
    }

    private function getResource($userId, int $storeId)
    {

    }
}
