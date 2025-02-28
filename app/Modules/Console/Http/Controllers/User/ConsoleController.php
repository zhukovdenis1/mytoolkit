<?php

declare(strict_types=1);

namespace App\Modules\Console\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Console\Http\Requests\User\RunCommandRequest;
use Illuminate\Support\Facades\Artisan;


class ConsoleController extends BaseController
{
    public function runCommand(RunCommandRequest $request): \Illuminate\Http\JsonResponse
    {

        // Вызов команды и захват вывода
        Artisan::call($request->command, [
            //'argument' => $request->input('argument'), // Передача аргумента
            //'--option' => $request->input('option'),    // Передача опции
        ]);

        // Получение вывода команды
        $output = Artisan::output();

        // Возврат вывода в ответе
        return response()->json([
            'output' => $output,
        ]);
    }
}
