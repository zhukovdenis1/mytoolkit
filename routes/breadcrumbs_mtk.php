<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::macro('setDefault', function(BreadcrumbTrail $trail, int $popCounter = 0) {
    $currentPath = request()->path();
    $segments = explode('/', $currentPath);

    $brcr = [['title' => 'Главная', 'path' => route('home') ]];

    $accumulatedPath = '';
    foreach ($segments as $segment) {
        $accumulatedPath .= ($accumulatedPath ? '/' : '') . $segment;

        // Специальные названия для некоторых сегментов
        $title = match($segment) {
            'utils' => 'Утилиты',
            default => ucfirst($segment)
        };

        $brcr[] = ['title' => $title, 'path' => url($accumulatedPath)];
    }

    for ($i = 0; $i < count($brcr) - $popCounter; $i++) {
        $trail->push($brcr[$i]['title'], $brcr[$i]['path']);
    }
});

Breadcrumbs::for('default', function (BreadcrumbTrail $trail): void
{
    $routeName = request()->route()?->getName();

    if ($routeName && Breadcrumbs::exists($routeName)) {
        Breadcrumbs::render($routeName, ...array_values(request()->route()->parameters()));
    } else {
        Breadcrumbs::setDefault($trail);
    }
});

Breadcrumbs::for('home', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('note.detail', function (BreadcrumbTrail $trail, string $noteId): void
{
    $noteService = app(\App\Modules\Note\Services\Shared\NoteService::class);
    $children = $noteService->parents((int) $noteId) ?? [];
    //$trail->parent('auto.path');
    Breadcrumbs::setDefault($trail, 1);
    foreach ($children as $child) {
        $trail->push($child['name'], route('note.detail', ['noteId' => $child['id']]));
    }
});

Breadcrumbs::for('music', function (BreadcrumbTrail $trail): void
{
    $trail->parent('home');
    $trail->push('Песни', route('music'));
});

Breadcrumbs::for('music.detail', function (BreadcrumbTrail $trail, \App\Modules\Music\Models\Song $song): void
{
    $trail->parent('music');
    $trail->push($song->title, '#');
});
