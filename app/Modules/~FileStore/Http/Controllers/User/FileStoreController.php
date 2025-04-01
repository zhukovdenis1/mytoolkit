<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\FileStore\Http\Requests\User\StoreFileRequest;
use App\Modules\FileStore\Http\Resources\User\FileResource;
use App\Modules\FileStore\Services\FileStorageService;

class FileStoreController extends BaseController
{
    protected FileStorageService $fsService;

    public function __construct(FileStorageService $fsService)
    {
        $this->fsService = $fsService;
    }

    public function store(StoreFileRequest $request): FileResource
    {
        $data = $this->fsService->saveFile($request->getWithUserId());
        return (new FileResource($data['note']))->additional(['success' => $data['success']]);
    }
}
