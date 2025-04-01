<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Exceptions\ErrorException;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\Note\Http\Requests\User\Category\DestroyNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\SearchNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\ShowCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\StoreNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\UpdateNoteCategoryRequest;
use App\Modules\Note\Http\Resources\User\NoteCategoryResource;
use App\Modules\Note\Http\Resources\User\NoteCategoryResourceCollection;
use App\Modules\Note\Models\NoteCategory;
use App\Modules\Note\Services\NoteCategoryService;
use Illuminate\Auth\Access\AuthorizationException;

class NoteCategoryController extends BaseController
{

    public function __construct(private readonly NoteCategoryService $noteCategoryService) {}

    public function all(): NoteCategoryResourceCollection
    {
        $categories = NoteCategory::where('user_id', auth()->id());

        return new NoteCategoryResourceCollection($categories->get());
    }

    public function tree(): AnonymousResource
    {
        return new AnonymousResource($this->noteCategoryService->tree(auth()->id()));
    }

    public function index(SearchNoteCategoryRequest $request): NoteCategoryResourceCollection
    {
        $data = $this->noteCategoryService->find($request->withUserId()->validated());
        return new NoteCategoryResourceCollection($data);
    }

    public function store(StoreNoteCategoryRequest $request): NoteCategoryResource
    {
        $category = $this->noteCategoryService->save(new NoteCategory(), $request->withUserId()->validated());
        return new NoteCategoryResource($category);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(NoteCategory $category): NoteCategoryResource
    {
        $this->authorize('show', $category);
        return new NoteCategoryResource($category);
    }

    /**
     * @throws AuthorizationException
     * @throws ErrorException
     */
    public function update(UpdateNoteCategoryRequest $request, NoteCategory $category): NoteCategoryResource
    {
        $this->authorize('update', $category);
        $category = $this->noteCategoryService->update($category, $request->validated());
        return new NoteCategoryResource($category);
    }

    /**
     * @throws AuthorizationException
     * @throws ErrorException
     */
    public function destroy(NoteCategory $category): AnonymousResource
    {
        $this->authorize('destroy', $category);
        $this->noteCategoryService->destroy($category);
        return new AnonymousResource(["success" => true]);
    }
}
