<?php

declare(strict_types=1);

namespace App\Modules\Util\Http\Controllers\Shared;

use App\Http\Controllers\BaseController;
use App\Modules\Util\Services\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UtilController extends BaseController
{
    public function __construct(private readonly UtilService $utilService)
    {
    }

    public function index(Request $request, ?string $utilName = null)
    {
        if (is_null($utilName)) {
            return view('util.index');
        }

        if (method_exists($this, $utilName)) {
            return $this->$utilName($request);
        }

        abort(404);
    }

    private function copyPaste(Request $request)
    {
        $fileName = 'copypaste_my.txt';
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:1', 'max:100000'],
            'reset' => ['nullable', 'boolean']
        ]);

        if (!empty($validated['content'])) {
            $content = empty($validated['reset']) ? $validated['content'] : '';
            Storage::put($fileName, $content);
        }

        return view('util.copypaste', [
            'content' => Storage::get($fileName)
        ]);
    }

    private function aliexpress(Request $request)
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:1', 'max:1000000'],
        ]);

        return view('util.aliexpress', [
            'content' => $this->utilService->aliexpress($validated['content'] ?? null)
        ]);
    }

    private function parseJson(Request $request)
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:1', 'max:1000000'],
        ]);

        return view('util.parseJson', [
            'content' => $this->utilService->parseJson($validated['content'] ?? null)
        ]);
    }

    private function esb(Request $request)
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:1', 'max:1000000'],
        ]);

        return view('util.esb', [
            'content' => $this->utilService->esb($validated['content'] ?? null)
        ]);
    }

}
