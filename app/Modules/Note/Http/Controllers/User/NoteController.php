<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Exceptions\ErrorException;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\FileStorage\Http\Requests\StoreFileRequest;
use App\Modules\FileStorage\Services\FileStorageService;
use App\Modules\Note\Http\Requests\User\AddCategoriesToNoteRequest;
use App\Modules\Note\Http\Requests\User\DropDownNoteRequest;
use App\Modules\Note\Http\Requests\User\SearchNoteRequest;
use App\Modules\Note\Http\Requests\User\StoreNoteRequest;
use App\Modules\Note\Http\Requests\User\TreeNoteRequest;
use App\Modules\Note\Http\Requests\User\UpdateContentNoteRequest;
use App\Modules\Note\Http\Requests\User\UpdateNoteRequest;
use App\Modules\Note\Http\Resources\User\NoteResource;
use App\Modules\Note\Http\Resources\User\NoteResourceCollection;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Services\NoteService;
use Illuminate\Auth\Access\AuthorizationException;

class NoteController extends BaseController
{
    public function __construct(private readonly NoteService $noteService) {}

    public function index(SearchNoteRequest $request): NoteResourceCollection
    {
        $notes = $this->noteService->findNotesPaginated(
            $request->withUserId()->validated()
        );
        return new NoteResourceCollection($notes);
    }

    public function getDropDown(DropDownNoteRequest $request): AnonymousResource
    {
        return new AnonymousResource(
            $this->noteService->getDropDownNotes(
                $request->withUserId()->validated()
            )
        );
    }

    public function tree(TreeNoteRequest $request): AnonymousResource
    {
        return new AnonymousResource(
            $this->noteService->tree(
                $request->withUserId()->validated()
            )
        );
    }

    public function store(StoreNoteRequest $request): NoteResource
    {
        $note = $this->noteService->create(
            $request->withUserId()->validated()
        );
        return new NoteResource($note);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(int $id): NoteResource
    {
        //$note = Note::with('categories')->findOrFail($id);
        $note = Note::with('categories:id')->findOrFail($id);
        $this->authorize('show', $note);
        return new NoteResource($note);
    }

    /**
     * @throws AuthorizationException
     * @throws ErrorException
     */
    public function update(UpdateNoteRequest $request, Note $note): NoteResource
    {
        $this->authorize('update', $note);
        $note = $this->noteService->update(
            $note,
            $request->validated()
        );
        return new NoteResource($note);
    }

    /**
     * @throws AuthorizationException
     * @throws ErrorException
     */
    public function updateContent(UpdateContentNoteRequest $request, Note $note): NoteResource
    {
        $this->authorize('update', $note);
        $note = $this->noteService->updateContent(
            $request->validated(),
            $note
        );
        return new NoteResource($note);
    }

    /**
     * @throws ErrorException
     * @throws AuthorizationException
     */
    public function destroy(Note $note): AnonymousResource
    {
        $this->authorize('destroy', $note);
        $this->noteService->destroy($note);
        return new AnonymousResource(["success" => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function addCategories(AddCategoriesToNoteRequest $request, Note $note): NoteResource
    {
        $this->authorize('update', $note);
        $categoryIds = $request->category_ids;
        $note->categories()->syncWithoutDetaching($categoryIds);
        //return $this->jsonResponse($note->load('categories')->toArray());
        return new NoteResource($note);
    }

    /**
     * @throws ErrorException
     * @throws AuthorizationException
     */
    public function storeFile(StoreFileRequest $request, Note $note, FileStorageService $service): AnonymousResource
    {
        $this->authorize('storeFile', $note);
        return new AnonymousResource($service->saveByRequest($request, (int)$note->id, 'note', true));
    }
}
