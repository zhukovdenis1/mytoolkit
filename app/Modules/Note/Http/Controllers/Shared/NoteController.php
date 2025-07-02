<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\Shared;


use App\Helpers\EditorHelper;
use App\Http\Controllers\BaseController;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\Shared\NoteService;

class NoteController extends BaseController
{
    public function __construct(
        private readonly EditorHelper $editorHelper,
        private readonly NoteService $noteService,
    ) {
    }

    public function detail(string $noteId)
    {
        $note = Note::query()
            ->where('id', $noteId)
            ->whereNotNull('published_at')
            ->first();

        if (!$note) {
            abort(404);
        }

        $text = $this->editorHelper->jsonToHtml($note->text);
        $note->text = $text;

        $children = $this->noteService->tree((int) $noteId);

        return view('note', [
            'note' => $note,
            'children' => $children
        ]);
    }
}
