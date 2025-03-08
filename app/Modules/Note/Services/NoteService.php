<?php

declare(strict_types=1);

namespace App\Modules\Note\Services;

use App\DTOs\DTO;
use App\DTOs\DTOInterface;
use App\Modules\Note\DTOs\User\CreateNoteResponseDTO;
use App\Modules\Note\Http\Resources\User\NoteResource;
use App\Modules\Note\Models\Note;
use App\Modules\Note\DTOs\NoteDTO;
use App\Modules\Note\Models\NoteCategory;
use App\Modules\Note\DTOs\User\CreateNoteRequestDTO;
use App\Modules\Note\Models\NoteNoteCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\Builder;

class NoteService
{
    public function createNote(array $validatedData): array
    {
        $note =  Note::create($validatedData);

        $note->categories()->sync($validatedData['categories']);

        //$note->categories = NoteNoteCategory::where('note_id', $note->id)->pluck('note_category_id')->toArray();

        return [
            //'note' => $note,
            'success' => $note->exists
        ];
    }

    public function updateNote(array $validatedData, Note $note): array
    {
        $note->update($validatedData);

        $categoriesChanged = false;
        if (isset($validatedData['categories'])) {
            $synced = $note->categories()->sync($validatedData['categories']);
            $categoriesChanged = !empty($synced['attached']) || !empty($synced['detached']) || !empty($synced['updated']);
        }

        return [
            'success' => $note->wasChanged() || $categoriesChanged
        ];
    }

    public function updateNoteContent(array $validatedData, Note $note): array
    {
        $note->update($validatedData);

        return [
            'success' => $note->wasChanged()
        ];
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
            $notes->where('title', 'like', '%' . $search . '%');
        }
        $notes->limit(100)->orderBy('title', 'asc');

        return $notes->get()->toArray();
    }

    public function tree(array $validatedData): array
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

    public function findNotes(array $validatedData): AnonymousResourceCollection
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

        return NoteResource::collection($notesPaginated);
    }


//    public function assignCategories(Note $note, array $categoryIds): void
//    {
//        $categories = NoteCategory::find($categoryIds);
//        $note->categories()->sync($categories);
//    }
}
