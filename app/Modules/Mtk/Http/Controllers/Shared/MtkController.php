<?php

declare(strict_types=1);

namespace App\Modules\Mtk\Http\Controllers\Shared;


use App\Helpers\EditorHelper;
use App\Http\Controllers\BaseController;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\NoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MtkController extends BaseController
{
    public function __construct(private readonly EditorHelper $editorHelper)
    {
    }

    public function index()
    {
        $note = Note::query()
            ->where('id', 1)
            ->first();

        if (!$note) {
            abort(404);
        }

        $publicPart = json_decode($note->text ?? '', true);
        $privatePart = [array_pop($publicPart)];

        return view('home', [
            'public' => $this->editorHelper->arrayToHtml($publicPart),
            'private' => $this->editorHelper->arrayToHtml($privatePart),
            'user_id' => request()->user()?->id
        ]);
    }

    public function copyPaste(Request $request)
    {
        $fileName = 'copypaste.txt';
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:1', 'max:100000'],
            'reset' => ['nullable', 'boolean']
        ]);

        if (!empty($validated['content'])) {
            $content = empty($validated['reset']) ? $validated['content'] : '';
            Storage::put($fileName, $content);
        }

        return view('util.copypaste', [
            'content' => Storage::get($fileName)
        ]);
    }

    public function myIp(Request $request)
    {
        return view('myip', [
            'ip' => $request->ip(),
            'userAgent' => $request->userAgent()
        ]);
    }
}
