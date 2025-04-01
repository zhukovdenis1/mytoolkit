<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Http\Controllers\User;

use App\Exceptions\ErrorException;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;

use App\Modules\FileStorage\Http\Requests\GetFileRequest;
use App\Modules\FileStorage\Models\File;
use App\Modules\FileStorage\Services\FileStorageService;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageController extends BaseController
{
    public function __construct(private readonly FileStorageService $service) {}

    /**
     * @throws AuthorizationException
     */
    public function get(
        GetFileRequest $request,
        int $userId,
        string $moduleName,
        int $moduleId,
        File $file
    ): StreamedResponse|BinaryFileResponse {
        $this->authorize('view', $file);
        return $this->service->download($file);
    }

    /**
     * @throws ErrorException
     * @throws AuthorizationException
     */
    public function delete(
        File $file
    ): AnonymousResource {
        //var_dump(Illuminate\Support\Facades\Gate::getPolicyFor($file));die;
        $this->authorize('delete', $file);
        return new AnonymousResource(['success' => $this->service->delete($file)]);
    }
}

