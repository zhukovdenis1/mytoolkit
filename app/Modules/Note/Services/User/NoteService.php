<?php

declare(strict_types=1);

namespace App\Modules\Note\Services\User;

use App\Exceptions\ErrorException;
use App\Models\BaseModel;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Models\NoteCategory;
use App\Modules\Note\Services\NoteServiceTrait;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class NoteService extends BaseService
{
    use NoteServiceTrait;
    public function create(array $validatedData): Note
    {
        $validatedData = $this->prepareForSave($validatedData);
        $note =  Note::create($validatedData);

        $note->categories()->sync($validatedData['categories']);

        //$note->categories = NoteNoteCategory::where('note_id', $note->id)->pluck('note_category_id')->toArray();
        //$note->refresh();
        return $note;
    }

    public function update(Note|BaseModel $model, array $attributes): Note
    {
        $attributes = $this->prepareForSave($attributes, $model);
        $model->update($attributes);

        $categoriesChanged = false;
        if (isset($attributes['categories'])) {
            $synced = $model->categories()->sync($attributes['categories']);
            $categoriesChanged = !empty($synced['attached']) || !empty($synced['detached']) || !empty($synced['updated']);
        }

        if (!$model->wasChanged() && !$categoriesChanged) {
            throw new ErrorException('Data was not changed');
        }

        //$note->refresh();

        return $model;
    }

    private function prepareForSave(array $attributes, ?Note $model = null): array
    {
        $published = $attributes['published'] ?? false;
        unset($attributes['published']);
        if (is_null($model?->published_at) && $published) {
            $attributes['published_at'] = Carbon::now();
        } elseif (!$published) {
            $attributes['published_at'] = null;
        }

        return $attributes;
    }

    public function updateContent(array $validatedData, Note $note): Note
    {
        $note->update($validatedData);

        if (!$note->wasChanged()) {
            throw new ErrorException('Data was not changed');
        }

        return $note;
    }

    public function getDropDownNotes(array $validatedData): array
    {
        $id = isset($validatedData['id']) ? intval($validatedData['id']) : 0;
        $search = $validatedData['search'] ?? '';
        $userId = isset($validatedData['user_id']) ? intval($validatedData['user_id']) : 0;

        $notes = Note::select('id','title as name')->where('user_id', $userId);

        if ($id) {
            $notes->where('id', $id);
        } elseif ($search) {
            $notes
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('text', 'like', '%' . $search . '%');
        }
        $notes->limit(100)->orderBy('title', 'asc');

        return $notes->get()->toArray();
    }

/*    public function tree(array $validatedData): array
    {
        $userId = $validatedData['user_id'] ?? 0;
        $parentId = isset($validatedData['parent_id']) ? intval($validatedData['parent_id']) : null;

        $data = [];
        $currentLevelIds = [$parentId];

        while (!empty($currentLevelIds)) {
            // Запрашиваем все узлы текущего уровня
            $notes = Note::where('user_id', $userId)
                ->whereIn('parent_id', $currentLevelIds)
                ->select('id', 'title as name', 'parent_id', 'text')
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();

            // Добавляем полученные данные в общий массив
            $data = array_merge($data, $notes);

            // Собираем ID узлов текущего уровня для следующего запроса
            $nextLevelIds = array_column($notes, 'id');

            // Переходим на следующий уровень
            $currentLevelIds = $nextLevelIds;
        }

        return $this->buildNoteTree($data, $parentId);
    }

    private  function buildNoteTree(array $notes, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($notes as $note) {
            if ($note['parent_id'] === $parentId) {
                $children = $this->buildNoteTree($notes, $note['id']);
                $tree[] = [
                    'id'    => $note['id'],
                    'name'    => $note['name'],
                    'text' => ($note['text'] && $children) ? $note['text'] : '' ,
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    public function parents(array $validatedData): array
    {
        $userId = (int) $validatedData['user_id'] ?? 0;
        $noteId = (int) $validatedData['note_id'] ?? 0;

        $data = [];
        $currentChildId = [$noteId];

        while (!empty($currentChildId)) {
            $note = Note::where('user_id', $userId)
                ->where('id', $currentChildId)
                ->select('id', 'title as name', 'parent_id')
                ->first()
                ->toArray();

            $data = array_merge($data, [$note]);

            $currentChildId = $note['parent_id'];
        }

        return $data;
    }*/

    public function findNotesPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $notes = Note::with('categories:id,name')
            ->where('user_id', $validatedData['user_id']);

        //$notes = Note::where('user_id', $validatedData['user_id']);

        $search = $validatedData['search'] ?? null;
        $rCategories = $validatedData['categories'] ?? null;
        $page = empty($validatedData['_page']) ? 1 : intval($validatedData['_page']);
        $limit = empty($validatedData['_limit']) ? 10 : intval($validatedData['_limit']);
        $sortColumn = $validatedData['_sort'] ?? 'id';
        $order = $validatedData['_order'] ?? 'desc';


        if ($search) {
            $notes->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('text', 'like', '%' . $search . '%');
            });
        }

        if ($rCategories) {
            $categoriesWithSubcategories = collect($rCategories);

            // Рекурсивно получаем все подкатегории
            $getSubcategories = function ($categories) use (&$getSubcategories) {
                $subcategories = NoteCategory::whereIn('parent_id', $categories)->pluck('id');

                if ($subcategories->isNotEmpty()) {
                    return $subcategories->merge($getSubcategories($subcategories));
                }

                return $subcategories;
            };

            $allCategories = $categoriesWithSubcategories->merge($getSubcategories($rCategories))->unique();

            $notes->whereHas('categories', function (Builder $query) use ($allCategories) {
                $query->whereIn('id', $allCategories);
            });
        }


        //return NoteResource::collection($notes->get());

        //Сортировка

        $notes->orderBy($sortColumn, $order);

        //Пагинация
        $notesPaginated = $notes->paginate($limit, ['*'], 'page', $page);

        return $notesPaginated;

//        return [
//            'items' => $notesPaginated->items(),
//            'current_page' => $notesPaginated->currentPage(),
//            'per_page' => $notesPaginated->perPage(),
//            'total' => $notesPaginated->total()
//
//        ];

    }

//    public function assignCategories(Note $note, array $categoryIds): void
//    {
//        $categories = NoteCategory::find($categoryIds);
//        $note->categories()->sync($categories);
//    }


}
