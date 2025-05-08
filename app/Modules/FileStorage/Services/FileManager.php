<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services;


use App\Helpers\StringHelper;
use App\Modules\FileStorage\Models\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\MimeTypes;
use App\Helpers\Helper;

class FileManager
{
    private const CHUNK_SIZE = 1048576; // 1MB для потоковой передачи

    public function __construct(private readonly StringHelper $stringHelper) {}

    public function normalizeFileName($string): string
    {
        return $this->stringHelper->normalizeFileName($string);
    }

    public function download(File $file, string $downloadLink, ?bool $attachment): StreamedResponse|BinaryFileResponse
    {
        // Для больших файлов используем потоковую передачу
        if ($file->size > $this->getMemorySafeLimit()) {
            return $this->streamFile($file, $downloadLink, $attachment);
        }

        // Для маленьких файлов - обычный ответ
        return response()->file($downloadLink, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => $this->getContentDisposition($file, $attachment)
        ]);
    }

    /**
     * Определяет MIME-тип и расширение файла
     *
     * @param string $filePath Абсолютный путь к файлу
     * @param string|null $extension Предполагаемое расширение файла (необязательно)
     * @return array ['mime_type' => string, 'extension' => string]
     * @throws \RuntimeException Если файл не существует
     */
    public function getFileMimeTypeAndExtension(string $filePath, ?string $extension = null): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $mimeTypes = new MimeTypes();

        // 1. Определяем MIME-тип
        $mimeType = $mimeTypes->guessMimeType($filePath) ?? 'application/octet-stream';

        // 2. Определяем расширение
        $guessedExtensions = $mimeTypes->getExtensions($mimeType);
        $validExtension = null;

        // Проверяем переданное расширение на валидность
        if (!empty($extension)) {
            $extension = strtolower(trim($extension, '.'));
            if (in_array($extension, $guessedExtensions, true)) {
                $validExtension = $extension;
            }
        }

        // Если переданное расширение невалидно, берем первое из предполагаемых
        if (!$validExtension && !empty($guessedExtensions)) {
            $validExtension = $guessedExtensions[0];
        }

        // Если вообще не смогли определить - берем оригинальное расширение
        $originalExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $finalExtension = $validExtension ?: strtolower($originalExtension) ?: 'ext';

        return [$mimeType, $finalExtension];
    }

    private function streamFile(File $file, string $path, ?bool $attachment): StreamedResponse
    {
        return response()->stream(
            function () use ($path) {
                $stream = fopen($path, 'rb');
                while (!feof($stream)) {
                    echo fread($stream, self::CHUNK_SIZE);
                    flush();
                }
                fclose($stream);
            },
            200,
            [
                'Content-Type' => $file->mime_type,
                'Content-Length' => filesize($path),
                'Content-Disposition' => $this->getContentDisposition($file, $attachment)
            ]
        );
    }

    private function getContentDisposition(File $file, ?bool $attachment): string
    {
        if ($attachment) {
            $disposition = 'attachment';
        } else {
            // Для изображений показываем в браузере, остальное - скачиваем
            $disposition = str_starts_with($file->mime_type, 'image/') ? 'inline' : 'attachment';
        }

        return sprintf('%s; filename="%s"', $disposition, $file->name);
    }

    private function getMemorySafeLimit(): int
    {
        $memoryLimit = ini_get('memory_limit');

        // Преобразуем строку "2024M" в байты
        $value = (int) $memoryLimit;
        $unit = strtoupper(substr($memoryLimit, -1));

        switch ($unit) {
            case 'G':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'M':
                $value *= 1024 * 1024;
                break;
            case 'K':
                $value *= 1024;
                break;
        }

        // Возвращаем 80% от лимита (с запасом)
        return (int) ($value * 0.8);
    }
}
