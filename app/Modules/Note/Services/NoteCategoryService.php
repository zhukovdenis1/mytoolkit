<?php

declare(strict_types=1);

namespace App\Modules\Note\Services;
use App\Modules\Note\Models\NoteCategory;

class NoteCategoryService
{
    public function tree(int $userId): array
    {
        $categories = NoteCategory::where('user_id', $userId)
            ->select('id', 'name', 'parent_id')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
        return $this->buildCategoryTree($categories);
    }

    private  function buildCategoryTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $tree[] = [
                    'id'    => $category['id'],
                    'name'    => $category['name'],
                    'children' => $this->buildCategoryTree($categories, $category['id']),
                ];
            }
        }

        return $tree;
    }
}
