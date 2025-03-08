<?php

declare(strict_types=1);

namespace App\Modules\Note\Validators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NoteFileValidator
{
    //private const MAX_FILE_SIZE = 102400; // 100 МБ
    private const ALLOWED_EXTENSIONS = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
        'video' => ['mp4', 'avi', 'mov'],
        'audio' => ['mp3', 'wav', 'ogg'],
        'text'  => ['txt', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ];

    private const MAX_FILE_SIZE = [
        'image' => 3000,
        'video' => 102400, //100 MБ
        'audio' => 10000,
        'text'  => 3000,
    ];


    /**
     * Проверяет файл на допустимые расширения и размер.
     *
     * @param UploadedFile $file
     * @throws ValidationException
     */
    public function validate(?UploadedFile $file, string $type): void
    {
//        $allowedExtensions = array_merge(
//            self::ALLOWED_EXTENSIONS['image'],
//            self::ALLOWED_EXTENSIONS['video'],
//            self::ALLOWED_EXTENSIONS['audio'],
//            self::ALLOWED_EXTENSIONS['text']
//        );
        $allowedExtensions = self::ALLOWED_EXTENSIONS[$type];

        Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'mimes:' . implode(',', $allowedExtensions),
                    'max:' . self::MAX_FILE_SIZE[$type],
                ],
            ]
        )->validate();
    }

    public static function getFileTypes(): array
    {
        return self::ALLOWED_EXTENSIONS;
    }


    public static function getMaxFileSize($type): int
    {
        return self::MAX_FILE_SIZE[$type];
    }
}
