<?php

declare(strict_types=1);

namespace App\Modules\Main\Http\Controllers\User;


use App\Helpers\EditorHelper;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\Note\Models\Note;
use Illuminate\Http\Request;



class MainController extends BaseController
{

    public function __construct(private readonly EditorHelper $editorHelper)
    {
    }
    public function links(Request $request): AnonymousResource
    {
        //$request->user->isAdmin()
        $note = Note::query()
            ->where('id', 1)
            ->first();
        if (!$note) {
            $data = null;
        } else {
            $data = json_decode($note->text ?? '', true);
        }

        return new AnonymousResource(["html" => $this->editorHelper->arrayToHtml($data)]);

    }
}
