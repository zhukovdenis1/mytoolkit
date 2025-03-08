<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Note\Http\Requests\User\File\StoreNoteFileRequest;
use App\Modules\Note\Http\Resources\User\NoteFileResource;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\NoteFileService;

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
}
