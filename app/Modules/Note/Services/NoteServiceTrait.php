<?php

declare(strict_types=1);

namespace App\Modules\Note\Services;

use App\Modules\Note\Models\Note;

trait NoteServiceTrait
{
    public function tree(int $parentId): array
    {
        //$parentId = isset($validatedData['parent_id']) ? intval($validatedData['parent_id']) : null;

        $data = [];
        $currentLevelIds = [$parentId];

        while (!empty($currentLevelIds)) {
            // Запрашиваем все узлы текущего уровня
            $notes = Note::select('id', 'title as name', 'parent_id', 'text')
                ->whereIn('parent_id', $currentLevelIds)
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

    public function parents(int $noteId): array
    {

        //$noteId = (int) $validatedData['note_id'] ?? 0;

        $data = [];
        $currentChildId = [$noteId];

        while (!empty($currentChildId)) {
            $note = Note::select('id', 'title as name', 'parent_id')
                ->where('id', $currentChildId)
                ->first()
                ->toArray();

            $data = array_merge($data, [$note]);

            $currentChildId = $note['parent_id'];
        }

        return array_reverse($data);
    }
}
