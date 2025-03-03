<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Note\Http\Requests\User\AddCategoriesToNoteRequest;
use App\Modules\Note\Http\Requests\User\DestroyNoteRequest;
use App\Modules\Note\Http\Requests\User\DropDownNoteRequest;
use App\Modules\Note\Http\Requests\User\SearchNoteRequest;
use App\Modules\Note\Http\Requests\User\TreeNoteRequest;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\NoteService;
use App\Modules\Note\Http\Requests\User\StoreNoteRequest;
use App\Modules\Note\Http\Requests\User\UpdateNoteRequest;
use App\Modules\Note\Http\Resources\User\NoteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoteController extends BaseController
{
    protected NoteService $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function index(SearchNoteRequest $request): AnonymousResourceCollection
    {
        return $this->noteService->findNotes($request->getWithUserId());
    }

    public function getDropDown(DropDownNoteRequest $request): array
    {
        return $this->noteService->getDropDownNotes($request->getWithUserId());
    }

    public function tree(TreeNoteRequest $request): array
    {
        return ['data' => $this->noteService->tree($request->getWithUserId())];
    }

    public function store(StoreNoteRequest $request): NoteResource
    {
        $data = $this->noteService->createNote($request->getWithUserId());
        return (new NoteResource($data['note']))->additional(['success' => $data['success']]);
    }

    public function show(int $id): NoteResource
    {
        //$note = Note::with('categories')->findOrFail($id);
        $note = Note::with('categories:id')->findOrFail($id);
        $this->abortWrongUser($note);
        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, Note $note): NoteResource
    {
        $data = $this->noteService->updateNote($request->validated(), $note);
        return  (new NoteResource($data['note']))->additional(['success' => $data['success']]);
    }

    public function destroy(DestroyNoteRequest $request, Note $note): JsonResponse
    {
        $wasDeleted = $note->delete();
        return $this->jsonResponse(['success' => $wasDeleted]);
    }

    public function addCategories(AddCategoriesToNoteRequest $request, Note $note): JsonResponse
    {
        $categoryIds = $request->category_ids;
        $note->categories()->syncWithoutDetaching($categoryIds);
        return $this->jsonResponse($note->load('categories')->toArray());
    }
}
