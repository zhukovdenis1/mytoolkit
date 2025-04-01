<?php

declare(strict_types=1);

namespace App\Modules\Note\Services;
use App\Modules\Note\Models\NoteCategory;
use App\Services\BaseService;

class NoteCategoryService extends BaseService
{

    public function __construct(NoteCategory $category)
    {
        $this->model = $category;
    }
    public function find(array $validatedData): array
    {
        $categories = NoteCategory::where('user_id', $validatedData['user_id']);
        if ($search = $validatedData['search']) {
            $categories->where('name', 'like', '%' . $search . '%');
        }

        $categories->where('parent_id', $validatedData['parent_id']);

        return $categories->get();
    }


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
