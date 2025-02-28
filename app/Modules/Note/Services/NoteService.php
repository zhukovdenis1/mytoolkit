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
            'note' => $note,
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
            'note' => $note,
            'success' => $note->wasChanged() || $categoriesChanged
        ];
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


    public function assignCategories(Note $note, array $categoryIds): void
    {
        $categories = NoteCategory::find($categoryIds);
        $note->categories()->sync($categories);
    }
}
