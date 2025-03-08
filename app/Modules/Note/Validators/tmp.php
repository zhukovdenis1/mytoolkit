<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\File;

use App\Http\Requests\BaseFormRequest;
use App\Modules\Note\Validators\NoteFileValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\File;

class StoreNoteFileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->verifyUser($this->route('note'));
    }

    public function rules(): array
    {
        return [
            'store_id' => 'required|integer|in:1',
            'link'     => 'nullable|string|max:1024|required_without:file',
            'file'     => 'nullable|file', // Базовая проверка, детали в prepareForValidation
        ];
    }

    /**
     * Подготавливает данные перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        // Если передан link, скачиваем файл и добавляем его в request как file
        if ($this->has('link') && !$this->hasFile('file')) {
            $this->downloadFileFromLink();
        }
    }

    /**
     * Скачивает файл по ссылке и добавляет его в request как file.
     *
     * @throws \Exception
     */
    private function downloadFileFromLink(): void
    {
        $link = $this->input('link');

        // Проверяем, что это валидный URL
        $validator = Validator::make(['link' => $link], [
            'link' => 'required|url',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Invalid link URL.');
        }

        // Извлекаем расширение из ссылки
        $fileExt = pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_EXTENSION);

        // Скачиваем файл по ссылке
        $tempFile = tempnam(sys_get_temp_dir(), 'linkfile');

        $res = file_put_contents($tempFile, file_get_contents($link));

        // Создаем объект UploadedFile из скачанного файла
        $uploadedFile = new UploadedFile(
            $tempFile,
            basename($link),
            mime_content_type($tempFile),
            filesize($tempFile),
            false, // Не перемещать файл
            true   // Тестовый режим
        );

        // Валидируем скачанный файл
        $fileValidator = new NoteFileValidator();
        $fileValidator->validate($uploadedFile);

        // Добавляем файл в request
        $this->files->set('file', $uploadedFile);
    }
}
