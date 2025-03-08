<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Note\Http\Requests\User\Category\DestroyNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\SearchNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\ShowCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\StoreNoteCategoryRequest;
use App\Modules\Note\Http\Requests\User\Category\UpdateNoteCategoryRequest;
use App\Modules\Note\Http\Resources\User\NoteCategoryResource;
use App\Modules\Note\Http\Resources\User\NoteCategoryResourceCollection;
use App\Modules\Note\Models\NoteCategory;
use App\Modules\Note\Services\NoteCategoryService;
use Illuminate\Http\JsonResponse;

class NoteCategoryController extends BaseController
{
    protected NoteCategoryService $noteCategoryService;

    public function __construct(NoteCategoryService $noteCategoryService)
    {
        $this->noteCategoryService = $noteCategoryService;
    }

    public function all(): NoteCategoryResourceCollection
    {
        $categories = NoteCategory::where('user_id', auth()->id());

        return new NoteCategoryResourceCollection($categories->get());
    }

    public function tree(): array
    {
        return ['data' => $this->noteCategoryService->tree(auth()->id())];
    }

    public function index(SearchNoteCategoryRequest $request): NoteCategoryResourceCollection
    {
        $categories = NoteCategory::where('user_id', auth()->id());
        if ($search = $request->validated('search')) {
            $categories->where('name', 'like', '%' . $search . '%');
        }

        $categories->where('parent_id', $request->validated('parent_id'));

        return new NoteCategoryResourceCollection($categories->get());
    }

    public function store(StoreNoteCategoryRequest $request): NoteCategoryResource
    {
        $category = NoteCategory::create($request->getWithUserId());
        return (new NoteCategoryResource($category))->additional(['success' => $category->exists]);
    }

    public function show(ShowCategoryRequest $request, NoteCategory $category): NoteCategoryResource
    {
        return new NoteCategoryResource($category);
    }

    public function update(UpdateNoteCategoryRequest $request, NoteCategory $category): NoteCategoryResource
    {
        $category->update($request->validated());
        return (new NoteCategoryResource($category))->additional(['success' => $category->wasChanged()]);
    }

    public function destroy(DestroyNoteCategoryRequest $request, NoteCategory $category): JsonResponse
    {
        $wasDeleted = $category->delete();
        return $this->jsonResponse(['success' => $wasDeleted]);
    }
}
