<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services;

use App\Exceptions\ErrorException;
use App\Helpers\Helper;
use App\Modules\FileStorage\Http\Requests\StoreFileRequest;
use App\Modules\FileStorage\Models\Enums\StorageType;
use App\Modules\FileStorage\Models\File;
use App\Modules\FileStorage\Models\Storage;
use App\Modules\FileStorage\Services\StorageManager\StorageFactory;
use App\Modules\Note\Validators\NoteFileValidator;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;


readonly class FileStorageService
{
    public function __construct(private FileManager $fileManager) {}

    /**
     * @throws ErrorException
     */
    public function saveByRequest(StoreFileRequest $request, int $moduleId, string $moduleName, bool $makeReserveFile): array
    {
        return $this->saveFile(
            (int) $request->input('storage_id'),
            (bool) $request->input('private'),
            (int) $request->user()->id,
            $moduleId,
            $moduleName,
            $makeReserveFile,
            $request->files->get('file')//$request->file('file')-так не работает с файлами через link
        );
    }

    /**
     * @throws ErrorException
     * @throws \Exception
     */
    private function saveFile(
        int $storageId,
        bool $isPrivate,
        int $userId,
        int $moduleId,
        string $moduleName,
        bool $makeReserveFile,
        UploadedFile $uploadedFile
    ): array {

        $filePath = $uploadedFile->getRealPath();

        if (!file_exists($filePath)) {
            throw new ErrorException('File was not saved');
        }
        //$ext = strtolower($uploadedFile->getClientOriginalExtension());
        [$mimeType, $extension] = $this->fileManager->getFileMimeTypeAndExtension(
            $filePath,
            $uploadedFile->getClientOriginalExtension()
        );
        $type = $this->getFileType($extension);
        $size = filesize($filePath);
        $name = $this->fileManager->normalizeFileName(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $data = [];
        if ($type === 'image') {
            [$data['width'], $data['height']] = getimagesize($filePath);
        }

        $fileData = [
            'user_id' => $userId,
            'name' => $name,
            'ext' => $extension,
            'module_id' => $moduleId,
            'module_name' => $moduleName
        ];

        /** @var File $file */
        $file = File::where($fileData)->first();

        if ($file) {
            throw new ErrorException('File already exists');
        }

        /** @var File $file */
        $file = File::create([
            ...$fileData,
            'storage_id' => $storageId,
            'data' => $data,
            'type' => $type,
            'mime_type' => $mimeType,
            'size' => $size,
            'private_hash' => $isPrivate ? Helper::uid(8) : null,
            'cached_until' => ($type == StorageType::HOSTING) ? null : (Carbon::now())->addSeconds(Config::get('fileStorage.ttl'))
        ]);

        if (!$file->exists) {
            throw new ErrorException('File was not saved');
        }

        try {
            /** @var Storage $storage */
            $storage = $file->storage()->first();
            $storageManager = StorageFactory::create($storage);

            //сохраняем файл
            $saveResult = $storageManager->saveFile($file, $uploadedFile->getRealPath());

            //удаляем запись в бд, если файл не сохранился
            if (empty($saveResult['success'])) {
                $file->delete();
                throw new ErrorException('File was not saved');
            }

            //Создаём кэш файл, если он разрешен для данного Storage
            if ($storageManager->cache?->allowed($file)) {
                $storageManager->cache->save($file, $uploadedFile->getRealPath());
            }

            //Создаем резервный файл, если он предусмотрен
            if ($storage->backup_id && $makeReserveFile) {
                /** @var Storage $backupStorage */
                $backupStorage = $storage->backup()->first();
//            if ($backupStorage->type == $storage->type) {
//                throw new ErrorException('Backup storage should have another type');
//            }
                $backupStorageManager = StorageFactory::create($backupStorage);
                $backupSaveResult = $backupStorageManager->saveFile($file, $uploadedFile->getRealPath());
                if ($backupSaveResult['success']) {
                    //добавляем в data данные бэкапа
                    $saveResult['data']['backup'] = $backupSaveResult['data'];
                }//если ошибка создания бэкапа - не критично
            }

            $file->update(['data' => array_merge($data, $saveResult['data'])]);

            //$url = (app()->isProduction()) ? '' : Config::get('app.url');

            return [
                'id' => $file->id,
                'url_inline' => $storageManager->getUrl($file),
                'url_attachment' => $storageManager->getUrl($file, true),
                'size' => $file->size,
                'extra' => $data,
            ];

        } catch (Error $e) {
            $file->delete();
            if (app()->isProduction()) {
                throw new ErrorException('File was not saved');
            } else {
                throw new Error($e->getMessage());
            }
        }
    }

    /**
     * @throws ErrorException
     * @throws \Exception
     */
    public function delete(File $file): bool
    {
        /** @var Storage $storage */
        $storage = $file->storage()->first();
        $storageManager = StorageFactory::create($storage);
        try {
            $deleted = $storageManager->deleteFile($file);
        } catch (\Exception $e) {
            if ($storageManager->isDeleted($file)) {
                $deleted = true;
            } else {
                $deleted = false;
            }

        }

        if (!$deleted || !$file->delete()) {
            throw new ErrorException('File was not deleted');
        }
        if (!empty($file->data['backup'])) {
            foreach ($file->data['backup'] as $backupType => $backupData) {
                /** @var Storage $backupStorage */
                $backupStorage = $storage->backup()->first();
                $backupStorageManager = StorageFactory::create($backupStorage);
                $backupStorageManager->deleteFile($file, true);
            }
        }

        $storageManager->cache?->delete($file);

        return true;
    }

    public function download(File $file, ?bool $attachment = null): StreamedResponse|BinaryFileResponse
    {
        /** @var Storage $storage */
        $storage = $file->storage()->first();
        $storageManager = StorageFactory::create($storage);

        $downloadPath = $storageManager->getDownloadPath($file);

        if (!$storageManager->cache?->exists($file) && $storageManager->cache?->allowed($file)) {
            $storageManager->cache->refresh($file, $downloadPath);
            $file->requested_at = now();
            $file->request_counter +=1;
            $file->save();
        }

        if ($storageManager->cache?->exist($file)) {
            $downloadPath = $storageManager->cache->getFullPath($file);
        }

        return $this->fileManager->download($file, $downloadPath, $attachment);
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
